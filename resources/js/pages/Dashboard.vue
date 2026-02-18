<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import CreateOrder from '@/components/CreateOrder.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Encomendar',
        href: dashboard().url,
    },
];

const props= defineProps<{ ingredients?: any[] }>();

</script>

<template>
    <Head title="Encomendar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <CreateOrder :ingredients="props.ingredients" />
        </div>
    </AppLayout>
</template>
