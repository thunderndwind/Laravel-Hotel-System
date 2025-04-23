<script setup>
import { inject } from "vue";

const props = defineProps({
    placeholder: {
        type: String,
        default: "Select an option",
    },
});

const { selectedValue } = inject("select");
</script>

<template>
    <span class="block truncate">
        <slot v-if="$slots.default">
            <slot />
        </slot>
        <template v-else-if="selectedValue">
            <slot name="selected">
                {{ selectedValue }}
            </slot>
        </template>
        <template v-else>{{ placeholder }}</template>
    </span>
</template>
