<script lang="ts">
    import AppLayout from '@/layouts/app/AppSidebarLayout.svelte';
    import type { BreadcrumbItemType } from '@/types';
    import type { Snippet } from 'svelte';

    interface Props {
        children?: Snippet;
    }

    let {  children }: Props = $props();
</script>

<AppLayout>
    {@render children?.()}
</AppLayout>
