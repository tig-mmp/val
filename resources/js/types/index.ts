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

export type ListUser = {
    id: number;
    name: string;
    email: string;
};
