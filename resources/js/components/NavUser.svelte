<script lang="ts">
    import UserInfo from '@/components/UserInfo.svelte';
    import UserMenuContent from '@/components/UserMenuContent.svelte';
    import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
    import { SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
    import { page } from '@inertiajs/svelte';
    import { ChevronsUpDown } from 'lucide-svelte';
    import ToggleAppearance from './ToggleAppearance.svelte';

    const user = $derived($page.props.auth.user);
</script>

<SidebarMenu>
    <SidebarMenuItem>
        <div class="flex w-full items-center justify-between gap-2">
            <DropdownMenu>
                <DropdownMenuTrigger>
                    <SidebarMenuButton size="lg" class="group text-sidebar-accent-foreground data-[state=open]:bg-sidebar-accent">
                        <UserInfo {user} />
                        <ChevronsUpDown class="ml-auto size-4" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-lg" side="bottom" align="end" sideOffset={4}>
                    <UserMenuContent {user} />
                </DropdownMenuContent>
            </DropdownMenu>
            <ToggleAppearance />
        </div>
    </SidebarMenuItem>
</SidebarMenu>
