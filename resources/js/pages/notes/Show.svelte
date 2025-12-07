<script lang="ts">
    import EditBook from '@/components/book/EditBook.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem, type Book, type Note } from '@/types';
    import CreateNote from '@/components/note/CreateNote.svelte';
    import NoteComponent from '@/components/note/Note.svelte';
    import Button from '@/components/ui/button/button.svelte';
    import NoteController from '@/actions/App/Http/Controllers/NoteController';
    import { inertia, Link } from '@inertiajs/svelte';
    import { Settings } from 'lucide-svelte';

    interface Props {
        book: Book;
        note: Note;
    }

    let { book, note }: Props = $props();
    const breadcrumbs: BreadcrumbItem[] = $derived([
        {
            title: book.title,
            href: '/book',
        },
        {
            title: 'Note',
            href: NoteController.show([book.ulid, note.ulid]),
        },
    ]);
</script>

<svelte:head>
    <title>{book.title}</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="space-y-4 overflow-x-auto px-4 pt-4">
        {@html note.body}
        {#if note.details}
            {@html note.details}
        {/if}
        <Button variant="ghost">
            <Link href={NoteController.edit([book.ulid, note.ulid])}>
                <Settings class="h-4 w-4" />
                <span class="sr-only">Open Full</span>
            </Link>
        </Button>
    </div>
</AppLayout>
