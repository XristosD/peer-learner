<script lang="ts">
    import EditBook from '@/components/book/EditBook.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem, type Book, type Note } from '@/types';
    import CreateNote from '@/components/note/CreateNote.svelte';
    import NoteComponent from '@/components/note/Note.svelte';
    import RangeCalendar from '@/components/ui/range-calendar/range-calendar.svelte';
    import { CalendarDate, today, getLocalTimeZone } from '@internationalized/date';
    import type { DateRange } from 'bits-ui';
    import * as Collapsible from '@/components/ui/collapsible/index.js';
    import Button from '@/components/ui/button/button.svelte';

    interface Props {
        notes: Note[];
        book: Book;
        dateFilter?: { from: string; to: string };
    }

    let { notes, book, dateFilter }: Props = $props();

    const breadcrumbs: BreadcrumbItem[] = $derived([
        {
            title: book.title,
            href: '/book',
        },
    ]);

    let dateRangeFilter: DateRange = $derived.by(() => {
        if (!dateFilter) {
            return { start: undefined, end: undefined };
        }

        let fromDate = new Date(dateFilter.from);
        let toDate = new Date(dateFilter.to);

        return {
            start: new CalendarDate(fromDate.getFullYear(), fromDate.getMonth() + 1, fromDate.getDate()),
            end: new CalendarDate(toDate.getFullYear(), toDate.getMonth() + 1, toDate.getDate()),
        };
    });

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
        <Collapsible.Root class="rounded-lg border shadow-sm">
            <Collapsible.Trigger class="">Date Filter</Collapsible.Trigger>
            <Collapsible.Content class="mb-4">
                <div class="align-center flex flex-col justify-center">
                    <RangeCalendar bind:value={dateRangeFilter} maxValue={today(getLocalTimeZone())} minValue={dateRangeFilter.start} />
                    <Button variant="secondary" class="mt-10 w-full">Apply</Button>
                </div>
            </Collapsible.Content>
        </Collapsible.Root>
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
