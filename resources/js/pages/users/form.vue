<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

interface User {
    id?: number;
    name?: string;
    email?: string;
}

const props = defineProps<{ user?: User }>();

const form = useForm({
    email: props.user?.email ?? '',
    name: props.user?.name ?? '',
});

const submit = () => {
    if (!props.user?.id) {
        form.post(`/users`, {
            onSuccess: () => {
                form.reset();
            },
        });
    } else {    
        form.put(`/users/${props.user.id}`, {
            onSuccess: () => {
                form.reset();
            },
        });
    }
};
</script>

<template>
    <Head title="User Edit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <form @submit.prevent="submit">
                <div class="grid gap-2 mt-4">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        name="name"
                        v-model="form.name"
                        required
                        placeholder="Name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div class="grid gap-2 mt-4">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        name="email"
                        v-model="form.email"
                        required
                        placeholder="Email"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="flex items-center gap-4 mt-4">
                    <Button :disabled="form.processing">Update</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
