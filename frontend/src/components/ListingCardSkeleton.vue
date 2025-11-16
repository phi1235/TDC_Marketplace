<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden animate-pulse">
    <!-- Image skeleton -->
    <div class="h-48 bg-gray-300 dark:bg-gray-700"></div>
    
    <!-- Content skeleton -->
    <div class="p-4 space-y-3">
      <!-- Title -->
      <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
      
      <!-- Price -->
      <div class="h-6 bg-gray-300 dark:bg-gray-700 rounded w-1/2"></div>
      
      <!-- Description -->
      <div class="space-y-2">
        <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded"></div>
        <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-5/6"></div>
      </div>
      
      <!-- Footer -->
      <div class="flex items-center justify-between pt-2">
        <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-20"></div>
        <div class="h-8 bg-gray-300 dark:bg-gray-700 rounded w-24"></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// No props needed for skeleton
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
