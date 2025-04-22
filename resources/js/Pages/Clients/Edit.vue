<template>
    <AppLayout>
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Client</h1>
  
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <!-- Name -->
            <div class="mb-4">
              <InputLabel for="name" value="Full Name" />
              <TextInput
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full"
                required
              />
              <InputError class="mt-2" :message="form.errors.name" />
            </div>
  
            <!-- Email -->
            <div class="mb-4">
              <InputLabel for="email" value="Email" />
              <TextInput
                id="email"
                v-model="form.email"
                type="email"
                class="mt-1 block w-full"
                required
              />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>
  
            <!-- Phone Number -->
            <div class="mb-4">
              <InputLabel for="phone_number" value="Phone Number" />
              <TextInput
                id="phone_number"
                v-model="form.phone_number"
                type="tel"
                class="mt-1 block w-full"
                required
              />
              <InputError class="mt-2" :message="form.errors.phone_number" />
            </div>
  
            <!-- Gender -->
            <div class="mb-4">
              <InputLabel value="Gender" />
              <div class="flex space-x-4 mt-2">
                <label class="inline-flex items-center">
                  <input
                    v-model="form.gender"
                    type="radio"
                    value="male"
                    class="text-indigo-600 focus:ring-indigo-500"
                    required
                  >
                  <span class="ml-2">Male</span>
                </label>
                <label class="inline-flex items-center">
                  <input
                    v-model="form.gender"
                    type="radio"
                    value="female"
                    class="text-indigo-600 focus:ring-indigo-500"
                  >
                  <span class="ml-2">Female</span>
                </label>
              </div>
              <InputError class="mt-2" :message="form.errors.gender" />
            </div>
  
            <!-- Country -->
            <div class="mb-4">
              <InputLabel for="country" value="Country" />
              <select
                id="country"
                v-model="form.country"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required
              >
                <option value="">Select a country</option>
                <option v-for="(name, code) in countries" :key="code" :value="code">
                  {{ name }}
                </option>
              </select>
              <InputError class="mt-2" :message="form.errors.country" />
            </div>
  
            <!-- Avatar -->
            <div class="mb-6">
              <InputLabel for="avatar_image" value="Profile Picture" />
              <input
                id="avatar_image"
                type="file"
                @input="form.avatar_image = $event.target.files[0]"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                accept="image/jpeg,image/jpg"
              >
              <div class="mt-2" v-if="client.avatar_image">
                <img 
                  :src="`/storage/${client.avatar_image}`" 
                  class="h-20 w-20 rounded-full"
                >
              </div>
              <InputError class="mt-2" :message="form.errors.avatar_image" />
            </div>
  
            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-4">
              <Link 
                :href="route('clients.index')"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Cancel
              </Link>
              <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save Changes
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import InputError from '@/Components/InputError.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import TextInput from '@/Components/TextInput.vue';
  import { Link, useForm } from '@inertiajs/vue3';
  
  const props = defineProps({
    client: Object,
    countries: Object,
    genders: Array
  });
  
  const form = useForm({
    name: props.client.user.name,
    email: props.client.user.email,
    phone_number: props.client.phone_number,
    gender: props.client.gender,
    country: props.client.country,
    avatar_image: null,
  });
  
  const submit = () => {
    form.put(route('clients.update', props.client.id), {
      forceFormData: true,
      preserveScroll: true,
    });
  };
  </script>