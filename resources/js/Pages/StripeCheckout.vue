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
              <div class="col-span-2">
                <label for="specialRequests" class="block text-sm font-medium text-gray-700 mb-1">Special Requests</label>
                <textarea
                  id="specialRequests"
                  v-model="booking.specialRequests"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Enter any special requests or notes..."
                ></textarea>
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
  </div>
  
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import ToastService from '@/Services/ToastService'

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

// Booking info with special requests
const booking = ref({
  specialRequests: 'I would like a room with a view if possible.'
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
  
  // Show loading toast
  const toastId = ToastService.loading('Processing Payment', 'Please wait while we process your payment...');
  
  try {
    // Create token
    const { token, error } = await stripe.value.createToken(card.value, {
      name: `${billing.value.firstName} ${billing.value.lastName}`,
      email: billing.value.email
    })
    
    if (error) {
      formError.value = error.message
      loading.value = false
      ToastService.loadingToError(toastId, 'Payment Failed', error.message);
      return
    }
    
    // Send token to backend
    router.post('/stripe', { 
      stripeToken: token.id,
      amount: amount * 100, // Convert to cents
      email: billing.value.email,
      name: `${billing.value.firstName} ${billing.value.lastName}`,
      specialRequests: booking.value.specialRequests
    }, {
      onFinish: () => loading.value = false,
      onSuccess: (page) => {
        // Debug response data
        console.log('Payment response:', page.props);
        
        // Always treat onSuccess as a successful payment
        // Convert loading toast to success
        ToastService.loadingToSuccess(toastId, 'Payment Successful!', 'Your payment has been processed.');
        
        // Show success message with details
        setTimeout(() => {
          ToastService.success('Booking Confirmed', 'Thank you for your booking. A confirmation email has been sent.');
        }, 1000);
        
        // Reset form
        resetForm();
      },
      onError: (errors) => {
        const errorMessage = errors.payment || 'Payment processing failed. Please try again.';
        formError.value = errorMessage;
        console.error(errors);
        ToastService.loadingToError(toastId, 'Payment Failed', errorMessage);
      }
    });
  } catch (err) {
    formError.value = 'An unexpected error occurred. Please try again.';
    loading.value = false;
    console.error(err);
    ToastService.loadingToError(toastId, 'Payment Failed', 'An unexpected error occurred.');
  }
}

// Reset form after successful payment
const resetForm = () => {
  billing.value = {
    firstName: '',
    lastName: '',
    email: ''
  }
  
  booking.value.specialRequests = ''
  
  if (card.value) {
    card.value.clear();
  }
  
  formError.value = '';
  loading.value = false;
}

// Continue after successful payment
const handleContinue = () => {
  router.visit('/')
}
</script>
  