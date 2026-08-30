<script setup>
import Breadcrumb from "@/components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed, nextTick } from "vue";
const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Sales', url: '/admin/sales' },
    { label: 'Record Sale' },
];
const props = defineProps({
    inventoryItems: Array,
});

const form = useForm({
    sale_date: new Date().toISOString().split("T")[0],
    reference: "",
    notes: "",
    items: [],
});

// Search filter for each row
const searchTerms = ref([]);
const showDropdown = ref([]);

function addRow() {
    form.items.push({
        inventory_item_id: "",
        quantity: 1,
        total_price: "",
    });
    searchTerms.value.push("");
    showDropdown.value.push(false);
}

function removeRow(index) {
    form.items.splice(index, 1);
    searchTerms.value.splice(index, 1);
    showDropdown.value.splice(index, 1);
}

// Filter items based on search term
function getFilteredItems(searchTerm, excludeIds) {
    const available = props.inventoryItems.filter(
        (item) => !excludeIds.includes(item.id)
    );
    if (!searchTerm || searchTerm.trim() === "") return [];

    const term = searchTerm.toLowerCase().trim();
    return available.filter(
        (item) =>
            item.name.toLowerCase().includes(term) ||
            (item.sku && item.sku.toLowerCase().includes(term))
    );
}

// Select an item for a row
function selectItem(index, itemId) {
    form.items[index].inventory_item_id = itemId;
    searchTerms.value[index] = "";
    showDropdown.value[index] = false;
}

// Handle input in search field
function handleSearchInput(index, value) {
    searchTerms.value[index] = value;
    showDropdown.value[index] = value.trim() !== "";
}

// Start with one empty row
if (form.items.length === 0) {
    addRow();
}

function submit() {
    form.post("/admin/sales", {
        preserveScroll: true,
    });
}

// Check if a row has selected item
function isItemSelected(index) {
    return form.items[index].inventory_item_id !== "";
}

// Get selected item name
function getSelectedItemName(index) {
    const item = props.inventoryItems.find(
        (i) => i.id === form.items[index].inventory_item_id
    );
    return item ? item.name + (item.sku ? " (" + item.sku + ")" : "") : "";
}

// Computed totals
const totalQuantity = computed(() => {
    return form.items.reduce(
        (sum, item) => sum + (parseInt(item.quantity) || 0),
        0
    );
});

const totalAmount = computed(() => {
    return form.items.reduce(
        (sum, item) => sum + (parseFloat(item.total_price) || 0),
        0
    );
});

// Get item cost price
function getItemCostPrice(itemId) {
    const item = props.inventoryItems.find((i) => i.id === itemId);
    return item?.cost_price || null;
}

// Calculate profit for a row
function calculateProfit(index) {
    const item = form.items[index];
    if (!item.inventory_item_id || !item.quantity || !item.total_price)
        return null;

    const costPrice = getItemCostPrice(item.inventory_item_id);
    if (!costPrice) return null;

    const unitPrice = item.total_price / item.quantity;
    return (unitPrice - costPrice) * item.quantity;
}
</script>

