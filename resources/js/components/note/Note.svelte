<script lang="ts">
    import { fly } from 'svelte/transition';
    import { sineIn } from 'svelte/easing';
    import { type Book, type Note } from '@/types';
    import { Button } from '@/components/ui/button';
    import { Badge } from '@/components/ui/badge';
    import { Eye, ChevronDown, ChevronUp, FileText, Pen } from 'lucide-svelte';
    import NoteController from '@/actions/App/Http/Controllers/NoteController';
    import { Link } from '@inertiajs/svelte';
    import * as Card from '@/components/ui/card/index.js';
    import { buttonVariants } from '@/components/ui/button';

    interface Props {
        book: Book;
        note: Note;
    }

    let { book, note }: Props = $props();

    const maxHeight: number = 200;
    const transitionDuration: number = 300;
    let contentElement: HTMLElement | null = null;

    let expanded: boolean = $state(false);
    let isOverflowing: boolean = $state(false);
    let contentHeight: number = $state(0);

    let animatedHeight = $state<string | number>(maxHeight);

    let resizeObserver: ResizeObserver | null = null;
    let transitioning = $state(false);

    let windowHeight = $state(0);

    $inspect(animatedHeight);

    function measure() {
        if (!contentElement) return;

        contentHeight = contentElement.scrollHeight;
        isOverflowing = contentHeight > maxHeight;

        if (!expanded) animatedHeight = maxHeight;
    }

    function toggle() {
        if (!contentElement) return;

        transitioning = true;
        expanded = !expanded;

        if (expanded) {
            // expand
            animatedHeight = contentHeight;

            setTimeout(() => {
                transitioning = false;
            }, transitionDuration);
        } else {
            // collapse
            animatedHeight = contentHeight;

            requestAnimationFrame(() => {
                animatedHeight = maxHeight;
                transitioning = false;

                // Scroll up by the amount that was hidden
                const scrollAmount = contentHeight - maxHeight;
                window.scrollBy({
                    top: -scrollAmount,
                    behavior: 'smooth',
                });
            });
        }
    }

    $effect(() => {
        if (!contentElement) return;

        measure();

        resizeObserver = new ResizeObserver(() => measure());
        resizeObserver.observe(contentElement);

        const onResize = () => measure();
        window.addEventListener('resize', onResize);

        return () => {
            resizeObserver?.disconnect();
            window.removeEventListener('resize', onResize);
        };
    });
</script>

<svelte:window bind:innerHeight={windowHeight} />

<div in:fly={{ duration: 300, easing: sineIn, y: -100, opacity: 0.5 }}>
    <Card.Root class="gap-1">
        <Card.Content class="relative">
            <div
                style:max-height={`${animatedHeight}px`}
                style:transition={`max-height ${transitionDuration}ms ease-in-out`}
                class="relative overflow-hidden"
            >
                <div bind:this={contentElement}>
                    {@html note.body}
                </div>
                {#if !expanded && isOverflowing}
                    <div class="absolute bottom-0 left-0 h-8 w-full bg-linear-to-t from-card via-card to-transparent"></div>
                {/if}
            </div>
            {#if isOverflowing}
                <Button
                    variant="outline"
                    size="icon"
                    onclick={toggle}
                    aria-expanded={expanded}
                    aria-controls="expandable-content"
                    class="absolute right-1 bottom-3 h-9 w-9 rounded-full border"
                >
                    {#if expanded}
                        <ChevronUp class="h-4 w-4" />
                    {:else}
                        <ChevronDown class="h-4 w-4" />
                    {/if}
                </Button>
            {/if}
        </Card.Content>
        <Card.Footer>
            <div class="flex w-full flex-col gap-2">
                {#if note.tags && note.tags.length > 0}
                    <div class="mt-1 flex flex-wrap gap-2">
                        {#each note.tags.sort((a, b) => a.order - b.order) as tag}
                            <Badge variant="secondary">{tag.title}</Badge>
                        {/each}
                    </div>
                {/if}
                <div class="flex items-center gap-2">
                    <Link href={NoteController.edit([book.ulid, note.ulid])} class={buttonVariants({ variant: 'ghost', size: 'icon' })}>
                        <Pen class="h-4 w-4" />
                        <span class="sr-only">Edit Note</span>
                    </Link>
                    <div class="ml-auto flex items-center">
                        {#if note.details}
                            <Badge variant="outline" class="mr-1 flex items-center gap-1">
                                <FileText class="h-3 w-3 text-gray-600 dark:text-gray-400" />
                                <span class="text-xs text-gray-600 dark:text-gray-400">details</span>
                            </Badge>
                        {/if}
                        <Link href={NoteController.show([book.ulid, note.ulid])} class={buttonVariants({ variant: 'ghost', size: 'icon' })}>
                            <Eye class="h-4 w-4" />
                            <span class="sr-only">Open Note</span>
                        </Link>
                    </div>
                </div>
            </div>
        </Card.Footer>
    </Card.Root>
</div>
