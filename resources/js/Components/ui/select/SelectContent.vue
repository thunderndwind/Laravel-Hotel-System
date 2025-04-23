<script setup>
import { inject } from "vue";

const { isOpen } = inject("select");
</script>

<template>
    <div
        v-if="isOpen"
        class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
    >
        <slot />
    </div>
</template>
