import { BreadcrumbItem } from '@/types';
import { clsx, type ClassValue } from 'clsx';
import { createContext, Snippet } from 'svelte';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export type WithoutChild<T> = T extends { child?: any } ? Omit<T, 'child'> : T;
export type WithoutChildren<T> = T extends { children?: any } ? Omit<T, 'children'> : T;
export type WithoutChildrenOrChild<T> = WithoutChildren<WithoutChild<T>>;
export type WithElementRef<T, U extends HTMLElement = HTMLElement> = T & { ref?: U | null };

export const [getHeaderContext, setHeaderContext] = createContext<readonly [BreadcrumbItem[], Snippet<[]> | null]>();
