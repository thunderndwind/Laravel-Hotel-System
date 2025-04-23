<script setup>
import { inject, computed } from "vue";

const props = defineProps({
    value: {
        type: [String, Number],
        required: true,
    },
});

const { selectedValue, setSelectedValue } = inject("select");

const isSelected = computed(() => selectedValue.value === props.value);
</script>

<template>
    <div
        class="relative cursor-pointer select-none py-2 pl-3 pr-9 hover:bg-indigo-100"
        :class="{ 'bg-indigo-50': isSelected }"
        @click="setSelectedValue(value)"
    >
        <slot />
        <span v-if="isSelected" class="absolute right-3 top-2 text-indigo-600">
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
                class="h-4 w-4"
            >
                <polyline points="20 6 9 17 4 12" />
            </svg>
        </span>
    </div>
</template>
