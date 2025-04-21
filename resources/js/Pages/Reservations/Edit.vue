<template>
  <AppLayout title="Edit Reservation">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Reservation
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Room Info Summary -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
              <h3 class="text-lg font-medium text-gray-900">Room {{ reservation.room.number }}</h3>
              <div class="mt-2 text-sm text-gray-600">
                <p><span class="font-medium">Type:</span> {{ reservation.room.type }}</p>
                <p><span class="font-medium">Capacity:</span> {{ reservation.room.capacity }} guests</p>
                <p><span class="font-medium">Price:</span> ${{ reservation.room.price }} per night</p>
                <p class="mt-2">{{ reservation.room.description }}</p>
              </div>
            </div>

            <form @submit.prevent="updateReservation">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Check-in Date -->
                <div>
                  <Label for="check_in_date" value="Check-in Date" />
                  <Input
                    id="check_in_date"
                    type="date"
                    class="mt-1 block w-full"
                    v-model="form.check_in_date"
                    required
                    :min="minDate"
                  />
                  <InputError :message="form.errors.check_in_date" class="mt-2" />
                </div>

                <!-- Check-out Date -->
                <div>
                  <Label for="check_out_date" value="Check-out Date" />
                  <Input
                    id="check_out_date"
                    type="date"
                    class="mt-1 block w-full"
                    v-model="form.check_out_date"
                    required
                    :min="minCheckoutDate"
                  />
                  <InputError :message="form.errors.check_out_date" class="mt-2" />
                </div>

                <!-- Number of Guests -->
                <div class="col-span-2">
                  <Label for="accompany_number" value="Number of Guests" />
                  <Input
                    id="accompany_number"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="form.accompany_number"
                    required
                    min="1"
                    :max="reservation.room.capacity"
                  />
                  <InputError :message="form.errors.accompany_number" class="mt-2" />
                  <p class="mt-1 text-sm text-gray-500">
                    Maximum capacity: {{ reservation.room.capacity }} guests
                  </p>
                </div>

                <!-- Updated Summary -->
                <div v-if="isFormValid" class="col-span-2 p-4 bg-gray-50 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900">Updated Reservation Summary</h3>
                  <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <p class="text-sm text-gray-600">Check-in</p>
                      <p class="font-medium">{{ formattedCheckInDate }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-600">Check-out</p>
                      <p class="font-medium">{{ formattedCheckOutDate }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-600">Duration</p>
                      <p class="font-medium">{{ duration }} nights</p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-600">Guests</p>
                      <p class="font-medium">{{ form.accompany_number }}</p>
                    </div>
                    <div class="col-span-2">
                      <p class="text-sm text-gray-600">Updated Total</p>
                      <p class="text-lg font-bold">${{ updatedTotalPrice }}</p>
                      
                      <div v-if="updatedTotalPrice !== reservation.paid_price" class="mt-2 text-sm">
                        <span :class="updatedTotalPrice > reservation.paid_price ? 'text-red-600' : 'text-green-600'">
                          {{ updatedTotalPrice > reservation.paid_price ? '+' : '' }}${{ updatedTotalPrice - reservation.paid_price }}
                        </span> from original price of ${{ reservation.paid_price }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex items-center justify-end mt-6">
                <Link 
                  :href="route('reservations.show', reservation.id)" 
                  class="mr-3"
                >
                  Cancel
                </Link>
                <Button 
                  type="submit" 
                  class="ml-3" 
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  Update Reservation
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/PrimaryButton.vue';
import Input from '@/Components/TextInput.vue';
import Label from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import ToastService from '@/Services/ToastService';

// Props
const props = defineProps({
  reservation: Object
});

// Form state
const form = useForm({
  check_in_date: props.reservation.check_in_date,
  check_out_date: props.reservation.check_out_date,
  accompany_number: props.reservation.accompany_number
});

// Computed
const minDate = computed(() => {
  const today = new Date();
  return today.toISOString().split('T')[0];
});

const minCheckoutDate = computed(() => {
  if (!form.check_in_date) return minDate.value;
  
  const checkInDate = new Date(form.check_in_date);
  checkInDate.setDate(checkInDate.getDate() + 1);
  return checkInDate.toISOString().split('T')[0];
});

const isFormValid = computed(() => {
  return form.check_in_date && form.check_out_date && form.accompany_number;
});

const formattedCheckInDate = computed(() => {
  if (!form.check_in_date) return '';
  return new Date(form.check_in_date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});

const formattedCheckOutDate = computed(() => {
  if (!form.check_out_date) return '';
  return new Date(form.check_out_date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});

const duration = computed(() => {
  if (!form.check_in_date || !form.check_out_date) return 0;
  
  const checkIn = new Date(form.check_in_date);
  const checkOut = new Date(form.check_out_date);
  
  // Calculate the difference in days
  return Math.round((checkOut - checkIn) / (1000 * 60 * 60 * 24));
});

const updatedTotalPrice = computed(() => {
  if (!duration.value) return 0;
  return props.reservation.room.price * duration.value;
});

// Methods
const updateReservation = () => {
  const toastId = ToastService.loading('Updating Reservation', 'Please wait...');
  
  form.put(route('reservations.update', props.reservation.id), {
    onSuccess: () => {
      ToastService.loadingToSuccess(toastId, 'Reservation Updated', 'Your reservation has been successfully updated.');
    },
    onError: () => {
      ToastService.loadingToError(toastId, 'Error', 'There was a problem updating your reservation.');
    }
  });
};

// Check availability when dates change
const checkAvailability = () => {
  if (form.check_in_date && form.check_out_date && 
      (form.check_in_date !== props.reservation.check_in_date || 
       form.check_out_date !== props.reservation.check_out_date)) {
    // Call availability endpoint
    router.post(route('reservations.check-availability'), {
      room_id: props.reservation.room.id,
      check_in_date: form.check_in_date,
      check_out_date: form.check_out_date,
      exclude_reservation_id: props.reservation.id
    }, {
      preserveState: true,
      onSuccess: (page) => {
        if (!page.props.available) {
          form.setError('check_in_date', 'The room is not available for the selected dates');
          form.setError('check_out_date', 'The room is not available for the selected dates');
        }
      }
    });
  }
};
</script> 