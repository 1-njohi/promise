<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Breadcrumb from "@/components/Breadcrumb.vue";
const breadcrumbs = [
    { label: 'Home', url: '/admin/dashboard' },
    { label: 'Catalogue', url: '/admin/products' },
    { label: 'Products', url: '/admin/products' },
    { label: 'Edit Product' },
];
const props = defineProps({
    product: Object,
    categories: Array,
});

const form = useForm({
    title: props.product.title,
    price: props.product.price,
    category_id: props.product.category_id,
    image: null,
    is_visible: props.product.is_visible,
});

const preview = ref(
    props.product.image_path ? "/storage/" + props.product.image_path : null
);

function submit() {
    form.put("/admin/products/" + props.product.id, {
        preserveScroll: true,
        onSuccess: () => form.reset("image"),
    });
}

function handleFileChange(e) {
    const file = e.target.files[0];
    form.image = file;
    if (file) {
        preview.value = URL.createObjectURL(file);
    }
}
</script>

<template>
    <div class="p-12">
        <Breadcrumb :breadcrumbs="breadcrumbs" />
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <!-- Catalogue fields -->
                    <h3 class="text-lg font-bold border-b pb-2 mb-4">
                        📦 Catalogue Info
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium"
                                >Title</label
                            >
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full border-gray-300 rounded-md shadow-sm p-4"
                                required
                            />
                            <span class="text-red-500 text-sm">{{
                                form.errors.title
                            }}</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium"
                                >Price</label
                            >
                            <input
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                class="w-full border-gray-300 rounded-md shadow-sm p-4"
                                required
                            />
                            <span class="text-red-500 text-sm">{{
                                form.errors.price
                            }}</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium"
                                >Category</label
                            >
                            <select
                                v-model="form.category_id"
                                class="w-full border-gray-300 rounded-md shadow-sm p-4"
                                required
                            >
                                <option value="">Select...</option>
                                <option
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    :value="cat.id"
                                >
                                    {{ cat.name }}
                                </option>
                            </select>
                            <span class="text-red-500 text-sm">{{
                                form.errors.category_id
                            }}</span>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium"
                                >Image</label
                            >
                            <input
                                type="file"
                                @change="handleFileChange"
                                class="w-full"
                                accept="image/*"
                            />
                            <img
                                v-if="preview"
                                :src="preview"
                                class="mt-2 w-32 h-32 object-cover rounded border"
                            />
                            <span class="text-red-500 text-sm">{{
                                form.errors.image
                            }}</span>
                        </div>
                        <div class="col-span-2">
                            <label class="flex items-center">
                                <input
                                    v-model="form.is_visible"
                                    type="checkbox"
                                    class="mr-2"
                                />
                                Visible in shop
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? "Updating..."
                                    : "Update Product"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
