<script lang="ts">
    import InputError from '@/components/InputError.svelte';
    import * as Sheet from '@/components/ui/sheet';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { type Errors, type Book } from '@/types';
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

    let localVisibility = $state(book.visibility);
    let localIsDefault = $state(book.is_default);
    let open = $state(false);

    const toggleLocalVisibility = () => {
        localVisibility = localVisibility === 'public' ? 'private' : 'public';
    };

    const toggleLocalIsDefault = () => {
        localIsDefault = !localIsDefault;
    };

    let form: Form;

    const resetForm = () => {
        form.resetAndClearErrors();
        localVisibility = book.visibility;
        localIsDefault = book.is_default;
    };
</script>

<Sheet.Root
    onOpenChange={(isOpen) => {
        if (!isOpen && form) resetForm();
    }}
    bind:open
>
    <Sheet.Trigger>
        <button class="transition-transform hover:scale-110">
            <Settings class="h-4 w-4" />
            <span class="sr-only">Edit Book</span>
        </button>
    </Sheet.Trigger>
    <Sheet.Content class="w-full overflow-y-auto">
        <Sheet.Header>
            <Sheet.Title class="text-lg font-medium">Edit Book</Sheet.Title>
            <p class="mt-1 text-xs text-muted-foreground">
                Editing <span class="font-medium italic">{book.title}</span>
            </p>
        </Sheet.Header>
        <Form
            {...BookController.update.form(book)}
            options={{
                preserveScroll: true,
                only: ['book', 'books'],
            }}
            class="mt-6 space-y-5 p-4"
            bind:this={form}
        >
            {#snippet children({ errors, processing, recentlySuccessful }: { errors: Errors; processing: boolean; recentlySuccessful: boolean })}
                <div class="space-y-2">
                    <Label for="title" class="text-sm font-medium">Book Title</Label>
                    <Input id="title" name="title" class="h-9" required placeholder="Book title..." defaultValue={book.title} disabled={processing} />
                    <InputError class="mt-1 text-xs" message={errors.title} />
                </div>

                <div class="space-y-2">
                    <Label class="text-sm font-medium">Visibility</Label>
                    <p class="text-xs text-muted-foreground">Choose who can access this book</p>
                    <input type="hidden" name="visibility" value={localVisibility} />
                    <div class="flex gap-2">
                        <Toggle
                            variant="outline"
                            pressed={localVisibility === 'public'}
                            onPressedChange={toggleLocalVisibility}
                            disabled={processing}
                            class="flex-1"
                        >
                            <span class="text-sm">Public</span>
                        </Toggle>
                        <Toggle
                            variant="outline"
                            pressed={localVisibility === 'private'}
                            onPressedChange={toggleLocalVisibility}
                            disabled={processing}
                            class="flex-1"
                        >
                            <span class="text-sm">Private</span>
                        </Toggle>
                    </div>
                </div>

                {#if book.is_default === true}
                    <div class="rounded bg-blue-50 px-3 py-2 text-xs text-blue-600 dark:bg-blue-950/30 dark:text-blue-500">
                        ℹ This book is currently set as your default book
                    </div>
                {:else}
                    <div class="space-y-2">
                        <Label class="text-sm font-medium">Default Book</Label>
                        <p class="text-xs text-muted-foreground">Make this your default book for new notes</p>
                        <input type="hidden" name="is_default" value={Number(localIsDefault)} />
                        <Toggle
                            variant="outline"
                            pressed={localIsDefault}
                            onPressedChange={toggleLocalIsDefault}
                            disabled={processing}
                            class="w-full justify-start"
                        >
                            <span class="text-sm">{localIsDefault ? 'Yes, set as default' : 'No, keep as regular'}</span>
                        </Toggle>
                        <InputError class="mt-1 text-xs" message={errors.is_default} />
                    </div>
                {/if}

                {#if recentlySuccessful}
                    <div
                        class="rounded bg-green-50 px-3 py-2 text-xs text-green-600 dark:bg-green-950/30 dark:text-green-500"
                        transition:fade={{ duration: 200 }}
                    >
                        ✓ Book updated successfully
                    </div>
                {/if}

                <div class="flex gap-2 pt-2">
                    <Button type="submit" disabled={processing} class="flex-1">
                        {processing ? 'Updating...' : 'Update Book'}
                    </Button>
                    <Button type="button" variant="outline" disabled={processing} onclick={() => (open = false)}>Close</Button>
                </div>
            {/snippet}
        </Form>
    </Sheet.Content>
</Sheet.Root>
