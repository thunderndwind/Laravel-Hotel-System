<script setup>
defineProps({
  message: {
    type: String,
    required: false
  }
});
</script>

<template>
  <div v-if="message" class="text-sm text-red-600">
    {{ message }}
  </div>
</template>
