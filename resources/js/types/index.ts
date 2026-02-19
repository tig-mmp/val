export * from './auth';
export * from './navigation';
export * from './ui';
import type { UserRole } from '@/enums/UserRole';

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Pagination<T> = {
    data: T[];
    links: PaginationLink[];
};

export type User = {
    id: number;
    name: string;
    email: string;
    role: UserRole;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type UserList = {
    id: number;
    name: string;
    email: string;
};

export type Order = {
    id: number;
    user_id: number;
    size: string;
    base: string;
    state: string;
    created_at: string;
    updated_at: string;
    user?: User;
    order_ingredients?: OrderIngredient[];
};

export type OrderIngredient = {
    id: number;
    order_id: number;
    ingredient_id: number;
    ingredient?: Ingredient;
    created_at?: string;
    updated_at?: string;
};

export type Ingredient = {
    id: number;
    name: string;
    created_at?: string;
    updated_at?: string;
};
