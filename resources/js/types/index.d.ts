import '@inertiajs/svelte';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: any;
    isActive?: boolean;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    [key: string]: unknown;
    ziggy: Config & { location: string };
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export type BookVisibility = 'public' | 'private';

export type Book = {
    ulid: string;
    title: string;
    slug: string;
    visibility: BookVisibility;
    is_default: boolean;
    created_at: string;
    updated_at: string;
};

export type Note = {
    ulid: string;
    body: string;
    details: string;
    book_ulid: string;
    created_at: string;
    updated_at: string;
};
