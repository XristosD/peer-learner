<script lang="ts">
    import { type Book, type Note } from "@/types";
    import HeadingSmall from "../HeadingSmall.svelte";
    import { Form } from "@inertiajs/svelte";
    import BookController from "@/actions/App/Http/Controllers/BookController";
    import { Label } from '@/components/ui/label';
    import { Textarea } from '@/components/ui/textarea';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import * as Sheet from "@/components/ui/sheet";
    import { Settings } from 'lucide-svelte';
    import Ellipsis from "@lucide/svelte/icons/ellipsis";


    interface Props {
        notes: Note[];
        book: Book;
    }

    let { book } = $props();

    let note: Note = $state({
        ulid: '',
        body: '',
        details: '',
        created_at: '',
        updated_at: '',
    })

    let fullOpen = $state(false);

    function getFullOpen() {
        return fullOpen;
    }
    
    function setFullOpen(newOpen: boolean) {
        fullOpen = newOpen;
    }

    function toggleFullOpen() {
        fullOpen = !fullOpen;
    }


</script>

<div>
    <HeadingSmall title="Add a note" />
    <Form 
        {...BookController.createNote.form(book)} 
        options={{ 
            preserveScroll: true,
            only: ['notes'],
            }}
        resetOnSuccess
        onSuccess={() => setFullOpen(false)}
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
                <Textarea 
                    bind:value={note.body}
                    name="body" 
                    class="mt-1 block w-full" 
                    required placeholder="Note body..."
                    disabled={fullOpen === true}
                />
                <InputError class="mt-2" message={errors.body} />
            </div>
    
            <div class="flex items-center gap-1">
                <Button type="submit" disabled={processing}>New</Button>
                <Button variant="ghost" onclick={() => setFullOpen(true)} type="button">
                    <Ellipsis class="h-4 w-4" />
                    <span class="sr-only">Open Full</span>
                </Button>
            </div>
            <Sheet.Root bind:open={getFullOpen, setFullOpen}>
                <Sheet.Content portalProps={{disabled: true}} class="w-full px-2 space-y-4">
                    <Sheet.Header>
                        <Sheet.Title class="text-sm font-light">Add a note on <span class="italic">{book.title}</span></Sheet.Title>
                    </Sheet.Header>
                    <div class="grid gap-2">
                        <Label hidden for="body">Body</Label>
                        <Textarea 
                            bind:value={note.body}
                            name="body" 
                            class="mt-1 block w-full" 
                            required placeholder="Note body..."
                            disabled={fullOpen === false}
                        />
                        <InputError class="mt-2" message={errors.body} />
                    </div>
                    <div class="grid gap-2">
                        <Label hidden for="details">Details</Label>
                        <Textarea 
                            bind:value={note.details}
                            name="details" 
                            class="mt-1 block w-full" 
                            required placeholder="Note details..."
                            disabled={fullOpen === false}
                        />
                        <InputError class="mt-2" message={errors.details} />
                    </div>
                    <div class="flex items-center gap-4">
                        <Button type="submit" disabled={processing}>New</Button>
                        <Button onclick={() => setFullOpen(false)} type="button">Close Full</Button>
                    </div>
                </Sheet.Content>
            </Sheet.Root>  
            
        {/snippet}
    </Form> 
</div>