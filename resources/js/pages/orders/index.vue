<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type {
    BreadcrumbItem,
    Pagination as PaginationType,
    OrderList,
} from '@/types';
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

const props = defineProps<{ orders: PaginationType<OrderList> }>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Orders',
        href: '/orders',
    },
];

</script>

<template>
    <Head title="Orders" />
    <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div
            class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
        >
            <Table>
                <TableCaption>A list of your recent orders.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]"> ID </TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Image</TableHead>
                        <TableHead class="text-right"> Actions </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="order in props.orders.data" :key="order.id">
                        <TableCell class="font-medium">
                            {{ order.id }}
                        </TableCell>
                        <TableCell>{{ order.name }}</TableCell>
                        <TableCell>{{ order.email }}</TableCell>
                        <TableCell class="flex justify-end gap-2">
                            <Link
                                :href="`/orders/${order.id}`"
                                class="text-green-500 hover:text-green-600"
                                >Show</Link
                            >
                            <Link
                                :href="`/orders/${order.id}/edit`"
                                class="text-indigo-500 hover:text-indigo-600"
                                >Edit</Link
                            >
                            <!-- TODO confirmation -->
                            <Link
                                :href="`/orders/${order.id}`"
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
                <Pagination :links="props.orders.links" />
            </div>
        </div>
    </div>
    </AppLayout>
</template>
