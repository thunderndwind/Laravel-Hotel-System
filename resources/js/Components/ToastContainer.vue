<template>
  <div class="toast-container fixed top-4 right-4 z-50 space-y-2" aria-live="polite" aria-atomic="true">
    <ToastNotification
      v-for="toast in toasts"
      :key="toast.id"
      :type="toast.type"
      :title="toast.title"
      :message="toast.message"
      :duration="toast.duration"
      :persistent="toast.persistent"
      @close="removeToast(toast.id)"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import ToastNotification from './ToastNotification.vue';
import ToastService from '../Services/ToastService';

const toasts = ref([]);

const updateToasts = () => {
  toasts.value = [...ToastService.getToasts()];
};

const removeToast = (id) => {
  ToastService.remove(id);
  updateToasts();
};

// Watch for changes in toast collection
let interval;
onMounted(() => {
  // Poll for changes in toast collection (simple approach)
  interval = setInterval(() => {
    updateToasts();
  }, 100);
});

onUnmounted(() => {
  clearInterval(interval);
});
</script> 