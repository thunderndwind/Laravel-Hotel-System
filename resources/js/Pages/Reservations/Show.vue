<template>
  <AppLayout title="Reservation Details">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Reservation Details
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Status Banner -->
        <div class="mb-6">
          <div :class="{
            'p-4 rounded-md': true,
            'bg-green-50 text-green-800 border border-green-200': reservation.status === 'active',
            'bg-blue-50 text-blue-800 border border-blue-200': reservation.status === 'upcoming',
            'bg-gray-50 text-gray-800 border border-gray-200': reservation.status === 'completed',
            'bg-red-50 text-red-800 border border-red-200': reservation.status === 'cancelled'
          }">
            <div class="flex">
              <div class="flex-shrink-0">
                <CheckCircleIcon v-if="reservation.status === 'active'" class="h-5 w-5" />
                <ClockIcon v-else-if="reservation.status === 'upcoming'" class="h-5 w-5" />
                <BadgeCheckIcon v-else-if="reservation.status === 'completed'" class="h-5 w-5" />
                <XCircleIcon v-else-if="reservation.status === 'cancelled'" class="h-5 w-5" />
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium">
                  {{ statusMessage }}
                </h3>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <!-- Reservation Details -->
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- Left Column - Reservation Info -->
              <div class="col-span-2">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reservation Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm font-medium text-gray-500">Reservation ID</p>
                    <p class="mt-1 text-sm text-gray-900">#{{ reservation.id }}</p>
                  </div>
                  
                  <div>
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <p class="mt-1">
                      <span :class="{
                        'px-2 py-1 text-xs rounded-full font-medium': true,
                        'bg-green-100 text-green-800': reservation.status === 'active',
                        'bg-blue-100 text-blue-800': reservation.status === 'upcoming',
                        'bg-gray-100 text-gray-800': reservation.status === 'completed',
                        'bg-red-100 text-red-800': reservation.status === 'cancelled'
                      }">
                        {{ reservation.status.charAt(0).toUpperCase() + reservation.status.slice(1) }}
                      </span>
                    </p>
                  </div>
                  
                  <div>
                    <p class="text-sm font-medium text-gray-500">Check-in Date</p>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(reservation.check_in_date) }}</p>
                  </div>
                  
                  <div>
                    <p class="text-sm font-medium text-gray-500">Check-out Date</p>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(reservation.check_out_date) }}</p>
                  </div>
                  
                  <div>
                    <p class="text-sm font-medium text-gray-500">Duration</p>
                    <p class="mt-1 text-sm text-gray-900">{{ calculateDuration(reservation.check_in_date, reservation.check_out_date) }} nights</p>
                  </div>
                  
                  <div>
                    <p class="text-sm font-medium text-gray-500">Number of Guests</p>
                    <p class="mt-1 text-sm text-gray-900">{{ reservation.accompany_number }}</p>
                  </div>
                  
                  <div>
                    <p class="text-sm font-medium text-gray-500">Total Price</p>
                    <p class="mt-1 text-sm text-gray-900 font-bold">${{ reservation.paid_price }}</p>
                  </div>
                  
                  <div>
                    <p class="text-sm font-medium text-gray-500">Booked On</p>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(reservation.created_at) }}</p>
                  </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-8 flex flex-wrap gap-3" v-if="reservation.status === 'upcoming'">
                  <Link 
                    :href="route('reservations.edit', reservation.id)"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
                  >
                    <PencilIcon class="h-4 w-4 mr-1" />
                    Modify Reservation
                  </Link>
                  
                  <button 
                    @click="confirmCancellation()"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition"
                  >
                    <XCircleIcon class="h-4 w-4 mr-1" />
                    Cancel Reservation
                  </button>
                </div>
              </div>
              
              <!-- Right Column - Room Info -->
              <div class="border rounded-lg overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                  <h3 class="text-lg font-medium text-gray-900">Room Information</h3>
                </div>
                <div class="p-4">
                  <div class="mb-4">
                    <img 
                      v-if="reservation.room.image_url" 
                      :src="reservation.room.image_url" 
                      :alt="'Room ' + reservation.room.number"
                      class="w-full h-48 object-cover rounded"
                    />
                    <div v-else class="w-full h-48 bg-gray-200 rounded flex items-center justify-center">
                      <span class="text-gray-400">No image available</span>
                    </div>
                  </div>

                  <h4 class="text-md font-medium text-gray-900">Room {{ reservation.room.number }}</h4>
                  <p class="text-sm text-gray-600 mt-1">{{ reservation.room.type }}</p>
                  
                  <div class="mt-3 space-y-2">
                    <p class="text-sm"><span class="font-medium">Floor:</span> {{ reservation.room.floor }}</p>
                    <p class="text-sm"><span class="font-medium">Capacity:</span> {{ reservation.room.capacity }} guests</p>
                    <p class="text-sm"><span class="font-medium">Price:</span> ${{ reservation.room.price }} per night</p>
                  </div>
                  
                  <p class="mt-3 text-sm text-gray-600">{{ reservation.room.description }}</p>
                  
                  <div class="mt-4">
                    <Link 
                      :href="route('rooms.show', reservation.room.id)"
                      class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      View Room Details
                    </Link>
                  </div>
                </div>
              </div>
            </div>
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
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { CheckCircleIcon, ClockIcon, BadgeCheckIcon, XCircleIcon, PencilIcon } from '@heroicons/vue/outline';
import ToastService from '@/Services/ToastService';

// Props
const props = defineProps({
  reservation: Object
});

// State
const showCancellationModal = ref(false);
const processing = ref(false);

// Computed
const statusMessage = computed(() => {
  switch (props.reservation.status) {
    case 'active':
      return 'Your reservation is currently active.';
    case 'upcoming':
      return 'Your reservation is confirmed and upcoming.';
    case 'completed':
      return 'This reservation has been completed.';
    case 'cancelled':
      return 'This reservation has been cancelled.';
    default:
      return '';
  }
});

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

const calculateDuration = (checkInDate, checkOutDate) => {
  const checkIn = new Date(checkInDate);
  const checkOut = new Date(checkOutDate);
  return Math.round((checkOut - checkIn) / (1000 * 60 * 60 * 24));
};

const confirmCancellation = () => {
  showCancellationModal.value = true;
};

const cancelReservation = () => {
  processing.value = true;
  
  const toastId = ToastService.loading('Cancelling Reservation', 'Please wait...');
  
  router.delete(route('reservations.cancel', props.reservation.id), {}, {
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