<script lang="ts">
    import { type Book, type Note } from '@/types';
    import HeadingSmall from '../HeadingSmall.svelte';
    import { Form } from '@inertiajs/svelte';
    import NoteController from '@/actions/App/Http/Controllers/NoteController';
    import { Label } from '@/components/ui/label';
    import TipexEditor from '@/components/ui/tipex-editor';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';

    interface Props {
        book: Book;
    }

    let { book }: Props = $props();

    let body: string = $state('');
</script>

<div>
    <HeadingSmall title="Add a note" />
    <Form
        {...NoteController.create.form(book)}
        options={{
            preserveScroll: true,
            only: ['notes'],
        }}
        resetOnSuccess
        class="space-y-4"
    >
        {#snippet children({ errors, processing, recentlySuccessful }: { errors: Record; processing: boolean; recentlySuccessful: boolean })}
            <div class="grid gap-2">
                <Label hidden for="body">Body</Label>
                <TipexEditor
                    body=""
                    placeholder="Note body..."
                    floating
                    class="mt-1 block w-full"
                    onchange={(content) => {
                        body = content;
                    }}
                ></TipexEditor>
                <input type="hidden" name="body" value={body} />
                <InputError class="mt-2" message={errors.body} />
            </div>
            <div class="flex items-center gap-1">
                <Button type="submit" disabled={processing}>New</Button>
            </div>
        {/snippet}
    </Form>
</div>
