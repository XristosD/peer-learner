<script lang="ts">
    import EditBook from '@/components/book/EditBook.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem, type Book, type Note } from '@/types';
    import CreateNote from '@/components/note/CreateNote.svelte';
    import NoteComponent from '@/components/note/Note.svelte';
    import { Form } from "@inertiajs/svelte";
    import { Label } from '@/components/ui/label';
    import TipexEditor from '@/components/ui/tipex-editor';
    import InputError from '@/components/InputError.svelte';
    import BookController from '@/actions/App/Http/Controllers/BookController';
    import NoteController from '@/actions/App/Http/Controllers/NoteController';
    import { Button } from '@/components/ui/button';
    import { fade, fly } from 'svelte/transition';


    interface Props {
        book: Book;
        note: Note;
    }

    let { book, note }: Props = $props();
    let body = $state(note.body);
    let details = $state(note.details);
    const breadcrumbs: BreadcrumbItem[] = $derived([
        {
            title: book.title,
            href: '/book',
        },
        {
            title: 'Note',
            href: NoteController.show([book.ulid, note.ulid]),
        },
        {
            title: 'Edit',
            href: NoteController.edit([book.ulid, note.ulid]),
        }
    ]);
</script>

<svelte:head>
    <title>{book.title}</title>
</svelte:head>

<AppLayout {breadcrumbs} >
    <div class="space-y-4 px-4 pt-4 overflow-x-auto">
        <Form 
            {...NoteController.update.form([book, note])}
            options={{ 
                preserveScroll: true,
                only: ['notes'],
                }}
            resetOnSuccess
            class="space-y-4"
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
                    <Label hidden for="body">Body</Label>
                    <TipexEditor 
                        body={note.body}
                        placeholder="Note body..."
                        floating
                        class="mt-1 block w-full"
                        onchange={(content) => {
                            body = content;
                        }}
                    />
                    <input type="hidden" name="body" value={body} />
                    <InputError class="mt-2" message={errors.body} />
                </div>
                <div class="grid gap-2">
                    <Label hidden for="details">Details</Label>
                    <TipexEditor 
                        body={note.details}
                        placeholder="Note details..."
                        floating
                        class="mt-1 block w-full"
                        onchange={(content) => {
                            details = content;
                        }}
                    />
                    <input type="hidden" name="details" value={details} />
                    <InputError class="mt-2" message={errors.details} />
                </div>
                <div class="flex items-center gap-1">
                    <Button type="submit" disabled={processing}>Edit</Button>
                </div>                        
            {/snippet}
        </Form> 
    </div>
</AppLayout>
