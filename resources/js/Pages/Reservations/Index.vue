<template>
  <AppLayout title="My Reservations">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        My Reservations
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <!-- Filter Bar -->
          <div class="p-6 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-wrap items-center gap-4">
              <div>
                <select 
                  v-model="filters.status" 
                  @change="filterReservations"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                >
                  <option value="">All Reservations</option>
                  <option value="upcoming">Upcoming</option>
                  <option value="active">Active</option>
                  <option value="completed">Completed</option>
                </select>
              </div>
              <Link 
                v-if="canCreateReservation" 
                :href="route('rooms.index')" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
              >
                Book a Room
              </Link>
            </div>
          </div>
          
          <!-- No Reservations Message -->
          <div v-if="reservations.data.length === 0" class="p-6 text-center text-gray-500">
            <p class="mb-4">You don't have any reservations yet.</p>
            <Link 
              v-if="canCreateReservation" 
              :href="route('rooms.index')" 
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
            >
              Book a Room
            </Link>
          </div>
          
          <!-- Reservations List -->
          <div v-else class="divide-y divide-gray-200">
            <div v-for="reservation in reservations.data" :key="reservation.id" class="p-6 hover:bg-gray-50">
              <div class="flex flex-col md:flex-row justify-between">
                <div class="mb-4 md:mb-0">
                  <div class="flex items-center">
                    <div :class="{
                      'px-2 py-1 text-xs rounded-full font-medium': true,
                      'bg-green-100 text-green-800': reservation.status === 'active',
                      'bg-blue-100 text-blue-800': reservation.status === 'upcoming',
                      'bg-gray-100 text-gray-800': reservation.status === 'completed',
                      'bg-red-100 text-red-800': reservation.status === 'cancelled'
                    }">
                      {{ reservation.status.charAt(0).toUpperCase() + reservation.status.slice(1) }}
                    </div>
                    <h3 class="ml-2 text-lg font-medium text-gray-900">Room {{ reservation.room.number }}</h3>
                  </div>
                  <div class="mt-2 flex items-center text-sm text-gray-500">
                    <CalendarIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                    {{ formatDate(reservation.check_in_date) }} - {{ formatDate(reservation.check_out_date) }}
                  </div>
                  <div class="mt-2 flex items-center text-sm text-gray-500">
                    <UserGroupIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                    {{ reservation.accompany_number }} {{ reservation.accompany_number === 1 ? 'guest' : 'guests' }}
                  </div>
                </div>
                <div class="flex flex-col space-y-2">
                  <div class="text-lg font-medium text-gray-900">
                    ${{ reservation.paid_price }}
                  </div>
                  <div class="flex">
                    <Link 
                      :href="route('reservations.show', reservation.id)" 
                      class="inline-flex items-center px-3 py-2 mr-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <EyeIcon class="h-4 w-4 mr-1" />
                      View
                    </Link>
                    <Link 
                      v-if="reservation.status === 'upcoming'" 
                      :href="route('reservations.edit', reservation.id)" 
                      class="inline-flex items-center px-3 py-2 mr-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <PencilIcon class="h-4 w-4 mr-1" />
                      Edit
                    </Link>
                    <button 
                      v-if="reservation.status === 'upcoming'" 
                      @click="confirmCancellation(reservation)"
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                      <XCircleIcon class="h-4 w-4 mr-1" />
                      Cancel
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Pagination -->
          <div v-if="reservations.data.length > 0" class="p-6 bg-white border-t border-gray-200">
            <Pagination :links="reservations.links" />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Cancellation Modal -->
    <Modal :show="showCancellationModal" @close="showCancellationModal = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Cancel Reservation</h2>
        <p class="mt-1 text-sm text-gray-600">
          Are you sure you want to cancel this reservation? This action cannot be undone.
        </p>
        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="showCancellationModal = false" class="mr-2">
            No, Keep It
          </SecondaryButton>
          <DangerButton @click="cancelReservation" :class="{ 'opacity-25': processing }" :disabled="processing">
            Yes, Cancel Reservation
          </DangerButton>
        </div>
      </div>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { CalendarIcon, UserGroupIcon, EyeIcon, PencilIcon, XCircleIcon } from '@heroicons/vue/outline';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ToastService from '@/Services/ToastService';

// Props
const props = defineProps({
  reservations: Object,
  filters: Object,
  canCreateReservation: {
    type: Boolean,
    default: true
  }
});

// State
const filters = ref({
  status: props.filters.status || ''
});

const showCancellationModal = ref(false);
const reservationToCancel = ref(null);
const processing = ref(false);

// Methods
const filterReservations = () => {
  router.get(route('reservations.index'), { status: filters.value.status }, {
    preserveState: true,
    replace: true
  });
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const confirmCancellation = (reservation) => {
  reservationToCancel.value = reservation;
  showCancellationModal.value = true;
};

const cancelReservation = () => {
  if (!reservationToCancel.value) return;
  
  processing.value = true;
  
  const toastId = ToastService.loading('Cancelling Reservation', 'Please wait...');
  
  router.delete(route('reservations.cancel', reservationToCancel.value.id), {}, {
    onSuccess: () => {
      ToastService.loadingToSuccess(toastId, 'Reservation Cancelled', 'Your reservation has been successfully cancelled.');
      showCancellationModal.value = false;
      processing.value = false;
    },
    onError: () => {
      ToastService.loadingToError(toastId, 'Error', 'There was a problem cancelling your reservation.');
      processing.value = false;
    }
  });
};
</script> 