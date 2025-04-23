<template>
  <AuthenticatedLayout title="Reservation Details">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Reservation Details
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <!-- Reservation Status Banner -->
          <div class="border-b border-gray-200" 
               :class="{
                 'bg-green-50': reservation.status === 'active',
                 'bg-blue-50': reservation.status === 'upcoming',
                 'bg-gray-50': reservation.status === 'completed'
               }">
            <div class="p-6 flex items-center justify-between">
              <div class="flex items-center">
                <div :class="{
                  'px-3 py-1 text-sm rounded-full font-medium mr-3': true,
                  'bg-green-100 text-green-800': reservation.status === 'active',
                  'bg-blue-100 text-blue-800': reservation.status === 'upcoming',
                  'bg-gray-100 text-gray-800': reservation.status === 'completed'
                }">
                  {{ reservation.status.charAt(0).toUpperCase() + reservation.status.slice(1) }}
                </div>
                <h3 class="text-xl font-medium">
                  Reservation #{{ reservation.id }}
                </h3>
              </div>
              <div class="flex">
                <Link 
                  v-if="reservation.can_edit"
                  :href="route('reservations.edit', reservation.id)" 
                  class="inline-flex items-center px-4 py-2 mr-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50"
                >
                  Edit
                </Link>
                <button 
                  v-if="reservation.can_delete"
                  @click="confirmCancellation"
                  class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>
          
          <div class="p-6">
            <!-- Reservation Details -->
            <div class="mb-8">
              <h4 class="text-lg font-medium text-gray-900 mb-4">Reservation Information</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-600">Booking Date</p>
                  <p class="font-medium">{{ formatDateTime(reservation.created_at) }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Last Updated</p>
                  <p class="font-medium">{{ formatDateTime(reservation.updated_at) }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Check-in Date</p>
                  <p class="font-medium">{{ formatDate(reservation.check_in_date) }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Check-out Date</p>
                  <p class="font-medium">{{ formatDate(reservation.check_out_date) }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Duration</p>
                  <p class="font-medium">{{ reservation.duration }} nights</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Number of Guests</p>
                  <p class="font-medium">{{ reservation.total_guests }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Total Price</p>
                  <p class="font-medium text-lg">${{ reservation.paid_price }}</p>
                </div>
              </div>
            </div>
            
            <!-- Room Details -->
            <div class="mb-8">
              <h4 class="text-lg font-medium text-gray-900 mb-4">Room Information</h4>
              <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center justify-between mb-4">
                  <h5 class="text-lg font-medium">Room {{ reservation.room.number }}</h5>
                  <Link 
                    :href="route('rooms.show', reservation.room.id)"
                    class="text-sm text-indigo-600 hover:text-indigo-500"
                  >
                    View Room Details
                  </Link>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm text-gray-600">Type</p>
                    <p class="font-medium">{{ reservation.room.type || 'Standard' }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Floor</p>
                    <p class="font-medium">{{ reservation.room.floor }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Capacity</p>
                    <p class="font-medium">{{ reservation.room.capacity }} guests</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Price Per Night</p>
                    <p class="font-medium">${{ reservation.room.price }}</p>
                  </div>
                </div>
                <div class="mt-4">
                  <p class="text-sm text-gray-600">Description</p>
                  <p class="mt-1">{{ reservation.room.description || 'No description available' }}</p>
                </div>
              </div>
            </div>
            
            <!-- Guest Information -->
            <div>
              <h4 class="text-lg font-medium text-gray-900 mb-4">Guest Information</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-600">Name</p>
                  <p class="font-medium">{{ reservation.user.name }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Email</p>
                  <p class="font-medium">{{ reservation.user.email }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between">
              <Link 
                :href="route('reservations.index')" 
                class="text-sm text-gray-600 hover:text-gray-900"
              >
                Back to Reservations
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Cancellation Confirmation Modal -->
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
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ToastService from '@/Services/ToastService';

// Props
const props = defineProps({
  reservation: {
    type: Object,
    required: true
  }
});

// State
const showCancellationModal = ref(false);
const processing = ref(false);

// Methods
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    weekday: 'long',
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
};

const formatDateTime = (dateTimeString) => {
  const date = new Date(dateTimeString);
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const confirmCancellation = () => {
  showCancellationModal.value = true;
};

const cancelReservation = () => {
  processing.value = true;
  
  const toastId = ToastService.loading('Cancelling Reservation', 'Please wait...');
  
  router.delete(route('reservations.destroy', props.reservation.id), {}, {
    onSuccess: () => {
      ToastService.loadingToSuccess(toastId, 'Reservation Cancelled', 'Your reservation has been successfully cancelled.');
      router.visit(route('reservations.index'));
    },
    onError: () => {
      ToastService.loadingToError(toastId, 'Error', 'There was a problem cancelling your reservation.');
      processing.value = false;
      showCancellationModal.value = false;
    }
  });
};
</script> 