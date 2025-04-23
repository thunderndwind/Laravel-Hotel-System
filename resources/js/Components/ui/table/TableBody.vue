<script setup>
import { computed } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
  class: {
    type: String,
    default: ''
  }
});

const bodyClass = computed(() => {
  return cn('[&_tr:last-child]:border-0', props.class);
});
</script>

<template>
  <tbody :class="bodyClass">
    <slot></slot>
  </tbody>
</template>