<script setup>
import { computed } from "vue";
import { cn } from "@/lib/utils";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    defaultChecked: {
        type: Boolean,
        default: false,
    },
    class: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);

const checked = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});
</script>

<template>
    <button
        type="button"
        role="switch"
        :aria-checked="checked"
        :data-state="checked ? 'checked' : 'unchecked'"
        :disabled="disabled"
        class="focus-visible:ring-ring focus-visible:ring-offset-background data-[state=checked]:bg-primary data-[state=unchecked]:bg-input relative inline-flex h-[24px] w-[44px] cursor-pointer items-center rounded-full border-2 border-transparent transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        :class="class"
        @click="checked = !checked"
    >
        <span
            :data-state="checked ? 'checked' : 'unchecked'"
            class="bg-background pointer-events-none block h-5 w-5 rounded-full shadow-lg ring-0 transition-transform data-[state=checked]:translate-x-5 data-[state=unchecked]:translate-x-0"
        />
    </button>
</template>
