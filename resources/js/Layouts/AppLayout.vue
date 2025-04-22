<template>
  <div class="min-h-screen bg-gray-100">
    <Navigation />

    <!-- Page Heading -->
    <header class="bg-white shadow" v-if="$slots.header">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Role-based Content -->
    <main>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Different layouts based on role -->
          <template v-if="isAdmin">
            <AdminLayout>
              <slot />
            </AdminLayout>
          </template>

          <template v-if="isManager">
            <ManagerLayout>
              <slot />
            </ManagerLayout>
          </template>

          <template v-if="isReceptionist">
            <ReceptionistLayout>
              <slot />
            </ReceptionistLayout>
          </template>

          <template v-if="isClient">
            <ClientLayout>
              <slot />
            </ClientLayout>
          </template>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import Navigation from '@/Components/Navigation.vue'
import { useRole } from '@/Composables/useRole'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ManagerLayout from '@/Layouts/ManagerLayout.vue'
import ReceptionistLayout from '@/Layouts/ReceptionistLayout.vue'
import ClientLayout from '@/Layouts/ClientLayout.vue'

const { isAdmin, isManager, isReceptionist, isClient } = useRole()
</script>