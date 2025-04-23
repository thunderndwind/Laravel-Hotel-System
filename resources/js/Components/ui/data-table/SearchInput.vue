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
    <div class="flex items-center gap-2 w-full">
        <Button
            variant="ghost"
            size="sm"
            class="h-10 w-10 shrink-0 flex items-center justify-center hover:bg-transparent focus:ring-0 focus:ring-offset-0"
            @click="handleSearch"
        >
            <MagnifyingGlassIcon
                class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors"
            />
        </Button>

        <div class="relative flex-1">
            <Input
                v-model="localValue"
                :placeholder="placeholder"
                class="h-10 w-full rounded-md border border-input bg-background text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pr-10"
                @keyup.enter="handleSearch"
                :class="[
                    '[&::-webkit-search-cancel-button]:hidden',
                    '[&::-webkit-search-decoration]:hidden',
                    '[&::-webkit-search-results-button]:hidden',
                    '[&::-webkit-search-results-decoration]:hidden',
                ]"
            />
            <Button
                v-if="localValue"
                variant="ghost"
                size="sm"
                class="absolute right-2 top-1/2 -translate-y-1/2 h-8 w-8 p-0 hover:bg-transparent focus:ring-0 focus:ring-offset-0"
                @click="clearSearch"
            >
                <XMarkIcon
                    class="h-5 w-5 text-gray-500 hover:text-gray-700 transition-colors"
                />
            </Button>
        </div>
    </div>
</template>
