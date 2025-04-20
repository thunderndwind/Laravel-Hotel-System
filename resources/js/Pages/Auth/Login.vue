<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-teal-900 to-teal-700 p-4 relative overflow-hidden">
      <!-- Floating Bubbles Background -->
      <div class="absolute inset-0 overflow-hidden">
        <div v-for="i in 12" :key="i" 
             class="absolute rounded-full bg-white/10"
             :style="{
               width: `${Math.random() * 10 + 5}rem`,
               height: `${Math.random() * 10 + 5}rem`,
               left: `${Math.random() * 100}%`,
               top: `${Math.random() * 100}%`,
               animation: `float ${Math.random() * 15 + 10}s linear infinite`,
               animationDelay: `${Math.random() * 5}s`
             }"></div>
      </div>
  
      <!-- Main Card -->
      <div class="relative w-full max-w-6xl bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-2 z-10 transform transition-all duration-500 hover:scale-[1.01]">
        <!-- Left Section (Visual) -->
        <div class="relative hidden lg:block min-h-[500px]">
          <!-- Gradient Overlay -->
          <div class="absolute inset-0 bg-gradient-to-t from-teal-900/80 to-teal-700/60 z-10"></div>
          
          <!-- Background Image -->
          <img src="/statics/8847e476-2174-410d-91b4-4ce06e902c2c.jpeg" 
               alt="Decorative background"
               class="absolute inset-0 h-full w-full object-cover" />
          
          <!-- Animated Text -->
          <div class="absolute bottom-10 left-10 right-10 z-20 text-white">
            <h3 class="text-3xl font-bold mb-3 animate-fadeIn">Welcome Back</h3>
            <p class="text-lg animate-fadeIn delay-100">
              "The expert in anything was once a beginner."
            </p>
            <div class="mt-6 w-20 h-1 bg-teal-400 animate-growWidth"></div>
          </div>
        </div>
  
        <!-- Right Section (Form) -->
        <div class="p-8 sm:p-12">
          <!-- Logo/Title -->
          <div class="text-center mb-8">
            <h2 class="text-4xl font-extrabold text-teal-900 mb-2 animate-fadeIn">Sign In</h2>
            <p class="text-gray-600 animate-fadeIn delay-75">Access your personalized dashboard</p>
          </div>
  
          <!-- Status Message -->
          <div v-if="status" class="mb-6 p-3 text-center text-sm font-medium text-green-800 bg-green-100/80 rounded-lg animate-fadeIn">
            {{ status }}
          </div>
  
          <form @submit.prevent="submit" class="space-y-5 animate-fadeIn delay-150">
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
  
  const submit = () => {
    form.post(route('login'), {
      onFinish: () => form.reset('password'),
    })
  }
  </script>
  
  <style scoped>
  /* Floating bubbles animation */
  @keyframes float {
    0% {
      transform: translateY(0) rotate(0deg);
    }
    50% {
      transform: translateY(-20px) rotate(5deg);
    }
    100% {
      transform: translateY(0) rotate(0deg);
    }
  }
  
  /* Fade-in animation */
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .animate-fadeIn {
    animation: fadeIn 0.6s ease-out forwards;
  }
  
  .delay-75 { animation-delay: 75ms; }
  .delay-100 { animation-delay: 100ms; }
  .delay-150 { animation-delay: 150ms; }
  
  /* Growing width animation */
  @keyframes growWidth {
    from { width: 0; }
    to { width: 5rem; }
  }
  
  .animate-growWidth {
    animation: growWidth 1s ease-out forwards;
    animation-delay: 0.5s;
  }
  
  /* Smooth transitions */
  .transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
  }
  </style>