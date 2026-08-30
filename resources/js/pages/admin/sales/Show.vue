<script setup>
import Breadcrumb from "@/components/Breadcrumb.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";
const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Sales', url: '/admin/sales' },
    { label: 'Sale #' + sale.id },
];
// Define props with proper default
const props = defineProps({
    sale: {
        type: Object,
        required: true,
    },
});

// Computed totals
const totalProfit = computed(() => {
    if (!props.sale || !props.sale.items) return 0;
    return props.sale.items.reduce((sum, item) => sum + (item.profit || 0), 0);
});

// Format currency
const formatCurrency = (amount) => {
    if (amount === null || amount === undefined || isNaN(amount)) return '0.00';
    return Number(amount).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
};

// Format date to human readable
const formatDate = (date) => {
    if (!date) return "-";
    try {
        return new Date(date).toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
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
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <!-- Sale Details -->
                <div class="mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <span class="font-semibold">Date:</span>
                            <span class="ml-2">{{
                                formatDate(sale.sale_date)
                            }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Reference:</span>
                            <span class="ml-2">{{
                                sale.reference || "-"
                            }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Total Amount:</span>
                            <span class="ml-2 font-bold text-indigo-600">{{
                                formatCurrency(sale.total_amount)
                            }}</span>
                        </div>
                    </div>
                    <div v-if="sale.notes" class="mt-2">
                        <span class="font-semibold">Notes:</span>
                        <span class="ml-2">{{ sale.notes }}</span>
                    </div>
                </div>

                <!-- Items Table - Mobile responsive -->
                <!-- Desktop view -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left py-2 px-2">Item</th>
                                <th class="text-left py-2 px-2">SKU</th>
                                <th class="text-right py-2 px-2">Quantity</th>
                                <th class="text-right py-2 px-2">Unit Price</th>
                                <th class="text-right py-2 px-2">
                                    Total Price
                                </th>
                                <th class="text-right py-2 px-2">Cost Price</th>
                                <th class="text-right py-2 px-2">Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in sale.items"
                                :key="item.id"
                                class="border-b hover:bg-gray-50"
                            >
                                <td class="py-2 px-2">
                                    {{ item.inventory_item.name }}
                                </td>
                                <td class="py-2 px-2">
                                    {{ item.inventory_item.sku || "-" }}
                                </td>
                                <td class="py-2 px-2 text-right">
                                    {{ item.quantity }}
                                </td>
                                <td class="py-2 px-2 text-right">
                                    {{ formatCurrency(item.unit_price) }}
                                </td>
                                <td class="py-2 px-2 text-right font-semibold">
                                    {{ formatCurrency(item.total_price) }}
                                </td>
                                <td class="py-2 px-2 text-right">
                                    {{
                                        item.cost_price
                                            ? formatCurrency(item.cost_price)
                                            : "-"
                                    }}
                                </td>
                                <td
                                    class="py-2 px-2 text-right"
                                    :class="{
                                        'text-green-600': item.profit > 0,
                                        'text-red-600': item.profit < 0,
                                    }"
                                >
                                    {{
                                        item.profit
                                            ? formatCurrency(item.profit)
                                            : "-"
                                    }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot
                            class="bg-gray-50 font-semibold border-t-2 border-gray-300"
                        >
                            <tr>
                                <td colspan="2" class="py-2 px-2 text-right">
                                    Totals:
                                </td>
                                <td class="py-2 px-2 text-right">
                                    {{ sale.total_quantity }}
                                </td>
                                <td class="py-2 px-2"></td>
                                <td class="py-2 px-2 text-right">
                                    {{ formatCurrency(sale.total_amount) }}
                                </td>
                                <td class="py-2 px-2"></td>
                                <td class="py-2 px-2 text-right text-green-600">
                                    {{ formatCurrency(totalProfit) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Mobile view: Cards -->
                <div class="sm:hidden space-y-4">
                    <div
                        v-for="item in sale.items"
                        :key="item.id"
                        class="border rounded-lg p-4 bg-gray-50"
                    >
                        <div class="font-semibold text-lg mb-2">
                            {{ item.inventory_item.name }}
                        </div>
                        <div class="text-sm text-gray-600 mb-1">
                            SKU: {{ item.inventory_item.sku || "N/A" }}
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-2 pt-2 border-t">
                            <div>
                                <span class="text-xs text-gray-500"
                                    >Quantity</span
                                >
                                <div class="font-medium">
                                    {{ item.quantity }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500"
                                    >Unit Price</span
                                >
                                <div class="font-medium">
                                    {{ formatCurrency(item.unit_price) }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500"
                                    >Total Price</span
                                >
                                <div class="font-semibold">
                                    {{ formatCurrency(item.total_price) }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500"
                                    >Cost Price</span
                                >
                                <div class="font-medium">
                                    {{
                                        item.cost_price
                                            ? formatCurrency(item.cost_price)
                                            : "-"
                                    }}
                                </div>
                            </div>
                            <div class="col-span-2">
                                <span class="text-xs text-gray-500"
                                    >Profit</span
                                >
                                <div
                                    :class="{
                                        'text-green-600': item.profit > 0,
                                        'text-red-600': item.profit < 0,
                                    }"
                                >
                                    {{
                                        item.profit
                                            ? formatCurrency(item.profit)
                                            : "-"
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile totals -->
                    <div class="border rounded-lg p-4 bg-gray-100">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-xs text-gray-500"
                                    >Total Items</span
                                >
                                <div class="font-semibold">
                                    {{ sale.items?.length || 0 }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500"
                                    >Total Quantity</span
                                >
                                <div class="font-semibold">
                                    {{ sale.total_quantity }}
                                </div>
                            </div>
                            <div class="col-span-2">
                                <span class="text-xs text-gray-500"
                                    >Total Amount</span
                                >
                                <div class="font-bold text-lg text-indigo-600">
                                    {{ formatCurrency(sale.total_amount) }}
                                </div>
                            </div>
                            <div class="col-span-2">
                                <span class="text-xs text-gray-500"
                                    >Total Profit</span
                                >
                                <div class="font-bold text-lg text-green-600">
                                    {{ formatCurrency(totalProfit) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end mt-6 gap-2">
                    <Link
                        href="/admin/sales"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 text-center"
                    >
                        Back
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
