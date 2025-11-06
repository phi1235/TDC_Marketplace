<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold mb-4">Thông báo của bạn</h1>

    <div v-if="loading" class="text-gray-500">Đang tải...</div>

    <div v-else>
      <div v-if="notifications.length === 0" class="text-gray-500">Chưa có thông báo nào</div>

      <ul v-else class="space-y-4">
        <li v-for="n in notifications" :key="n.id" class="bg-white shadow p-4 rounded-md">
          <h2 class="font-semibold text-lg">{{ n.title }}</h2>
          <p class="text-gray-700">{{ n.message }}</p>
          <p class="text-sm text-gray-500 mt-2">{{ formatDate(n.created_at) }}</p>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { userNotificationsService } from '@/services/userNotifications'

const notifications = ref([])
const loading = ref(true)

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleString('vi-VN')
}

onMounted(async () => {
  const res = await userNotificationsService.list()
  notifications.value = res.data
  loading.value = false
})
</script>
