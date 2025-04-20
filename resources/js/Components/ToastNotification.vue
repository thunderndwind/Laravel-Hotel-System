<template>
  <div v-if="isVisible" class="toast-container fixed top-4 right-4 z-50 w-full max-w-md">
    <div 
      class="toast p-4 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out"
      :class="[
        type === 'success' ? 'bg-green-500 text-white' : '',
        type === 'error' ? 'bg-red-500 text-white' : '',
        type === 'loading' ? 'bg-indigo-600 text-white' : '',
        type === 'info' ? 'bg-blue-500 text-white' : '',
      ]"
    >
      <div class="flex items-center">
        <!-- Loading spinner -->
        <div v-if="type === 'loading'" class="mr-3">
          <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
        
        <!-- Success icon -->
        <div v-else-if="type === 'success'" class="mr-3">
          <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        
        <!-- Error icon -->
        <div v-else-if="type === 'error'" class="mr-3">
          <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
        
        <!-- Info icon -->
        <div v-else-if="type === 'info'" class="mr-3">
          <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        
        <div class="flex-1">
          <div class="font-medium">{{ title }}</div>
          <div v-if="message" class="text-sm opacity-90">{{ message }}</div>
        </div>
        
        <!-- Close button -->
        <button 
          v-if="!persistent && type !== 'loading'"
          @click="close"
          class="ml-4 text-white hover:text-gray-200"
        >
          <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'info', // 'success', 'error', 'loading', 'info'
    validator: (value) => ['success', 'error', 'loading', 'info'].includes(value)
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    default: ''
  },
  duration: {
    type: Number,
    default: 5000 // milliseconds
  },
  persistent: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close']);

const isVisible = ref(true);
let timeoutId = null;

const close = () => {
  isVisible.value = false;
  emit('close');
};

const setupAutoClose = () => {
  if (props.duration > 0 && !props.persistent && props.type !== 'loading') {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      close();
    }, props.duration);
  }
};

onMounted(() => {
  setupAutoClose();
});

onBeforeUnmount(() => {
  clearTimeout(timeoutId);
});

watch(() => props.type, (newType) => {
  if (newType !== 'loading') {
    setupAutoClose();
  }
});
</script>

<style scoped>
.toast-container {
  pointer-events: none;
}
.toast {
  pointer-events: auto;
}
</style> 