<script lang="ts">
    import PlaceholderPattern from '@/components/PlaceholderPattern.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem, type Book, type Note } from '@/types';

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
</script>

<svelte:head>
    <title>Dashboard</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="space-y-4 px-4 pt-4 overflow-x-auto">
        {#each notes as note (note.ulid)}
            <div class="mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm bg-white dark:bg-gray-800">
                <h2 class="text-lg font-semibold mb-2">{note.body}</h2>
                <p class="text-gray-700 dark:text-gray-300">{note.details}</p>
            </div>
        {:else}
            <div class="text-center py-10">
                <h2 class="text-2xl font-semibold mb-4">No Notes Available</h2>
                <p class="text-gray-600">You haven't added any notes for this book yet.</p>
            </div>
        {/each}
    </div>
</AppLayout>
