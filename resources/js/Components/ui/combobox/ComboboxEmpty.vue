<script setup>
import { cn } from "@/lib/utils";

defineProps({
    class: String,
});
</script>

<template>
    <div
        :class="
            cn(
                'relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm',
                'text-muted-foreground',
                props.class,
            )
        "
    >
        <slot>No results found.</slot>
    </div>
</template>
