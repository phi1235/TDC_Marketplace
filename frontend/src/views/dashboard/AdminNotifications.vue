<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold mb-4">Quản lý thông báo</h1>

    <!-- Form tạo thông báo -->
    <form @submit.prevent="createNotification" class="bg-white p-4 rounded-lg shadow-md mb-6">
      <h2 class="text-lg font-semibold mb-3">Tạo thông báo mới</h2>

      <div class="mb-3">
        <label class="block text-gray-700 mb-1">Người nhận (User)</label>
        <select v-model="form.user_id" class="border rounded px-3 py-2 w-full">
          <option value="">Gửi tất cả người dùng</option>
          <option v-for="user in users" :key="user.id" :value="user.id">
            {{ user.name }}
          </option>
        </select>
      </div>

      <!-- Ẩn ô nhập loại, vì mặc định là "system" -->
      <input type="hidden" v-model="form.type" />

      <div class="mb-3">
        <label class="block text-gray-700 mb-1">Tiêu đề</label>
        <input
          v-model="form.title"
          type="text"
          class="border rounded px-3 py-2 w-full"
          placeholder="Nhập tiêu đề"
        />
      </div>

      <div class="mb-3">
        <label class="block text-gray-700 mb-1">Nội dung thông báo</label>
        <textarea
          v-model="form.message"
          class="border rounded px-3 py-2 w-full"
          rows="3"
          placeholder="Nhập nội dung"
        ></textarea>
      </div>

      <button
        type="submit"
        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded"
        :disabled="loading"
      >
        {{ loading ? 'Đang gửi...' : 'Gửi thông báo' }}
      </button>
    </form>

    <!-- Danh sách thông báo -->
    <div class="bg-white p-4 rounded-lg shadow-md">
      <h2 class="text-lg font-semibold mb-3">Danh sách thông báo đã gửi</h2>

      <div v-if="loadingList">Đang tải...</div>

      <table v-else class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-100 text-left">
            <th class="p-2 border">ID</th>
            <th class="p-2 border">User</th>
            <th class="p-2 border">Tiêu đề</th>
            <th class="p-2 border">Nội dung</th>
            <th class="p-2 border">Thời gian</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="n in notifications" :key="n.id" class="hover:bg-gray-50">
            <td class="p-2 border">{{ n.id }}</td>
            <td class="p-2 border">{{ n.user?.name || 'User ' + n.user_id }}</td>
            <td class="p-2 border">{{ n.title }}</td>
            <td class="p-2 border">{{ n.message }}</td>
            <td class="p-2 border text-sm text-gray-500">
              {{ formatDate(n.created_at) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>


<script setup>
import api from '@/services/api'
import { ref, onMounted } from 'vue'
import { adminNotificationsService } from '@/services/adminNotifications'
import { showToast } from '@/utils/toast'

const loading = ref(false)
const loadingList = ref(false)
const notifications = ref([])
const form = ref({
    user_id: '',
    type: 'system',
    title: '',
    message: '',
})

async function fetchNotifications() {
    const res = await adminNotificationsService.list()
    notifications.value = res.data
}

async function createNotification() {
    await adminNotificationsService.create(form.value)
    showToast('Tạo thông báo thành công', 'success')
    await fetchNotifications()

    
}
const users = ref([])

onMounted(async () => {
  const res = await api.get('/users') // route api lấy danh sách user
  users.value = res.data.data
})

// Hàm format ngày cho dễ đọc
function formatDate(dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleString('vi-VN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}
 
</script>
