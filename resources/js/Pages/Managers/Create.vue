<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
  national_id: '',
  phone_number: '',
  avatar_image: '',
  password: '',
});

function submit() {
  form.post(route('managers.store'));
}
</script>

<template>
  <div>
    <h1>Create Manager</h1>
    <form @submit.prevent="submit">
      <input v-model="form.name" placeholder="Name" />
      <input v-model="form.email" type="email" placeholder="Email" />
      <input v-model="form.national_id" placeholder="National ID" />
      <input v-model="form.phone_number" placeholder="Phone Number" />
      <input v-model="form.avatar_image" placeholder="Avatar Image URL" />
      <input v-model="form.password" type="password" placeholder="Password" />
      <button type="submit">Create</button>
    </form>

    <div v-if="form.errors" style="color: red;">
      <div v-for="(error, key) in form.errors" :key="key">{{ error }}</div>
    </div>
  </div>
</template>