<template>
    <div class="p-6 sm:py-12">
        <Breadcrumb :breadcrumbs="breadcrumbs" />
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <form @submit.prevent="submit">
                    <!-- Header fields - responsive grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Sale Date *</label
                            >
                            <input
                                v-model="form.sale_date"
                                type="date"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                required
                            />
                            <span class="text-red-500 text-sm">{{
                                form.errors.sale_date
                            }}</span>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Reference</label
                            >
                            <input
                                v-model="form.reference"
                                type="text"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                placeholder="Invoice #"
                            />
                            <span class="text-red-500 text-sm">{{
                                form.errors.reference
                            }}</span>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Notes</label
                            >
                            <input
                                v-model="form.notes"
                                type="text"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                placeholder="Optional notes"
                            />
                            <span class="text-red-500 text-sm">{{
                                form.errors.notes
                            }}</span>
                        </div>
                    </div>

                    <!-- Items - Card based layout for mobile, table for desktop -->
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

                        <!-- Mobile view: Card-based -->
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

                                <!-- Item name with search -->
                                <div class="mb-3 relative">
                                    <label
                                        class="block text-xs font-medium text-gray-700 mb-1"
                                        >Item Name *</label
                                    >
                                    <input
                                        type="text"
                                        :value="
                                            isItemSelected(index)
                                                ? getSelectedItemName(index)
                                                : ''
                                        "
                                        @input="
                                            (e) =>
                                                handleSearchInput(
                                                    index,
                                                    e.target.value
                                                )
                                        "
                                        @focus="searchTerms[index] = ''"
                                        placeholder="Type to search item..."
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                        :required="!isItemSelected(index)"
                                    />

                                    <!-- Dropdown -->
                                    <div
                                        v-if="
                                            showDropdown[index] &&
                                            getFilteredItems(
                                                searchTerms[index],
                                                form.items
                                                    .map(
                                                        (i) =>
                                                            i.inventory_item_id
                                                    )
                                                    .filter(
                                                        (id) =>
                                                            id !==
                                                            form.items[index]
                                                                .inventory_item_id
                                                    )
                                            ).length > 0
                                        "
                                        class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto mt-1"
                                    >
                                        <div
                                            v-for="itemOption in getFilteredItems(
                                                searchTerms[index],
                                                form.items
                                                    .map(
                                                        (i) =>
                                                            i.inventory_item_id
                                                    )
                                                    .filter(
                                                        (id) =>
                                                            id !==
                                                            form.items[index]
                                                                .inventory_item_id
                                                    )
                                            )"
                                            :key="itemOption.id"
                                            @click="
                                                selectItem(index, itemOption.id)
                                            "
                                            class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b last:border-b-0"
                                        >
                                            <div class="font-medium text-sm">
                                                {{ itemOption.name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                SKU:
                                                {{ itemOption.sku || "N/A" }} |
                                                Stock: {{ itemOption.quantity }}
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        v-if="
                                            form.errors[
                                                'items.' +
                                                    index +
                                                    '.inventory_item_id'
                                            ]
                                        "
                                        class="text-red-500 text-xs"
                                    >
                                        {{
                                            form.errors[
                                                "items." +
                                                    index +
                                                    ".inventory_item_id"
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
                                            >Total Price *</label
                                        >
                                        <input
                                            v-model.number="item.total_price"
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                            required
                                        />
                                        <span
                                            v-if="
                                                form.errors[
                                                    'items.' +
                                                        index +
                                                        '.total_price'
                                                ]
                                            "
                                            class="text-red-500 text-xs"
                                        >
                                            {{
                                                form.errors[
                                                    "items." +
                                                        index +
                                                        ".total_price"
                                                ]
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Calculations -->
                                <div
                                    class="grid grid-cols-3 gap-2 mt-3 pt-3 border-t"
                                >
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500">
                                            Unit Price
                                        </div>
                                        <div class="font-medium text-sm">
                                            {{
                                                item.quantity &&
                                                item.total_price
                                                    ? (
                                                          item.total_price /
                                                          item.quantity
                                                      ).toFixed(2)
                                                    : "-"
                                            }}
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500">
                                            Cost Price
                                        </div>
                                        <div class="font-medium text-sm">
                                            {{
                                                getItemCostPrice(
                                                    item.inventory_item_id
                                                )
                                                    ? getItemCostPrice(
                                                          item.inventory_item_id
                                                      )
                                                    : "-"
                                            }}
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500">
                                            Profit
                                        </div>
                                        <div
                                            class="font-medium text-sm"
                                            :class="{
                                                'text-green-600':
                                                    calculateProfit(index) > 0,
                                                'text-red-600':
                                                    calculateProfit(index) < 0,
                                            }"
                                        >
                                            {{
                                                calculateProfit(index) !== null
                                                    ? calculateProfit(
                                                          index
                                                      ).toFixed(2)
                                                    : "-"
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop view: Table -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th
                                            class="text-left py-2 px-2 min-w-[250px]"
                                        >
                                            Item Name *
                                        </th>
                                        <th
                                            class="text-left py-2 px-2 min-w-[100px]"
                                        >
                                            Quantity *
                                        </th>
                                        <th
                                            class="text-left py-2 px-2 min-w-[150px]"
                                        >
                                            Total Price *
                                        </th>
                                        <th
                                            class="text-left py-2 px-2 min-w-[120px]"
                                        >
                                            Unit Price
                                        </th>
                                        <th
                                            class="text-left py-2 px-2 min-w-[120px]"
                                        >
                                            Cost Price
                                        </th>
                                        <th
                                            class="text-left py-2 px-2 min-w-[120px]"
                                        >
                                            Profit
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
                                        <td class="py-2 px-2 relative">
                                            <input
                                                type="text"
                                                :value="
                                                    isItemSelected(index)
                                                        ? getSelectedItemName(
                                                              index
                                                          )
                                                        : ''
                                                "
                                                @input="
                                                    (e) =>
                                                        handleSearchInput(
                                                            index,
                                                            e.target.value
                                                        )
                                                "
                                                @focus="searchTerms[index] = ''"
                                                placeholder="Type to search item..."
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                                :required="
                                                    !isItemSelected(index)
                                                "
                                            />

                                            <!-- Dropdown -->
                                            <div
                                                v-if="
                                                    showDropdown[index] &&
                                                    getFilteredItems(
                                                        searchTerms[index],
                                                        form.items
                                                            .map(
                                                                (i) =>
                                                                    i.inventory_item_id
                                                            )
                                                            .filter(
                                                                (id) =>
                                                                    id !==
                                                                    form.items[
                                                                        index
                                                                    ]
                                                                        .inventory_item_id
                                                            )
                                                    ).length > 0
                                                "
                                                class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto mt-1"
                                            >
                                                <div
                                                    v-for="itemOption in getFilteredItems(
                                                        searchTerms[index],
                                                        form.items
                                                            .map(
                                                                (i) =>
                                                                    i.inventory_item_id
                                                            )
                                                            .filter(
                                                                (id) =>
                                                                    id !==
                                                                    form.items[
                                                                        index
                                                                    ]
                                                                        .inventory_item_id
                                                            )
                                                    )"
                                                    :key="itemOption.id"
                                                    @click="
                                                        selectItem(
                                                            index,
                                                            itemOption.id
                                                        )
                                                    "
                                                    class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b last:border-b-0"
                                                >
                                                    <div class="font-medium">
                                                        {{ itemOption.name }}
                                                    </div>
                                                    <div
                                                        class="text-xs text-gray-500"
                                                    >
                                                        SKU:
                                                        {{
                                                            itemOption.sku ||
                                                            "N/A"
                                                        }}
                                                        | Stock:
                                                        {{
                                                            itemOption.quantity
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                            <span
                                                v-if="
                                                    form.errors[
                                                        'items.' +
                                                            index +
                                                            '.inventory_item_id'
                                                    ]
                                                "
                                                class="text-red-500 text-xs"
                                            >
                                                {{
                                                    form.errors[
                                                        "items." +
                                                            index +
                                                            ".inventory_item_id"
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
                                                v-model.number="
                                                    item.total_price
                                                "
                                                type="number"
                                                step="0.01"
                                                min="0.01"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-4 focus:ring-opacity-50"
                                                required
                                            />
                                            <span
                                                v-if="
                                                    form.errors[
                                                        'items.' +
                                                            index +
                                                            '.total_price'
                                                    ]
                                                "
                                                class="text-red-500 text-xs"
                                            >
                                                {{
                                                    form.errors[
                                                        "items." +
                                                            index +
                                                            ".total_price"
                                                    ]
                                                }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-2 text-center">
                                            {{
                                                item.quantity &&
                                                item.total_price
                                                    ? (
                                                          item.total_price /
                                                          item.quantity
                                                      ).toFixed(2)
                                                    : "-"
                                            }}
                                        </td>
                                        <td class="py-2 px-2 text-center">
                                            {{
                                                getItemCostPrice(
                                                    item.inventory_item_id
                                                )
                                                    ? getItemCostPrice(
                                                          item.inventory_item_id
                                                      )
                                                    : "-"
                                            }}
                                        </td>
                                        <td class="py-2 px-2 text-center">
                                            <span
                                                v-if="
                                                    calculateProfit(index) !==
                                                    null
                                                "
                                                :class="{
                                                    'text-green-600':
                                                        calculateProfit(index) >
                                                        0,
                                                    'text-red-600':
                                                        calculateProfit(index) <
                                                        0,
                                                }"
                                            >
                                                ${{
                                                    calculateProfit(
                                                        index
                                                    ).toFixed(2)
                                                }}
                                            </span>
                                            <span v-else>-</span>
                                        </td>
                                        <td class="py-2 px-2 text-center">
                                            <button
                                                type="button"
                                                @click="removeRow(index)"
                                                class="text-red-500 hover:text-red-700 text-xl"
                                                v-if="form.items.length > 1"
                                            >
                                                ×
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <div
                            class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-center sm:text-right"
                        >
                            <div>
                                <span class="font-semibold">Items:</span>
                                <span class="ml-2">{{
                                    form.items.length
                                }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">Quantity:</span>
                                <span class="ml-2">{{ totalQuantity }}</span>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <span class="font-semibold">Total Amount:</span>
                                <span
                                    class="ml-2 font-bold text-lg text-indigo-600"
                                    >${{ totalAmount.toFixed(2) }}</span
                                >
                            </div>
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
                            href="/admin/sales"
                            class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 text-center"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ form.processing ? "Saving..." : "Record Sale" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
