<script setup>
import { computed } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
  class: {
    type: String,
    default: ''
  }
});

const captionClass = computed(() => {
  return cn('mt-4 text-sm text-muted-foreground', props.class);
});
</script>

<template>
  <caption :class="captionClass">
    <slot></slot>
  </caption>
</template>