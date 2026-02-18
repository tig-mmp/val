<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
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
} from '@/types';

// const props = defineProps<{ orders: PaginationType<OrderList> }>();
const props = defineProps<{ orders: PaginationType<any> }>();

const updateStateForm = useForm<{
    orderId: number | null;
    state: string | null;
}>({ orderId: null, state: null });
const updateState = (orderId: number, state: string) => {
    updateStateForm.state = state;
    updateStateForm.patch(`/orders/${orderId}`, {
        onSuccess: () => {
            updateStateForm.reset();
        },
    });
};
const statePending = "Pendente";
const states = ref<string[]>([statePending, 'Concluído', 'Cancelado']);
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
                            <TableHead>User</TableHead>
                            <TableHead>Size</TableHead>
                            <TableHead>Base</TableHead>
                            <TableHead>Ingredients</TableHead>
                            <TableHead>State</TableHead>
                            <TableHead class="text-right"> Actions </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="order in props.orders.data"
                            :key="order.id"
                        >
                            <TableCell>{{ order.user?.name }}</TableCell>
                            <TableCell>{{ order.size }}</TableCell>
                            <TableCell>{{ order.base }}</TableCell>
                            <TableCell>{{
                                order.order_ingredients
                                    ?.map((oi) => oi.ingredient.name)
                                    .join(', ')
                            }}</TableCell>
                            <TableCell>
                                <template v-if="order.state !== statePending">{{order.state}}</template>
                                <select v-else
                                    v-model="order.state"
                                    class="rounded border"
                                    @change="updateState(order.id, order.state)"
                                >
                                    <option
                                        v-for="state in states"
                                        :key="state"
                                        :value="state"
                                    >
                                        {{ state }}
                                    </option>
                                </select>
                                <!-- TODO confirmation -->
                            </TableCell>
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
