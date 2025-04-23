<script setup>
defineProps({
    column: {
        type: String,
        required: true,
    },
    sortBy: {
        type: String,
        default: "",
    },
    onSort: {
        type: Function,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="flex items-center cursor-pointer" @click="onSort(column)">
        {{ label }}
        <span class="ml-1">
            {{ sortBy === column ? "↑" : sortBy === `-${column}` ? "↓" : "" }}
        </span>
    </div>
</template>
