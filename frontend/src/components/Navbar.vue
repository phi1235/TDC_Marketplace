<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="fixed h-screen w-64 bg-gray-800 text-white flex flex-col p-4">
      <div class="text-xl font-bold mb-6">TDC Admin</div>
      <nav class="space-y-2">
        <p class="text-gray-500">Menu</p>
        <router-link to="/dashboard" class="block px-3 py-2 rounded hover:bg-gray-700">🏠 Dashboard</router-link>
        <p class="text-gray-500">App</p>
        <router-link to="/dashboard/users" class="block px-3 py-2 rounded hover:bg-gray-700">🙍‍♂️ Users</router-link>
        <router-link to="/dashboard/listings" class="block px-3 py-2 rounded hover:bg-gray-700">📖
          Listings</router-link>
        <router-link to="/dashboard/pending" class="block px-3 py-2 rounded hover:bg-gray-700">🕔
          Pending</router-link>
        <div class="relative">
          <!-- Button / Link chính -->
          <button @click="open = !open"
            class="flex items-center w-full px-3 py-2 rounded hover:bg-gray-700 focus:outline-none">
            📅 Dashboard
            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Dropdown items -->
          <div v-show="open"
            class="absolute left-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50">
            <router-link to=""
              class="block px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Comparision</router-link>
            <router-link to=""
              class="block px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Search Analytics</router-link>
          </div>
        </div>
      </nav>
    </aside>


    <!-- Main area -->
    <div class="flex-1 flex flex-col bg-gray-100">
      <!-- Top Header -->
      <header class="flex items-center justify-between p-4 bg-white shadow">
        <div class="text-lg font-semibold">Search Analytics Dashboard</div>
        <div class="flex items-center gap-4">
          <input type="text" placeholder="Search..." class="border rounded px-3 py-1" />
          <button class="p-2">⚙️</button>
        </div>
      </header>
     <main class="p-6 ml-64" style="max-width: calc(100% - 64px - 20px);">
        <router-view />  <!-- UsersView.vue sẽ hiển thị TRONG KHUNG NÀY -->
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const open = ref(false)
</script>