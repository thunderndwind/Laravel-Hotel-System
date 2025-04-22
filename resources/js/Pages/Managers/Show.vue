<script setup>
defineProps({ manager: Object });
</script>

<template>
  <div>
    <h1>Manager Info</h1>
    <p>Name: {{ manager.user.name }}</p>
    <p>Email: {{ manager.user.email }}</p>
    <p>National ID: {{ manager.national_id }}</p>
    <p>Phone: {{ manager.phone_number }}</p>
  </div>
</template>
