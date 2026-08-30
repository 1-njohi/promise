<script setup>
import Breadcrumb from "@/components/Breadcrumb.vue";
import { Head, useForm } from "@inertiajs/vue3";
const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Inventory', url: '/admin/inventory/items' },
    { label: 'Inventory Items', url: '/admin/inventory/items' },
    { label: 'Edit Item' },
];
const props = defineProps({
    item: Object,
});

const form = useForm({
    name: props.item.name,
    sku: props.item.sku,
    reorder_level: props.item.reorder_level,
    location: props.item.location,
});

function submit() {
    form.put("/admin/inventory/items/" + props.item.id);
}
</script>

<template>
    <div class="p-12">
        <Breadcrumb :breadcrumbs="breadcrumbs" />
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Name *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full border-gray-300 rounded-md shadow-sm p-4"
                            required
                        />
                        <span class="text-red-500 text-sm">{{
                            form.errors.name
                        }}</span>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium">SKU *</label>
                        <input
                            v-model="form.sku"
                            type="text"
                            class="w-full border-gray-300 rounded-md shadow-sm p-4"
                            required
                        />
                        <span class="text-red-500 text-sm">{{
                            form.errors.sku
                        }}</span>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium"
                            >Reorder Level</label
                        >
                        <input
                            v-model="form.reorder_level"
                            type="number"
                            class="w-full border-gray-300 rounded-md shadow-sm p-4"
                        />
                        <span class="text-red-500 text-sm">{{
                            form.errors.reorder_level
                        }}</span>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium"
                            >Location</label
                        >
                        <input
                            v-model="form.location"
                            type="text"
                            class="w-full border-gray-300 rounded-md shadow-sm p-4"
                        />
                        <span class="text-red-500 text-sm">{{
                            form.errors.location
                        }}</span>
                    </div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 disabled:opacity-50"
                    >
                        Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
