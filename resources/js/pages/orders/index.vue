<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
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
import type { BreadcrumbItem, Pagination as PaginationType } from '@/types';

// const props = defineProps<{ orders: PaginationType<OrderList> }>();
const props = defineProps<{
    orders: PaginationType<any>;
    user_name?: string;
    states?: string[];
    search?: string;
}>();

const updateStateForm = useForm<{
    orderId: number | null;
    state: string | null;
}>({ orderId: null, state: null });
const filters = useForm<{
    user_name?: string;
    states?: string[];
    search?: string;
}>({
    user_name: props.user_name,
    states: props.states,
    search: props.search,
});

const statePending = 'Pendente';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Orders',
        href: '/orders',
    },
];
const optionStates = ref<string[]>([statePending, 'Concluído', 'Cancelado']);

const updateState = (orderId: number, state: string) => {
    updateStateForm.state = state;
    updateStateForm.patch(`/orders/${orderId}`, {
        onSuccess: () => {
            updateStateForm.reset();
        },
    });
};
const fetch = () => {
    console.log(filters);
    console.log(filters.user_name);
    
    filters.get('/orders', {
    });
};
</script>

<template>
    <Head title="Orders" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="mb-2 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col">
                        <Label for="filter-name">Utilizador</Label>
                        <Input
                            v-model="filters.user_name"
                            id="filter-user-name"
                            type="text"
                            class="mt-1 w-64"
                            placeholder="Nome do utilizador"
                            @keyup.enter="fetch"
                        />
                    </div>
                    <div class="flex flex-col">
                        <Label for="filter-states">Estados</Label>
                        <select
                            v-model="filters.states"
                            class="rounded border"
                            @change="fetch"
                        >
                            <option
                                v-for="state in optionStates"
                                :key="state"
                                :value="state"
                            >
                                {{ state }}
                            </option>
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <Label for="filter-name">Pesquisa avançada</Label>
                        <Input
                            id="filter-search"
                            type="search"
                            class="mt-1 w-64"
                            placeholder="Pesquisa avançada"
                            v-model="filters.search"
                            @keyup.enter="fetch"
                        />
                    </div>
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <Table>
                    <TableCaption>Encomendas</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead>User</TableHead>
                            <TableHead>Size</TableHead>
                            <TableHead>Base</TableHead>
                            <TableHead>Ingredients</TableHead>
                            <TableHead>State</TableHead>
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
                                <template v-if="order.state !== statePending">{{
                                    order.state
                                }}</template>
                                <select
                                    v-else
                                    v-model="order.state"
                                    class="rounded border"
                                    @change="updateState(order.id, order.state)"
                                >
                                    <option
                                        v-for="state in optionStates"
                                        :key="state"
                                        :value="state"
                                    >
                                        {{ state }}
                                    </option>
                                </select>
                                <!-- TODO confirmation -->
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
