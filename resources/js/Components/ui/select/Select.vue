<script setup>
import { ref, provide, watch } from "vue";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);

const isOpen = ref(false);
const selectedValue = ref(props.modelValue);

// Watch for external changes to modelValue
watch(
    () => props.modelValue,
    (newValue) => {
        selectedValue.value = newValue;
    },
);

provide("select", {
    isOpen,
    selectedValue,
    setSelectedValue: (value) => {
        selectedValue.value = value;
        emit("update:modelValue", value);
        isOpen.value = false;
    },
    toggleOpen: () => (isOpen.value = !isOpen.value),
});
</script>

<template>
    <div class="relative">
        <slot />
    </div>
</template>
