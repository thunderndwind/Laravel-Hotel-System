<script setup>
import { inject } from "vue";

defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
});

const { toggleOpen } = inject("select");
</script>

<template>
    <button
        type="button"
        class="flex w-full items-center justify-between rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:opacity-50"
        :disabled="disabled"
        @click="toggleOpen"
    >
        <slot />
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="h-4 w-4 opacity-50"
        >
            <polyline points="6 9 12 15 18 9" />
        </svg>
    </button>
</template>
