<template>
    <div class="'relative">
      <div class="relative w-full h-screen overflow-hidden">
      <!-- Transparent Navbar -->
      <nav ref="navbar" class="fixed top-0 left-0 right-0 z-50 py-8 px-6 flex  justify-between items-center transition-all duration-300">
 
        <div class="flex items-center">
          <!-- <img src="/statics/logo.png" alt="Logo" class="h-12 w-auto mr-4"> -->
          <span class="text-2xl font-bold text-white">HavenStay</span>
        </div>
    <!-- Left: Navigation Links -->
    <div class="flex items-center space-x-6">
      <a href="#" class="text-white font-semibold p-2 hover:text-[#cb8670]" style="background-color:#cb8670 ;">Home</a>
      <a href="#" class="text-white font-semibold hover:text-[#cb8670]">About Us</a>

      <!-- Pages Dropdown -->
      <div class="relative group">
        <button class="text-white font-semibold hover:text-[#cb8670] focus:outline-none">
          Pages
        </button>
        <div
          class="absolute left-0 top-full mt-2 w-40 bg-white shadow-md rounded-md hidden group-hover:block z-50"
        >
          <a href="#" class="block px-4 py-2 text-black hover:bg-gray-100">Rooms</a>
          <a href="#" class="block px-4 py-2 text-black hover:bg-gray-100">Services</a>
        </div>
      </div>

      <a href="#" class="text-white font-semibold hover:text-[#cb8670]">Services</a>
      <a href="#" class="text-white font-semibold hover:text-[#cb8670]">Contact</a>
    </div>

    <!-- Right: Auth Links -->
    <div class="flex items-center space-x-3">
      <router-link
        v-if="$page.props.auth.user"
        :to="route('dashboard')"
        class="rounded-md px-4 py-2 text-white ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
      >
        Dashboard
      </router-link>
    <Link
  :href="route('logout')"
  method="post"
  as="button"
  class="rounded-md px-4 py-2 text-white ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
>
  Log out
