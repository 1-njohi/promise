<script setup>
import Breadcrumb from "@/components/Breadcrumb.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Inventory', url: '/admin/inventory' },
    { label: 'Receipts' },
];
defineProps({
    receipts: Array,
});

const flash = usePage().props.flash;

function deleteReceipt(id) {
    if (confirm("Delete this receipt? This will revert all stock added.")) {
        router.delete("/admin/inventory/receipts/" + id);
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

                <Link
                    href="/admin/inventory/add"
                    class="inline-block mb-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                >
                    + Add Stock
                </Link>

                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left py-2 px-2">ID</th>
                                <th class="text-left py-2 px-2">Date</th>
                                <th class="text-left py-2 px-2">Supplier</th>
                                <th class="text-left py-2 px-2">Items</th>
                                <th class="text-left py-2 px-2">Total Qty</th>
                                <th class="text-left py-2 px-2">Notes</th>
                                <th class="text-left py-2 px-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="receipt in receipts"
                                :key="receipt.id"
                                class="border-b hover:bg-gray-50"
                            >
                                <td class="py-2 px-2">#{{ receipt.id }}</td>
                                <td class="py-2 px-2">
                                    {{ formatDate(receipt.receipt_date) }}
                                </td>
                                <td class="py-2 px-2">
                                    {{ receipt.supplier?.name }}
                                </td>
                                <td class="py-2 px-2">
                                    {{ receipt.items_count }}
                                </td>
                                <td class="py-2 px-2">
                                    {{ receipt.total_quantity }}
                                </td>
                                <td class="py-2 px-2">
                                    {{ receipt.notes || "-" }}
                                </td>
                                <td class="py-2 px-2">
                                    <button
                                        @click="deleteReceipt(receipt.id)"
                                        class="text-red-500 hover:underline"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="receipts.length === 0">
                                <td
                                    colspan="7"
                                    class="py-4 text-center text-gray-500"
                                >
                                    No receipts found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="sm:hidden space-y-4">
                    <div
                        v-for="receipt in receipts"
                        :key="receipt.id"
                        class="border rounded-lg p-4 bg-gray-50"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold text-lg">
                                    Receipt #{{ receipt.id }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ formatDate(receipt.receipt_date) }}
                                </div>
                            </div>
                            <span class="text-sm font-medium"
                                >{{ receipt.total_quantity }} units</span
                            >
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t">
                            <div>
                                <span class="text-xs text-gray-500"
                                    >Supplier</span
                                >
                                <div class="font-medium text-sm">
                                    {{ receipt.supplier?.name || "-" }}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Items</span>
                                <div class="font-medium text-sm">
                                    {{ receipt.items_count }}
                                </div>
                            </div>
                            <div class="col-span-2">
                                <span class="text-xs text-gray-500">Notes</span>
                                <div class="text-sm">
                                    {{ receipt.notes || "-" }}
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-3 pt-3 border-t">
                            <button
                                @click="deleteReceipt(receipt.id)"
                                class="text-red-500 hover:underline text-sm"
                            >
                                Delete Receipt
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="receipts.length === 0"
                        class="text-center text-gray-500 py-4"
                    >
                        No receipts found.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
