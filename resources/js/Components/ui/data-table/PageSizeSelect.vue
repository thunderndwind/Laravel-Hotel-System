<script setup>
const props = defineProps({
    modelValue: {
        type: Number,
        required: true,
    },
    options: {
        type: Array,
        default: () => [10, 25, 50, 100],
    },
});

const emit = defineEmits(["update:modelValue"]);
</script>

<template>
    <select
        :value="modelValue"
        @change="$emit('update:modelValue', Number($event.target.value))"
        class="h-9 w-16 rounded-md border border-input bg-transparent px-2 py-1 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
    >
        <option v-for="size in options" :key="size" :value="size">
            {{ size }}
        </option>
    </select>
</template>
