<template>
  <div id="app" :class="isDark ? 'dark' : ''">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 relative">

      <!-- Header - Only show on non-admin routes -->
      <Header v-if="!isAdminRoute" />

      <!-- Skeleton Loading Overlay - DISABLED -->
      <!-- <transition name="fade">
        <div
          v-if="isLoading"
          class="absolute inset-0 bg-gray-50/80 dark:bg-gray-900/80 flex items-center justify-center z-50"
        >
          <div class="w-1/2">
            <SkeletonLoader :count="8" />
          </div>
        </div>
      </transition> -->

      <!-- Main Content -->
      <main class="transition-colors duration-300">
        <router-view @route-loading="handleLoading" />
      </main>

      <!-- Toast Container -->
      <div id="toast-root" class="fixed top-4 right-4 z-50 space-y-2"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue'
import SkeletonLoader from '@/components/SkeletonLoader.vue'
import Header from '@/components/Header.vue'
import { useRouter, useRoute } from 'vue-router'

const isDark = ref(false)
const isLoading = ref(false)
const router = useRouter()
const route = useRoute()

// Check if current route is an admin route
const isAdminRoute = computed(() => {
  return route.path.startsWith('/dashboard') || route.path.startsWith('/admin')
})

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

// 🔄 Skeleton loading khi chuyển route - DISABLED
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

/* Toast animations */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateX(100%);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

/* Toast transitions */
#toast-root > div {
  transition: opacity 0.3s ease-out, transform 0.3s ease-out;
}
</style>
