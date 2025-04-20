<template>
    <div class="min-h-screen flex items-center justify-center relative p-4" style="background-image: url('/statics/8847e476-2174-410d-91b4-4ce06e902c2c.jpeg'); background-size: cover; background-position: center;">
      <!-- Background Overlay -->
      <div class="absolute inset-0 bg-black opacity-70"></div>
  
      <!-- Static Decorative Elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-20 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute bottom-20 right-20 w-60 h-60 bg-white/10 rounded-full"></div>
      </div>
  
      <!-- Main Card Container -->
      <div class="relative w-full max-w-2xl z-10 bg-white/70 backdrop-blur-md rounded-xl shadow-xl p-8 sm:p-12 animate-slideIn">
        <div class="grid grid-cols-1 gap-8">
          <!-- Right Section (Form) -->
          <div class="bg-white/90 rounded-xl shadow-lg p-8 sm:p-12">
            <div class="text-center mb-8">
              <h2 class="text-4xl font-extrabold text-teal-900 mb-4">Sign In</h2>
              <p class="text-gray-600 mb-6">Access your personalized dashboard</p>
            </div>
  
            <div v-if="status" class="mb-6 p-3 text-center text-sm font-medium text-green-800 bg-green-100/80 rounded-lg">
              {{ status }}
            </div>
  
            <form @submit.prevent="submit" class="space-y-5">
              <!-- Email -->
              <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Email</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <input v-model="form.email" type="email" placeholder="you@example.com" 
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all" required />
                </div>
                <p v-if="form.errors.email" class="text-sm text-rose-600 mt-1">{{ form.errors.email }}</p>
              </div>
  
              <!-- Password -->
              <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                  </div>
                  <input v-model="form.password" type="password" placeholder="••••••••" 
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all" required />
                </div>
                <p v-if="form.errors.password" class="text-sm text-rose-600 mt-1">{{ form.errors.password }}</p>
              </div>
  
              <!-- Remember & Forgot -->
              <div class="flex items-center justify-between">
                <label class="flex items-center">
                  <input type="checkbox" v-model="form.remember" 
                        class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded" />
                  <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
                <Link v-if="canResetPassword" :href="route('password.request')" 
                      class="text-sm text-teal-600 hover:text-teal-800 hover:underline transition-colors">
                  Forgot password?
                </Link>
              </div>
  
              <!-- Submit Button -->
              <button type="submit" :disabled="form.processing"
                      class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-700 hover:to-teal-600 text-white font-semibold rounded-lg shadow-md hover:shadow-teal-500/30 transition-all duration-300 flex items-center justify-center"
                      :class="{'opacity-70 cursor-not-allowed': form.processing}">
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ form.processing ? 'Signing in...' : 'Sign In' }}</span>
              </button>
  
              <!-- Divider -->
              <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                  <span class="px-2 bg-white text-gray-500">Or continue with</span>
                </div>
              </div>
  
              <!-- Social Login -->
              <div class="grid grid-cols-2 gap-3">
                <a href="#" class="flex items-center justify-center py-2.5 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.545 10.239v3.821h5.445c-.712 2.315-2.647 3.972-5.445 3.972-3.332 0-6.033-2.701-6.033-6.032s2.701-6.032 6.033-6.032c1.498 0 2.866.549 3.921 1.453l2.814-2.814c-1.784-1.664-4.13-2.675-6.735-2.675-5.523 0-10 4.477-10 10s4.477 10 10 10c8.396 0 10-7.524 10-10 0-.167-.007-.333-.02-.5h-9.98z"/>
                  </svg>
                  <span class="ml-2 text-sm font-medium">Google</span>
                </a>
                <a href="#" class="flex items-center justify-center py-2.5 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                  <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                  </svg>
                  <span class="ml-2 text-sm font-medium">Facebook</span>
                </a>
              </div>
  
              <!-- Register Link -->
              <p class="text-center text-sm text-gray-600 mt-6">
                Don't have an account? 
                <a href="/register" class="font-medium text-teal-600 hover:text-teal-800 hover:underline transition-colors">Sign up</a>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { useForm, Head, Link } from '@inertiajs/vue3'
  
  defineProps({
    canResetPassword: Boolean,
    status: String,
  })
  
  const form = useForm({
    email: '',
    password: '',
    remember: false,
  })
  
  function submit() {
    form.post('/login')
  }
  </script>
  
  <style scoped>
  /* Animations */
  @keyframes slideIn {
    0% {
      transform: translateY(50px);
      opacity: 0;
    }
    100% {
      transform: translateY(0);
      opacity: 1;
    }
  }
  
  .animate-slideIn {
    animation: slideIn 1s ease-out;
  }
  </style>
  