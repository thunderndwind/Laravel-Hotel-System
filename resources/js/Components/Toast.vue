<template>
  <div class="fixed inset-0 flex flex-col items-end justify-start p-4 pointer-events-none space-y-4 z-50">
    <transition-group name="toast">
      <div 
        v-for="toast in toasts" 
        :key="toast.id" 
        class="toast-notification pointer-events-auto"
        :class="[
          toast.type === 'success' ? 'bg-green-50 border-green-500 text-green-800' : '',
          toast.type === 'error' ? 'bg-red-50 border-red-500 text-red-800' : '',
          toast.type === 'info' ? 'bg-blue-50 border-blue-500 text-blue-800' : '',
          toast.type === 'loading' ? 'bg-gray-50 border-gray-500 text-gray-800' : '',
        ]"
      >
        <div class="flex items-start">
          <!-- Icon based on type -->
          <div class="flex-shrink-0 mr-3 mt-0.5">
            <div v-if="toast.type === 'success'" class="h-6 w-6 text-green-500">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div v-if="toast.type === 'error'" class="h-6 w-6 text-red-500">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </div>
            <div v-if="toast.type === 'info'" class="h-6 w-6 text-blue-500">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div v-if="toast.type === 'loading'" class="h-6 w-6 text-gray-500">
              <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
          </div>

          <!-- Content -->
          <div class="flex-grow">
            <p class="font-medium">{{ toast.title }}</p>
            <p v-if="toast.message" class="text-sm">{{ toast.message }}</p>
          </div>

          <!-- Close button (unless loading) -->
          <div class="ml-4 flex-shrink-0 flex" v-if="toast.type !== 'loading'">
            <button
              @click="closeToast(toast.id)"
              class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
            >
              <span class="sr-only">Close</span>
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import ToastService from '@/Services/ToastService';

const toasts = ref([]);

const updateToasts = () => {
  toasts.value = ToastService.getToasts();
};

const closeToast = (id) => {
  ToastService.remove(id);
};

// Update toasts initially
onMounted(() => {
  updateToasts();
  
  // Set up an interval to check for new toasts
  const interval = setInterval(updateToasts, 100);
  
  // Clean up interval on unmount
  onUnmounted(() => {
    clearInterval(interval);
  });
});
</script>

<style scoped>
.toast-notification {
  width: 350px;
  margin-bottom: 0.5rem;
  padding: 1rem;
  border-radius: 0.375rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  border-left-width: 4px;
}

.toast-enter-active, .toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.toast-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style> 