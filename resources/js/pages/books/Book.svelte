<script lang="ts">
    import EditBook from '@/components/book/EditBook.svelte';
    import HeadingSmall from '@/components/HeadingSmall.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Textarea } from '@/components/ui/textarea';
    import { Label } from '@/components/ui/label';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem, type Book, type Note } from '@/types';
    import { Form } from '@inertiajs/svelte';
    import BookController from '@/actions/App/Http/Controllers/BookController';
    import { fly } from 'svelte/transition';
    import { sineIn } from 'svelte/easing';
    import CreateNote from '@/components/note/CreateNote.svelte';
    import NoteComponent from '@/components/note/Note.svelte';

    interface Props {
        notes: Note[];
        book: Book;
    }

    let { notes, book }: Props = $props();

    const breadcrumbs: BreadcrumbItem[] = $derived([
        {
            title: book.title,
            href: '/book',
        },
    ]);
</script>

<svelte:head>
    <title>Dashboard</title>
</svelte:head>

{#snippet editBook() }
    <EditBook {book}></EditBook>
{/snippet}

<AppLayout {breadcrumbs} headerAction={editBook}>
    <div class="space-y-4 px-4 pt-4 overflow-x-auto">
        <CreateNote {book} />
        {#each notes as note (note.ulid)}
            <NoteComponent {book} {note} />
        {:else}
            <div class="text-center py-10">
                <h2 class="text-2xl font-semibold mb-4">No Notes Available</h2>
                <p class="text-gray-600">You haven't added any notes for this book yet.</p>
            </div>
        {/each}
    </div>
</AppLayout>
