<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ manager: Object });

const form = useForm({
  name: props.manager.name,
  email: props.manager.email,
  national_id: props.manager.national_id,
  phone_number: props.manager.phone_number,
  avatar_image: props.manager.avatar_image,
});

function submit() {
  form.patch(route('managers.update', props.manager.id));
}
</script>

<template>
  <div>
    <h1>Edit Manager</h1>
    <form @submit.prevent="submit">
      <input v-model="form.name" placeholder="Name" />
      <input v-model="form.email" type="email" placeholder="Email" />
      <input v-model="form.national_id" placeholder="National ID" />
      <input v-model="form.phone_number" placeholder="Phone Number" />
      <input v-model="form.avatar_image" placeholder="Avatar Image URL" />
      <button type="submit">Update</button>
    </form>

    <div v-if="form.errors" style="color: red;">
      <div v-for="(error, key) in form.errors" :key="key">{{ error }}</div>
    </div>
  </div>
</template>
