<script setup>
import { Combobox } from "@headlessui/vue";
import { CheckIcon } from "@heroicons/vue/20/solid";
import { cn } from "@/lib/utils";

defineProps({
    value: {
        type: [String, Number],
        required: true,
    },
    text: {
        type: String,
        required: true,
    },
    class: String,
});
</script>

<template>
    <Combobox.Option :value="value" v-slot="{ selected, active }" as="template">
        <li
            :class="
                cn(
                    'relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none',
                    active
                        ? 'bg-accent text-accent-foreground'
                        : 'text-popover-foreground',
                    props.class,
                )
            "
        >
            <span class="flex flex-1 items-center gap-2">
                <CheckIcon v-if="selected" class="h-4 w-4" />
                <span>{{ text }}</span>
            </span>
        </li>
    </Combobox.Option>
</template>
