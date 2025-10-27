<template>
  <div class="animate-pulse space-y-4">
    <div
      v-for="n in count"
      :key="n"
      class="bg-gray-200 dark:bg-gray-700 rounded-lg h-6"
      :style="{ width: widths[n % widths.length] }"
    ></div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  count: { type: Number, default: 5 },
  widths: {
    type: Array as () => string[],
    default: () => ['100%', '80%', '90%', '70%', '95%']
  }
})
</script>

<style scoped>
.animate-pulse {
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 0.6;
  }
  50% {
    opacity: 1;
  }
}
</style>
