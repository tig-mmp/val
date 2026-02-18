<script setup lang="ts">
import { Head,Link } from '@inertiajs/vue3';
import Pagination from '@/components/Pagination.vue';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type {
    BreadcrumbItem,
    Pagination as PaginationType,
    UserList,
} from '@/types';

const props = defineProps<{ users: PaginationType<UserList> }>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

</script>

<template>
    <Head title="Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="flex justify-end">
            <Link
                href="/users/create"
                class="text-indigo-500 hover:text-indigo-600"
                >Create User</Link
            >
        </div>
        <div
            class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
        >
            <Table>
                <TableCaption>Utilizadores</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead class="text-right"> Actions </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="user in props.users.data" :key="user.id">
                        <TableCell>{{ user.name }}</TableCell>
                        <TableCell>{{ user.email }}</TableCell>
                        <TableCell class="flex justify-end gap-2">
                            <Link
                                :href="`/users/${user.id}`"
                                class="text-green-500 hover:text-green-600"
                                >Show</Link
                            >
                            <Link
                                :href="`/users/${user.id}/edit`"
                                class="text-indigo-500 hover:text-indigo-600"
                                >Edit</Link
                            >
                            <!-- TODO confirmation -->
                            <Link
                                :href="`/users/${user.id}`"
                                method="delete"
                                as="button"
                                class="text-red-500 hover:text-red-600"
                                >Delete</Link
                            >
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <div>
                <Pagination :links="props.users.links" />
            </div>
        </div>
    </div>
    </AppLayout>
</template>
