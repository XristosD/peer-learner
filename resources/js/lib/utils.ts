import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";
import { createContext, Snippet } from 'svelte';
import { Header } from "@/components/ui/sheet";
import { BreadcrumbItem } from "@/types";

export function cn(...inputs: ClassValue[]) {
	return twMerge(clsx(inputs));
}

export type WithoutChild<T> = T extends { child?: any } ? Omit<T, "child"> : T;
export type WithoutChildren<T> = T extends { children?: any } ? Omit<T, "children"> : T;
export type WithoutChildrenOrChild<T> = WithoutChildren<WithoutChild<T>>;
export type WithElementRef<T, U extends HTMLElement = HTMLElement> = T & { ref?: U | null };


export const [getHeaderContext, setHeaderContext] = createContext<readonly [BreadcrumbItem[], Snippet<[]> | null]>();
