<template>
    <AppLayout>
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-gray-900">Client Details</h1>
          <div class="space-x-2">
            <Link 
              :href="route('clients.edit', client.id)"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              v-if="can.update"
            >
              Edit
            </Link>
          </div>
        </div>
  
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:px-6 flex items-center space-x-4">
            <img 
              :src="client.avatar_image ? `/storage/${client.avatar_image}` : '/images/default-avatar.png'"
              class="h-16 w-16 rounded-full"
            >
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ client.user.name }}
              </h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ client.user.email }}
              </p>
            </div>
          </div>
          <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ client.phone_number }}
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Gender</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ client.gender }}
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Country</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ client.country }}
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  <span 
                    :class="{
                      'bg-green-100 text-green-800': client.approved_at,
                      'bg-yellow-100 text-yellow-800': !client.approved_at
                    }"
                    class="px-2 py-1 text-xs font-semibold rounded-full"
                  >
                    {{ client.approved_at ? 'Approved' : 'Pending Approval' }}
                  </span>
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" v-if="client.approved_at">
                <dt class="text-sm font-medium text-gray-500">Approved By</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ client.approver?.name || 'System' }}
                </dd>
              </div>
            </dl>
          </div>
        </div>
  
        <!-- Reservations Section -->
        <div class="mt-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Reservations</h2>
          <Link 
            :href="route('clients.reservations', client.id)"
            class="text-indigo-600 hover:text-indigo-900"
          >
            View all reservations →
          </Link>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { Link } from '@inertiajs/vue3';
  
  const props = defineProps({
    client: Object,
    can: Object
  });
  </script>