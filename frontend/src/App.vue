<template>
  <div id="app">
    <div class="min-h-screen bg-gray-50">
      <!-- Header Component -->
      <Header />

      <!-- Main Content -->
      <main>
        <router-view />
        <!-- Toast container -->
        <div id="toast-root" class="fixed top-4 right-4 z-50 space-y-3"></div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import Header from '@/components/Header.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

// Fetch user data when app starts
onMounted(() => {
  auth.fetchUser()
})
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  margin: 0;
  padding: 0;
}
</style>