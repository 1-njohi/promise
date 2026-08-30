<script setup>
import Breadcrumb from "@/components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Inventory', url: '/admin/inventory' },
    { label: 'Current Stock', url: '/admin/inventory' },
    { label: 'Add Stock' },
];
const props = defineProps({
    suppliers: Array,
});

const form = useForm({
    supplier_id: "",
    receipt_date: new Date().toISOString().split("T")[0],
    notes: "",
    items: [],
});

function addRow() {
    form.items.push({
        name: "",
        sku: "",
        quantity: 1,
        cost_price: "",
    });
}

function removeRow(index) {
    form.items.splice(index, 1);
}

// Start with one empty row
if (form.items.length === 0) {
    addRow();
}

function submit() {
    form.post("/admin/inventory", {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="p-6 sm:py-12">
        <Breadcrumb :breadcrumbs="breadcrumbs" />
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <form @submit.prevent="submit">
                    <!-- Header fields - responsive grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Supplier *</label
                            >
                            <select
                                v-model="form.supplier_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                required
                            >
                                <option value="">Select supplier...</option>
                                <option
                                    v-for="supplier in suppliers"
                                    :key="supplier.id"
                                    :value="supplier.id"
                                >
                                    {{ supplier.name }}
                                </option>
                            </select>
                            <span class="text-red-500 text-sm">{{
                                form.errors.supplier_id
                            }}</span>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Receipt Date</label
                            >
                            <input
                                v-model="form.receipt_date"
                                type="date"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                required
                            />
                            <span class="text-red-500 text-sm">{{
                                form.errors.receipt_date
                            }}</span>
                        </div>
                        <div class="col-span-1 sm:col-span-2">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Notes</label
                            >
                            <textarea
                                v-model="form.notes"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                rows="2"
                            ></textarea>
                            <span class="text-red-500 text-sm">{{
                                form.errors.notes
                            }}</span>
                        </div>
                    </div>

                    <!-- Items section -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-lg">Items</h3>
                            <button
                                type="button"
                                @click="addRow"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm whitespace-nowrap"
                            >
                                + Add Row
                            </button>
                        </div>

                        <!-- Mobile: Card-based rows -->
                        <div class="sm:hidden space-y-4">
                            <div
                                v-for="(item, index) in form.items"
                                :key="index"
                                class="border rounded-lg p-4 bg-gray-50"
                            >
                                <div
                                    class="flex justify-between items-start mb-3"
                                >
                                    <span class="font-semibold text-sm"
                                        >Item #{{ index + 1 }}</span
                                    >
                                    <button
                                        type="button"
                                        @click="removeRow(index)"
                                        class="text-red-500 hover:text-red-700 text-sm"
                                        v-if="form.items.length > 1"
                                    >
                                        Remove
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 mb-1"
                                            >Item Name *</label
                                        >
                                        <input
                                            v-model="item.name"
                                            type="text"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50 p-4"
                                            placeholder="Item name"
                                            required
                                        />
                                        <span
                                            v-if="
                                                form.errors[
                                                    'items.' + index + '.name'
                                                ]
                                            "
                                            class="text-red-500 text-xs"
                                        >
                                            {{
                                                form.errors[
                                                    "items." + index + ".name"
                                                ]
                                            }}
                                        </span>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 mb-1"
                                            >SKU *</label
                                        >
                                        <input
                                            v-model="item.sku"
                                            type="text"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                            placeholder="SKU"
                                            required
                                        />
                                        <span
                                            v-if="
                                                form.errors[
                                                    'items.' + index + '.sku'
                                                ]
                                            "
                                            class="text-red-500 text-xs"
                                        >
                                            {{
                                                form.errors[
                                                    "items." + index + ".sku"
                                                ]
                                            }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 mb-1"
                                                >Quantity *</label
                                            >
                                            <input
                                                v-model.number="item.quantity"
                                                type="number"
                                                min="1"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                                required
                                            />
                                            <span
                                                v-if="
                                                    form.errors[
                                                        'items.' +
                                                            index +
                                                            '.quantity'
                                                    ]
                                                "
                                                class="text-red-500 text-xs"
                                            >
                                                {{
                                                    form.errors[
                                                        "items." +
                                                            index +
                                                            ".quantity"
                                                    ]
                                                }}
                                            </span>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 mb-1"
                                                >Cost Price</label
                                            >
                                            <input
                                                v-model.number="item.cost_price"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                                placeholder="0.00"
                                            />
                                            <span
                                                v-if="
                                                    form.errors[
                                                        'items.' +
                                                            index +
                                                            '.cost_price'
                                                    ]
                                                "
                                                class="text-red-500 text-xs"
                                            >
                                                {{
                                                    form.errors[
                                                        "items." +
                                                            index +
                                                            ".cost_price"
                                                    ]
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop: Table -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th
                                            class="text-left py-2 px-2 min-w-[150px]"
                                        >
                                            Item Name *
                                        </th>
                                        <th
                                            class="text-left py-2 px-2 min-w-[120px]"
                                        >
                                            SKU *
                                        </th>
                                        <th
                                            class="text-left py-2 px-2 min-w-[100px]"
                                        >
                                            Quantity *
                                        </th>
                                        <th
                                            class="text-left py-2 px-2 min-w-[120px]"
                                        >
                                            Cost Price
                                        </th>
                                        <th
                                            class="text-left py-2 px-2 min-w-[80px]"
                                        >
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, index) in form.items"
                                        :key="index"
                                        class="border-b"
                                    >
                                        <td class="py-2 px-2">
                                            <input
                                                v-model="item.name"
                                                type="text"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50 p-4"
                                                placeholder="Item name"
                                                required
                                            />
                                            <span
                                                v-if="
                                                    form.errors[
                                                        'items.' +
                                                            index +
                                                            '.name'
                                                    ]
                                                "
                                                class="text-red-500 text-xs"
                                            >
                                                {{
                                                    form.errors[
                                                        "items." +
                                                            index +
                                                            ".name"
                                                    ]
                                                }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-2">
                                            <input
                                                v-model="item.sku"
                                                type="text"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                                placeholder="SKU"
                                                required
                                            />
                                            <span
                                                v-if="
                                                    form.errors[
                                                        'items.' +
                                                            index +
                                                            '.sku'
                                                    ]
                                                "
                                                class="text-red-500 text-xs"
                                            >
                                                {{
                                                    form.errors[
                                                        "items." +
                                                            index +
                                                            ".sku"
                                                    ]
                                                }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-2">
                                            <input
                                                v-model.number="item.quantity"
                                                type="number"
                                                min="1"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                                required
                                            />
                                            <span
                                                v-if="
                                                    form.errors[
                                                        'items.' +
                                                            index +
                                                            '.quantity'
                                                    ]
                                                "
                                                class="text-red-500 text-xs"
                                            >
                                                {{
                                                    form.errors[
                                                        "items." +
                                                            index +
                                                            ".quantity"
                                                    ]
                                                }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-2">
                                            <input
                                                v-model.number="item.cost_price"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                                placeholder="0.00"
                                            />
                                            <span
                                                v-if="
                                                    form.errors[
                                                        'items.' +
                                                            index +
                                                            '.cost_price'
                                                    ]
                                                "
                                                class="text-red-500 text-xs"
                                            >
                                                {{
                                                    form.errors[
                                                        "items." +
                                                            index +
                                                            ".cost_price"
                                                    ]
                                                }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-2 text-center">
                                            <button
                                                type="button"
                                                @click="removeRow(index)"
                                                class="text-red-500 hover:text-red-700 text-xl"
                                                v-if="form.items.length > 1"
                                            >
                                                ✕
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div
                        v-if="form.errors.items"
                        class="mt-2 text-red-500 text-sm"
                    >
                        {{ form.errors.items }}
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex flex-col sm:flex-row justify-end gap-2 mt-6"
                    >
                        <Link
                            href="/admin/inventory"
                            class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 text-center"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{
                                form.processing ? "Saving..." : "Save Inventory"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
