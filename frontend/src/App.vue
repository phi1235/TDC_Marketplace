<template>
  <div id="app">
    <div class="min-h-screen bg-gray-50">
      <Header />
      <!-- Simple Header -->
      <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 py-4">
          <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-lg">T</span>
              </div>
              <span class="text-xl font-bold text-gray-900">TDC Marketplace</span>
            </div>
            
            <nav class="flex items-center space-x-4">
              <router-link to="/" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                Trang chủ
              </router-link>
              <router-link to="/listings" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                Danh sách
              </router-link>
              <router-link to="/login" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                Đăng nhập
              </router-link>
              <router-link to="/register" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium">
                Đăng ký
              </router-link>
              <router-link to="/dashboard" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                Dashboard Page
              </router-link>
              <router-link to="/panel" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                Panel Page
              </router-link>
              <router-link to="/userpanel" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                User Page
              </router-link>
              <router-link to="/listwish" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                List wish page
              </router-link>
              <router-link to="/listingcard" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                Listing Card page
              </router-link>
            </nav>
          </div>
        </div>
      </header>

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
import Header from '@/components/Header.vue'
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

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