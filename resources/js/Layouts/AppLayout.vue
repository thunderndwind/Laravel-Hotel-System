<template>
  <div class="min-h-screen bg-gray-100">
    <Navigation />
    
    <header v-if="$slots.header" class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <main>
      <slot />
    </main>
  </div>
</template>

<script setup>
</script>