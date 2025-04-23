<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap -mb-1">
      <template v-for="(link, key) in links" :key="key">
        <div v-if="link.url === null" 
             class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
             v-html="link.label" />
        <Link v-else
              :class="{'bg-indigo-500 text-white': link.active}"
              class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-gray-100 focus:border-indigo-500 focus:text-indigo-500"
              :href="link.url"
              v-html="link.label" />
      </template>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: Array
});
</script> 