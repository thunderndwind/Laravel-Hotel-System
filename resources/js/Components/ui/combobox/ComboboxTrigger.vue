<script setup>
import { ChevronUpDownIcon } from "@heroicons/vue/20/solid";
import { cn } from "@/lib/utils";

defineProps({
    class: String,
});
</script>

<template>
    <div class="relative w-full">
        <slot />
        <button
            type="button"
            class="absolute inset-y-0 right-0 flex items-center pr-2"
        >
            <ChevronUpDownIcon
                class="h-4 w-4 text-muted-foreground"
                aria-hidden="true"
            />
        </button>
    </div>
</template>
