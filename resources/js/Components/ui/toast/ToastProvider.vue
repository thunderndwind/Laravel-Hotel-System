<script setup>
import { useToast } from "../use-toast";
import Toast from "./Toast.vue";

const { toasts } = useToast();
</script>

<template>
    <div
        class="fixed top-0 right-0 z-50 flex w-full max-w-sm flex-col gap-2 p-4"
    >
        <Toast
            v-for="toast in toasts"
            :key="toast.id"
            :title="toast.title"
            :description="toast.description"
            :variant="toast.variant"
        />
    </div>
</template>