</Link>
    
      <router-link
        v-if="canLogin"
        :to="route('login')"
        class="rounded-md px-4 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
      >
        Log in
      </router-link>
      <router-link
        v-else-if="canRegister"
        :to="route('register')"
        class="rounded-md px-4 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
      >
        Register
      </router-link>
    </div>
      </nav>
    <hr class="border-t-5 border-[#cb8670]  mx-auto mb-4 w-1/4 mt-50">
      <!-- Background Images (Fixed) -->
      <div class="fixed inset-0 w-full h-full slide-animation">
        <transition-group name="fade" tag="div" class="w-full h-full slide-animation">
          <div 
            v-for="(slide, index) in slides" 
            :key="index"
            v-show="currentIndex === index"
            class="absolute inset-0 w-full h-full"
          >
            <img 
              class="w-full h-full object-cover slide-animation" 
              :src="slide.image" 
              :alt="slide.alt"
              :style="{ zIndex: currentIndex === index ? 1 : 0 }"
            >
          </div>
        </transition-group>
      </div>
  
      <!-- Content Overlay -->
      <div class="relative z-10 w-full h-full">
        <transition-group name="content-fade" tag="div" class="w-full h-full">
          <div 
            v-for="(slide, index) in slides" 
            :key="`content-${index}`"
            v-show="currentIndex === index"
            class="absolute inset-0 w-full h-full flex items-center justify-center"
          >
            <div class="text-center text-white px-6 py-10 bg-black/50 backdrop-blur-sm max-w-2xl mx-auto" s
            style="border:2px solid #cb8670;">
              <hr class="border-t-2 border-[#cb8670]  mx-auto mb-4 w-1/4">
              <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">{{ slide.title }}</h1>
              <p class="text-lg md:text-xl mb-8 leading-relaxed">{{ slide.description }}</p>
              <button class=" from-amber-600 to-amber-800 text-white px-8 py-4  text-lg font-medium hover:from-amber-700 hover:to-amber-900 transition-all duration-300 shadow-lg hover:shadow-xl "
              style="background-color: #cb8670;">
                {{ slide.buttonText }}
              </button>
            </div>
          </div>
        </transition-group>
  
        <!-- Navigation buttons -->
        <button 
          @click="goToPreviousSlide" 
          class="absolute left-8 top-1/2 transform -translate-y-1/2 p-3 bg-black/50 text-white rounded-full hover:bg-black/70 transition-all duration-300 shadow-lg hover:scale-110 z-20"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button 
          @click="goToNextSlide" 
          class="absolute right-8 top-1/2 transform -translate-y-1/2 p-3 bg-black/50 text-white rounded-full hover:bg-black/70 transition-all duration-300 shadow-lg hover:scale-110 z-20"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
  
        <!-- Pagination dots -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3 z-20">
          <button 
            v-for="(slide, i) in slides" 
            :key="`dot-${i}`" 
            @click="goToSlide(i)"
            class="w-3 h-3 rounded-full transition-all duration-300"
            :class="{
              'bg-white w-6': currentIndex === i, 
              'bg-white/50 hover:bg-white/70': currentIndex !== i
            }"
            :aria-label="`Go to slide ${i + 1}`"
          ></button>
        </div>
      </div>
  

    </div>
    <section class="py-20 bg-white relative z-20">
  <div class="container mx-auto px-6 md:px-12 lg:px-20 flex flex-col md:flex-row items-center gap-12">
    
    <!-- Left Content -->
    <div class="md:w-1/2">
      <div class="mb-4 w-20 h-0.5 " style="background-color: #cb8670;"></div>
      <h2 class="text-4xl font-semibold text-gray-800 mb-6">A place to remember</h2>
      <p class="text-gray-600 mb-6 leading-relaxed">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec malesuada lorem maximus mauris scelerisque, at rutrum nulla dictum. Ut ac ligula sapien. Suspendisse cursus faucibus finibus.
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec malesuada lorem maximus mauris scelerisque, at rutrum nulla dictum.
      </p>

      <!-- List Items -->
      <ul class="space-y-4 mb-8">
        <li class="flex items-center">
          <span class="w-6 h-6 flex items-center justify-center rounded-full  text-white mr-3" style="background-color: #cb8670;">
            ✓
          </span>
          <span class="text-gray-800">Donec malesuada lorem maximus mauris sceleri</span>
        </li>
        <li class="flex items-center">
          <span class="w-6 h-6 flex items-center justify-center rounded-full  text-white mr-3"  style="background-color: #cb8670;">
            ✓
          </span>
          <span class="text-gray-800">Malesuada lorem maximus mauris sceleri</span>
        </li>
      </ul>

      <!-- Read More Button -->
      <a href="#" class="inline-block  text-white px-6 py-3 rounded-md  transition" style="background-color: #cb8670;">
        Read More
      </a>
    </div>

    <!-- Right Images -->
    <div class="md:w-1/2 relative flex justify-center h-96">
  <!-- Images with consistent height and layered animation -->
  <img 
    src="/statics/img1.jpg" 
    alt="Image 1" 
    class="rounded-lg shadow-lg w-64 h-64 object-cover md:w-72 lg:w-80 absolute transition-all duration-500 hover:scale-105 hover:z-40" 
    style="top: 0rem; left: 15.5rem; z-index: 10;"
  />
  
  <img 
    src="/statics/img3.jpg" 
    alt="Image 2" 
    class="rounded-lg shadow-lg w-64 h-64 object-cover md:w-64 lg:w-72 absolute transition-all duration-500 hover:scale-105 hover:z-40" 
    style="top: 2.5rem; left: 0rem; z-index: 20;"
  />
  
  <img 
    src="/statics/img4.jpg" 
    alt="Image 3" 
    class="rounded-lg shadow-lg w-64 h-64 object-cover md:w-72 lg:w-80 absolute transition-all duration-500 hover:scale-105 hover:z-40" 
    style="top: 9rem; left: 7rem; z-index: 30;"
  />
</div>
  </div>
