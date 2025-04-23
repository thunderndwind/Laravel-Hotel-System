<script setup>
import { computed } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
  class: {
    type: String,
    default: ''
  }
});

const rowClass = computed(() => {
  return cn('border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted', props.class);
});
</script>

<template>
  <tr :class="rowClass">
    <slot></slot>
  </tr>
</template>