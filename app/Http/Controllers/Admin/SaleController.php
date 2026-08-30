<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SaleController extends Controller
{
    // List all sales
    public function index()
    {
        $sales = Sale::withCount('items')
            ->orderBy('sale_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('admin/sales/Index', [
            'sales' => $sales,
        ]);
    }

    // Show create form
    public function create()
    {
        $inventoryItems = InventoryItem::orderBy('name')
            ->where('quantity', '>', 0) // Only show items with stock
            ->get(['id', 'name', 'sku', 'quantity', 'cost_price']);

        return Inertia::render('admin/sales/Create', [
            'inventoryItems' => $inventoryItems,
        ]);
    }

    // Store sale
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.total_price' => 'required|numeric|min:0.01',
        ]);

        // Check for duplicate items
        $itemIds = array_column($validated['items'], 'inventory_item_id');
        if (count($itemIds) !== count(array_unique($itemIds))) {
            return back()->withErrors(['items' => 'Duplicate items are not allowed in one sale.']);
        }

        DB::transaction(function () use ($validated) {
            $totalItems = count($validated['items']);
            $totalQuantity = 0;
            $totalAmount = 0;

            // Create sale
            $sale = Sale::create([
                'sale_date' => $validated['sale_date'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $inventoryItem = InventoryItem::find($item['inventory_item_id']);

                // Check stock
                if ($inventoryItem->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for: {$inventoryItem->name}. Available: {$inventoryItem->quantity}");
                }

                // Calculate values
                $unitPrice = $item['total_price'] / $item['quantity'];
                $costPrice = $inventoryItem->cost_price;
                $profit = $costPrice ? ($unitPrice - $costPrice) * $item['quantity'] : null;

                // Create sale item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['total_price'],
                    'unit_price' => $unitPrice,
                    'cost_price' => $costPrice,
                    'profit' => $profit,
                ]);

                // Deduct stock
                $inventoryItem->quantity -= $item['quantity'];
                $inventoryItem->save();

                // Accumulate totals
                $totalQuantity += $item['quantity'];
                $totalAmount += $item['total_price'];
            }

            // Update sale totals
            $sale->update([
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
                'total_amount' => $totalAmount,
            ]);
        });

        return redirect('/admin/sales')->with('success', 'Sale recorded successfully.');
    }

    // View single sale
    public function show(Sale $sale)
    {
        // $sale->load(['items.inventoryItem']);

        // Eager load the relationships
        $sale->load(['items.inventoryItem']);

        // Make sure each item has the required fields
        // If some are missing, provide defaults
        foreach ($sale->items as $item) {
            if ($item->profit === null && $item->cost_price !== null && $item->unit_price !== null) {
                $item->profit = ($item->unit_price - $item->cost_price) * $item->quantity;
            }
        }

        return Inertia::render('admin/sales/Show', [
            'sale' => $sale,
        ]);
    }

    // Delete sale (revert stock)
    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            // Revert stock
            foreach ($sale->items as $item) {
                $inventoryItem = InventoryItem::find($item->inventory_item_id);
                if ($inventoryItem) {
                    $inventoryItem->quantity += $item->quantity;
                    $inventoryItem->save();
                }
            }
            $sale->delete();
        });

        return redirect('/admin/sales')->with('success', 'Sale deleted and stock reverted.');
    }
}