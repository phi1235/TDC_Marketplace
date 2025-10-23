<template>
  <div id="app" :class="isDark ? 'dark' : ''">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 relative">

      <!-- Header -->
      <header class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 z-10 relative">
        <div class="max-w-7xl mx-auto px-4 py-4">
          <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-lg">T</span>
              </div>
              <span class="text-xl font-bold text-gray-900 dark:text-gray-100">TDC Marketplace</span>
            </div>
            
            <!-- Navigation -->
            <nav class="flex items-center space-x-4">
              <router-link to="/" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">
                Trang chủ
              </router-link>
              <router-link to="/listings" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">
                Danh sách
              </router-link>
              <router-link to="/login" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">
                Đăng nhập
              </router-link>
              <router-link to="/register" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium">
                Đăng ký
              </router-link>
              <router-link to="/dashboard" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">
                Dashboard
              </router-link>

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

      <!-- Skeleton Loading Overlay -->
      <transition name="fade">
        <div
          v-if="isLoading"
          class="absolute inset-0 bg-gray-50/80 dark:bg-gray-900/80 flex items-center justify-center z-50"
        >
          <div class="w-1/2">
            <SkeletonLoader :count="8" />
          </div>
        </div>
      </transition>

      <!-- Main Content -->
      <main class="transition-colors duration-300">
        <router-view @route-loading="handleLoading" />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import SkeletonLoader from '@/components/SkeletonLoader.vue'
import { useRouter } from 'vue-router'

const isDark = ref(false)
const isLoading = ref(false)
const router = useRouter()

// 🌙 Dark mode
onMounted(() => {
  const saved = localStorage.getItem('theme')
  if (saved === 'dark') {
    isDark.value = true
    document.documentElement.classList.add('dark')
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

// 🔄 Skeleton loading khi chuyển route
router.beforeEach((to, from, next) => {
  isLoading.value = true
  setTimeout(() => next(), 200) // Delay giả lập
})
router.afterEach(() => {
  setTimeout(() => (isLoading.value = false), 600)
})

// Cho phép component con bật/tắt loading nếu cần
const handleLoading = (val: boolean) => {
  isLoading.value = val
}
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
