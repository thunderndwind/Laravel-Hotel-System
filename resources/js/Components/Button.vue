<script setup>
defineProps({
  type: {
    type: String,
    default: 'submit',
  },
  variant: {
    type: String,
    default: 'primary',
  }
});
</script>

<template>
  <button
    :type="type"
    :class="[
      'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150',
      variant === 'primary' ? 'bg-gray-800 border-transparent text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:ring-indigo-500' : 'border-gray-300 text-gray-700 bg-white hover:text-gray-500 focus:border-indigo-500 active:text-gray-800 focus:ring-indigo-500'
    ]"
  >
    <slot />
  </button>
</template>