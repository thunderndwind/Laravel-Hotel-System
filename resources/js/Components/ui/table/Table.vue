<script setup>
import { computed } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
  class: {
    type: String,
    default: ''
  }
});

const tableClass = computed(() => {
  return cn('w-full caption-bottom text-sm', props.class);
});
</script>

<template>
  <div class="w-full overflow-auto">
    <table :class="tableClass">
      <slot></slot>
    </table>
  </div>
</template>