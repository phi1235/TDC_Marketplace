<template>
  <div class="min-h-screen bg-gray-50 py-10">
    <div class="container mx-auto px-4 max-w-5xl">
      <!-- Tiêu đề -->
      <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6H19a2 2 0 100-4H8.1M7 13L5.4 5M16 21a1 1 0 11-2 0 1 1 0z" />
        </svg>
        Đơn hàng của tôi
      </h1>

      <!-- Tabs -->
      <div class="flex space-x-3 mb-6">
        <button @click="activeTab = 'buyer'" :class="tabClass('buyer')">Tôi là người mua</button>
        <button @click="activeTab = 'seller'" :class="tabClass('seller')">Tôi là người bán</button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-gray-500 italic">Đang tải danh sách đơn hàng...</div>

      <!-- Error -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 p-4 rounded-lg text-red-700">
        {{ error }}
      </div>

      <!-- Buyer list -->
      <div v-else-if="activeTab === 'buyer'">
        <div v-if="buyerOrders.length === 0" class="text-gray-500 italic">Bạn chưa có đơn hàng nào.</div>
        <div v-else class="grid gap-4">
          <div v-for="order in buyerOrders" :key="order.id" @click="goToDetail(order.id)"
            class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between hover:shadow-md hover:bg-gray-50 transition cursor-pointer">
            <div>
              <h3 class="font-semibold text-gray-900">{{ order.product_title }}</h3>
              <p class="text-sm text-gray-600">Mã đơn: {{ order.order_number }}</p>
              <p class="text-sm text-gray-600">Giá: {{ formatPrice(order.total_amount) }}</p>
            </div>

            <div class="mt-3 sm:mt-0 flex items-center gap-3">
              <span :class="getStatusClass(order.status)">
                {{ getStatusText(order.status) }}
              </span>

              <button v-if="order.status === 'shipped'" @click.stop="markDelivered(order.id)"
                class="px-3 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                Tôi đã nhận hàng
              </button>

              <button v-if="order.status === 'delivered'" @click.stop="completeOrder(order.id)"
                class="px-3 py-2 text-sm bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition">
                Hoàn tất đơn hàng
              </button>

              <button v-if="order.status === 'completed'" :disabled="order.has_rated"
                @click.stop="openRateModal(order.id)" class="px-3 py-2 text-sm rounded-md transition" :class="order.has_rated
                  ? 'bg-gray-300 text-gray-600 cursor-not-allowed'
                  : 'bg-yellow-500 text-white hover:bg-yellow-600'">
                {{ order.has_rated ? 'Đã đánh giá' : 'Đánh giá người bán' }}
              </button>

            </div>
          </div>
        </div>
      </div>

      <!-- Seller list -->
      <div v-else>
        <div v-if="sellerOrders.length === 0" class="text-gray-500 italic">
          Chưa có đơn hàng nào cần bạn xử lý.
        </div>
        <div v-else class="grid gap-4">
          <div v-for="order in sellerOrders" :key="order.id"  @click="goToDetail(order.id)"
            class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between hover:shadow-md hover:bg-gray-50 transition">
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900">{{ order.product_title }}</h3>
              <p class="text-sm text-gray-600">Mã đơn: {{ order.order_number }}</p>
              <p class="text-sm text-gray-600">Giá: {{ formatPrice(order.total_amount) }}</p>
              <p class="text-sm text-gray-600">Người mua ID: {{ order.buyer_id }}</p>
            </div>

            <div class="mt-3 sm:mt-0 flex items-center gap-3">
              <span :class="getStatusClass(order.status)">
                {{ getStatusText(order.status) }}
              </span>

              <button v-if="order.status === 'paid' && !confirmingIds.has(order.id)" @click="confirmOrder(order.id)"
                class="px-3 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                Xác nhận
              </button>

              <button v-else-if="order.status === 'pending' && confirmingIds.has(order.id)" disabled
                class="px-3 py-2 text-sm bg-green-600/70 text-white rounded-md cursor-wait">
                Đang xác nhận...
              </button>

              <button v-if="order.status === 'confirmed'" @click.stop="shipOrder(order.id)"
                class="px-3 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                Đánh dấu đang giao
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ✅ Modal Đánh giá -->
      <RateUserModal :is-open="showRateModal" :order-id="selectedOrderId" @close="showRateModal = false"
        @submitted="loadOrders" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { showToast } from '@/utils/toast'
import { useRouter } from 'vue-router'
import RateUserModal from '@/components/orders/RateUserModal.vue'

const router = useRouter()

const activeTab = ref<'buyer' | 'seller'>('buyer')
const buyerOrders = ref<any[]>([])
const sellerOrders = ref<any[]>([])
const loading = ref(true)
const error = ref('')
const confirmingIds = ref<Set<number>>(new Set())

// ✅ Thêm 2 biến và hàm mở modal
const showRateModal = ref(false)
const selectedOrderId = ref<number | null>(null)
function openRateModal(id: number) {
  selectedOrderId.value = id
  showRateModal.value = true
}

