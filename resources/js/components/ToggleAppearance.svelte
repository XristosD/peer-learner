<script lang='ts'>
    import type { Appearance } from '@/hooks/useAppearance.svelte';
    import { useAppearance } from '@/hooks/useAppearance.svelte';
    import { SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
    import { Button } from '@/components/ui/button';
    import { MoonIcon, SunIcon } from 'lucide-svelte';

    const appearanceManager = useAppearance();
</script>


<Button onclick={appearanceManager.toggleAppearance} variant="outline" size="icon">
    <SunIcon
        class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 !transition-all dark:-rotate-90 dark:scale-0"
    />
    <MoonIcon
        class="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 !transition-all dark:rotate-0 dark:scale-100"
    />
    <span class="sr-only">Toggle theme</span>
</Button>
