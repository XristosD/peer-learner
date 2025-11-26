---
applyTo: "resources/js/**/*.svelte, resources/js/**/*.svelte.ts, resources/js/**/*.ts"
---

# Svelte 5, shadcn-svelte & TypeScript Instructions

## Critical: Use Svelte MCP Server First
- **ALWAYS** use the Svelte MCP server tools (`mcp_svelte_*`) before writing any Svelte code.
- Call `mcp_svelte_list-sections` first to see available documentation sections and their use cases.
- Analyze the `use_cases` field to determine which sections are relevant for the user's query.
- Use `mcp_svelte_get-documentation` to fetch ALL relevant sections at once before implementing.
- Use `mcp_svelte_svelte-autofixer` to validate your Svelte code before returning it to the user.
- This ensures you're using correct Svelte 5 syntax and following best practices.

## Svelte 5 Core Principles

### Runes System (Svelte 5)
- **ALWAYS** use Svelte 5 runes syntax - this project uses Svelte 5.
- Use `$state()` for reactive state, not `let` variables.
- Use `$derived()` for computed values, not `$:` reactive statements.
- Use `$effect()` for side effects, not `$:` statements.
- Use `$props()` for component props with full TypeScript support.
- Use `$bindable()` for two-way binding with parent components.
- Use `@render` snippets instead of slots for content projection.

<code-snippet name="Svelte 5 Runes Example" lang="svelte">
<script lang="ts">
    interface Props {
        initialCount?: number;
        class?: string;
    }

    let { initialCount = 0, class: className }: Props = $props();
    
    // Reactive state
    let count = $state(initialCount);
    
    // Derived/computed value
    let doubled = $derived(count * 2);
    
    // Side effects
    $effect(() => {
        console.log('Count changed:', count);
    });
    
    function increment() {
        count++;
    }
</script>

<button onclick={increment} class={className}>
    Count: {count} (doubled: {doubled})
</button>
</code-snippet>

### Snippets Over Slots
- Use `{@render children?.()}` instead of `<slot />`.
- Define reusable snippets with `{#snippet name(params)}...{/snippet}`.
- Pass snippets as props for flexible component composition.

<code-snippet name="Snippets Example" lang="svelte">
<script lang="ts">
    import type { Snippet } from 'svelte';
    
    interface Props {
        header?: Snippet;
        children?: Snippet;
        footer?: Snippet<[{ closeModal: () => void }]>;
    }
    
    let { header, children, footer }: Props = $props();
    
    function closeModal() {
        console.log('Closing...');
    }
</script>

