export * from './auth';
export * from './navigation';
export * from './ui';

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Pagination<T> = {
    data: T[];
    links: PaginationLink[];
};

export type UserList = {
    id: number;
    name: string;
    email: string;
};

export type OrderList = {
    id: number;
    userName: string;
    size: string;
    base: string;
    state: string;
    ingredients: string[];
};

export type Ingredient = {
    id: number;
    name: string;
};
