<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-5xl">
      <h1 class="text-2xl font-bold text-gray-900 mb-6">⚖️ Khiếu nại của tôi</h1>

      <div v-if="loading" class="text-gray-500 italic">Đang tải...</div>
      <div v-else-if="error" class="text-red-600">{{ error }}</div>

      <div v-else>
        <div v-if="disputes.length === 0" class="bg-white border rounded-lg p-6 text-gray-600">
          Bạn chưa có khiếu nại nào.
        </div>

        <div v-else class="bg-white border rounded-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  <th class="px-4 py-3">#</th>
                  <th class="px-4 py-3">Đơn hàng</th>
                  <th class="px-4 py-3">Sản phẩm</th>
                  <th class="px-4 py-3">Đối tượng</th>
                  <th class="px-4 py-3">Trạng thái</th>
                  <th class="px-4 py-3">Ngày tạo</th>
                  <th class="px-4 py-3">Hành động</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="d in disputes" :key="d.id" class="text-sm">
                  <td class="px-4 py-3 text-gray-700">#{{ d.id }}</td>
                  <td class="px-4 py-3 font-mono text-blue-600">
                    {{ d.order?.order_number || '—' }}
                  </td>
                  <td class="px-4 py-3">{{ d.listing?.title || '—' }}</td>
                  <td class="px-4 py-3">
                    <span class="text-gray-700">{{ d.against_user?.name || '—' }}</span>
                  </td>
                  <td class="px-4 py-3">
                    <span :class="badgeClass(d.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                      {{ statusText(d.status) }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-gray-600">{{ formatDate(d.created_at) }}</td>
                  <td class="px-4 py-3">
                    <router-link
                      :to="`/my-disputes/${d.id}`"
                      class="inline-flex items-center px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700"
                    >
                      Xem
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

const disputes = ref<any[]>([])
const loading = ref(true)
const error = ref('')

const statusText = (s: string) => ({
  open: 'Mở',
  under_review: 'Đang xử lý',
  resolved: 'Đã giải quyết',
  rejected: 'Bị từ chối',
  closed: 'Đã đóng'
}[s] || s)

const badgeClass = (s: string) => ({
  open: 'bg-yellow-100 text-yellow-800',
  under_review: 'bg-blue-100 text-blue-800',
  resolved: 'bg-green-100 text-green-800',
  rejected: 'bg-red-100 text-red-800',
  closed: 'bg-gray-100 text-gray-700'
}[s] || 'bg-gray-100 text-gray-700')

const formatDate = (d?: string) =>
  d ? new Date(d).toLocaleString('vi-VN') : '—'

const getToken = () => localStorage.getItem('auth_token') || ''

const load = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/disputes', {
      headers: { Authorization: `Bearer ${getToken()}` }
    })
    // API trả mảng (index dùng ->orWhere). Chuẩn hóa vài field để hiển thị
    disputes.value = (res.data || []).map((x: any) => ({
      ...x,
      against_user: x.against_user ?? x.againstUser ?? x.against_user_id, // tùy eager load
    }))
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Không thể tải danh sách khiếu nại'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>
