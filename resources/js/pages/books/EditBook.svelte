<script lang="ts">
    import InputError from '@/components/InputError.svelte';
    import * as Sheet from "@/components/ui/sheet";
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { type Book } from '@/types';
    import { Form } from '@inertiajs/svelte';
    import { Settings } from 'lucide-svelte';
    import { Toggle } from '@/components/ui/toggle';
    import BookController from '@/actions/App/Http/Controllers/BookController';
    import Button from '@/components/ui/button/button.svelte';
    import { fade } from 'svelte/transition';

    interface Props {
        book: Book;
    }

    let { book }: Props = $props();

    let localVisibility: string = $state(book.visibility);
    let open = $state(false);

    let toggleLocalVisibility = () => {
        localVisibility = localVisibility === 'public' ? 'private' : 'public';
    };

    let form: Form;

    const resetForm = () => {
        form.resetAndClearErrors();
        localVisibility = book.visibility;
    }

</script>

    <Sheet.Root onOpenChange={(open) => {form && resetForm()}} bind:open>
        <Sheet.Trigger>
            <button>
                <Settings class="h-4 w-4" />
                <span class="sr-only">Edit Book</span>
            </button>
        </Sheet.Trigger>
        <Sheet.Content>
            <Sheet.Close />
            <Sheet.Header>
                <Sheet.Title class="text-sm font-light">Edit <span class="italic">{book.title}</span></Sheet.Title>
            </Sheet.Header>
            <Form
                {...BookController.update.form(book)}
                options={{ 
                    preserveScroll: true,
                    only: ['book'],
                }}
                class="px-2 space-y-4" bind:this={form}
            >
                {#snippet children({
                        errors,
                        processing,
                        recentlySuccessful,
                        reset,
                    }: {
                        errors: Record<string, string>;
                        processing: boolean;
                        recentlySuccessful: boolean;
                        reset: (open: boolean) => void;
                    })}
                    <div class="grid gap-2">
                        <Label for="title">Title</Label>
                        <Input
                            name="title"
                            class="mt-1 block w-full"
                            required
                            placeholder="Book title..."
                            defaultValue={book.title}
                        />
                        <InputError class="mt-2" message={errors.title} />
                    </div>
                    <div class="grid gap-2">
                        <Label for="visibility">Visibility</Label>
                        <input type="hidden" name="visibility" value={localVisibility} />
                        <div class="flex items-center gap-2">
                            <Toggle pressed={localVisibility === 'public'}  onPressedChange={toggleLocalVisibility}>Public</Toggle>
                            <Toggle pressed={localVisibility === 'private'}  onPressedChange={toggleLocalVisibility}>Private</Toggle>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <Button type="submit" disabled={processing}>Save</Button>
                        <Button type="button" disabled={processing} onclick={(e: MouseEvent) => open = false}>Close</Button>
                    </div>
                    {#if recentlySuccessful}
                        <div class="flex items-center gap-4" in:fade out:fade>
                            <p class="text-sm text-green-600">Success</p>
                        </div>
                    {/if}
                {/snippet}
            </Form>
        </Sheet.Content>
    </Sheet.Root>

