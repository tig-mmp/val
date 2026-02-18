<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import type { Ingredient } from '@/types';

const props = defineProps<{ ingredients?: Ingredient[] }>();

const form = useForm({
    size: null,
    base: null,
    ingredients: [],
});

const sizes = ref<string[]>(['Individual', 'Média', 'Grande', 'Familiar']);
// TODO create table bases/pizzas
const bases = ref<string[]>(['Havaiana',' Fiambre']);
const submit = () => {
    form.post(`/orders`, {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <div class="mb-4 text-center text-sm font-medium">
        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="mt-4 grid gap-2">
                    <Label for="name">Tamanho</Label>
                    <select v-model="form.size" class="rounded border">
                        <option v-for="size in sizes" :key="size" :value="size">
                            {{ size }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.size" />
                </div>
                <div class="mt-4 grid gap-2">
                    <Label for="name">Base</Label>
                    <select v-model="form.base" class="rounded border">
                        <option v-for="base in bases" :key="base" :value="base">
                            {{ base }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.base" />
                </div>
                <div class="mt-4 grid gap-2">
                    <Label for="name">Ingredientes extra</Label>
                    <div v-for="ingredient in props.ingredients" :key="ingredient.id">
                        <label class=" mr-2">{{ ingredient.name }}</label>
                        <input
                            type="checkbox"
                            v-model="form.ingredients"
                            :value="ingredient"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.ingredients" />
                </div>
                <div class="mt-4 flex items-center gap-4">
                    <Button :disabled="form.processing">Efetuar encomendas</Button>
                </div>
            </div>
        </form>
    </div>
</template>
