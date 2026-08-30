<script setup>
import Breadcrumb from "@/components/Breadcrumb.vue";
const breadcrumbs = [
    { label: "Home", url: "/admin/dashboard" },
    { label: "Inventory" },
    { label: "Inventory Items" },
];
import { Head, Link, usePage, router } from "@inertiajs/vue3";

defineProps({
    items: Array,
});

const flash = usePage().props.flash;

function deleteItem(id) {
    if (confirm("Delete this inventory item?")) {
        router.delete("/admin/inventory/items/" + id);
    }
}

const formatCurrency = (amount) => {
    if (amount === null || amount === undefined || isNaN(amount)) return "0.00";
    return Number(amount).toLocaleString("en-US", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    });
};
</script>

<template>
    <div class="p-12">
        <Breadcrumb :breadcrumbs="breadcrumbs" />
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div
                    v-if="flash?.success"
                    class="mb-4 p-4 bg-green-100 text-green-700 rounded"
                >
                    {{ flash.success }}
                </div>

                <Link
                    href="/admin/inventory/items/create"
                    class="inline-block mb-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                >
                    + New Inventory Item
                </Link>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Name</th>
                            <th>SKU</th>
                            <th>Quantity</th>
                            <th>Cost Price</th>
                            <th>Reorder Level</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in items"
                            :key="item.id"
                            class="border-b"
                        >
                            <td class="py-2">{{ item.name }}</td>
                            <td>{{ item.sku }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>
                                {{
                                    item.cost_price
                                        ? formatCurrency(item.cost_price)
                                        : "-"
                                }}
                            </td>
                            <td>{{ item.reorder_level }}</td>
                            <td>{{ item.location }}</td>
                            <td>
                                <Link
                                    :href="
                                        '/admin/inventory/items/' +
                                        item.id +
                                        '/edit'
                                    "
                                    class="text-blue-500 hover:underline mr-2"
                                    >Edit</Link
                                >
                                <button
                                    @click="deleteItem(item.id)"
                                    class="text-red-500 hover:underline"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
