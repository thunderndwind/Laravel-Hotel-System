<template>
  <AppLayout title="Book a Reservation">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Book a Reservation
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div v-if="room" class="mb-6 p-4 bg-gray-50 rounded-lg">
              <h3 class="text-lg font-medium text-gray-900">Room {{ room.number }}</h3>
              <div class="mt-2 text-sm text-gray-600">
                <p><span class="font-medium">Type:</span> {{ room.type }}</p>
                <p><span class="font-medium">Capacity:</span> {{ room.capacity }} guests</p>
                <p><span class="font-medium">Price:</span> ${{ room.price }} per night</p>
                <p class="mt-2">{{ room.description }}</p>
              </div>
            </div>

            <form @submit.prevent="submitForm">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Room Selection (if no room is pre-selected) -->
                <div v-if="!room" class="col-span-2">
                  <Label for="room_id" value="Select a Room" />
                  <Select 
                    id="room_id"
                    v-model="form.room_id"
                    class="mt-1 block w-full" 
                    required
                  >
                    <option value="">Select a Room</option>
                    <option v-for="room in availableRooms" :key="room.id" :value="room.id">
                      Room {{ room.number }} - {{ room.type }} - ${{ room.price }} per night
                    </option>
                  </Select>
                  <InputError :message="form.errors.room_id" class="mt-2" />
                </div>

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
                    :max="room ? room.capacity : null"
                  />
                  <InputError :message="form.errors.accompany_number" class="mt-2" />
                  <p v-if="room" class="mt-1 text-sm text-gray-500">
                    Maximum capacity: {{ room.capacity }} guests
                  </p>
                </div>

                <!-- Summary -->
                <div v-if="canShowSummary" class="col-span-2 p-4 bg-gray-50 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900">Reservation Summary</h3>
                  <div class="mt-2 space-y-2">
                    <p><span class="font-medium">Check-in:</span> {{ formattedCheckInDate }}</p>
                    <p><span class="font-medium">Check-out:</span> {{ formattedCheckOutDate }}</p>
                    <p><span class="font-medium">Duration:</span> {{ duration }} nights</p>
                    <p><span class="font-medium">Guests:</span> {{ form.accompany_number }}</p>
                    <p v-if="room" class="text-lg font-medium mt-4">Total: ${{ totalPrice }}</p>
                  </div>
                </div>
              </div>

              <div class="flex items-center justify-end mt-6">
                <Button 
                  type="button" 
                  class="mr-3" 
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                  @click="goBack"
                >
                  Cancel
                </Button>
                <Button 
                  type="submit" 
                  class="ml-3" 
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  Book Now
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
import { ref, computed, onMounted } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/PrimaryButton.vue';
import Input from '@/Components/TextInput.vue';
import Select from '@/Components/Select.vue';
import Label from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import ToastService from '@/Services/ToastService';

// Props
const props = defineProps({
  room: Object,
  checkInDate: String,
  checkOutDate: String,
  availableRooms: {
    type: Array,
    default: () => []
  }
});

// Form state
const form = useForm({
  room_id: props.room ? props.room.id : '',
  check_in_date: props.checkInDate || '',
  check_out_date: props.checkOutDate || '',
  accompany_number: 1
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

const canShowSummary = computed(() => {
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

const totalPrice = computed(() => {
  if (!props.room || !duration.value) return 0;
  return props.room.price * duration.value;
});

// Methods
const submitForm = () => {
  const toastId = ToastService.loading('Creating Reservation', 'Please wait...');

  form.post(route('reservations.store'), {
    onSuccess: () => {
      ToastService.loadingToSuccess(toastId, 'Reservation Booked', 'Your reservation has been successfully booked.');
    },
    onError: () => {
      ToastService.loadingToError(toastId, 'Error', 'There was a problem booking your reservation.');
    }
  });
};

const goBack = () => {
  if (props.room) {
    router.visit(route('rooms.show', props.room.id));
  } else {
    router.visit(route('rooms.index'));
  }
};

// Check availability when dates change
const checkAvailability = () => {
  if (form.room_id && form.check_in_date && form.check_out_date) {
    // Call availability endpoint
    router.post(route('reservations.check-availability'), {
      room_id: form.room_id,
      check_in_date: form.check_in_date,
      check_out_date: form.check_out_date
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

// Watchers (setup manually since we can't use watch in script setup without additional imports)
onMounted(() => {
  form.reset('room_id', props.room ? props.room.id : '');
  form.reset('check_in_date', props.checkInDate || '');
  form.reset('check_out_date', props.checkOutDate || '');
});
</script> 