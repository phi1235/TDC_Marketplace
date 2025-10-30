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
              class="block px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Search
              Analytics</router-link>
          </div>
        </div>
      </nav>
    </aside>


    <!-- Main area -->
    <div class="flex-1 flex flex-col bg-gray-100">
      <!-- Top Header -->
      <!-- <header class="flex items-center justify-between p-4 bg-white shadow">
        <div class="text-lg font-semibold">Search Analytics Dashboard</div>
        <div class="flex items-center gap-4">
          <input type="text" placeholder="Search..." class="border rounded px-3 py-1" />
          <button class="p-2">⚙️</button>
        </div>
      </header> -->
      <header class="flex items-center justify-between p-4 bg-white shadow">
        <!-- Bọc container -->
        <div class="flex items-center justify-between w-full ml-64 max-w-[calc(100%-64px-20px)]">
          <!-- Search bar -->
          <div class="flex items-center flex-1 max-w-md">
            <input type="text" placeholder="Search..."
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button class="ml-2 text-gray-500 hover:text-gray-700">
              🔍
            </button>
          </div>

          <!-- Icons right -->
          <div class="flex items-center gap-4">
            <!-- Dark mode toggle -->
            <button class="p-2 hover:bg-gray-100 rounded-full">
              🌙
            </button>

            <!-- Full screen -->
            <button class="p-2 hover:bg-gray-100 rounded-full">
              ⛶
            </button>

            <!-- Notifications -->
            <div class="relative">
              <button class="p-2 hover:bg-gray-100 rounded-full">
                🔔
              </button>
              <span
                class="absolute top-0 right-0 inline-block w-4 h-4 bg-red-500 text-white text-xs rounded-full text-center leading-4">
                5
              </span>
            </div>

            <!-- Settings -->
            <button class="p-2 hover:bg-gray-100 rounded-full">
              ⚙️
            </button>

            <div class="relative user-menu-container">
              <!-- User Avatar -->
              <button @click="toggleUserMenu"
                class="flex items-center text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                {{ }} AT
                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              <!-- Dropdown -->
              <div v-if="showUserMenu"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="showUserMenu = false">
                  Hồ sơ
                </router-link>
                <button @click="handleLogout"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  Đăng xuất
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>
      <main class="p-6 ml-64" style="max-width: calc(100% - 64px - 20px);">
        <router-view /> <!-- UsersView.vue sẽ hiển thị TRONG KHUNG NÀY -->
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { showToast } from '@/utils/toast'

const router = useRouter()
const auth = useAuthStore()

const open = ref(false)
//logout header
const showUserMenu = ref(false)

const toggleUserMenu = () => (showUserMenu.value = !showUserMenu.value)

const handleLogout = async () => {
  try {
    await auth.logout()
    showToast('success', 'Đăng xuất thành công')
    router.push('/')
    showUserMenu.value = false
  } catch (error) {
    showToast('error', 'Đăng xuất thất bại')
  }
}

// Close dropdowns when clicking outside
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement

  // Close user menu if clicking outside
  if (!target.closest('.menu-navbar')) {
    showUserMenu.value = false
  }
}
</script>