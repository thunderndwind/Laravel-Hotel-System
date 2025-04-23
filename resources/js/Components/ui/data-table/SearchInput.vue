<script setup>
import { ref, watch } from "vue";
import { Input } from "@/Components/ui/input";
import { Button } from "@/Components/ui/button";
import { MagnifyingGlassIcon, XMarkIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "Search...",
    },
});

const emit = defineEmits(["update:modelValue", "search"]);
const localValue = ref(props.modelValue);

// Watch for external changes
watch(
    () => props.modelValue,
    (newValue) => {
        if (newValue !== localValue.value) {
            localValue.value = newValue;
        }
    },
);

const handleSearch = () => {
    emit("search", localValue.value);
    emit("update:modelValue", localValue.value);
};

const clearSearch = () => {
    localValue.value = "";
    emit("search", "");
    emit("update:modelValue", "");
};
</script>

<template>
    <div class="relative w-full">
        <Input
            v-model="localValue"
            :placeholder="placeholder"
            class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-10 pr-10"
            @keyup.enter="handleSearch"
            :class="[
                '[&::-webkit-search-cancel-button]:hidden',
                '[&::-webkit-search-decoration]:hidden',
                '[&::-webkit-search-results-button]:hidden',
                '[&::-webkit-search-results-decoration]:hidden',
            ]"
        />
        <Button
            variant="ghost"
            size="sm"
            class="absolute left-2 top-1/2 -translate-y-1/2 h-8 w-8 p-0"
            @click="handleSearch"
        >
            <MagnifyingGlassIcon
                class="h-5 w-5 text-gray-400 hover:text-gray-600"
            />
        </Button>
        <Button
            v-if="localValue"
            variant="ghost"
            size="sm"
            class="absolute right-2 top-1/2 -translate-y-1/2 h-8 w-8 p-0"
            @click="clearSearch"
        >
            <XMarkIcon class="h-5 w-5 text-gray-500 hover:text-gray-700" />
        </Button>
    </div>
</template>
