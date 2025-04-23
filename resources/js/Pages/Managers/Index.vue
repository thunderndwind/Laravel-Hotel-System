<script setup>
import { Link, useForm } from '@inertiajs/vue3';

defineProps({ managers: Object });

const form = useForm({});
function destroy(id) {
  if (confirm('Are you sure?')) {
    form.delete(route('managers.destroy', id));
  }
}
</script>

<template>
  <div>
    <h1 class="text-xl font-bold mb-4">Managers List</h1>
    <Link :href="route('managers.create')" class="text-blue-500">Create New Manager </Link>

    <table class="mt-4 w-full">
      <thead>
        <tr>
          <th>Avatar</th>
          <th>Name</th>
          <th>Email</th>
          <th>National ID</th>
          <th>Phone</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="manager in managers.data" :key="manager.id">
          <td>
            <img
              :src="manager.avatar_image"
              alt="Avatar"
              class="w-12 h-12 rounded-full"
            />
          </td>
          <td>{{ manager.name }}</td>
          <td>{{ manager.email }}</td>
          <td>{{ manager.national_id }}</td>
          <td>{{ manager.phone_number }}</td>
          <td class="space-x-2">
            <Link :href="route('managers.edit', manager.id)" class="text-green-600">
                Edit
            </Link>
            <Link :href="route('managers.show', manager.id)" class="text-blue-600">Show</Link>
            
            <button @click="destroy(manager.id)" class="text-red-600">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>