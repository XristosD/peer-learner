<script lang="ts">
    import { fly } from 'svelte/transition';
    import { sineIn } from 'svelte/easing';
    import { type Book, type Note } from '@/types';
    import Ellipsis from "@lucide/svelte/icons/ellipsis";
    import * as Sheet from "@/components/ui/sheet";
    import { Button } from '@/components/ui/button';
    import { Form } from "@inertiajs/svelte";
    import { Label } from '@/components/ui/label';
    import { Textarea } from '@/components/ui/textarea';
    import InputError from '@/components/InputError.svelte';
    import { Settings, SquarePen } from 'lucide-svelte';
    import BookController from '@/actions/App/Http/Controllers/BookController';
    import { onDestroy, onMount } from 'svelte';


    interface Props {
        book: Book;
        note: Note;
    }

    let { book, note }: Props = $props();

    let editedNote: Note = $state(structuredClone(note));

    let isEditMode = $state(false);

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

    $effect(() => {
        if (fullOpen) {
            editedNote = structuredClone(note);
        }
    });

    onMount(() => {
		console.log('the component has mounted');
	});
    onDestroy(() => {
		console.log('the component is being destroyed');
	});
</script>


<div in:fly={{ duration: 300, easing: sineIn, y: -100, opacity: .5 }} class="mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm bg-white dark:bg-gray-800">
    <p class="mb-2">{note.body}</p>
    <Button variant="ghost" onclick={() => {
        setFullOpen(true);
        isEditMode = false;
    }} type="button">
        <Settings class="h-4 w-4" />
        <span class="sr-only">Open Full</span>
    </Button>
    <Sheet.Root bind:open={getFullOpen, setFullOpen}>
        <Sheet.Content class="w-full px-2 space-y-4">
            <Sheet.Header>
                <Sheet.Title class="text-sm font-light">Add a note on <span class="italic"></span></Sheet.Title>
            </Sheet.Header>
            {#if !isEditMode}
                <p class="mb-2">{note.body}</p>
                {#if note.details}
                    <p class="">{note.details}</p>
                {/if}
                <div class="flex items-center gap-4">
                    <Button onclick={() => setFullOpen(false)} type="button">Close Full</Button>
                    <Button variant="ghost" onclick={() => {
                        isEditMode = true;
                    }} type="button">
                        <SquarePen class="h-4 w-4" />
                        <span class="sr-only">Edit</span>
                    </Button>
                </div>
            {:else}
                <Form 
                    {...BookController.updateNote.form([book, note])}
                    options={{ 
                        preserveScroll: true,
                        only: ['notes'],
                        }}
                    resetOnSuccess
                    onSuccess={() => isEditMode = false}
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
                                value={editedNote.body}
                                name="body" 
                                class="mt-1 block w-full" 
                                required 
                                placeholder="Note body..."
                            />
                            <InputError class="mt-2" message={errors.body} />
                        </div>
                        <div class="grid gap-2">
                            <Label hidden for="details">Details</Label>
                            <Textarea 
                                value={editedNote.details}
                                name="details" 
                                class="mt-1 block w-full" 
                                placeholder="Note details..."
                            />
                            <InputError class="mt-2" message={errors.details} />
                        </div>
                        <div class="flex items-center gap-1">
                            <Button type="submit" disabled={processing}>Edit</Button>
                            <Button variant="ghost" onclick={() => isEditMode = false} type="button">
                                <span>Cancel</span>
                            </Button>
                        </div>                        
                    {/snippet}
                </Form> 
            {/if}
        </Sheet.Content>
    </Sheet.Root>  
</div>