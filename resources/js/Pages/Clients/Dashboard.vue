<template>
    <AppLayout>
      <div class="dashboard-container">
        <h1>Welcome, {{ client.user.name }}</h1>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
          <div class="stat-card">
            <h3>Total Reservations</h3>
            <p>{{ client.reservations.length }}</p>
          </div>
          <div class="stat-card">
            <h3>Pending Reservations</h3>
            <p>{{ pendingReservations }}</p>
          </div>
        </div>
  
        <!-- Upcoming Reservations -->
        <div class="upcoming-reservations">
          <h2>Upcoming Reservations</h2>
          <div v-if="upcomingReservations.length">
            <div v-for="reservation in upcomingReservations" :key="reservation.id">
              {{ reservation.room.number }} - {{ reservation.check_in_date }}
            </div>
          </div>
          <p v-else>No upcoming reservations</p>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  
  const props = defineProps({
    client: Object,
    pendingReservations: Number,
    upcomingReservations: Array
  });
  </script>
  
  <style scoped>
  .dashboard-container {
    padding: 2rem;
  }
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 2rem 0;
  }
  .stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  </style>