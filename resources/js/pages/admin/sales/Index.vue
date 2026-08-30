<script setup>
import Breadcrumb from "@/components/Breadcrumb.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { computed } from "vue";
const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Sales' },
];
const props = defineProps({
    sales: Object,
});

const flash = usePage().props.flash;

function deleteSale(id) {
    if (confirm("Delete this sale? This will revert stock.")) {
        router.delete("/admin/sales/" + id);
    }
}

// Format date to human readable
function formatDate(date) {
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
}

// Format currency
const formatCurrency = (amount) => {
    if (amount === null || amount === undefined || isNaN(amount)) return '0.00';
    return Number(amount).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
};
</script>

<template>
    <div class="p-6 sm:py-12">
        <Breadcrumb :breadcrumbs="breadcrumbs" />
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <!-- Flash message -->
                <div
                    v-if="flash?.success"
                    class="mb-4 p-4 bg-green-100 text-green-700 rounded"
                >
                    {{ flash.success }}
                </div>

                <!-- Record Sale Button -->
                <Link
                    href="/admin/sales/create"
                    class="inline-block mb-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                >
                    + Record Sale
                </Link>

                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left py-2 px-2">ID</th>
                                <th class="text-left py-2 px-2">Date</th>
                                <th class="text-left py-2 px-2">Reference</th>
                                <th class="text-left py-2 px-2">Items</th>
                                <th class="text-left py-2 px-2">Qty</th>
                                <th class="text-left py-2 px-2">Total</th>
                                <th class="text-left py-2 px-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="sale in sales.data"
                                :key="sale.id"
                                class="border-b hover:bg-gray-50"
                            >
                                <td class="py-2 px-2">#{{ sale.id }}</td>
                                <td class="py-2 px-2">
                                    {{ formatDate(sale.sale_date) }}
                                </td>
                                <td class="py-2 px-2">
                                    {{ sale.reference || "-" }}
                                </td>
                                <td class="py-2 px-2">
                                    {{ sale.items_count }}
                                </td>
                                <td class="py-2 px-2">
                                    {{ sale.total_quantity }}
                                </td>
                                <td class="py-2 px-2 font-semibold">
                                    {{ formatCurrency(sale.total_amount) }}
                                </td>
                                <td class="py-2 px-2">
                                    <Link
                                        :href="'/admin/sales/' + sale.id"
                                        class="text-blue-500 hover:underline mr-3"
                                    >
                                        View
                                    </Link>
                                    <button
                                        @click="deleteSale(sale.id)"
                                        class="text-red-500 hover:underline"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="sales.data.length === 0">
                                <td
                                    colspan="7"
                                    class="py-4 text-center text-gray-500"
                                >
                                    No sales recorded yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="sm:hidden space-y-4">
                    <div
                        v-for="sale in sales.data"
                        :key="sale.id"
                        class="border rounded-lg p-4 bg-gray-50"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold text-lg">
                                    Sale #{{ sale.id }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ formatDate(sale.sale_date) }}
                                </div>
                            </div>
                            <span class="text-lg font-bold text-indigo-600">
                                {{ formatCurrency(sale.total_amount) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 mt-3 pt-3 border-t">
                            <div>
                                <span class="text-xs text-gray-500"
                                    >Reference</span
                                >
                                <div class="font-medium text-sm">
                                    {{ sale.reference || "-" }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Items</span>
                                <div class="font-medium text-sm">
                                    {{ sale.items_count }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Qty</span>
                                <div class="font-medium text-sm">
                                    {{ sale.total_quantity }}
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-3 pt-3 border-t">
                            <Link
                                :href="'/admin/sales/' + sale.id"
                                class="text-blue-500 hover:underline text-sm"
                            >
                                View
                            </Link>
                            <button
                                @click="deleteSale(sale.id)"
                                class="text-red-500 hover:underline text-sm"
                            >
                                Delete
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="sales.data.length === 0"
                        class="text-center text-gray-500 py-4"
                    >
                        No sales recorded yet.
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="sales.links && sales.links.length > 0"
                    class="mt-6 flex flex-wrap justify-center gap-1"
                >
                    <Link
                        v-for="link in sales.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1 border rounded hover:bg-gray-100 text-sm"
                        :class="{
                            'bg-blue-500 text-white': link.active,
                            'opacity-50 pointer-events-none': !link.url,
                        }"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
