<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
      <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Chi tiết khiếu nại #{{ dispute?.id }}</h1>
        <router-link
          to="/my-disputes"
          class="px-3 py-2 rounded-md border text-gray-700 hover:bg-gray-100"
        >Quay lại</router-link>
      </div>

      <div v-if="loading" class="text-gray-500 italic">Đang tải...</div>
      <div v-else-if="error" class="text-red-600">{{ error }}</div>

      <div v-else-if="dispute" class="space-y-6">
        <!-- Thông tin chung -->
        <div class="bg-white border rounded-lg p-5">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">Đơn hàng</p>
              <p class="font-mono text-blue-600">{{ dispute.order?.order_number || '—' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Sản phẩm</p>
              <p class="text-gray-900">{{ dispute.listing?.title || '—' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Người mở</p>
              <p class="text-gray-900">{{ dispute.opener?.name || '—' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Người bị khiếu nại</p>
              <p class="text-gray-900">{{ dispute.against_user?.name || '—' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Trạng thái</p>
              <span :class="badgeClass(dispute.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ statusText(dispute.status) }}
              </span>
            </div>
            <div>
              <p class="text-sm text-gray-500">Ngày tạo</p>
              <p class="text-gray-900">{{ formatDate(dispute.created_at) }}</p>
            </div>
          </div>

          <div class="mt-5">
            <p class="text-sm text-gray-500 mb-1">Lý do khiếu nại</p>
            <div class="bg-gray-50 border rounded p-3 whitespace-pre-line text-gray-800">
              {{ dispute.reason }}
            </div>
          </div>

          <div v-if="dispute.admin_note" class="mt-5">
            <p class="text-sm text-gray-500 mb-1">Ghi chú từ Admin</p>
            <div class="bg-blue-50 border border-blue-200 rounded p-3 text-blue-900">
              {{ dispute.admin_note }}
            </div>
          </div>
        </div>

        <!-- Hành động cho user -->
        <div class="bg-white border rounded-lg p-5">
          <h3 class="font-semibold text-gray-900 mb-3">Hành động</h3>

          <div v-if="['open','under_review'].includes(dispute.status)" class="flex gap-3">
            <button
              @click="closeDispute"
              :disabled="closing"
              class="px-4 py-2 rounded-md bg-gray-800 text-white hover:bg-gray-900 disabled:opacity-60"
            >
              {{ closing ? 'Đang đóng...' : 'Đóng khiếu nại' }}
            </button>
          </div>

          <p v-else class="text-gray-600">
            Khiếu nại đã kết thúc ({{ statusText(dispute.status) }}).
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { showToast } from '@/utils/toast'

const route = useRoute()
const router = useRouter()

const dispute = ref<any>(null)
const loading = ref(true)
const closing = ref(false)
const error = ref('')

const getToken = () => localStorage.getItem('auth_token') || ''

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

const load = async () => {
  loading.value = true
  try {
    const res = await axios.get(`/api/disputes/${route.params.id}`, {
      headers: { Authorization: `Bearer ${getToken()}` }
    })
    dispute.value = res.data
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Không thể tải chi tiết khiếu nại'
  } finally {
    loading.value = false
  }
}

const closeDispute = async () => {
  if (!confirm('Bạn chắc chắn muốn đóng khiếu nại này?')) return
  closing.value = true
  try {
    const res = await axios.post(
      `/api/disputes/${route.params.id}/close`,
      {},
      { headers: { Authorization: `Bearer ${getToken()}` } }
    )
    showToast('success', res.data?.message || 'Đã đóng khiếu nại')
    await load()
  } catch (e: any) {
    showToast('error', e?.response?.data?.message || 'Không thể đóng khiếu nại')
  } finally {
    closing.value = false
  }
}

onMounted(load)
</script>
