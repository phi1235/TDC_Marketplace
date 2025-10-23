<template>
  <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6" aria-label="Breadcrumb">
    <router-link
      to="/"
      class="hover:text-blue-600 transition-colors flex items-center"
    >
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
      </svg>
      Trang chủ
    </router-link>

    <template v-for="(item, index) in items" :key="index">
      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
      
      <router-link
        v-if="item.to && index < items.length - 1"
        :to="item.to"
        class="hover:text-blue-600 transition-colors"
      >
        {{ item.label }}
      </router-link>
      
      <span
        v-else
        class="font-medium text-gray-900 truncate max-w-xs"
        :class="{ 'cursor-default': index === items.length - 1 }"
      >
        {{ item.label }}
      </span>
    </template>
  </nav>
</template>

<script setup lang="ts">
interface BreadcrumbItem {
  label: string
  to?: string
}

interface Props {
  items: BreadcrumbItem[]
}

defineProps<Props>()
</script>

