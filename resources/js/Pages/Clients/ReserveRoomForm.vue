<template>
    <div class="container mt-5">
      <h2 class="mb-4">Reserve Room {{ room.number }}</h2>
  
      <form @submit.prevent="submit">
        <div class="form-group mb-3">
          <label>Check In Date</label>
          <input v-model="form.check_in_date" type="date" class="form-control" />
          <div v-if="form.errors.check_in_date" class="text-danger">{{ form.errors.check_in_date }}</div>
        </div>
  
        <div class="form-group mb-3">
          <label>Check Out Date</label>
          <input v-model="form.check_out_date" type="date" class="form-control" />
          <div v-if="form.errors.check_out_date" class="text-danger">{{ form.errors.check_out_date }}</div>
        </div>
  
        <div class="form-group mb-3">
          <label>Number of Accompanying Guests</label>
          <input v-model="form.accompany_number" type="number" min="0" class="form-control" />
          <div v-if="form.errors.accompany_number" class="text-danger">{{ form.errors.accompany_number }}</div>
        </div>
  
        <div class="form-group mb-3">
          <strong>Room Capacity:</strong> {{ room.capacity }}<br />
          <strong>Price per Night:</strong> ${{ room.price }}
        </div>
  
        <button type="submit" class="btn btn-primary">Confirm Reservation</button>
      </form>
    </div>
  </template>
  
  <script setup>
  import { useForm } from '@inertiajs/vue3'
  import { defineProps } from 'vue'
  
  const props = defineProps({
    room: Object,
  })
  
  const form = useForm({
    accompany_number: '',
    check_in_date: '',
    check_out_date: '',
  })
  
  const submit = () => {
    form.post(route('reservations.reserve.store', props.room.id))
  }
  </script>
  