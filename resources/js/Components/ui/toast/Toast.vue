<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: "",
    },
    variant: {
        type: String,
        default: "default",
    },
});

const variants = {
    default: "bg-white",
    destructive: "bg-red-50",
};
</script>

<template>
    <div
        class="pointer-events-auto flex w-full items-center rounded-lg border p-4 shadow-lg transition-all"
        :class="[variants[variant]]"
    >
        <div class="flex w-full flex-col gap-1">
            <div class="text-sm font-semibold">{{ title }}</div>
            <div class="text-sm opacity-90">{{ description }}</div>
        </div>
    </div>
</template>
