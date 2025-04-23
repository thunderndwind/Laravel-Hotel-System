<script setup>
import { computed } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
  class: {
    type: String,
    default: ''
  }
});

const headClass = computed(() => {
  return cn('h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0', props.class);
});
</script>

<template>
  <th :class="headClass">
    <slot></slot>
  </th>
</template>