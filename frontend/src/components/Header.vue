<template>
  <header class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 z-10 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-lg">T</span>
            </div>
            <span class="text-xl font-bold text-gray-900 dark:text-gray-100">TDC Marketplace</span>
          </router-link>
        </div>

        <!-- Dual Search Bar -->
        <div class="flex-1 max-w-lg mx-8">
          <DualSearchBar
            placeholder="Tìm kiếm sản phẩm..."
            :show-engine-status="false"
            :show-results="false"
            :show-analytics="false"
            @search="handleDualSearch"
          />
        </div>

        <!-- Navigation -->
        <nav class="flex items-center space-x-4">
          <router-link
            to="/"
            class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium"
          >
            Trang chủ
          </router-link>
          
          <router-link
            to="/listings"
            class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium"
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
              class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium"
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

          <!-- Dark Mode Toggle -->
          <button
            @click="toggleDark"
            class="ml-3 p-2 rounded-md bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
            :title="isDark ? 'Chuyển sang chế độ sáng' : 'Chuyển sang chế độ tối'"
          >
            <span v-if="!isDark">🌙</span>
            <span v-else>☀️</span>
          </button>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { showToast } from '@/utils/toast'
import DualSearchBar from './DualSearchBar.vue'
// import type { DualSearchResult } from '@/services/dualSearch'

const router = useRouter()
const auth = useAuthStore()

const searchQuery = ref('')
const showUserMenu = ref(false)
const showTestMenu = ref(false)
const isDark = ref(false)

const isAuthenticated = computed(() => auth.isAuthenticated)
const user = computed(() => auth.user)
const isAdmin = computed(() => {
  // Debug logging
  console.log('Header - isAdmin computed:', {
    user: auth.user,
    role: auth.user?.role,
    isAdmin: auth.user?.role === 'admin'
  })
  // Ensure user data is loaded before checking admin status
  return auth.user?.role === 'admin'
})

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'listings', query: { q: searchQuery.value } })
  }
}

const handleDualSearch = (result) => {
  // Redirect to listings with search results
  router.push({
    name: 'listings',
    query: { 
      q: result.elasticsearch.data[0]?.title || result.solr.data[0]?.title || '',
      dual_search: 'true',
      es_count: result.elasticsearch.count,
      solr_count: result.solr.count,
      winner: result.comparison.winner
    }
  })
  
  showToast(`Search completed! ES: ${result.elasticsearch.count} results, Solr: ${result.solr.count} results`, 'success')
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

// Dark mode functions
onMounted(async () => {
  document.addEventListener('click', handleClickOutside)
  const saved = localStorage.getItem('theme')
  if (saved === 'dark') {
    isDark.value = true
    document.documentElement.classList.add('dark')
  }
  
  // Ensure user data is loaded on mount
  if (auth.token && !auth.user) {
    await auth.fetchUser()
  } else if (auth.token && auth.user) {
    // Refresh user data to ensure it's up to date
    await auth.refreshUser()
  }
})

watch(isDark, (val) => {
  if (val) {
    document.documentElement.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
})

const toggleDark = () => {
  isDark.value = !isDark.value
}

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>