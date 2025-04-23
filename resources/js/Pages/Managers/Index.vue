<script setup lang="ts">
import { h, defineProps } from 'vue'
import type { ColumnDef } from '@tanstack/vue-table'
import { Link } from '@inertiajs/vue3'
import ManagersTable from '@/Pages/Managers/ManagersTable.vue'
import { useForm } from '@inertiajs/vue3'

defineProps({ managers: Object })

const form = useForm({})
function destroy(id: number) {
  if (confirm('Are you sure?')) {
    form.delete(route('managers.destroy', id))
  }
}

// Define table columns
const columns: ColumnDef<any, any>[] = [
  {
    header: 'Avatar',
    cell: ({ row }) =>
      h('img', {
        src: row.original.avatar_image,
        class: 'w-12 h-12 rounded-full',
      }),
  },
  {
    accessorKey: 'name',
    header: 'Name',
  },
  {
    accessorKey: 'email',
    header: 'Email',
  },
  {
    accessorKey: 'national_id',
    header: 'National ID',
  },
  {
    accessorKey: 'phone_number',
    header: 'Phone',
  },
  {
    header: 'Actions',
    cell: ({ row }) =>
      h('div', { class: 'space-x-2' }, [
        h(
          Link,
          {
            href: route('managers.edit', row.original.id),
            class: 'text-green-600',
          },
          'Edit'
        ),
        h(
          Link,
          {
            href: route('managers.show', row.original.id),
            class: 'text-blue-600',
          },
          'Show'
        ),
        h(
          'button',
          {
            class: 'text-red-600',
            onClick: () => destroy(row.original.id),
          },
          'Delete'
        ),
      ]),
  },
]
</script>

<template>
  
  <div>


    <h1 class="text-xl font-bold mb-4">Managers List</h1>



    <Link :href="route('managers.create')" class="text-blue-500">Create New Manager </Link>


    <div class="mt-4">
      <ManagersTable :columns="columns" :data="managers.data" />
    </div>

    <!-- <table class="mt-4 w-full">
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
    </table> -->
  </div>
</template>