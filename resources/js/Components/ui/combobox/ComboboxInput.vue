<script setup>
import { Combobox } from "@headlessui/vue";
import { cn } from "@/lib/utils";
import { inject } from "vue";

const props = defineProps({
    placeholder: String,
    class: String,
    displayValue: {
        type: Function,
        required: true,
    },
    modelValue: [String, Number],
});

const emit = defineEmits(["update:modelValue"]);
const query = inject("combobox-query");
</script>

<template>
    <div class="relative w-full">
        <input
            type="text"
            :value="displayValue(modelValue)"
            @input="$emit('update:modelValue', $event.target.value)"
            :class="
                cn(
                    'w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background',
                    'placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring',
                    'focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                    props.class,
                )
            "
            :placeholder="placeholder"
        />
    </div>
</template>
