<script setup>
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import Breadcrumb from "@/components/Breadcrumb.vue";
const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Catalogue' },
    { label: 'Categories' },
];
defineProps({
    categories: Array,
});

const flash = usePage().props.flash;

function deleteCategory(id) {
    if (confirm("Delete this category?")) {
        router.delete("/admin/categories/" + id);
    }
}
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
                    href="/admin/categories/create"
                    class="inline-block mb-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                >
                    + New Category
                </Link>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Name</th>
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="cat in categories"
                            :key="cat.id"
                            class="border-b"
                        >
                            <td class="py-2">{{ cat.name }}</td>
                            <td>{{ cat.slug }}</td>
                            <td>
                                <Link
                                    :href="
                                        '/admin/categories/' + cat.id + '/edit'
                                    "
                                    class="text-blue-500 hover:underline mr-2"
                                    >Edit</Link
                                >
                                <button
                                    @click="deleteCategory(cat.id)"
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
