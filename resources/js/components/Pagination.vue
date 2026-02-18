<script setup lang="ts">
import type { PaginationLink } from '@/types';
import { Link } from '@inertiajs/vue3';

defineProps<{ links: PaginationLink[] }>();
</script>

<template>
    <div class="mt-4 flex flex-wrap items-center space-x-1">
        <Link
            v-for="(link, index) in links"
            :key="index"
            :href="link.url || '#'"
            v-html="link.label"
            class="rounded border px-3 py-1 text-sm"
            :class="[
                link.active
                    ? 'bg-blue-600 text-white'
                    : 'bg-white text-gray-700',
                !link.url
                    ? 'pointer-events-none opacity-50'
                    : 'hover:bg-gray-100',
            ]"
        />
    </div>
</template>
