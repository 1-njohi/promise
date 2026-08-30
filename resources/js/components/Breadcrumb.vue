<template>
    <nav v-if="breadcrumbs && breadcrumbs.length > 0" class="flex items-center text-sm mb-4 mt-[-1rem]" aria-label="Breadcrumb">
        <ol class="flex items-center flex-wrap">
            <li v-for="(crumb, index) in breadcrumbs" :key="index" class="flex items-center">
                <Link
                    v-if="crumb.url && index < breadcrumbs.length - 1"
                    :href="crumb.url"
                    class="text-gray-500 hover:text-indigo-600 transition-colors"
                >
                    {{ crumb.label }}
                </Link>
                <span v-else class="text-gray-900 font-medium">
                    {{ crumb.label }}
                </span>
                <svg
                    v-if="index < breadcrumbs.length - 1"
                    class="w-4 h-4 text-gray-400 mx-2"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </li>
        </ol>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    breadcrumbs: {
        type: Array,
        required: false,
        default: () => [],
    },
});
</script>