</section>
    </div>
    
  </template>
  
  <script>
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    Link
  },
  data() {
    return {
      currentIndex: 0,
      slides: [
        {
          image: '/statics/img2.jpeg',
          alt: 'Luxury resort',
          title: 'A Place to Remember',
          description: 'Experience unparalleled luxury in our award-winning resort with world-class amenities.',
          buttonText: 'Explore Resort'
        },
        {
          image: '/statics/img4.jpg',
          alt: 'Beach view',
          title: 'Discover Your Escape',
          description: 'Private beaches, crystal clear waters, and unforgettable sunsets await you.',
          buttonText: 'View Packages'
        },
        {
          image: '/statics/hotels.jpg',
          alt: 'Spa relaxation',
          title: 'Relax & Unwind',
          description: 'Our world-class spa treatments will rejuvenate your mind, body and soul.',
          buttonText: 'Book Treatment'
        },
        {
          image: '/statics/img5.jpg',
          alt: 'Luxury suite',
          title: 'Exclusive Suites',
          description: 'Indulge in our premium suites with breathtaking views and personalized service.',
          buttonText: 'View Suites'
        },
        {
          image: '/statics/img6.jpeg',
          alt: 'Luxury dining',
          title: 'Gourmet Experiences',
          description: 'Savor exquisite culinary creations by our world-renowned chefs.',
          buttonText: 'Discover Menus'
        }
      ],
      interval: null,
      scrollListener: null
    }
  },
  methods: {
    goToNextSlide() {
      this.currentIndex = (this.currentIndex + 1) % this.slides.length;
      // No resetAutoPlay here
    },
    goToPreviousSlide() {
      this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
      // No resetAutoPlay here
    },
    goToSlide(index) {
      this.currentIndex = index;
      // No resetAutoPlay here
    },
    startAutoPlay() {
      this.interval = setInterval(() => {
        this.goToNextSlide();
      }, 5000);
    },
    resetAutoPlay() {
      clearInterval(this.interval);
      this.startAutoPlay();
    },
    handleScroll() {
      const navbar = this.$refs.navbar;
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    }
  },
}
</script>
  
  <style scoped>
  /* Fixed background image */
  .fixed-image {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  
  /* Slide animation */
  .slide-animation {
  animation: slide 5s ease-in-out infinite; /* <-- Add infinite */
  animation-delay: 0.1s;
}

@keyframes slide {
  0% {
    transform: scale(1.1);
  }
  30% {
    transform: scale(1.05);
  }
  60% {
    transform: scale(1.02);
  }
  80% {
    transform: scale(1.01);
  }
  100% {
    transform: scale(1);
  }
}


  /* Fade animation */
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
  }
  .fade-enter-active {
    transition: all 0.8s ease 0.3s ;
  }
  .fade-leave-active {
    transition: all 0.5s ease;
  }
  .fade-enter-from {
    opacity: 0;
    transform: translateY(20px);
  }
  .fade-leave-to {
    opacity: 0;
  }
  /* Content fade animation */
  .content-fade-enter-active, .content-fade-leave-active {
    transition: opacity 0.5s ease;
  }
  .content-fade-enter, .content-fade-leave-to /* .content-fade-leave-active in <2.1.8 */ {
    opacity: 0;
  }
  .content-fade-enter, .content-fade-leave-to {
    opacity: 0;
  }
  
  /* Content fade transition */
  .content-fade-enter-active {
    transition: all 0.8s ease 0.3s ;
  }
  .content-fade-leave-active {
    transition: all 0.5s ease;
  }
  .content-fade-enter-from {
    opacity: 0;
    transform: translateY(20px);
  }
  .content-fade-leave-to {
    opacity: 0;
  }
  
  /* Navbar styles */
  nav {
    background-color: rgba(0, 0, 0, 0.397);
    backdrop-filter: blur(0px);
  }
  
  nav.scrolled {
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
  }
  
  /* Responsive adjustments */
  @media (max-width: 768px) {
    h1 {
      font-size: 2rem;
    }
    
    p {
      font-size: 1rem;
    }
    
    button {
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
    }
  
    /* Navigation buttons smaller on mobile */
    .absolute.left-8, .absolute.right-8 {
      left: 1rem;
      right: 1rem;
      padding: 0.5rem;
    }
  
    .absolute.left-8 svg, .absolute.right-8 svg {
      height: 1.5rem;
      width: 1.5rem;
    }
    
    /* Faster transitions on mobile */
    .fade-enter-active,
    .fade-leave-active {
      transition: opacity 0.8s ease;
    }
    .slide-animation {
  animation: zoomInOut 5s ease-in-out infinite;
}
@keyframes zoomInOut {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.09515);
  }
  100% {
    transform: scale(0.8);
  }
  
}
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

.hover\:animate-float:hover {
  animation: float 3s ease-in-out infinite;
}

  }
  </style>