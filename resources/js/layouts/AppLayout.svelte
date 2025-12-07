<script lang="ts">
    import AppLayout from '@/layouts/app/AppSidebarLayout.svelte';
    import type { BreadcrumbItemType } from '@/types';
    import type { Snippet } from 'svelte';

    interface Props {
        breadcrumbs: BreadcrumbItemType[];
        headerAction?: Snippet<[]> | undefined;
        children?: Snippet;
    }

    let { breadcrumbs, headerAction, children }: Props = $props();
</script>

<AppLayout {breadcrumbs} {headerAction}>
    {@render children?.()}
</AppLayout>
