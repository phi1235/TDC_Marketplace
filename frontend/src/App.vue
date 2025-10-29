<template>
  <div id="app" :class="isDark ? 'dark' : ''">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 relative">

      <!-- Header -->
      <Header v-if="!auth.isAdmin" />
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
       <!-- check login -->
      <main class="transition-colors duration-300">
        <router-view @route-loading="handleLoading" />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue'
import SkeletonLoader from '@/components/SkeletonLoader.vue'
import Header from '@/components/Header.vue'
import Navbar from './components/Navbar.vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

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

// // 🔄 Skeleton loading khi chuyển route
// router.beforeEach((to, from, next) => {
//   isLoading.value = true
//   setTimeout(() => next(), 200) // Delay giả lập
// })
// router.afterEach(() => {
//   setTimeout(() => (isLoading.value = false), 600)
// })

// Cho phép component con bật/tắt loading nếu cần
const handleLoading = (val: boolean) => {
  isLoading.value = val
}

//check user or admin login
const auth = useAuthStore();
const isAuthenticated = computed(() => auth.isAuthenticated)
const isAdmin = computed(() => auth.isAdmin)
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
