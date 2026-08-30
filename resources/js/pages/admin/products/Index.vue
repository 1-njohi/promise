<script setup>
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import Breadcrumb from "@/components/Breadcrumb.vue";

const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Catalogue' },
    { label: 'Products' },
];
defineProps({
    products: Array,
});

const flash = usePage().props.flash;

function deleteProduct(id) {
    if (confirm("Delete this product?")) {
        router.delete("/admin/products/" + id);
    }
}
</script>

<template>
    <div class="p-12">
        <Breadcrumb :breadcrumbs="breadcrumbs" />
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div
                        v-if="flash?.success"
                        class="mb-4 rounded bg-green-100 p-4 text-green-700"
                    >
                        {{ flash.success }}
                    </div>

                    <Link
                        href="/admin/products/create"
                        class="mb-4 inline-block rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                    >
                        + Add New Product
                    </Link>

                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 text-left">Image</th>
                                <th class="text-left">Title</th>
                                <th class="text-left">Price</th>
                                <th class="text-left">Category</th>
                                <th class="text-left">SKU</th>
                                <th class="text-left">Stock</th>
                                <th class="text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="product in products"
                                :key="product.id"
                                class="border-b"
                            >
                                <td class="py-2">
                                    <img
                                        v-if="product.image_path"
                                        :src="'/storage/' + product.image_path"
                                        class="h-12 w-12 rounded object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-12 w-12 items-center justify-center rounded bg-gray-200 text-xs text-gray-400"
                                    >
                                        No img
                                    </div>
                                </td>
                                <td>{{ product.title }}</td>
                                <td>${{ product.price }}</td>
                                <td>{{ product.category?.name }}</td>
                                <td>{{ product.inventory?.sku }}</td>
                                <td>
                                    <span
                                        :class="{
                                            'text-red-500':
                                                product.inventory?.quantity <=
                                                0,
                                            'text-green-600':
                                                product.inventory?.quantity > 0,
                                        }"
                                    >
                                        {{ product.inventory?.quantity ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <Link
                                        :href="
                                            '/admin/products/' +
                                            product.id +
                                            '/edit'
                                        "
                                        class="mr-2 text-blue-500 hover:underline"
                                        >Edit</Link
                                    >
                                    <button
                                        @click="deleteProduct(product.id)"
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
    </div>
</template>