<div class="modal">
    {#if header}
        <div class="header">
            {@render header()}
        </div>
    {/if}
    
    <div class="content">
        {@render children?.()}
    </div>
    
    {#if footer}
        <div class="footer">
            {@render footer({ closeModal })}
        </div>
    {/if}
</div>
</code-snippet>

### Event Handling (Svelte 5)
- Use lowercase event handlers: `onclick`, `onsubmit`, `onchange`, etc.
- No more `on:click` - use `onclick={handler}` instead.
- For custom events, pass callbacks as props.

<code-snippet name="Event Handlers" lang="svelte">
<script lang="ts">
    interface Props {
        onSave?: (data: string) => void;
    }
    
    let { onSave }: Props = $props();
    let value = $state('');
    
    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        onSave?.(value);
    }
</script>

<form onsubmit={handleSubmit}>
    <input type="text" bind:value />
    <button type="submit">Save</button>
</form>
</code-snippet>

### TypeScript Best Practices

#### Component Props Type Safety
- Always define a `Props` interface for component properties.
- Use TypeScript's utility types when appropriate (`Partial`, `Pick`, `Omit`).
- Leverage `svelte/elements` types for HTML attributes.
- Use `Snippet` type from 'svelte' for render props.

<code-snippet name="Type-Safe Props" lang="svelte">
<script lang="ts">
    import type { HTMLButtonAttributes } from 'svelte/elements';
    import type { Snippet } from 'svelte';
    
    interface Props extends HTMLButtonAttributes {
        variant?: 'primary' | 'secondary' | 'ghost';
        size?: 'sm' | 'md' | 'lg';
        loading?: boolean;
        icon?: Snippet;
        children?: Snippet;
    }
    
    let { 
        variant = 'primary', 
        size = 'md',
        loading = false,
        icon,
        children,
        class: className,
        disabled,
        ...restProps 
    }: Props = $props();
</script>
</code-snippet>

#### Module-Level Types and Functions
- Use `<script lang="ts" module>` for types, utilities, and constants shared across instances.
- Define component-level variants, helper functions, and exported types here.
- This code runs once when the module loads, not per component instance.

<code-snippet name="Module Script" lang="svelte">
<script lang="ts" module>
    import { tv, type VariantProps } from 'tailwind-variants';
    
    export const cardVariants = tv({
        base: 'rounded-lg border bg-card text-card-foreground shadow-sm',
        variants: {
            padding: {
                none: '',
                sm: 'p-4',
                md: 'p-6',
                lg: 'p-8',
            },
        },
        defaultVariants: {
            padding: 'md',
        },
    });
    
    export type CardVariant = VariantProps<typeof cardVariants>;
    export type CardPadding = CardVariant['padding'];
</script>

<script lang="ts">
    interface Props {
        padding?: CardPadding;
        class?: string;
    }
    
    let { padding, class: className }: Props = $props();
</script>
</code-snippet>

## shadcn-svelte Integration

### Component Structure
- Follow the existing shadcn-svelte component patterns in `resources/js/components/ui/`.
- Use `bits-ui` for headless component primitives.
- Use `tailwind-variants` (tv) for variant-based styling.
- Use the `cn()` utility from `@/lib/utils.js` for className merging.

### Creating shadcn Components
- Check existing components before creating new ones.
- Follow the two-script pattern: module script for exports, instance script for props.
- Export variants using `tailwind-variants` from module script.
- Support all native HTML attributes using spread props `{...restProps}`.
- Use `data-slot` attributes for styling hooks.

<code-snippet name="shadcn Component Pattern" lang="svelte">
<script lang="ts" module>
    import { cn } from '@/lib/utils.js';
    import { tv, type VariantProps } from 'tailwind-variants';
    import type { HTMLAttributes } from 'svelte/elements';
    
    export const alertVariants = tv({
        base: 'relative w-full rounded-lg border p-4',
        variants: {
            variant: {
                default: 'bg-background text-foreground',
                destructive: 'border-destructive/50 text-destructive',
            },
        },
        defaultVariants: {
            variant: 'default',
        },
    });
    
    export type AlertVariant = VariantProps<typeof alertVariants>['variant'];
    export type AlertProps = HTMLAttributes<HTMLDivElement> & {
        variant?: AlertVariant;
    };
</script>

<script lang="ts">
    let {
        class: className,
        variant = 'default',
        children,
        ...restProps
    }: AlertProps = $props();
</script>

<div
    data-slot="alert"
    role="alert"
    class={cn(alertVariants({ variant }), className)}
    {...restProps}
>
    {@render children?.()}
</div>
</code-snippet>

### Form Handling with Formsnap
- Use `formsnap` for form state management with Inertia forms.
- Leverage type-safe field validation and error handling.
- Check existing form implementations before creating new patterns.

### Icon Usage
- The project has a custom `Icon` component for `lucide-svelte` icons.
- Use `<Icon name="iconName" />` for consistent icon rendering.
- Icons automatically receive proper sizing and styling.

<code-snippet name="Icon Component Usage" lang="svelte">
<script lang="ts">
    import Icon from '@/components/Icon.svelte';
</script>

<button>
    <Icon name="save" />
    Save Changes
</button>
</code-snippet>

## Component Organization

### File Structure
- Pages go in `resources/js/pages/` (e.g., `Dashboard.svelte`).
- Layouts go in `resources/js/layouts/` (e.g., `AppLayout.svelte`).
- Reusable components go in `resources/js/components/`.
- Feature-specific components can be grouped in subdirectories (e.g., `components/book/`).
- shadcn-svelte UI components go in `resources/js/components/ui/`.

### Naming Conventions
- Use PascalCase for component filenames: `UserProfile.svelte`, `BookCard.svelte`.
- Use kebab-case for multi-word props: `error-message`, `is-loading`.
- Use descriptive names that indicate component purpose.

### Import Aliases
- Use `@/` alias for imports from `resources/js/`: `import Button from '@/components/ui/button/button.svelte'`.
- This is configured in `tsconfig.json` and should always be used.

## Styling Guidelines

### Tailwind CSS v4
- Use Tailwind utility classes for all styling (this project uses Tailwind v4).
- Follow dark mode patterns: use `dark:` prefix for dark mode variants.
- Use the `cn()` utility to merge className props with component classes.
- Leverage Tailwind arbitrary values when needed: `w-[123px]`, `bg-[#1da1f2]`.

### Class Merging Pattern
<code-snippet name="Proper Class Merging" lang="svelte">
<script lang="ts">
    import { cn } from '@/lib/utils.js';
    
    interface Props {
        class?: string;
        variant?: 'default' | 'compact';
    }
    
    let { class: className, variant = 'default' }: Props = $props();
    
    const baseClasses = 'rounded-lg border p-4';
    const variantClasses = variant === 'compact' ? 'p-2' : 'p-4';
</script>

<div class={cn(baseClasses, variantClasses, className)}>
    <!-- Content -->
</div>
</code-snippet>

### Responsive Design
- Mobile-first approach: base styles are mobile, use `md:`, `lg:` for larger screens.
- Use Tailwind's responsive prefixes consistently.
- Test components at multiple breakpoints.

## Performance & Best Practices

### State Management
- Keep state as local as possible - lift only when necessary.
- Use `$derived()` for computed values to avoid unnecessary recalculation.
- Avoid deeply nested reactive state - flatten when possible.

### Effect Cleanup
- Always clean up side effects in `$effect()` by returning a cleanup function.
- Use `$effect.pre()` for effects that need to run before DOM updates.

<code-snippet name="Effect Cleanup" lang="svelte">
<script lang="ts">
    let count = $state(0);
    
    $effect(() => {
        const interval = setInterval(() => {
            count++;
        }, 1000);
        
        // Cleanup function
        return () => {
            clearInterval(interval);
        };
    });
</script>
</code-snippet>

### Component Composition
- Prefer composition over inheritance.
- Use snippets for flexible content projection.
- Create small, focused components that do one thing well.
- Reuse existing components before creating new ones.

### Avoid Anti-Patterns
- **Don't** use Svelte 4 syntax (`$:`, `on:click`, `<slot />`).
- **Don't** mutate props directly - use `$bindable()` for two-way binding.
- **Don't** mix reactive statements with runes.
- **Don't** forget to validate Svelte code with `mcp_svelte_svelte-autofixer`.

## Accessibility

### Semantic HTML
- Use proper semantic HTML elements: `<button>`, `<nav>`, `<main>`, `<article>`, etc.
- Add ARIA attributes when semantic HTML isn't sufficient: `role`, `aria-label`, `aria-describedby`.
- Ensure keyboard navigation works properly.

### Focus Management
- Manage focus for modals, dialogs, and dropdowns.
- Provide visible focus indicators (handled by Tailwind's `focus-visible:` utilities).
- Use `aria-disabled` for disabled states when appropriate.

### Screen Reader Support
- Provide meaningful alt text for images.
- Use `aria-live` regions for dynamic content updates.
- Ensure error messages are associated with form fields.

## Testing Considerations

### Component Testability
- Accept callbacks as props for testable event handling.
- Avoid tight coupling to external services.
- Use dependency injection for services when needed.
- Write components that are easy to test in isolation.

## Integration with Laravel & Inertia

### Page Components
- Receive props from Laravel controllers via Inertia.
- Define proper TypeScript interfaces for page props.
- Use `<svelte:head>` for page-specific meta tags and titles.

<code-snippet name="Inertia Page Component" lang="svelte">
<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    
    interface Props {
        users: Array<{ id: number; name: string; email: string }>;
        total: number;
    }
    
    let { users, total }: Props = $props();
    
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Users', href: '/users' },
    ];
</script>

<svelte:head>
    <title>Users - MyApp</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <!-- Page content -->
</AppLayout>
</code-snippet>

### Layouts
- Wrap page content with layouts for consistent structure.
- Pass layout-specific props like breadcrumbs, page titles.
- Use snippets for flexible layout sections.

## Code Quality

### Before Finalizing Code
1. **ALWAYS** run `mcp_svelte_svelte-autofixer` on your Svelte components.
2. Ensure all TypeScript types are properly defined.
3. Check that event handlers use Svelte 5 syntax (lowercase).
4. Verify runes are used correctly (`$state`, `$derived`, `$effect`, `$props`).
5. Confirm snippets are used instead of slots.
6. Test dark mode if applicable.

### Documentation
- Add JSDoc comments for complex component props or logic.
- Document non-obvious behavior or workarounds.
- Keep comments concise and meaningful.

### Consistency
- Follow existing patterns in the codebase.
- Check sibling components for naming and structure conventions.
- Maintain consistent prop ordering and grouping.
- Use the same styling patterns as existing components.

## Common Patterns in This Project

### Breadcrumbs
- Define breadcrumbs array with `BreadcrumbItem[]` type.
- Pass to `AppLayout` via `{breadcrumbs}` prop.

### Color Scheme Toggle
- The project uses `mode-watcher` for dark mode.
- Components should support dark mode via Tailwind's `dark:` prefix.

### Loading States
- Use skeleton components from shadcn-svelte for loading states.
- Provide meaningful loading indicators for async operations.

### Error Handling
- Display validation errors using form error components.
- Use toast notifications for user feedback (svelte-sonner).

## Quick Reference

### Migration from Svelte 4 to Svelte 5
| Svelte 4 | Svelte 5 |
|----------|----------|
| `let count = 0` | `let count = $state(0)` |
| `$: doubled = count * 2` | `let doubled = $derived(count * 2)` |
| `$: { console.log(count) }` | `$effect(() => { console.log(count) })` |
| `export let prop` | `let { prop }: Props = $props()` |
| `on:click={handler}` | `onclick={handler}` |
| `<slot />` | `{@render children?.()}` |
| `<slot name="header" />` | `{@render header?.()}` |

### Essential Imports
```typescript
// Svelte core
import { type Snippet } from 'svelte';
import type { HTMLButtonAttributes, HTMLInputAttributes } from 'svelte/elements';

// Utilities
import { cn } from '@/lib/utils.js';
import { tv, type VariantProps } from 'tailwind-variants';

// Components
import Button from '@/components/ui/button/button.svelte';
import Icon from '@/components/Icon.svelte';
```

