<script setup>
import { computed } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
  class: {
    type: String,
    default: ''
  }
});

const footerClass = computed(() => {
  return cn('bg-primary font-medium text-primary-foreground', props.class);
});
</script>

<template>
  <tfoot :class="footerClass">
    <slot></slot>
  </tfoot>
</template>