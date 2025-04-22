<script setup>
import { Combobox as HeadlessCombobox } from "@headlessui/vue";
import { ref, provide } from "vue";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);
const query = ref("");

provide("combobox-query", query);

defineExpose({ query });
</script>

<template>
    <HeadlessCombobox
        :model-value="modelValue"
        @update:model-value="emit('update:modelValue', $event)"
        as="div"
        class="relative"
    >
        <slot :query="query" />
    </HeadlessCombobox>
</template>
