<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({
    href: String,
});
</script>

<template>
    <Link :href="href" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition ease-in-out duration-150">
        <slot />
    </Link>
</template>
