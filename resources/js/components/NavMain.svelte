<script lang="ts">
    import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
    import type { Book, NavItem } from '@/types';
    import { Link, page } from '@inertiajs/svelte';
    import { show as bookShow } from '@/routes/book';

    const books: Book[] = $derived($page.props.books);
</script>

<SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Books</SidebarGroupLabel>
    <SidebarMenu>
        {#each books as book (book.ulid)}
            <SidebarMenuItem>
                <Link href={bookShow.url({ book: book.ulid, slug: book.slug })} class="block w-full">
                    <SidebarMenuButton isActive={false}>
                        {#snippet tooltipContent()}
                            {book.title}
                        {/snippet}
                        <span>{book.title}</span>
                    </SidebarMenuButton>
                </Link>
            </SidebarMenuItem>
        {/each}
    </SidebarMenu>
</SidebarGroup>
