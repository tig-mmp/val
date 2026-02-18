<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, UserList } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

const props = defineProps<{ user: UserList }>();
</script>

<template>
    <Head title="Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div>Name: {{ props.user.name }}</div>
            <div>Email: {{ props.user.email }}</div>
        </div>
    </AppLayout>
</template>
