<script lang="ts">
    import NavFooter from '@/components/NavFooter.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
    import { dashboard } from '@/routes';
    import { type NavItem } from '@/types';
    import { Link } from '@inertiajs/svelte';
    import { BookOpen, Folder, LayoutGrid } from 'lucide-svelte';
    import AppLogo from './AppLogo.svelte';
    import ToggleAppearance from './ToggleAppearance.svelte';

    const sidebar = useSidebar();

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
    ];

    const footerNavItems: NavItem[] = [
        {
            title: 'Repository',
            href: 'https://github.com/oseughu/svelte-starter-kit',
            icon: Folder,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs/starter-kits',
            icon: BookOpen,
        },
    ];
</script>

<Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg">
                    <Link href={dashboard()}>
                        <AppLogo />
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
        <NavMain {sidebar} />
    </SidebarContent>

    <SidebarFooter>
        <!-- <NavFooter items={footerNavItems} class="mt-auto" /> -->
        <NavUser />
    </SidebarFooter>
</Sidebar>
