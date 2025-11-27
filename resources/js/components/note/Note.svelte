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
    import NoteController from '@/actions/App/Http/Controllers/NoteController';
    import { inertia, Link } from '@inertiajs/svelte'


    interface Props {
        book: Book;
        note: Note;
    }

    let { book, note }: Props = $props();

</script>


<div in:fly={{ duration: 300, easing: sineIn, y: -100, opacity: .5 }} class="mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm bg-white dark:bg-gray-800">
    <p class="mb-2">{note.body}</p>
    <Button variant="ghost">
        <Link href={NoteController.show([book.ulid, note.ulid])}>
            <Settings class="h-4 w-4" />
            <span class="sr-only">Open Full</span>
        </Link>
    </Button>  
</div>