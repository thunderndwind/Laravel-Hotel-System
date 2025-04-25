<template>
    <AuthenticatedLayout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Make a New Reservation
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <div v-if="availableRooms.length > 0">
                <div class="mb-4">
                  <h3 class="text-lg font-medium">Available Rooms</h3>
                  <p class="text-sm text-gray-600">
                    Select a room to make a reservation
                  </p>
                </div>
  
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Room Number
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Floor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Capacity
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Price/Night
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Actions
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="room in availableRooms" :key="room.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                          {{ room.number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          {{ room.floor_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          {{ room.capacity }} guests
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          ${{ (room.price / 100).toFixed(2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <button
                            @click="openReservationForm(room)"
                            class="text-indigo-600 hover:text-indigo-900"
                          >
                            Make Reservation
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
  
              <div v-else class="text-center py-8">
                <p class="text-gray-500">No available rooms at the moment.</p>
              </div>
  
              <!-- Reservation Form Modal -->
              <DialogModal :show="showReservationForm" @close="closeReservationForm">
                <template #title>
                  Make Reservation for Room #{{ selectedRoom?.number }}
                </template>
  
                <template #content>
                  <div class="grid grid-cols-1 gap-4">
                    <div>
                      <InputLabel for="check_in_date" value="Check-in Date" />
                      <TextInput
                        id="check_in_date"
                        v-model="form.check_in_date"
                        type="date"
                        class="mt-1 block w-full"
                        :min="today"
                        required
                      />
                      <InputError class="mt-2" :message="form.errors.check_in_date" />
                    </div>
  
                    <div>
                      <InputLabel for="check_out_date" value="Check-out Date" />
                      <TextInput
                        id="check_out_date"
                        v-model="form.check_out_date"
                        type="date"
                        class="mt-1 block w-full"
                        :min="form.check_in_date || today"
                        required
                      />
                      <InputError class="mt-2" :message="form.errors.check_out_date" />
                    </div>
  
                    <div>
                      <InputLabel for="accompany_number" value="Number of Guests (excluding you)" />
                      <TextInput
                        id="accompany_number"
                        v-model="form.accompany_number"
                        type="number"
                        class="mt-1 block w-full"
                        min="0"
                        :max="selectedRoom?.capacity - 1 || 0"
                        required
                      />
                      <InputError class="mt-2" :message="form.errors.accompany_number" />
                    </div>
  
                    <div class="mt-4">
                      <p class="text-sm font-medium text-gray-700">
                        Total Price: ${{ calculateTotalPrice() }}
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ calculateNights() }} night(s) × ${{ (selectedRoom?.price / 100).toFixed(2) }}/night
                      </p>
                    </div>
                  </div>
                </template>
  
                <template #footer>
                  <SecondaryButton @click="closeReservationForm">
                    Cancel
                  </SecondaryButton>
  
                  <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submitReservation"
                  >
                    Confirm Reservation
                  </PrimaryButton>
                </template>
              </DialogModal>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import DialogModal from '@/Components/DialogModal.vue';
  import InputError from '@/Components/InputError.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import TextInput from '@/Components/TextInput.vue';
  import { ref, computed } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  
  const props = defineProps({
    availableRooms: {
      type: Array,
      required: true,
    },
  });
  
  const today = new Date().toISOString().split('T')[0];
  const showReservationForm = ref(false);
  const selectedRoom = ref(null);
  
  const form = useForm({
    room_id: null,
    check_in_date: today,
    check_out_date: '',
    accompany_number: 0,
  });
  
  const openReservationForm = (room) => {
    selectedRoom.value = room;
    form.room_id = room.id;
    form.check_in_date = today;
    form.check_out_date = '';
    form.accompany_number = 0;
    showReservationForm.value = true;
  };
  
  const closeReservationForm = () => {
    showReservationForm.value = false;
  };
  
  const calculateNights = () => {
    if (!form.check_in_date || !form.check_out_date) return 0;
    const checkIn = new Date(form.check_in_date);
    const checkOut = new Date(form.check_out_date);
    return Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
  };
  
  const calculateTotalPrice = () => {
    if (!selectedRoom.value || !form.check_in_date || !form.check_out_date) return '0.00';
    const nights = calculateNights();
    return ((nights * selectedRoom.value.price) / 100).toFixed(2);
  };
  
  const submitReservation = () => {
    form.post(route('reservations.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeReservationForm();
      },
    });
  };
  </script>