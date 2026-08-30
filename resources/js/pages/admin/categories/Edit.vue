<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/components/Breadcrumb.vue";
const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Catalogue', url: '/admin/categories' },
    { label: 'Categories', url: '/admin/categories' },
    { label: 'Edit Category' },
];

const props = defineProps({
    category: Object,
});

const form = useForm({
    name: props.category.name,
});

function submit() {
    form.put("/admin/categories/" + props.category.id);
}
</script>

<template>
    <div class="p-12">
        <Breadcrumb :breadcrumbs="breadcrumbs" />
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            required
                        />
                        <span class="text-red-500 text-sm">{{
                            form.errors.name
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