function goToDetail(orderId: number) {
  router.push({
    path: `/orders/${orderId}`,
    query: { role: activeTab.value } // 👈 thêm dòng này
  })
}
function getToken() {
  return localStorage.getItem('token_buyer') || localStorage.getItem('auth_token') || ''
}

function tabClass(type: 'buyer' | 'seller') {
  return [
    'px-4 py-2 rounded-md text-sm font-medium transition',
    activeTab.value === type ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
  ].join(' ')
}

function formatPrice(price: number) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Number(price || 0))
}

function getStatusText(status: string) {
  const map: Record<string, string> = {
    pending: 'Chờ thanh toán',
    confirmed: 'Đã xác nhận',
    shipped: 'Đang giao',
    paid: 'Đã thanh toán',
    delivered: 'Đã giao',
    completed: 'Hoàn tất',
    cancelled: 'Đã hủy',
    disputed: 'Tranh chấp'
  }
  return map[status] || status
}
function handleRated() {
  const o = buyerOrders.value.find(o => o.id === selectedOrderId.value)
  if (o) o.has_rated = true
}

function getStatusClass(status: string) {
  const base = 'px-4 py-1.5 rounded-full border shadow-sm font-medium transition-all duration-200'
  const map: Record<string, string> = {
    pending: `${base} bg-yellow-50 text-yellow-700 border-yellow-200`,
    confirmed: `${base} bg-blue-50 text-blue-700 border-blue-200`,
    shipped: `${base} bg-indigo-50 text-indigo-700 border-indigo-200`,
    delivered: `${base} bg-green-50 text-green-700 border-green-200`,
    completed: `${base} bg-emerald-50 text-emerald-700 border-emerald-200`,
    cancelled: `${base} bg-red-50 text-red-700 border-red-200`,
    disputed: `${base} bg-orange-50 text-orange-700 border-orange-200`
  }
  return map[status] || `${base} bg-gray-50 text-gray-700 border-gray-200`
}

async function loadOrders() {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get('/api/orders/my', {
      headers: { Authorization: `Bearer ${getToken()}`, Accept: 'application/json' }
    })
    buyerOrders.value = res.data.buyer_orders || res.data.orders || []
    sellerOrders.value = res.data.seller_orders || []
  } catch (err: any) {
    error.value = err?.response?.data?.message || 'Không thể tải danh sách đơn hàng.'
    showToast('error', error.value)
  } finally {
    loading.value = false
  }
}

async function shipOrder(orderId: number) {
  try {
    await axios.post(`/api/orders/${orderId}/ship`, {}, {
      headers: { Authorization: `Bearer ${getToken()}`, Accept: 'application/json' }
    })
    const o = sellerOrders.value.find(o => o.id === orderId)
    if (o) o.status = 'shipped'
    showToast('success', '📦 Đơn hàng đã được đánh dấu là đang giao hàng.')
  } catch (err: any) {
    showToast('error', err?.response?.data?.message || 'Không thể cập nhật trạng thái.')
  }
}

async function markDelivered(orderId: number) {
  try {
    await axios.post(`/api/orders/${orderId}/deliver`, {}, {
      headers: { Authorization: `Bearer ${getToken()}`, Accept: 'application/json' }
    })
    const o = buyerOrders.value.find(o => o.id === orderId)
    if (o) o.status = 'delivered'
    showToast('success', '✅ Bạn đã xác nhận đã nhận hàng.')
  } catch (err: any) {
    showToast('error', err?.response?.data?.message || 'Không thể cập nhật trạng thái.')
  }
}

async function completeOrder(orderId: number) {
  try {
    await axios.post(`/api/orders/${orderId}/complete`, {}, {
      headers: { Authorization: `Bearer ${getToken()}`, Accept: 'application/json' }
    })
    const o = buyerOrders.value.find(o => o.id === orderId)
    if (o) o.status = 'completed'
    showToast('success', '🎉 Đơn hàng đã hoàn tất! Tiền đã được chuyển cho người bán.')
  } catch (err: any) {
    showToast('error', err?.response?.data?.message || 'Không thể hoàn tất đơn hàng.')
  }
}

async function confirmOrder(orderId: number) {
  if (confirmingIds.value.has(orderId)) return
  confirmingIds.value.add(orderId)
  try {
    await axios.post(`/api/orders/${orderId}/confirm`, {}, {
      headers: { Authorization: `Bearer ${getToken()}`, Accept: 'application/json' }
    })
    const o = sellerOrders.value.find(o => o.id === orderId)
    if (o) o.status = 'confirmed'
    showToast('success', '✅ Đã xác nhận đơn hàng!')
  } catch (err: any) {
    const msg = err?.response?.data?.message || 'Không thể xác nhận đơn hàng.'
    showToast('error', msg)
  } finally {
    confirmingIds.value.delete(orderId)
  }
}


onMounted(loadOrders)
</script>
