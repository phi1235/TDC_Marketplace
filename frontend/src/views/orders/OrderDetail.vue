<template>
  <div class="min-h-screen bg-gray-50 py-10">
    <div class="container mx-auto px-4 max-w-6xl">
      <!-- Tiêu đề -->
      <h1 class="text-3xl font-bold text-gray-900 mb-8 border-b pb-3">Chi tiết đơn hàng</h1>

      <!-- Trạng thái tải -->
      <div v-if="loading" class="text-gray-500 italic text-center py-10">Đang tải thông tin đơn hàng...</div>
      <div v-else-if="error" class="text-red-600 text-center py-10">{{ error }}</div>

      <!-- Nội dung -->
      <div v-else-if="order" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cột trái: Ảnh + thông tin đơn -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl shadow-sm p-6">
          <!-- Ảnh sản phẩm -->
          <div class="w-full h-80 rounded-lg overflow-hidden mb-6">
            <img
              v-if="order.listing?.images?.length"
              :src="order.listing.images[0].full_url"
              alt="Ảnh sản phẩm"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400">
              Không có ảnh sản phẩm
            </div>
          </div>

          <!-- Thông tin đơn hàng -->
          <div class="space-y-3">
            <p class="text-gray-700">
              <strong class="text-gray-900">Mã đơn: </strong>
              <span class="font-mono text-blue-600">{{ order.order_number }}</span>
            </p>
            <p><strong>Sản phẩm:</strong> {{ order.product_title || 'Không có tiêu đề' }}</p>
            <p><strong>Giá:</strong> {{ formatPrice(order.total_amount || order.product_price) }}</p>
            <p><strong>Ngày đặt hàng:</strong> {{ formatDate(order.created_at) }}</p>
            <p><strong>Trạng thái:</strong> 
              <span
                class="px-3 py-1 rounded-full text-sm font-medium"
                :class="statusColor(order.status)"
              >
                {{ getStatusText(order.status) }}
              </span>
            </p>

            <!-- Địa chỉ giao hàng -->
            <div v-if="order.shipping_address" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-3">
              <h3 class="font-semibold text-gray-800 mb-1">📦 Địa chỉ giao hàng</h3>
              <p class="text-gray-700">{{ order.shipping_address }}</p>
            </div>
          </div>

          <!-- Khiếu nại
          <div class="mt-8 border-t border-gray-200 pt-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">⚠️ Khiếu nại giao dịch</h2>
            <p class="text-sm text-gray-600 mb-3">
              Nếu bạn gặp sự cố trong giao dịch này, bạn có thể gửi khiếu nại để được hỗ trợ.
            </p>
            <button
              v-if="['delivered', 'completed'].includes(order.status)"
              @click="showModal = true"
              class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 active:scale-95 transition-all flex items-center gap-2"
            >
              🧾 Gửi khiếu nại
            </button>
            <p v-else class="text-gray-500 text-sm italic">
              Khiếu nại chỉ khả dụng sau khi đơn đã giao hoặc hoàn tất.
            </p>
          </div> -->
        </div>

        <!-- Cột phải: Người bán & hành động -->
        <div class="space-y-6">
          <!-- Thông tin người bán -->
          <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
              🧍‍♂️ Thông tin người bán
            </h3>
            <div class="flex items-center gap-3 mb-3">
              <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-lg font-bold">
                {{ getInitials(order.seller?.name) }}
              </div>
              <div>
                <p class="font-semibold text-gray-900">{{ order.seller?.name || 'Không có tên' }}</p>
                <p class="text-sm text-gray-500">{{ order.seller?.email || 'Không có email' }}</p>
              </div>
            </div>
            <p><strong>Số điện thoại:</strong> {{ order.seller?.phone || 'Không có dữ liệu' }}</p>
          </div>

        <!-- Thanh toán -->
<div
  v-if="order.status !== 'completed'"
  class="bg-white border border-gray-200 rounded-xl shadow-sm p-5"
>
  <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
    💳 Thanh toán
  </h3>
  <div class="flex flex-col gap-3">
    <button
      v-if="order.status === 'pending'"
      @click="payOrder"
      :disabled="paying"
      class="w-full px-4 py-3 text-white rounded-lg shadow transition-all duration-200 flex items-center justify-center gap-2"
      :class="[paying ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 active:scale-95']"
    >
      <svg
        v-if="paying"
        class="animate-spin h-5 w-5 text-white"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
        ></path>
      </svg>
      <span>{{ paying ? 'Đang xử lý...' : 'Thanh toán ngay' }}</span>
    </button>

    <p
      v-if="order.status === 'paid'"
      class="text-green-700 font-semibold text-center"
    >
      ✅ Đã thanh toán – Tiền đang được giữ an toàn.
    </p>
  </div>
</div>
<!-- Khiếu nại giao dịch -->
<div
  v-if="order.status === 'delivered' || order.status === 'completed'"
  class="bg-white border border-gray-200 rounded-xl shadow-sm p-5"
