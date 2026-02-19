<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Pagination from '@/components/Pagination.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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
    Order,
    Pagination as PaginationType,
} from '@/types';

const props = defineProps<{
    orders: PaginationType<Order>;
    user_name?: string;
    states?: string[];
    search?: string;
}>();

const updateStateForm = useForm<{ state: string | null }>({ state: null });
const filters = useForm({
    user_name: props.user_name,
    states: props.states,
    search: props.search,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Orders',
        href: '/orders',
    },
];
const statePending = 'Pendente';
const stateCompleted = 'Concluído';
const stateCancelled = 'Cancelado';
const optionStates = ref<string[]>([
    statePending,
    stateCompleted,
    stateCancelled,
]);

const stateClass = (state: string): string => {
    return state === statePending
        ? 'text-yellow-500'
        : state === stateCompleted
          ? 'text-green-500'
          : 'text-red-500';
};

const updateState = (orderId: number, state: string) => {
    if (!state) return;
    updateStateForm.state = state;
    updateStateForm.patch(`/orders/${orderId}`, {
        onSuccess: () => {
            updateStateForm.reset();
        },
    });
};
const fetch = () => {
    filters.get('/orders', {
        preserveState: true,
        replace: true,
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
                        <Label for="filter-user-name">Utilizador</Label>
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
                        <Select
                            v-model="filters.states"
                            multiple
                            @update:model-value="fetch"
                            class="mt-1 w-64"
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select os estados" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem
                                        v-for="optionState in optionStates"
                                        :key="optionState"
                                        :value="optionState"
                                    >
                                        <span
                                            :class="stateClass(optionState)"
                                            >{{ optionState }}</span
                                        >
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="flex flex-col">
                        <Label for="filter-search">Pesquisa avançada</Label>
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
                                    ?.map((oi) => oi.ingredient?.name)
                                    .join(', ')
                            }}</TableCell>
                            <TableCell>
                                <span
                                    v-if="order.state !== statePending"
                                    :class="stateClass(order.state)"
                                    >{{ order.state }}</span
                                >
                                <Select
                                    v-else
                                    v-model="order.state"
                                    @update:model-value="
                                        (state) => updateState(order.id, state)
                                    "
                                >
                                    <SelectTrigger class="w-32">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem
                                                v-for="state in optionStates"
                                                :key="state"
                                                :value="state"
                                            >
                                                <span
                                                    :class="stateClass(state)"
                                                    >{{ state }}</span
                                                >
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
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
