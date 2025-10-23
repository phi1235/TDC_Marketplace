<template>
  <header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-lg">T</span>
            </div>
            <span class="text-xl font-bold text-gray-900">TDC Marketplace</span>
          </router-link>
        </div>

        <!-- Search Bar -->
        <div class="flex-1 max-w-lg mx-8">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm kiếm sản phẩm..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              @keyup.enter="handleSearch"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Navigation -->
        <nav class="flex items-center space-x-4">
          <router-link
            to="/"
            class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
          >
            Trang chủ
          </router-link>
          
          <router-link
            to="/listings"
            class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
          >
            Danh sách
          </router-link>

          <!-- Test links dropdown for team -->
          <div class="relative test-menu-container">
            <button
              @click="showTestMenu = !showTestMenu"
              class="flex items-center space-x-1 text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
            >
              <span>Test Pages</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>

            <!-- Test pages dropdown -->
            <div
              v-if="showTestMenu"
              class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
            >
              <router-link
                to="/dashboard"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showTestMenu = false"
              >
                Dashboard Page
              </router-link>
              <router-link
                to="/panel"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showTestMenu = false"
              >
                Panel Page
              </router-link>
              <router-link
                to="/userpanel"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showTestMenu = false"
              >
                User Page
              </router-link>
              <router-link
                to="/listwish"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showTestMenu = false"
              >
                List wish page
              </router-link>
              <router-link
                to="/listingcard"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showTestMenu = false"
              >
                Listing Card page
              </router-link>
            </div>
          </div>

          <!-- Auth Buttons -->
          <div v-if="!isAuthenticated" class="flex items-center space-x-2">
            <router-link
              to="/login"
              class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
            >
              Đăng nhập
            </router-link>
            <router-link
              to="/register"
              class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium"
            >
              Đăng ký
            </router-link>
          </div>

          <div v-else class="flex items-center space-x-2">
            <!-- Menu cho admin -->
            <template v-if="auth.isAdmin">
              <router-link
                to="/dashboard"
                class="flex items-center space-x-1 text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
              >
                Quản trị
              </router-link>
              
              <!-- Admin User Menu -->
              <div class="relative user-menu-container">
                <button
                  @click="toggleUserMenu"
                  class="flex items-center text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
                >
                  {{ user?.name }}
                  <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>
                
                <!-- Dropdown Menu -->
                <div
                  v-if="showUserMenu"
                  class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                >
                  <router-link
                    to="/profile"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false"
                  >
                    Hồ sơ
                  </router-link>
                  <button
                    @click="handleLogout"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Đăng xuất
                  </button>
                </div>
              </div>
            </template>

            <!-- User Menu -->
            <template v-else>
              <router-link
                to="/create-listing"
                class="bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-md text-sm font-medium"
              >
                Đăng tin
              </router-link>
              <router-link
                to="/my-listings"
                class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
              >
                Tin của tôi
              </router-link>
              <div class="relative user-menu-container">
                <button
                  @click="toggleUserMenu"
                  class="flex items-center text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
                >
                  {{ user?.name }}
                  <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>
                
                <!-- Dropdown Menu -->
                <div
                  v-if="showUserMenu"
                  class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                >
                  <router-link
                    to="/profile"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false"
                  >
                    Hồ sơ
                  </router-link>
                  <router-link
                    v-if="isAdmin"
                    to="/dashboard"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false"
                  >
                    Quản trị
                  </router-link>
                  <button
                    @click="handleLogout"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Đăng xuất
                  </button>
                </div>
              </div>
            </template>
          </div>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { showToast } from '@/utils/toast'

const router = useRouter()
const auth = useAuthStore()

const searchQuery = ref('')
const showUserMenu = ref(false)
const showTestMenu = ref(false)

const isAuthenticated = computed(() => auth.isAuthenticated)
const user = computed(() => auth.user)
const isAdmin = computed(() => auth.isAdmin)

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'listings', query: { q: searchQuery.value } })
  }
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const handleLogout = async () => {
  try {
    await auth.logout()
    showToast('Đăng xuất thành công', 'success')
    router.push('/')
    showUserMenu.value = false
  } catch (error) {
    showToast('Đăng xuất thất bại', 'error')
  }
}

// Close dropdowns when clicking outside
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement
  
  // Close user menu if clicking outside
  if (!target.closest('.user-menu-container')) {
    showUserMenu.value = false
  }
  
  // Close test menu if clicking outside
  if (!target.closest('.test-menu-container')) {
    showTestMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>