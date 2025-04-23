<script setup>
defineProps({ manager: Object });
</script>

<template>
  <div>
    <h1>Manager Info</h1>
    <img
      :src="manager.avatar_image"
      alt="Avatar"
      class="w-32 h-32 rounded-full mb-4"
    />
    <h2 class="text-xl font-bold mb-2">Manager Details</h2>
    <p>Name: {{ manager.user.name }}</p>
    <p>Email: {{ manager.user.email }}</p>
    <p>National ID: {{ manager.national_id }}</p>
    <p>Phone: {{ manager.phone_number }}</p>
  </div>
</template>
