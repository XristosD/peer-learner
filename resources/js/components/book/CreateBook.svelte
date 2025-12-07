<script lang="ts">
    import InputError from '@/components/InputError.svelte';
    import * as Sheet from "@/components/ui/sheet";
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { type Book } from '@/types';
    import { Form } from '@inertiajs/svelte';
    import { CirclePlus } from 'lucide-svelte';
    import { Toggle } from '@/components/ui/toggle';
    import BookController from '@/actions/App/Http/Controllers/BookController';
    import Button from '@/components/ui/button/button.svelte';
    import { fade } from 'svelte/transition';

    let { sidebar } = $props();

    let book: Book = {
        ulid: '',
        title: '',
        visibility: 'public',
        is_default: false,
        slug: '',
        created_at: '',
        updated_at: '',
    }

    let localVisibility: string = $state(book.visibility);
    let localIsDefault: boolean = $state(book.is_default);
    let open = $state(false);

    let toggleLocalVisibility = () => {
        localVisibility = localVisibility === 'public' ? 'private' : 'public';
    };

    let toggleLocalIsDefault = () => {
        localIsDefault = !localIsDefault;
    };

    let form: Form;

    const resetForm = () => {        
        form.resetAndClearErrors();
        localVisibility = book.visibility;
    }

    const handleSuccess = () => {
        open = false;
        resetForm();
        sidebar.toggle();
    };

</script>

<Sheet.Root onOpenChange={(open) => {
    form && resetForm();
}} 
bind:open
>
    <Sheet.Trigger>
        <button>
            <CirclePlus class="h-4 w-4" />
            <span class="sr-only">Create Book</span>
        </button>
    </Sheet.Trigger>
    <Sheet.Content class="w-full">
        <Sheet.Header>
            <Sheet.Title class="text-sm font-light">Create Book</Sheet.Title>
        </Sheet.Header>
        <Form
            {...BookController.create.form()}
            class="px-2 space-y-4" bind:this={form}
            resetOnSuccess
            onSuccess={handleSuccess}
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
                        <Toggle variant="outline" pressed={localVisibility === 'public'}  onPressedChange={toggleLocalVisibility}>Public</Toggle>
                        <Toggle variant="outline" pressed={localVisibility === 'private'}  onPressedChange={toggleLocalVisibility}>Private</Toggle>
                    </div>
                </div>
                <div class="grid gap-2">
                    <Label for="is_default" class="mb-1">Set as Default Book</Label>
                    <input type="hidden" name="is_default" value={Number(localIsDefault)} />
                    <div class="flex items-center gap-2">
                        <Toggle variant="outline" name="is_default" pressed={localIsDefault} onPressedChange={toggleLocalIsDefault} >{localIsDefault ? 'Yes' : 'No'}</Toggle>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <Button type="submit" disabled={processing}>Save</Button>
                    <Button type="button" disabled={processing} onclick={(e: MouseEvent) => open = false}>Close</Button>
                </div>
            {/snippet}
        </Form>
    </Sheet.Content>
</Sheet.Root>

