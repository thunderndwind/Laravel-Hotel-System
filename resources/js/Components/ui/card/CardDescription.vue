<script setup>
defineProps({
    className: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <p :class="['text-sm text-muted-foreground', className]">
        <slot />
    </p>
</template>
