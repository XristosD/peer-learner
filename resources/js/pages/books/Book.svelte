<script lang="ts">
    import EditBook from '@/components/book/EditBook.svelte';
    import HeadingSmall from '@/components/HeadingSmall.svelte';
    import InputError from '@/components/InputError.svelte';
    import PlaceholderPattern from '@/components/PlaceholderPattern.svelte';
    import { Button } from '@/components/ui/button';
    import * as Sheet from "@/components/ui/sheet";
    import { Input } from '@/components/ui/input';
    import { Textarea } from '@/components/ui/textarea';
    import { Label } from '@/components/ui/label';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem, type Book, type Note } from '@/types';
    import { Form } from '@inertiajs/svelte';
    import BookController from '@/actions/App/Http/Controllers/BookController';
    import { fly } from 'svelte/transition';
    import { sineIn } from 'svelte/easing';
    import { setHeaderContext } from '@/lib/utils';
    import { Settings } from 'lucide-svelte';
    import { Toggle } from '@/components/ui/toggle';

    interface Props {
        notes: Note[];
        book: Book;
    }

    let { notes, book }: Props = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: book.title,
            href: '/book',
        },
    ];

    setHeaderContext([
            [
                {
                    title: book.title,
                    href: '/book',
                },
            ], 
            editBook
        ]);
</script>

<svelte:head>
    <title>Dashboard</title>
</svelte:head>

{#snippet editBook() }
    <EditBook {book}></EditBook>
{/snippet}

<AppLayout>
    <div class="space-y-4 px-4 pt-4 overflow-x-auto">
        <HeadingSmall title="Add a note" />
        <Form 
            {...BookController.createNote.form(book)} 
            options={{ 
                preserveScroll: true,
                only: ['notes'],
             }}
             resetOnSuccess
            class="space-y-6"
        >
            {#snippet children({
                errors,
                processing,
                recentlySuccessful,
            }: {
                errors: Record<string, string>;
                processing: boolean;
                recentlySuccessful: boolean;
            })}
                <div class="grid gap-2">
                    <Label for="body">Body</Label>
                    <Textarea name="body" class="mt-1 block w-full" required placeholder="Note body..."/>
                    <InputError class="mt-2" message={errors.body} />
                </div>

                <div class="grid gap-2">
                    <Label for="details">Details</Label>
                    <Textarea
                        name="details"
                        class="mt-1 block w-full"
                        placeholder="Note details..."
                    />
                    <InputError class="mt-2" message={errors.details} />
                </div>


                <div class="flex items-center gap-4">
                    <Button type="submit" disabled={processing}>New</Button>
                </div>
            {/snippet}
        </Form> 
        {#each notes as note (note.ulid)}
            <div in:fly={{ duration: 500, easing: sineIn, y: -200, opacity: .5 }} class="mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm bg-white dark:bg-gray-800">
                <p class="mb-2">{note.body}</p>
                <p class="">{note.details}</p>
            </div>
        {:else}
            <div class="text-center py-10">
                <h2 class="text-2xl font-semibold mb-4">No Notes Available</h2>
                <p class="text-gray-600">You haven't added any notes for this book yet.</p>
            </div>
        {/each}
    </div>
</AppLayout>
