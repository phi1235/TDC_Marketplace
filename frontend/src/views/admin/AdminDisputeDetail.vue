<template>
  <div class="p-6 max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">⚖️ Chi tiết khiếu nại #{{ dispute?.id }}</h1>

    <div v-if="loading" class="text-gray-500 italic">Đang tải dữ liệu...</div>
    <div v-else-if="error" class="text-red-600">{{ error }}</div>
    <div v-else>
      <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
        <div class="grid grid-cols-2 gap-6">
          <div>
            <h2 class="font-semibold text-gray-700 mb-2">📄 Thông tin</h2>
            <p><strong>Trạng thái:</strong>
              <span :class="statusColor(dispute.status)" class="px-2 py-1 rounded-full text-xs font-semibold">
                {{ statusText(dispute.status) }}
              </span>
            </p>
            <p><strong>Ngày tạo:</strong> {{ formatDate(dispute.created_at) }}</p>
          </div>

          <div>
            <h2 class="font-semibold text-gray-700 mb-2">💬 Lý do khiếu nại</h2>
            <p class="text-gray-800 bg-gray-50 p-3 rounded border border-gray-200 whitespace-pre-line">
              {{ dispute.reason || 'Không có lý do' }}
            </p>
          </div>
        </div>

        <!-- Người mở -->
        <div class="mt-6">
          <h2 class="font-semibold text-gray-700 mb-2">👤 Người mở khiếu nại</h2>
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p><strong>Tên:</strong> {{ dispute.opener?.name }}</p>
            <p><strong>Email:</strong> {{ dispute.opener?.email }}</p>
          </div>
        </div>

        <!-- Người bị khiếu nại -->
        <div class="mt-6">
          <h2 class="font-semibold text-gray-700 mb-2">⚠️ Người bị khiếu nại</h2>
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p><strong>Tên:</strong> {{ dispute.against_user?.name }}</p>
            <p><strong>Email:</strong> {{ dispute.against_user?.email }}</p>
          </div>
        </div>

        <!-- Sản phẩm -->
        <div class="mt-6">
          <h2 class="font-semibold text-gray-700 mb-2">📦 Sản phẩm liên quan</h2>
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p><strong>Tiêu đề:</strong> {{ dispute.listing?.title }}</p>
          </div>
        </div>

        <!-- Form xử lý -->
        <div class="mt-8 border-t pt-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-3">🛠️ Xử lý khiếu nại</h2>

          <select
            v-model="newStatus"
            class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
          >
            <option value="">-- Chọn hành động --</option>
            <option value="under_review">Đang xử lý</option>
            <option value="resolved">Đã giải quyết</option>
            <option value="rejected">Từ chối</option>
            <option value="closed">Đóng</option>
          </select>

          <button
            @click="updateStatus"
            :disabled="updating"
            class="ml-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
          >
            {{ updating ? 'Đang cập nhật...' : 'Cập nhật' }}
          </button>
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
const error = ref('')
const updating = ref(false)
const newStatus = ref('')

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

async function loadDispute() {
  try {
    const res = await axios.get(`/api/admin/disputes/${route.params.id}`, {
      headers: { Authorization: `Bearer ${getToken()}` }
    })
    dispute.value = res.data
  } catch (err: any) {
    error.value = err?.response?.data?.message || 'Không thể tải khiếu nại.'
  } finally {
    loading.value = false
  }
}

async function updateStatus() {
  if (!newStatus.value) return showToast('error', 'Vui lòng chọn trạng thái.')

  updating.value = true
  try {
    await axios.put(
      `/api/admin/disputes/${route.params.id}`,
      { status: newStatus.value },
      { headers: { Authorization: `Bearer ${getToken()}` } }
    )
    showToast('success', 'Cập nhật trạng thái thành công!')
    router.push('/dashboard/disputes')
  } catch (err: any) {
    showToast('error', err?.response?.data?.message || 'Không thể cập nhật.')
  } finally {
    updating.value = false
  }
}

onMounted(loadDispute)
</script>
