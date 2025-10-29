<template>
  <div class="min-h-screen bg-gray-50 py-10">
    <div
      class="container mx-auto px-4 max-w-3xl bg-white rounded-lg shadow-sm border border-gray-200 p-6 transition-all duration-300"
      :class="{ 'opacity-50 pointer-events-none': paying }"
    >
      <h1 class="text-2xl font-bold text-gray-900 mb-4">Chi tiết đơn hàng</h1>

      <div v-if="loading" class="text-gray-500 italic">Đang tải thông tin...</div>
      <div v-else-if="error" class="text-red-600">{{ error }}</div>

      <div v-else-if="order" class="space-y-2">
        <p><strong>Mã đơn:</strong> {{ order.order_number }}</p>
        <p><strong>Sản phẩm:</strong> {{ order.product_title }}</p>
<div class="p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg border border-green-200 mt-4 shadow-sm">
  <h2 class="font-semibold text-green-700 mb-2 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>
    Thông tin người bán
  </h2>

  <p><strong>Tên:</strong> {{ order.seller?.name || 'Không có dữ liệu' }}</p>
  <p><strong>Email:</strong> {{ order.seller?.email || 'Không có dữ liệu' }}</p>
  <p><strong>Số điện thoại:</strong> {{ order.seller?.phone || 'Không có dữ liệu' }}</p>
</div>        <p><strong>Giá:</strong> {{ formatPrice(order.total_amount) }}</p>
        <p><strong>Trạng thái:</strong> {{ getStatusText(order.status) }}</p>

        <div class="mt-6">
          <!-- Nút thanh toán -->
          <button
            v-if="order.status === 'confirmed'"
            @click="payOrder"
            :disabled="paying"
            class="relative px-6 py-3 text-white rounded-lg transition-all duration-300 ease-in-out shadow-md focus:outline-none"
            :class="[
              paying
                ? 'bg-gray-400 cursor-not-allowed'
                : 'bg-green-600 hover:bg-green-700 active:scale-95'
            ]"
          >
            <span v-if="!paying" class="flex items-center justify-center gap-2">
              💳 Thanh toán ngay
            </span>

            <!-- Loading spinner -->
            <span v-else class="flex items-center justify-center gap-2">
              <svg
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
              Đang xử lý...
            </span>
          </button>

          <!-- Đã thanh toán -->
          <transition name="fade">
            <p
              v-if="order.status === 'paid'"
              class="text-green-700 font-semibold mt-3 flex items-center gap-2"
            >
              ✅ <span>Thanh toán thành công! Tiền đang được giữ an toàn.</span>
            </p>
          </transition>
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
const paying = ref(false) // trạng thái loading khi thanh toán

function getToken() {
  return localStorage.getItem('auth_token') || ''
}

function formatPrice(price: number) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Number(price || 0))
}

function getStatusText(status: string) {
  const map: Record<string, string> = {
    pending: 'Chờ xác nhận',
    confirmed: 'Đã xác nhận',
    paid: 'Đã thanh toán',
    shipped: 'Đang giao',
    delivered: 'Đã giao',
    completed: 'Hoàn tất',
  }
  return map[status] || status
}

async function loadOrder() {
  loading.value = true
  try {
    const res = await axios.get(`/api/orders/${route.params.id}`, {
      headers: { Authorization: `Bearer ${getToken()}` },
    })
    order.value = res.data
  } catch (err: any) {
    error.value = err?.response?.data?.message || 'Không thể tải thông tin đơn hàng'
  } finally {
    loading.value = false
  }
}

async function payOrder() {
  if (!order.value) return
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
    console.error('Thanh toán thất bại:', err?.response || err)
  } finally {
    paying.value = false
  }
}

onMounted(loadOrder)
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
