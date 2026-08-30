<script setup>
import { Head, Link } from "@inertiajs/vue3";

import Breadcrumb from "@/components/Breadcrumb.vue";

const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Dashboard' },
];

const props = defineProps({
    metrics: Object,
    recent_sales: Array,
    low_stock_items: Array,
    top_selling_items: Array,
});

// Format currency
const formatCurrency = (amount) => {
    if (amount === null || amount === undefined || isNaN(amount)) return '0.00';
    return Number(amount).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
};

// Format date
const formatDate = (date) => {
    if (!date) return "-";
    try {
        return new Date(date).toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    } catch {
        return date;
    }
};
</script>

<template>
    <div class="p-6 sm:py-12">

        <Breadcrumb :breadcrumbs="breadcrumbs" />
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Catalogue Overview -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">
                    📦 Catalogue
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Total Products</div>
                        <div class="text-2xl font-bold text-indigo-600">
                            {{ metrics.catalogue.total_products }}
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Categories</div>
                        <div class="text-2xl font-bold text-indigo-600">
                            {{ metrics.catalogue.total_categories }}
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Visible</div>
                        <div class="text-2xl font-bold text-green-600">
                            {{ metrics.catalogue.visible_products }}
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Hidden</div>
                        <div class="text-2xl font-bold text-gray-600">
                            {{ metrics.catalogue.hidden_products }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Overview -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">
                    📊 Inventory
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Total Items</div>
                        <div class="text-2xl font-bold text-indigo-600">
                            {{ metrics.inventory.total_items }}
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Total Value</div>
                        <div class="text-2xl font-bold text-green-600">
                            {{
                                formatCurrency(
                                    metrics.inventory.total_stock_value
                                )
                            }}
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Low Stock</div>
                        <div class="text-2xl font-bold text-yellow-600">
                            {{ metrics.inventory.low_stock_items }}
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Out of Stock</div>
                        <div class="text-2xl font-bold text-red-600">
                            {{ metrics.inventory.out_of_stock_items }}
                        </div>
                    </div>
                    <div
                        class="bg-white shadow-sm rounded-lg p-4 col-span-2 sm:col-span-1"
                    >
                        <div class="text-sm text-gray-500">Suppliers</div>
                        <div class="text-2xl font-bold text-indigo-600">
                            {{ metrics.inventory.total_suppliers }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Overview -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">
                    💰 Sales
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Total Sales</div>
                        <div class="text-2xl font-bold text-indigo-600">
                            {{ metrics.sales.total_sales }}
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Revenue</div>
                        <div class="text-2xl font-bold text-green-600">
                            {{ formatCurrency(metrics.sales.total_revenue) }}
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Total Profit</div>
                        <div class="text-2xl font-bold text-emerald-600">
                            {{ formatCurrency(metrics.sales.total_profit) }}
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Avg Sale Value</div>
                        <div class="text-2xl font-bold text-indigo-600">
                            {{
                                formatCurrency(metrics.sales.average_sale_value)
                            }}
                        </div>
                    </div>
                </div>

                <!-- Sales by Period -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">Today</div>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold"
                                >{{ metrics.sales.today.sales }} sales</span
                            >
                            <span class="text-lg font-bold text-green-600">{{
                                formatCurrency(metrics.sales.today.revenue)
                            }}</span>
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">This Week</div>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold"
                                >{{ metrics.sales.this_week.sales }} sales</span
                            >
                            <span class="text-lg font-bold text-green-600">{{
                                formatCurrency(metrics.sales.this_week.revenue)
                            }}</span>
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <div class="text-sm text-gray-500">This Month</div>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold"
                                >{{
                                    metrics.sales.this_month.sales
                                }}
                                sales</span
                            >
                            <span class="text-lg font-bold text-green-600">{{
                                formatCurrency(metrics.sales.this_month.revenue)
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Sales & Top Selling Items -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Sales -->
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-semibold text-gray-800">
                            📋 Recent Sales
                        </h3>
                        <Link
                            href="/admin/sales"
                            class="text-sm text-blue-500 hover:underline"
                            >View All</Link
                        >
                    </div>
                    <div v-if="recent_sales.length > 0" class="space-y-3">
                        <div
                            v-for="sale in recent_sales"
                            :key="sale.id"
                            class="border-b pb-2 last:border-b-0"
                        >
                            <div class="flex justify-between items-start">
                                <div>
                                    <Link
                                        :href="'/admin/sales/' + sale.id"
                                        class="font-medium text-blue-600 hover:underline"
                                    >
                                        Sale #{{ sale.id }}
                                    </Link>
                                    <div class="text-xs text-gray-500">
                                        {{ formatDate(sale.sale_date) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ sale.items_count }} items •
                                        {{ sale.total_quantity }} qty
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-indigo-600">
                                        {{ formatCurrency(sale.total_amount) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ sale.reference || "No ref" }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500 py-4">
                        No recent sales
                    </div>
                </div>

                <!-- Top Selling Items -->
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">
                        🏆 Top Selling Items
                    </h3>
                    <div v-if="top_selling_items.length > 0" class="space-y-3">
                        <div
                            v-for="item in top_selling_items"
                            :key="item.id"
                            class="border-b pb-2 last:border-b-0"
                        >
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-medium">
                                        {{ item.name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        SKU: {{ item.sku || "N/A" }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold">
                                        {{ item.total_quantity_sold }} units
                                    </div>
                                    <div class="text-sm text-green-600">
                                        {{ formatCurrency(item.total_revenue) }}
                                    </div>
                                    <div class="text-xs text-emerald-500">
                                        Profit:
                                        {{
                                            formatCurrency(
                                                item.total_profit || 0
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500 py-4">
                        No sales data yet
                    </div>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="mt-6 bg-white shadow-sm rounded-lg p-4">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-semibold text-gray-800">
                        ⚠️ Low Stock Alerts
                    </h3>
                    <Link
                        href="/admin/inventory"
                        class="text-sm text-blue-500 hover:underline"
                        >View All</Link
                    >
                </div>
                <div
                    v-if="low_stock_items.length > 0"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3"
                >
                    <div
                        v-for="item in low_stock_items"
                        :key="item.id"
                        class="bg-yellow-50 border border-yellow-200 rounded-lg p-3"
                    >
                        <div class="font-medium">{{ item.name }}</div>
                        <div class="text-sm text-gray-600">
                            SKU: {{ item.sku || "N/A" }}
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-sm"
                                >Stock:
                                <span class="font-bold text-yellow-700">{{
                                    item.quantity
                                }}</span></span
                            >
                            <span class="text-sm"
                                >Reorder: {{ item.reorder_level }}</span
                            >
                        </div>
                    </div>
                </div>
                <div v-else class="text-center text-gray-500 py-4">
                    ✅ All items are well stocked
                </div>
            </div>
        </div>
    </div>
</template>
