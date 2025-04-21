<template>
    <div class="relative w-full h-screen overflow-hidden">
      <!-- Slider Container -->
      <div class="slider-container flex w-full h-full transition-transform duration-700 ease-[cubic-bezier(0.65,0,0.35,1)]" 
           :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
        <!-- Slide 1 -->
        <div class="w-full flex-shrink-0 relative">
          <img class="w-full h-full object-cover" src="/statics/img2.jpeg" alt="Luxury resort">
          <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-b from-black/70 via-black/40 to-transparent">
            <div class="text-center text-white px-6 py-8 bg-black/50 backdrop-blur-sm max-w-2xl mx-auto rounded-lg">
              <h1 class="text-5xl font-bold mb-4 leading-tight animate-fade-in">A Place to Remember</h1>
              <p class="text-xl mb-8 leading-relaxed animate-fade-in delay-100">Experience unparalleled luxury in our award-winning resort with world-class amenities.</p>
              <button class="bg-gradient-to-r from-amber-600 to-amber-800 text-white px-10 py-5 rounded-full text-lg font-medium hover:from-amber-700 hover:to-amber-900 transition-all duration-300 shadow-lg hover:shadow-xl animate-fade-in delay-200">
                Explore Resort
              </button>
            </div>
          </div>
        </div>
  
        <!-- Slide 2 -->
        <div class="w-full h-full flex-shrink-0 relative">
          <img class="w-full h-full object-cover" src="/statics/img4.jpg" alt="Beach view">
          <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-b from-black/70 via-black/40 to-transparent">
            <div class="text-center text-white px-6 py-8 bg-black/50 backdrop-blur-sm max-w-2xl mx-auto rounded-lg">
              <h1 class="text-5xl font-bold mb-4 leading-tight">Discover Your Escape</h1>
              <p class="text-xl mb-8 leading-relaxed">Private beaches, crystal clear waters, and unforgettable sunsets await you.</p>
              <button class="bg-gradient-to-r from-amber-600 to-amber-800 text-white px-10 py-5 rounded-full text-lg font-medium hover:from-amber-700 hover:to-amber-900 transition-all duration-300 shadow-lg hover:shadow-xl">
                View Packages
              </button>
            </div>
          </div>
        </div>
  
        <!-- Slide 3 -->
        <div class="w-full h-full flex-shrink-0 relative">
          <img class="w-full h-full object-cover" src="/statics/hotels.jpg" alt="Spa relaxation">
          <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-b from-black/70 via-black/40 to-transparent">
            <div class="text-center text-white px-6 py-8 bg-black/50 backdrop-blur-sm max-w-2xl mx-auto rounded-lg">
              <h1 class="text-5xl font-bold mb-4 leading-tight">Relax & Unwind</h1>
              <p class="text-xl mb-8 leading-relaxed">Our world-class spa treatments will rejuvenate your mind, body and soul.</p>
              <button class="bg-gradient-to-r from-amber-600 to-amber-800 text-white px-10 py-5 rounded-full text-lg font-medium hover:from-amber-700 hover:to-amber-900 transition-all duration-300 shadow-lg hover:shadow-xl">
                Book Treatment
              </button>
            </div>
          </div>
        </div>
        <div class="w-full h-full flex-shrink-0 relative">
          <img class="w-full h-full object-cover" src="/statics/img2.avif" alt="Spa relaxation">
          <div class="absolute div inset-0 flex items-center justify-center bg-gradient-to-b from-black/70 via-black/40 to-transparent">
            <div class="text-center text-white px-6 py-8 bg-black/50 backdrop-blur-sm max-w-2xl mx-auto">
              <h1 class="text-5xl font-bold mb-4 leading-tight">Relax & Unwind</h1>
              <p class="text-xl mb-8 leading-relaxed">Our world-class spa treatments will rejuvenate your mind, body and soul.</p>
              <button class="bg-gradient-to-r from-amber-600 to-amber-800 text-white px-10 py-5 rounded-full text-lg font-medium hover:from-amber-700 hover:to-amber-900 transition-all duration-300 shadow-lg hover:shadow-xl">
                Book Treatment
              </button>
            </div>
          </div>
        </div>
      </div>
      
      
      <!-- Navigation buttons -->
      <button @click="goToPreviousSlide" 
              class="absolute left-8 top-1/2 transform -translate-y-1/2 p-3 bg-black/50 text-white rounded-full hover:bg-black/70 transition-all duration-300 shadow-lg hover:scale-110 z-10">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <button @click="goToNextSlide" 
              class="absolute right-8 top-1/2 transform -translate-y-1/2 p-3 bg-black/50 text-white rounded-full hover:bg-black/70 transition-all duration-300 shadow-lg hover:scale-110 z-10">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
  
      <!-- Pagination dots -->
      <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3 z-10">
        <button v-for="(slide, i) in slides" :key="i" 
                @click="goToSlide(i)"
                class="w-3 h-3 rounded-full transition-all duration-300"
                :class="{'bg-white w-6': currentIndex === i, 'bg-white/50 hover:bg-white/70': currentIndex !== i}">
        </button>
      </div>
  
      <!-- Progress bar -->
      <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/20 z-10">
        <div class="h-full bg-white transition-all duration-1000 ease-linear" 
             :style="{ width: `${progress}%` }"></div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        currentIndex: 0,
        slides: 3,
        interval: null,
        progress: 0,
        progressInterval: null
      }
    },
    methods: {
      goToNextSlide() {
        this.resetProgress()
        this.currentIndex = (this.currentIndex + 1) % this.slides
        this.startProgress()
      },
      goToPreviousSlide() {
        this.resetProgress()
        this.currentIndex = (this.currentIndex - 1 + this.slides) % this.slides
        this.startProgress()
      },
      goToSlide(index) {
        this.resetProgress()
        this.currentIndex = index
        this.startProgress()
      },
      startAutoPlay() {
        this.interval = setInterval(() => {
          this.goToNextSlide()
        }, 5000)
      },
      startProgress() {
        this.progress = 0
        clearInterval(this.progressInterval)
        this.progressInterval = setInterval(() => {
          this.progress += 100 / 50 // 5000ms / 100 = 50 steps
          if (this.progress >= 100) {
            clearInterval(this.progressInterval)
          }
        }, 100)
      },
      resetProgress() {
        clearInterval(this.progressInterval)
        this.progress = 0
      },
      pauseAutoPlay() {
        clearInterval(this.interval)
        clearInterval(this.progressInterval)
      },
      resumeAutoPlay() {
        this.startAutoPlay()
        this.startProgress()
      }
    },
    mounted() {
      this.startAutoPlay()
      this.startProgress()
    },
    beforeUnmount() {
      clearInterval(this.interval)
      clearInterval(this.progressInterval)
    }
  }
  </script>
  
  <style scoped>
  .slider-container {
    display: flex;
    width: 100%;
    height: 100%;
  }
  
  .animate-fade-in {
    animation: fadeIn 0.8s ease-out forwards;
  }
  
  .delay-100 {
    animation-delay: 0.1s;
  }
  
  .delay-200 {
    animation-delay: 0.2s;
  }
  
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  /* Smooth transition for slide content */
  .slider-container > div > div > div {
    transition: opacity 0.5s ease, transform 0.5s ease;
  }
  </style>