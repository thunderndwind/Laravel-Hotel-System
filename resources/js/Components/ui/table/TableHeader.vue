<script setup>
import { computed } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
  class: {
    type: String,
    default: ''
  }
});

const headerClass = computed(() => {
  return cn('[&_tr]:border-b', props.class);
});
</script>

<template>
  <thead :class="headerClass">
    <slot></slot>
  </thead>
</template>