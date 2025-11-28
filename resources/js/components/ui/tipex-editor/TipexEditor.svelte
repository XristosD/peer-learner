<script lang="ts">
	import { Tipex , type TipexEditor } from '@friendofsvelte/tipex';
	import '@friendofsvelte/tipex/styles/index.css';
	import type { Snippet } from 'svelte';
    import { Button } from '@/components/ui/button';
	import { Link } from '@tiptap/extension-link';
	import { Image } from '@tiptap/extension-image';
	import { Placeholder } from '@tiptap/extension-placeholder';
	import { CodeBlockLowlight } from '@tiptap/extension-code-block-lowlight';
	import { Underline } from '@tiptap/extension-underline';
	import { TaskList } from '@tiptap/extension-task-list';
	import { TaskItem } from '@tiptap/extension-task-item';
	import { lowlight } from 'lowlight';
	import Heading from '@tiptap/extension-heading'

	interface Props {
		body?: string;
		placeholder?: string;
		class?: string;
		disabled?: boolean;
		readonly?: boolean;
		floating?: boolean;
		focal?: boolean;
		onchange?: (content: string) => void;
		// controls?: Snippet<[editor: TipexEditor | null]>;
	}

	let {
		body = '',
		class: className = '',
		floating = false,
		focal = false,
		onchange,
		// controls,
	}: Props = $props();

	let editor = $state() as TipexEditor;

	function handleUpdate() {
		if (editor) {
			const html = editor.getHTML();
			onchange?.(html);
		}
	}
</script>

<Tipex
	bind:tipex={editor}
	extensions={[
		Link.configure({
			openOnClick: false,
			HTMLAttributes: {
				target: '_blank',
				rel: 'noopener noreferrer'
			}
		}),
		Image.configure({
			allowBase64: true
		}),
		Placeholder.configure({
			showOnlyWhenEditable: false
		}),
		CodeBlockLowlight.configure({
			lowlight,
			languageClassPrefix: 'language-',
			defaultLanguage: 'plaintext'
		}),
		Underline,
		TaskList,
		TaskItem.configure({
			nested: true
		}),
		Heading.configure({
			HTMLAttributes: {
				class: 'text-2xl font-normal',
			},
		})
	]}
	{body}
	{floating}
	{focal}
	onupdate={handleUpdate}
	class={className}
>
	{#snippet controlComponent(tipex)}
		{#if tipex}
		<div class="tipex-controller">
			<div class="tipex-basic-controller-wrapper">
				<!-- Text Styles Group -->
				<Button
					onclick={() => tipex?.chain().focus().toggleHeading({ level: 2 }).run()}
					class={`tipex-edit-button tipex-button-extra tipex-button-rigid ${tipex?.isActive('heading', { level: 2 }) ? 'active' : ''}`}
					aria-label="Heading"
					type="button"
					title="Heading"
				>
					<span class="font-semibold text-xs">H</span>
				</Button>
				<Button
					onclick={() => tipex?.chain().focus().toggleBold().run()}
					class={`tipex-edit-button tipex-button-extra tipex-button-rigid ${tipex?.isActive('bold') ? 'active' : ''}`}
					aria-label="Bold"
					type="button"
					title="Bold"
				>
					<span class="font-semibold text-xs">B</span>
				</Button>
			</div>
		</div>
		{/if}
	{/snippet}
</Tipex>


