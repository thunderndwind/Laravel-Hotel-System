<script setup>
import { computed } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
  class: {
    type: String,
    default: ''
  }
});

const cellClass = computed(() => {
  return cn('p-4 align-middle [&:has([role=checkbox])]:pr-0', props.class);
});
</script>

<template>
  <td :class="cellClass">
    <slot></slot>
  </td>
</template>