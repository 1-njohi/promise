<script setup>
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    items: Object,
    categories: Array,
    filters: Object,
});

const page = usePage();
</script>

<template>
    <!-- <Head title="Shop" /> -->

    <!-- <GuestLayout> -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap items-center gap-4">
                    <div>
                        <span class="font-semibold mr-2">Sort:</span>
                        <!-- <Link :href="route('home', { ...filters, sort: 'latest' })" class="mr-2 text-blue-600 hover:underline">Latest</Link> -->
                        <!-- <Link :href="route('home', { ...filters, sort: 'cheapest' })" class="text-blue-600 hover:underline">Cheapest</Link> -->
                    </div>
                    <div>
                        <span class="font-semibold mr-2">Category:</span>
                        <!-- <Link :href="route('home')" class="mr-2 text-blue-600 hover:underline">All</Link> -->
                        <Link v-for="cat in categories" :key="cat.id" 
                              <!-- :href="route('home', { ...filters, category: cat.id })"  -->
                              class="mr-2 text-blue-600 hover:underline">
                            {{ cat.name }}
                        </Link>
                    </div>
                </div>

                <!-- Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <div v-for="item in items.data" :key="item.id" class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="h-48 bg-gray-200">
                            <img v-if="item.image_path" :src="'/storage/' + item.image_path" class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">No Image</div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold">{{ item.title }}</h3>
                            <p class="text-lg font-bold text-green-600">${{ item.price }}</p>
                            <p class="text-sm text-gray-500">{{ item.category?.name }}</p>
                            <span v-if="item.inventory?.quantity > 0" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">In Stock</span>
                            <span v-else class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Out of Stock</span>
                        </div>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div class="mt-6">
                    <Link v-for="link in items.links" :key="link.label" 
                          :href="link.url || '#'" 
                          v-html="link.label" 
                          class="mx-1 px-3 py-1 border rounded hover:bg-gray-100"
                          :class="{'bg-blue-500 text-white': link.active}" />
                </div>
            </div>
        </div>
    <!-- </GuestLayout> -->
</template>