>
  <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
    ⚠️ Khiếu nại giao dịch
  </h3>
  <p class="text-sm text-gray-600 mb-4">
    Nếu bạn gặp sự cố trong giao dịch này, hãy gửi khiếu nại để được hỗ trợ bởi ban quản trị.
  </p>
  <button
    @click="showModal = true"
    class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 active:scale-95 transition-all flex items-center justify-center gap-2"
  >
    🧾 Gửi khiếu nại
  </button>
</div>

<div
  v-else
  class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 text-gray-500 italic text-sm"
>
  ⚠️ Khiếu nại chỉ khả dụng sau khi đơn hàng được giao hoặc hoàn tất.
</div>
        </div>
      </div>
    </div>

    <!-- Modal khiếu nại -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg animate-fadeIn">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">🧾 Gửi khiếu nại</h2>
        <textarea
          v-model="disputeReason"
          rows="4"
          placeholder="Nhập lý do khiếu nại (tối thiểu 20 ký tự)..."
          class="w-full border rounded-lg p-3 text-gray-700 focus:ring-2 focus:ring-red-400 focus:outline-none"
        ></textarea>
        <div class="mt-5 flex justify-end gap-3">
          <button @click="showModal = false" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
            Hủy
          </button>
          <button
            @click="submitDispute"
            :disabled="loadingDispute"
            class="px-5 py-2 rounded-lg text-white flex items-center justify-center gap-2 shadow transition-all duration-200"
            :class="loadingDispute ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 active:scale-95'"
          >
            <svg
              v-if="loadingDispute"
              class="animate-spin h-5 w-5 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
              ></path>
            </svg>
            <span>{{ loadingDispute ? 'Đang gửi...' : 'Gửi khiếu nại' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { showToast } from '@/utils/toast'

const route = useRoute()
const order = ref<any>(null)
const loading = ref(true)
const error = ref('')
const paying = ref(false)
const showModal = ref(false)
const disputeReason = ref('')
const loadingDispute = ref(false)

function getToken() {
  return localStorage.getItem('auth_token') || ''
}

function formatPrice(price: number) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Number(price || 0))
}

function formatDate(date: string) {
  if (!date) return 'Không có dữ liệu'
  const d = new Date(date)
  if (isNaN(d.getTime())) return 'Không có dữ liệu'
  return d.toLocaleString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function getStatusText(status: string) {
  const map: Record<string, string> = {
    pending: 'Chờ thanh toán',
    confirmed: 'Đã xác nhận',
    paid: 'Đã thanh toán',
    shipped: 'Đang giao',
    delivered: 'Đã giao',
    completed: 'Hoàn tất'
  }
  return map[status] || 'Không xác định'
}

function statusColor(status: string) {
  const map: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    paid: 'bg-green-100 text-green-800',
    shipped: 'bg-indigo-100 text-indigo-800',
    delivered: 'bg-cyan-100 text-cyan-800',
    completed: 'bg-gray-100 text-gray-700'
  }
  return map[status] || 'bg-gray-100 text-gray-600'
}

function getInitials(name: string = '') {
  return name ? name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : 'U'
}

async function loadOrder() {
  loading.value = true
  try {
    const res = await axios.get(`/api/orders/${route.params.id}`, {
      headers: { Authorization: `Bearer ${getToken()}` }
    })
    order.value = res.data.order || res.data
  } catch (err: any) {
    error.value = err?.response?.data?.message || 'Không thể tải đơn hàng'
  } finally {
    loading.value = false
  }
}

async function payOrder() {
  paying.value = true
  try {
    const res = await axios.post(
      `/api/orders/${route.params.id}/escrow-pay`,
      {},
      { headers: { Authorization: `Bearer ${getToken()}`, Accept: 'application/json' } }
    )
    order.value.status = 'paid'
    showToast('success', res.data.message || 'Thanh toán thành công!')
  } catch (err: any) {
    const msg = err?.response?.data?.message || 'Không thể thanh toán đơn hàng.'
    showToast('error', msg)
  } finally {
    paying.value = false
  }
}

async function submitDispute() {
  if (!disputeReason.value || disputeReason.value.trim().length < 20) {
    showToast('error', '⚠️ Lý do khiếu nại phải có ít nhất 20 ký tự.')
    return
  }
  loadingDispute.value = true
  try {
    const res = await axios.post(
      '/api/disputes',
      { listing_id: order.value.listing_id, reason: disputeReason.value },
      { headers: { Authorization: `Bearer ${getToken()}`, Accept: 'application/json' } }
    )
    showToast('success', res.data.message || '🎫 Khiếu nại đã được gửi thành công!')
    showModal.value = false
    disputeReason.value = ''
  } catch (err: any) {
    const msg = err?.response?.data?.message || 'Không thể gửi khiếu nại.'
    showToast('error', msg)
  } finally {
    loadingDispute.value = false
  }
}

onMounted(loadOrder)
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
