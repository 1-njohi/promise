<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CatalogueItem;
use App\Models\InventoryItem;
use App\Models\Sale;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Catalogue Metrics
        $totalProducts = CatalogueItem::count();
        $totalCategories = Category::count();
        $visibleProducts = CatalogueItem::where('is_visible', true)->count();
        $hiddenProducts = CatalogueItem::where('is_visible', false)->count();

        // Inventory Metrics
        $totalInventoryItems = InventoryItem::count();
        $totalStockValue = InventoryItem::sum(DB::raw('quantity * cost_price'));
        $lowStockItems = InventoryItem::whereColumn('quantity', '<=', 'reorder_level')->count();
        $outOfStockItems = InventoryItem::where('quantity', 0)->count();
        $totalSuppliers = Supplier::count();

        // Sales Metrics
        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('total_amount');
        
        // Sales by period
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $salesToday = Sale::whereDate('sale_date', $today)->count();
        $revenueToday = Sale::whereDate('sale_date', $today)->sum('total_amount');
        
        $salesThisWeek = Sale::where('sale_date', '>=', $thisWeek)->count();
        $revenueThisWeek = Sale::where('sale_date', '>=', $thisWeek)->sum('total_amount');
        
        $salesThisMonth = Sale::where('sale_date', '>=', $thisMonth)->count();
        $revenueThisMonth = Sale::where('sale_date', '>=', $thisMonth)->sum('total_amount');

        // Total Profit (from sales)
        $totalProfit = Sale::with('items')
            ->get()
            ->sum(function ($sale) {
                return $sale->items->sum('profit') ?? 0;
            });

        // Average Sale Value
        $averageSaleValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Recent Sales (last 5)
        $recentSales = Sale::with(['items.inventoryItem'])
            ->orderBy('sale_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'sale_date' => $sale->sale_date,
                    'reference' => $sale->reference,
                    'total_amount' => $sale->total_amount,
                    'total_quantity' => $sale->total_quantity,
                    'items_count' => $sale->items->count(),
                ];
            });

        // Low Stock Items (list)
        $lowStockItemsList = InventoryItem::whereColumn('quantity', '<=', 'reorder_level')
            ->where('quantity', '>', 0)
            ->orderBy('quantity')
            ->limit(10)
            ->get(['id', 'name', 'sku', 'quantity', 'reorder_level']);

        // Top Selling Items (by quantity)
        $topSellingItems = DB::table('sale_items')
            ->join('inventory_items', 'sale_items.inventory_item_id', '=', 'inventory_items.id')
            ->select(
                'inventory_items.id',
                'inventory_items.name',
                'inventory_items.sku',
                DB::raw('SUM(sale_items.quantity) as total_quantity_sold'),
                DB::raw('SUM(sale_items.total_price) as total_revenue'),
                DB::raw('SUM(sale_items.profit) as total_profit')
            )
            ->groupBy('inventory_items.id', 'inventory_items.name', 'inventory_items.sku')
            ->orderBy('total_quantity_sold', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'metrics' => [
                'catalogue' => [
                    'total_products' => $totalProducts,
                    'total_categories' => $totalCategories,
                    'visible_products' => $visibleProducts,
                    'hidden_products' => $hiddenProducts,
                ],
                'inventory' => [
                    'total_items' => $totalInventoryItems,
                    'total_stock_value' => $totalStockValue,
                    'low_stock_items' => $lowStockItems,
                    'out_of_stock_items' => $outOfStockItems,
                    'total_suppliers' => $totalSuppliers,
                ],
                'sales' => [
                    'total_sales' => $totalSales,
                    'total_revenue' => $totalRevenue,
                    'total_profit' => $totalProfit,
                    'average_sale_value' => $averageSaleValue,
                    'today' => [
                        'sales' => $salesToday,
                        'revenue' => $revenueToday,
                    ],
                    'this_week' => [
                        'sales' => $salesThisWeek,
                        'revenue' => $revenueThisWeek,
                    ],
                    'this_month' => [
                        'sales' => $salesThisMonth,
                        'revenue' => $revenueThisMonth,
                    ],
                ],
            ],
            'recent_sales' => $recentSales,
            'low_stock_items' => $lowStockItemsList,
            'top_selling_items' => $topSellingItems,
        ]);
    }
}