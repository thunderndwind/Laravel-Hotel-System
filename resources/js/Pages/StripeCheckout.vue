<template>
  <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
      <div class="bg-indigo-600 px-6 py-4">
        <h2 class="text-xl font-bold text-white">Complete Your Payment</h2>
        <p class="text-indigo-200 mt-1">Secure payment powered by Stripe</p>
      </div>
      
      <div class="px-6 py-6">
        <!-- Order Summary -->
        <div class="mb-6 bg-gray-50 p-4 rounded-md">
          <h3 class="font-medium text-gray-800 mb-2">Order Summary</h3>
          <div class="flex justify-between text-sm">
            <span>Premium Room (1 night)</span>
            <span>$90.00</span>
          </div>
          <div class="flex justify-between text-sm mt-1">
            <span>Tax</span>
            <span>$10.00</span>
          </div>
          <div class="border-t border-gray-200 my-2"></div>
          <div class="flex justify-between font-bold">
            <span>Total</span>
            <span>${{ amount }}</span>
          </div>
        </div>
        
        <form @submit.prevent="handleSubmit" id="payment-form">
          <!-- Form Errors -->
          <div v-if="formError" class="mb-4 bg-red-50 text-red-600 p-3 rounded-md text-sm">
            {{ formError }}
          </div>
          
          <!-- Billing Info -->
          <div class="mb-6">
            <h3 class="font-medium text-gray-800 mb-3">Billing Information</h3>
            <div class="grid grid-cols-2 gap-4">
              <div class="col-span-2 sm:col-span-1">
                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input 
                  type="text" 
                  id="firstName" 
                  v-model="billing.firstName"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
              </div>
              <div class="col-span-2 sm:col-span-1">
                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input 
                  type="text" 
                  id="lastName" 
                  v-model="billing.lastName"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
              </div>
              <div class="col-span-2">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input 
                  type="email" 
                  id="email" 
                  v-model="billing.email"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
              </div>
            </div>
          </div>
          
          <!-- Payment Details -->
          <div class="mb-6">
            <h3 class="font-medium text-gray-800 mb-3">Payment Details</h3>
            <div id="card-element" class="p-3 border border-gray-300 rounded-md"></div>
            <div id="card-errors" class="mt-2 text-sm text-red-600" role="alert"></div>
          </div>
          
          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors disabled:opacity-75 flex justify-center items-center"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Processing Payment...' : 'Pay $' + amount }}
          </button>
          
          <p class="text-xs text-gray-500 mt-4 text-center">
            Your payment is secured with industry-standard encryption.
          </p>
        </form>
      </div>
    </div>
    
    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 z-10 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Payment Successful!</h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Your payment of ${{ amount }} has been processed successfully. A receipt has been sent to your email.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="handleContinue" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
              Continue
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

// Constants & state
const amount = 100
const stripe = ref(null)
const card = ref(null)
const cardErrors = ref(null)
const loading = ref(false)
const formError = ref('')
const showSuccessModal = ref(false)

// Billing info
const billing = ref({
  firstName: '',
  lastName: '',
  email: ''
})

// Initialize Stripe
onMounted(() => {
  // Load Stripe.js
  const script = document.createElement('script')
  script.src = 'https://js.stripe.com/v3/'
  script.onload = () => {
    // Initialize Stripe with publishable key
    stripe.value = window.Stripe(import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY)
    
    // Create card element with custom styling
    const elements = stripe.value.elements({
      fonts: [
        {
          cssSrc: 'https://fonts.googleapis.com/css?family=Inter:400,500,600&display=swap',
        },
      ],
    })
    
    // Create and mount card element with custom styling
    card.value = elements.create('card', {
      style: {
        base: {
          color: '#4B5563',
          fontFamily: 'Inter, sans-serif',
          fontSmoothing: 'antialiased',
          fontSize: '16px',
          '::placeholder': {
            color: '#9CA3AF',
          },
        },
        invalid: {
          color: '#EF4444',
          iconColor: '#EF4444',
        },
      },
    })
    
    // Mount card element and handle validation errors
    card.value.mount('#card-element')
    card.value.on('change', (event) => {
      const displayError = document.getElementById('card-errors')
      if (event.error) {
        displayError.textContent = event.error.message
      } else {
        displayError.textContent = ''
      }
    })
  }
  
  document.head.appendChild(script)
})

// Form submission handler
const handleSubmit = async () => {
  formError.value = ''
  loading.value = true
  
  // Validate form
  if (!billing.value.firstName || !billing.value.lastName || !billing.value.email) {
    formError.value = 'Please fill in all required fields'
    loading.value = false
    return
  }
  
  // Check if Stripe is initialized
  if (!stripe.value || !card.value) {
    formError.value = 'Payment system is not initialized. Please refresh and try again.'
    loading.value = false
    return
  }
  
  try {
    // Create token
    const { token, error } = await stripe.value.createToken(card.value, {
      name: `${billing.value.firstName} ${billing.value.lastName}`,
      email: billing.value.email
    })
    
    if (error) {
      formError.value = error.message
      loading.value = false
      return
    }
    
    // Send token to backend
    router.post('/stripe', { 
      stripeToken: token.id,
      amount: amount * 100, // Convert to cents
      email: billing.value.email,
      name: `${billing.value.firstName} ${billing.value.lastName}`
    }, {
      onFinish: () => loading.value = false,
      onSuccess: () => {
        showSuccessModal.value = true
      },
      onError: (errors) => {
        formError.value = 'Payment processing failed. Please try again.'
        console.error(errors)
      }
    })
  } catch (err) {
    formError.value = 'An unexpected error occurred. Please try again.'
    loading.value = false
    console.error(err)
  }
}

// Continue after successful payment
const handleContinue = () => {
  showSuccessModal.value = false
  router.visit('/') // Navigate to home or another page
}
</script>
  