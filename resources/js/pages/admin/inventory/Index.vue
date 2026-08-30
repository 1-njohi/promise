<script setup>
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Breadcrumb from "@/components/Breadcrumb.vue";
const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Inventory' },
    { label: 'Current Stock' },
];

const props = defineProps({
    inventory: Object,
    filters: Object,
});

const flash = usePage().props.flash;

const search = ref(props.filters.search || "");
const lowStock = ref(props.filters.low_stock || false);
const outOfStock = ref(props.filters.out_of_stock || false);

function applyFilters() {
    const params = new URLSearchParams();
    if (search.value) params.append("search", search.value);
    if (lowStock.value) params.append("low_stock", 1);
    if (outOfStock.value) params.append("out_of_stock", 1);
    router.get("/admin/inventory?" + params.toString());
}

function clearFilters() {
    search.value = "";
    lowStock.value = false;
    outOfStock.value = false;
    router.get("/admin/inventory");
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

                <!-- Filters - Mobile friendly -->
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <div class="flex flex-wrap gap-2">
                        <Link
                            href="/admin/inventory/add"
                            class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 text-sm whitespace-nowrap"
                        >
                            + Add Stock
                        </Link>
                        <Link
                            href="/admin/inventory/receipts"
                            class="bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600 text-sm whitespace-nowrap"
                        >
                            Receipts
                        </Link>
                    </div>

                    <div class="flex-1"></div>

                    <div
                        class="flex flex-wrap items-center gap-2 w-full sm:w-auto"
                    >
                        <input
                            v-model="search"
                            @keyup.enter="applyFilters"
                            placeholder="Search by name or SKU"
                            class="border-gray-300 rounded-md shadow-sm text-sm flex-1 sm:flex-initial w-full sm:w-auto"
                        />
                        <div class="flex flex-wrap items-center gap-2 text-sm">
                            <label
                                class="flex items-center gap-1 whitespace-nowrap"
                            >
                                <input
                                    type="checkbox"
                                    v-model="lowStock"
                                    @change="applyFilters"
                                />
                                Low Stock
                            </label>
                            <label
                                class="flex items-center gap-1 whitespace-nowrap"
                            >
                                <input
                                    type="checkbox"
                                    v-model="outOfStock"
                                    @change="applyFilters"
                                />
                                Out of Stock
                            </label>
                            <button
                                @click="clearFilters"
                                class="text-gray-500 hover:underline text-sm"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left py-2 px-2">Name</th>
                                <th class="text-left py-2 px-2">SKU</th>
                                <th class="text-left py-2 px-2">Quantity</th>
                                <th class="text-left py-2 px-2">Cost Price</th>
                                <th class="text-left py-2 px-2">Total Value</th>
                                <th class="text-left py-2 px-2">
                                    Reorder Level
                                </th>
                                <th class="text-left py-2 px-2">Status</th>
                                <th class="text-left py-2 px-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in inventory.data"
                                :key="item.id"
                                class="border-b hover:bg-gray-50"
                            >
                                <td class="py-2 px-2">{{ item.name }}</td>
                                <td class="py-2 px-2">{{ item.sku }}</td>
                                <td class="py-2 px-2">{{ item.quantity }}</td>
                                <td class="py-2 px-2">
                                    {{
                                        item.cost_price
                                            ? formatCurrency(item.cost_price)
                                            : "-"
                                    }}
                                </td>
                                <td class="py-2 px-2">
                                    {{
                                        item.cost_price
                                            ? formatCurrency(
                                                  item.quantity *
                                                      item.cost_price
                                              )
                                            : "-"
                                    }}
                                </td>
                                <td class="py-2 px-2">
                                    {{ item.reorder_level }}
                                </td>
                                <td class="py-2 px-2">
                                    <span
                                        v-if="item.quantity <= 0"
                                        class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs"
                                        >Out of Stock</span
                                    >
                                    <span
                                        v-else-if="
                                            item.quantity <= item.reorder_level
                                        "
                                        class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs"
                                        >Low Stock</span
                                    >
                                    <span
                                        v-else
                                        class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs"
                                        >In Stock</span
                                    >
                                </td>
                                <td class="py-2 px-2 whitespace-nowrap">
                                    <Link
                                        :href="
                                            '/admin/products/' +
                                            item.id +
                                            '/edit'
                                        "
                                        class="text-blue-500 hover:underline mr-2 text-sm"
                                        >Edit</Link
                                    >
                                    <Link
                                        :href="
                                            '/admin/inventory/add?product=' +
                                            item.id
                                        "
                                        class="text-green-500 hover:underline text-sm"
                                        >Add Stock</Link
                                    >
                                </td>
                            </tr>
                            <tr v-if="inventory.data.length === 0">
                                <td
                                    colspan="8"
                                    class="py-4 text-center text-gray-500"
                                >
                                    No inventory items found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="sm:hidden space-y-4">
                    <div
                        v-for="item in inventory.data"
                        :key="item.id"
                        class="border rounded-lg p-4 bg-gray-50"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold text-lg">
                                    {{ item.name }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    SKU: {{ item.sku }}
                                </div>
                            </div>
                            <span class="text-sm font-medium"
                                >{{ item.quantity }} units</span
                            >
                        </div>

                        <div class="mt-2 flex items-center gap-2">
                            <span
                                v-if="item.quantity <= 0"
                                class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold"
                                >Out of Stock</span
                            >
                            <span
                                v-else-if="item.quantity <= item.reorder_level"
                                class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold"
                                >Low Stock</span
                            >
                            <span
                                v-else
                                class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold"
                                >In Stock</span
                            >
                            <span class="text-xs text-gray-500"
                                >Reorder: {{ item.reorder_level }}</span
                            >
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t">
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
                            <div>
                                <span class="text-xs text-gray-500"
                                    >Total Value</span
                                >
                                <div class="font-medium">
                                    {{
                                        item.cost_price
                                            ? formatCurrency(
                                                  item.quantity *
                                                      item.cost_price
                                              )
                                            : "-"
                                    }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t">
                            <Link
                                :href="'/admin/products/' + item.id + '/edit'"
                                class="text-blue-500 hover:underline text-sm"
                            >
                                Edit Product
                            </Link>
                            <Link
                                :href="
                                    '/admin/inventory/add?product=' + item.id
                                "
                                class="text-green-500 hover:underline text-sm"
                            >
                                Add Stock
                            </Link>
                        </div>
                    </div>

                    <div
                        v-if="inventory.data.length === 0"
                        class="text-center text-gray-500 py-4"
                    >
                        No inventory items found.
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="inventory.links && inventory.links.length > 0"
                    class="mt-6 flex flex-wrap justify-center gap-1"
                >
                    <Link
                        v-for="link in inventory.links"
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
