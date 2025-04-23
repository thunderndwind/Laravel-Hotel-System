<script setup>
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Search...'
  },
  minLength: {
    type: Number,
    default: 2
  },
  debounceMs: {
    type: Number,
    default: 300
  }
})

const emit = defineEmits(['update:modelValue'])
const localValue = ref(props.modelValue)

// Debounced emit for better performance
const debouncedEmit = debounce((value) => {
  const trimmedValue = value?.trim() || ''
  if (trimmedValue === '' || trimmedValue.length >= props.minLength) {
    emit('update:modelValue', trimmedValue)
  }
}, props.debounceMs)

// Watch for local changes
watch(localValue, (newValue) => {
  debouncedEmit(newValue)
})

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
  if (newValue !== localValue.value) {
    localValue.value = newValue
  }
})
</script>

<template>
  <div class="relative">
    <input
      v-model="localValue"
      type="search"
      :placeholder="placeholder"
      class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
    />
    <div
      v-if="localValue"
      class="absolute right-2 top-1/2 -translate-y-1/2"
    >
      <button
        class="rounded-full p-1 hover:bg-slate-100"
        @click="localValue = ''"
      >
        ✕
      </button>
    </div>
  </div>
</template>