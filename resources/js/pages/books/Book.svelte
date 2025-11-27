<script lang="ts">
    import EditBook from '@/components/book/EditBook.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem, type Book, type Note } from '@/types';
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
