<script setup>
defineProps({
  htmlFor: {
    type: String,
    required: false
  },
  value: {
    type: String,
    required: false
  }
})
</script>

<template>
  <label :for="htmlFor" class="block text-sm font-medium text-gray-700">
    <span v-if="value">{{ value }}</span>
    <span v-else><slot /></span>
  </label>
</template>