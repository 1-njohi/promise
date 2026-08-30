<script setup>
import Breadcrumb from "@/components/Breadcrumb.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Suppliers' },
];
defineProps({
    suppliers: Array,
});

const flash = usePage().props.flash;

function deleteSupplier(id) {
    if (confirm("Delete this supplier?")) {
        router.delete("/admin/suppliers/" + id);
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
                    href="/admin/suppliers/create"
                    class="inline-block mb-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                >
                    + New Supplier
                </Link>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Name</th>
                            <th>Location</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="supplier in suppliers"
                            :key="supplier.id"
                            class="border-b"
                        >
                            <td class="py-2">{{ supplier.name }}</td>
                            <td>{{ supplier.location }}</td>
                            <td>{{ supplier.phone }}</td>
                            <td>
                                <Link
                                    :href="
                                        '/admin/suppliers/' +
                                        supplier.id +
                                        '/edit'
                                    "
                                    class="text-blue-500 hover:underline mr-2"
                                    >Edit</Link
                                >
                                <button
                                    @click="deleteSupplier(supplier.id)"
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
