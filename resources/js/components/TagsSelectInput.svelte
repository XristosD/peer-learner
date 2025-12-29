<script lang="ts">
    import { type Tag, type NoteTag, type UpdatedTags } from '@/types';
    import { SortableList, sortItems, scaleFly, removeItem } from '@rodrigodagostino/svelte-sortable-list';
    import { cn } from '@/lib/utils.js';
    import { X } from 'lucide-svelte';
    import * as Command from '@/components/ui/command/index.js';
    import Spinner from '@/components/ui/spinner/spinner.svelte';
    import TagController from '@/actions/App/Http/Controllers/TagController';

    interface Props {
        selectedTags?: NoteTag[];
        onchange: (tags: UpdatedTags[]) => void;
    }

    let { selectedTags = [], onchange }: Props = $props();

    let query = $state('');
    let newTagTitle = $state('');
    let searchResults = $state<Tag[]>([]);
    let isLoading = $state(false);
    let error = $state<string | null>(null);

    let debounceTimer: ReturnType<typeof setTimeout> | null = null;
    let controller: AbortController | null = null;

    function convertTags(selectedTags: NoteTag[]): Array<{ ulid?: string; title?: string; order: number }> {
        return selectedTags.map((tag) => {
            if (tag.ulid.startsWith('new-tag-')) {
                return {
                    title: tag.title,
                    order: tag.order,
                };
            }
            return {
                ulid: tag.ulid,
                order: tag.order,
            };
        });
    }

    async function searchTags(searchQuery: string): Promise<Tag[]> {
        if (!searchQuery.trim()) {
            return [];
        }

        const excludeIds = selectedTags.map((tag) => tag.ulid);
        const params = new URLSearchParams({
            q: searchQuery,
        });

        excludeIds.forEach((id) => {
            params.append('exclude[]', id);
        });

        const response = await fetch(TagController.search.url({ query: { q: searchQuery, exclude: excludeIds } }), {
            signal: controller?.signal,
        });

        if (!response.ok) {
            throw new Error('Failed to search tags');
        }

        return response.json();
    }

    const handleSearch = (): void => {
        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }

        controller?.abort();
        error = null;

        if (!query.trim()) {
            searchResults = [];
            isLoading = false;
            newTagTitle = '';
            return;
        }

        isLoading = true;
        controller = new AbortController();

        // Set new debounced search
        debounceTimer = setTimeout(async () => {
            try {
                searchResults = await searchTags(query);
                if (
                    searchResults.every((tag) => tag.title.toLowerCase() !== query.toLowerCase()) &&
                    selectedTags.every((tag) => tag.title.toLowerCase() !== query.toLowerCase())
                ) {
                    newTagTitle = query;
                } else {
                    newTagTitle = '';
                }
            } catch (err) {
                error = err instanceof Error ? err.message : 'Failed to search tags';
                searchResults = [];
            } finally {
                isLoading = false;
            }
        }, 500);
    };

    function handleDragEnd(e: SortableList.RootEvents['ondragend']): void {
        const { draggedItem, draggedItemIndex, targetItemIndex, isCanceled } = e;
        draggedItem.classList.remove('opacity-40');
        if (!isCanceled && typeof targetItemIndex === 'number' && draggedItemIndex !== targetItemIndex) {
            selectedTags = sortItems(selectedTags, draggedItemIndex, targetItemIndex).map((tag, index) => ({
                ...tag,
                order: index,
            }));
        }
    }

    function handleDragStart(e: SortableList.RootEvents['ondragstart']): void {
        const { draggedItem } = e;
        draggedItem.classList.add('opacity-40');
    }

    function removeTag(ulid: string): void {
        const tagIndex = selectedTags.findIndex((tag) => tag.ulid === ulid);
        selectedTags = removeItem(selectedTags, tagIndex).map((tag, index) => ({
            ...tag,
            order: index,
        }));
    }

    function appendTag(tag: Tag): void {
        if (!selectedTags.find((t) => t.ulid === tag.ulid)) {
            selectedTags = [...selectedTags, { ...tag, order: selectedTags.length }];
        }
        query = '';
        searchResults = [];
        newTagTitle = '';
    }

    function appendNewTag(title: string): void {
        const slug = title.toLowerCase().replace(/\s+/g, '-');
        const newTag: Tag = {
            ulid: `new-tag-${slug}`,
            title,
            slug,
        };
        appendTag(newTag);
    }

    $effect(() => {
        onchange(convertTags(selectedTags));
        return () => {
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }
            controller?.abort();
        };
    });
</script>

<SortableList.Root ondragend={handleDragEnd} direction="horizontal" hasWrapping={true} ondragstart={handleDragStart}>
    {#each selectedTags.sort((a, b) => a.order - b.order) as tag (tag.ulid)}
        <SortableList.Item id={tag.ulid} index={tag.order}>
            <div
                class={cn(
                    'inline-flex items-center rounded-full border bg-muted px-3 py-1 text-xs font-medium text-muted-foreground transition-colors',
                    'dark:bg-muted dark:text-muted-foreground',
                )}
            >
                <span class="mr-2">{tag.title}</span>
                <button
                    type="button"
                    aria-label="Remove tag"
                    class={cn(
                        'flex h-4 w-4 items-center justify-center rounded-full hover:bg-accent focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none',
                        'transition-colors',
                    )}
                    onclick={() => removeTag(tag.ulid)}
                >
                    <X class="h-3 w-3" />
                </button>
            </div>
        </SortableList.Item>
    {/each}
</SortableList.Root>
<Command.Root class="rounded-lg border shadow-md md:min-w-[450px]" shouldFilter={false}>
    <Command.Input placeholder="Add a tag" bind:value={query} oninput={handleSearch} />
    <Command.List>
        {#if newTagTitle && !isLoading && !error}
            <Command.Item onclick={() => appendNewTag(newTagTitle)}>
                <span>Create new tag "{newTagTitle}"</span>
            </Command.Item>
        {/if}
        {#if isLoading}
            <Command.Empty>
                <div class="flex flex-col items-center gap-2">
                    <Spinner />
                </div>
            </Command.Empty>
        {:else if error}
            <Command.Empty>
                <div class="flex flex-col items-center gap-2">
                    <span>{error}</span>
                    <button type="button" onclick={handleSearch} class="text-xs text-primary hover:underline"> Try again </button>
                </div>
            </Command.Empty>
        {:else}
            {#each searchResults as tag (tag.ulid)}
                <Command.Item onclick={() => appendTag(tag)}>
                    <span>{tag.title}</span>
                </Command.Item>
            {/each}
        {/if}
    </Command.List>
</Command.Root>

<style>
    .ssl-ghost {
        opacity: 0.5;
    }
</style>
