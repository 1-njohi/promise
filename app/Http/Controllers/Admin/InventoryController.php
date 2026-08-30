<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockReceipt;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InventoryController extends Controller
{
    // Current stock listing
    public function index(Request $request)
    {
        $query = InventoryItem::query();

        // Search by name or SKU
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        // Filter low stock
        if ($request->has('low_stock')) {
            $query->whereColumn('quantity', '<=', 'reorder_level');
        }

        // Filter out of stock
        if ($request->has('out_of_stock')) {
            $query->where('quantity', 0);
        }

        $inventory = $query->orderBy('name')->paginate(20)->withQueryString();

        return Inertia::render('admin/inventory/Index', [
            'inventory' => $inventory,
            'filters' => $request->only(['search', 'low_stock', 'out_of_stock']),
        ]);
    }

    // Show Add Stock form
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $inventoryItems = InventoryItem::orderBy('name')->get();

        return Inertia::render('admin/inventory/Create', [
            'suppliers' => $suppliers,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    // Store new stock receipt
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.sku' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        // Check for duplicate SKUs within the same receipt
        $skus = array_column($validated['items'], 'sku');
        if (count($skus) !== count(array_unique($skus))) {
            return back()->withErrors(['items' => 'Duplicate SKUs are not allowed in one receipt.']);
        }

        DB::transaction(function () use ($validated) {
            // Create receipt
            $receipt = StockReceipt::create([
                'supplier_id' => $validated['supplier_id'],
                'receipt_date' => $validated['receipt_date'],
                'notes' => $validated['notes'],
            ]);

            // Process each item
            foreach ($validated['items'] as $item) {
                // Find or create inventory item by SKU
                $inventoryItem = InventoryItem::firstOrCreate(
                    ['sku' => $item['sku']],
                    [
                        'name' => $item['name'],
                        'quantity' => 0,
                        'cost_price' => $item['cost_price'] ?? null,
                        'reorder_level' => 0,
                    ]
                );

                // Update name if it changed
                if ($inventoryItem->name !== $item['name']) {
                    $inventoryItem->name = $item['name'];
                    $inventoryItem->save();
                }

                // Create receipt item
                $receipt->items()->create([
                    'inventory_item_id' => $inventoryItem->id,
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'] ?? null,
                ]);

                // Update inventory quantity
                $inventoryItem->quantity += $item['quantity'];
                if (isset($item['cost_price'])) {
                    $inventoryItem->cost_price = $item['cost_price'];
                }
                $inventoryItem->save();
            }
        });

        return redirect('/admin/inventory')->with('success', 'Stock added successfully.');
    }
    // List receipts
    public function receipts()
    {
        $receipts = StockReceipt::with('supplier')
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->get();

        $receipts->each(function ($receipt) {
            $receipt->total_quantity = $receipt->items()->sum('quantity');
        });

        return Inertia::render('admin/inventory/Receipts', [
            'receipts' => $receipts,
        ]);
    }

    // Delete a receipt (revert stock)
    public function destroyReceipt(StockReceipt $receipt)
    {
        DB::transaction(function () use ($receipt) {
            foreach ($receipt->items as $item) {
                $inventoryItem = InventoryItem::find($item->inventory_item_id);
                if ($inventoryItem) {
                    $newQty = $inventoryItem->quantity - $item->quantity;
                    if ($newQty < 0) {
                        $newQty = 0;
                    }
                    $inventoryItem->quantity = $newQty;
                    $inventoryItem->save();
                }
            }
            $receipt->delete();
        });

        return redirect('/admin/inventory/receipts')->with('success', 'Receipt deleted and stock reverted.');
    }

    // CRUD for inventory items
    public function itemsIndex()
    {
        $items = InventoryItem::orderBy('name')->get();
        return Inertia::render('admin/inventory/items/Index', [
            'items' => $items,
        ]);
    }

    public function itemsCreate()
    {
        return Inertia::render('admin/inventory/items/Create');
    }

    public function itemsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:inventory_items,sku',
            'reorder_level' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        InventoryItem::create([
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'quantity' => 0,
            'reorder_level' => $validated['reorder_level'] ?? 0,
            'location' => $validated['location'] ?? null,
        ]);

        return redirect('/admin/inventory/items')->with('success', 'Inventory item created.');
    }

    public function itemsEdit(InventoryItem $item)
    {
        return Inertia::render('admin/inventory/items/Edit', [
            'item' => $item,
        ]);
    }

    public function itemsUpdate(Request $request, InventoryItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:inventory_items,sku,' . $item->id,
            'reorder_level' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        $item->update($validated);

        return redirect('/admin/inventory/items')->with('success', 'Inventory item updated.');
    }

    public function itemsDestroy(InventoryItem $item)
    {
        $item->delete();
        return redirect('/admin/inventory/items')->with('success', 'Inventory item deleted.');
    }
}