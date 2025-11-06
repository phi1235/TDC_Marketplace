<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">⚖️ Quản lý khiếu nại</h1>

    <div v-if="loading" class="text-gray-500 italic">Đang tải dữ liệu...</div>
    <div v-else-if="error" class="text-red-600">{{ error }}</div>

    <div v-else class="overflow-x-auto bg-white border rounded-lg shadow-sm">
      <table class="min-w-full divide-y divide-gray-200">
       <thead class="bg-gray-100">
  <tr>
    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Người mở</th>
    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Người bị khiếu nại</th>
    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Sản phẩm</th>
    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">📝 Lý do</th> 
    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Ngày tạo</th>
    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Hành động</th>
    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nút</th>

  </tr>
</thead>

<tbody class="divide-y divide-gray-100">
  <tr v-for="d in disputes" :key="d.id" class="hover:bg-gray-50 transition">
    <td class="px-4 py-2 text-sm text-gray-800">#{{ d.id }}</td>
    <td class="px-4 py-2 text-sm text-gray-800">{{ d.opener?.name || '—' }}</td>
    <td class="px-4 py-2 text-sm text-gray-800">{{ d.against_user?.name || '—' }}</td>
    <td class="px-4 py-2 text-sm text-gray-800">{{ d.listing?.title || '—' }}</td>
    <td class="px-4 py-2 text-sm text-gray-600 line-clamp-2 max-w-xs">{{ d.reason }}</td> 
    <td class="px-4 py-2 text-sm text-gray-600">{{ formatDate(d.created_at) }}</td>
    <td class="px-4 py-2">
      <span :class="statusColor(d.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
        {{ statusText(d.status) }}
      </span>
    </td>
    <td class="px-4 py-2 text-left">
      <button
        @click.stop="goDetail(d.id)"
        class="px-3 py-1 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg"
      >
        Xem
      </button>
    </td>
  </tr>
</tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { showToast } from '@/utils/toast'

const router = useRouter()
const disputes = ref<any[]>([])
const loading = ref(true)
const error = ref('')

function getToken() {
  return localStorage.getItem('auth_token') || ''
}

function formatDate(date: string) {
  return new Date(date).toLocaleString('vi-VN')
}

function statusText(status: string) {
  const map: Record<string, string> = {
    open: 'Mở',
    under_review: 'Đang xử lý',
    resolved: 'Đã giải quyết',
    rejected: 'Từ chối',
    closed: 'Đã đóng'
  }
  return map[status] || status
}

function statusColor(status: string) {
  const colors: Record<string, string> = {
    open: 'bg-yellow-100 text-yellow-800',
    under_review: 'bg-blue-100 text-blue-800',
    resolved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    closed: 'bg-gray-100 text-gray-700'
  }
  return colors[status] || 'bg-gray-100 text-gray-700'
}

async function loadDisputes() {
  loading.value = true
  try {
    const res = await axios.get('/api/admin/disputes', {
      headers: { Authorization: `Bearer ${getToken()}` }
    })
    disputes.value = res.data.data || res.data
  } catch (err: any) {
    error.value = err?.response?.data?.message || 'Không thể tải khiếu nại.'
    showToast('error', error.value)
  } finally {
    loading.value = false
  }
}

function goDetail(id: number) {
  router.push(`/dashboard/disputes/${id}`)
}

onMounted(loadDisputes)
</script>
