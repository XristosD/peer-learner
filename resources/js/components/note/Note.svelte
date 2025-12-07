<script lang="ts">
    import { fly } from 'svelte/transition';
    import { sineIn } from 'svelte/easing';
    import { type Book, type Note } from '@/types';
    import Ellipsis from '@lucide/svelte/icons/ellipsis';
    import * as Sheet from '@/components/ui/sheet';
    import { Button } from '@/components/ui/button';
    import { Form } from '@inertiajs/svelte';
    import { Label } from '@/components/ui/label';
    import { Textarea } from '@/components/ui/textarea';
    import InputError from '@/components/InputError.svelte';
    import { Eye, Settings, SquarePen } from 'lucide-svelte';
    import BookController from '@/actions/App/Http/Controllers/BookController';
    import NoteController from '@/actions/App/Http/Controllers/NoteController';
    import { inertia, Link } from '@inertiajs/svelte';

    interface Props {
        book: Book;
        note: Note;
    }

    let { book, note }: Props = $props();
</script>

<div
    in:fly={{ duration: 300, easing: sineIn, y: -100, opacity: 0.5 }}
    class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800"
>
    <div>
        <div class="mb-2">
            {@html note.body}
        </div>
        <div class="flex items-center justify-end gap-2">
            <Button variant="ghost">
                <Link href={NoteController.show([book.ulid, note.ulid])} class="flex items-center">
                    {#if note.details}
                        <span class="pr-2 text-sm leading-none text-gray-500 dark:text-gray-400">more lines</span>
                    {/if}
                    <Eye class="mr-1 h-4 w-4" />
                    <span class="sr-only">Open Note</span>
                </Link>
            </Button>
        </div>
    </div>
</div>
