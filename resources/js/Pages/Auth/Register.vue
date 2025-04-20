<template>
  <div>
    <!-- Blurred full-page background -->
    <div class="fixed inset-0 bg-cover bg-center z-0 blur-background"></div>
  
    <!-- Foreground Content -->
    <div class="relative min-h-screen flex items-center justify-center z-10">
      <div class="w-full max-w-4xl bg-white rounded-lg shadow-lg overflow-hidden grid grid-cols-1 md:grid-cols-2">
        
        <!-- Left Section (Image + Overlay) -->
        <div class="relative hidden md:block">
          <div class="absolute inset-0 bg-teal-700 bg-opacity-40 z-10"></div>
          <img
            src="/statics/8847e476-2174-410d-91b4-4ce06e902c2c.jpeg"
            alt="Background"
            class="h-full w-full object-cover"
          />
        </div>
  
        <!-- Right Section (Form) -->
        <div class="relative z-20 p-8 md:p-12">
          <h2 class="text-3xl font-bold text-gray-900 text-center mb-2">Welcome</h2>
          <p class="text-sm text-center text-gray-500 mb-6">Create a new account to continue</p>
  
          <form @submit.prevent="submit" class="space-y-4">
            <!-- Name -->
            <input v-model="form.name" type="text" placeholder="Full Name" class="form-input" required />
            <p v-if="form.errors.name" class="error-msg">{{ form.errors.name }}</p>
  
            <!-- Email -->
            <input v-model="form.email" type="email" placeholder="Email Address" class="form-input" required />
            <p v-if="form.errors.email" class="error-msg">{{ form.errors.email }}</p>
  
            <!-- Password -->
            <input v-model="form.password" type="password" placeholder="Password" class="form-input" required />
            <p v-if="form.errors.password" class="error-msg">{{ form.errors.password }}</p>
  
            <!-- Confirm Password -->
            <input v-model="form.password_confirmation" type="password" placeholder="Confirm Password" class="form-input" required />
  
            <!-- Avatar -->
            <input type="file" @input="form.avatar_image = $event.target.files[0]" class="form-input" />
            <p v-if="form.errors.avatar_image" class="error-msg">{{ form.errors.avatar_image }}</p>
  
            <!-- Phone -->
            <input v-model="form.phone_number" type="tel" placeholder="Phone Number" class="form-input" required />
            <p v-if="form.errors.phone_number" class="error-msg">{{ form.errors.phone_number }}</p>
  
            <!-- Gender -->
            <select v-model="form.gender" class="form-input" required>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
  
            <!-- Country -->
            <input v-model="form.country" type="text" placeholder="Country" class="form-input" required />
            <p v-if="form.errors.country" class="error-msg">{{ form.errors.country }}</p>
  
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 rounded-md transition"
              :class="{ 'opacity-50': form.processing }"
            >
              <span v-if="form.processing">Registering...</span>
              <span v-else>Register</span>
            </button>
  
            <p class="text-sm text-center text-gray-600 mt-2">
              Already have an account?
              <a href="/login" class="text-teal-500 hover:underline">Log in</a>
            </p>
  
            <div class="flex justify-center space-x-4 mt-4 text-gray-500">
              <i class="fab fa-facebook-f hover:text-teal-600 cursor-pointer"></i>
              <i class="fab fa-twitter hover:text-teal-600 cursor-pointer"></i>
              <i class="fab fa-linkedin-in hover:text-teal-600 cursor-pointer"></i>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
  
  <script setup>
  import { useForm } from '@inertiajs/vue3';
  
  const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    avatar_image: null,
    phone_number: '',
    gender: 'male',
    country: '',
  });
  
  const submit = () => {
    form.post('/register', {
      forceFormData: true,
      onSuccess: () => form.reset('password', 'password_confirmation'),
    });
  };
  </script>
  
  <style scoped>
  /* Blurred background image */
  .blur-background {
    background-image: url('/statics/8847e476-2174-410d-91b4-4ce06e902c2c.jpeg');
    filter: blur(10px);
    position: fixed;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    z-index: 0;
  }
  
  /* Form input styling */
  .form-input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border-color 0.3s;
  }
  .form-input:focus {
    border-color: #4f46e5;
    outline: none;
  }
  
  /* Error message styling */
  .error-msg {
    color: red;
    font-size: 0.875rem;
  }
  .error-msg::before {
    content: '⚠️ ';
  }
  .error-msg::after {
    content: ' ❗';
  }
  </style>
  