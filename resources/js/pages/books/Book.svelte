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

    function formatDate(dateString: string): string {
        return new Date(dateString).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    }

    function getDateOnly(dateString: string): string {
        return new Date(dateString).toISOString().split('T')[0];
    }
</script>

<svelte:head>
    <title>{book.title}</title>
</svelte:head>

{#snippet editBook()}
    <EditBook {book}></EditBook>
{/snippet}

<AppLayout {breadcrumbs} headerAction={editBook}>
    <div class="space-y-4 overflow-x-auto px-4 pt-4">
        <CreateNote {book} />
        {#each notes as note, index (note.ulid)}
            {#if index === 0 || getDateOnly(notes[index - 1].created_at) !== getDateOnly(note.created_at)}
                <div class="mt-6 mb-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <h3 class="text-sm font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400">
                        {formatDate(note.created_at)}
                    </h3>
                </div>
            {/if}
            <NoteComponent {book} {note} />
        {:else}
            <div class="text-center py-10">
                <h2 class="text-2xl font-semibold mb-4">No Notes Available</h2>
                <p class="text-gray-600">You haven't added any notes for this book yet.</p>
            </div>
        {/each}
    </div>
</AppLayout>
