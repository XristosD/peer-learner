<script lang="ts">
    import AppearanceTabs from '@/components/AppearanceTabs.svelte';
    import HeadingSmall from '@/components/HeadingSmall.svelte';
    import { type BreadcrumbItem } from '@/types';

    import AppLayout from '@/layouts/AppLayout.svelte';
    import SettingsLayout from '@/layouts/settings/Layout.svelte';
    import { setHeaderContext } from '@/lib/utils';

    setHeaderContext([
        [
            {
                title: 'Appearance settings',
                href: '/settings/appearance',
            },
        ], 
        null
    ]);
</script>

<svelte:head>
    <title>Appearance Settings</title>
</svelte:head>

<AppLayout>
    <SettingsLayout>
        <div class="space-y-6">
            <HeadingSmall title="Appearance settings" description="Update your account's appearance settings" />
            <AppearanceTabs />
        </div>
    </SettingsLayout>
</AppLayout>
