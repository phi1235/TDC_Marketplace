<template>
  <div class="p-6 max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Thông báo hệ thống</h1>
<div v-if="loading" class="text-center text-gray-500">Đang tải...</div>

<div v-else>
  <div v-for="item in notifications" :key="item.id" class="bg-white shadow-md rounded-lg mb-4 p-4 border border-gray-200 hover:shadow-lg transition">
    <!-- Title -->
    <h2 class="font-semibold text-lg mb-2 text-gray-800">{{ item.title }}</h2>
    
    <!-- Content -->
    <p class="text-gray-700 mb-3">
      {{ shortText(item.message, 200) }}
      <router-link v-if="item.message.length > 200" :to="`/detailNotification/${item.id}`" class="text-blue-600 hover:underline ml-1">Xem thêm</router-link>
    </p>

    <!-- Footer -->
    <div class="flex justify-between items-center text-gray-400 text-sm">
      <span>{{ formatDate(item.created_at) }}</span>
      <!-- Nếu sau này muốn nút xóa cho admin, có thể thêm ở đây -->
    </div>
  </div>
</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { userNotificationsService } from '@/services/userNotifications'

const notifications = ref([])
const loading = ref(true)

async function fetchNotifications() {
  loading.value = true
  try {
    const res = await userNotificationsService.list()
    notifications.value = res.data.filter(n => n.type === 'system')
  } catch (err) {
    console.error('Lỗi lấy thông báo:', err)
    notifications.value = []
  } finally {
    loading.value = false
  }
}

function shortText(text, limit = 150) {
  if (!text) return ''
  return text.length > limit ? text.slice(0, limit) : text
}

function formatDate(dateStr) {
  const date = new Date(dateStr)
  return date.toLocaleDateString('vi-VN', { year: 'numeric', month: '2-digit', day: '2-digit' })
}

onMounted(() => {
  fetchNotifications()
})
</script>
