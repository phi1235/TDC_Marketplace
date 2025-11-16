<template>
  <div class="min-h-screen bg-gray-50 py-10">
    <div class="container mx-auto px-4 max-w-6xl">
      <h1 class="text-3xl font-bold text-gray-900 mb-8 border-b pb-3">Chi tiết đơn hàng</h1>

      <!-- Loading -->
      <div v-if="loading" class="text-gray-500 italic text-center py-10">
        Đang tải thông tin đơn hàng...
      </div>
      <div v-else-if="error" class="text-red-600 text-center py-10">{{ error }}</div>

      <!-- Content -->
      <div v-else-if="order" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <OrderSummary :order="order" />

        <div class="space-y-6">
          <SellerInfoCard :seller="order.seller" />
          <OrderActions :order="order" :is-buyer="isBuyer" :is-seller="isSeller" :action-loading="actionLoading"
            @open-pay="showPayModal = true" @confirm="confirmOrder" @ship="markShipped" @delivered="markDelivered"
            @complete="completeOrder" @rate="openRateModal" @confirm-free="confirmFreeOrder"/>

          <!-- Khiếu nại -->
          <div v-if="order.status === 'delivered' || order.status === 'completed'"
            class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">⚠️ Khiếu nại giao dịch</h3>
            <p class="text-sm text-gray-600 mb-4">
              Nếu bạn gặp sự cố trong giao dịch này, hãy gửi khiếu nại để được hỗ trợ bởi ban quản trị.
            </p>
            <button @click="showModal = true"
              class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 active:scale-95 transition-all">
              🧾 Gửi khiếu nại
            </button>
          </div>
          <div v-else class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 text-gray-500 italic text-xs">
            ⚠️ Khiếu nại chỉ khả dụng sau khi đơn hàng được giao hoặc hoàn tất.
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <DisputeModal v-if="showModal" :order="order" @close="showModal = false" @submitted="handleDisputeSubmitted" />
    <PayEscrowModal v-if="showPayModal" :order="order" :loading="paying"
      qr-url="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=https://google.com"
      @close="showPayModal = false" @confirm="payOrder" />
    <RateUserModal :is-open="showRateModal" :order-id="selectedOrderId" @close="showRateModal = false"
      @submitted="handleRated" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { showToast } from '@/utils/toast'

import OrderSummary from '@/components/orders/OrderSummary.vue'
import SellerInfoCard from '@/components/orders/SellerInfoCard.vue'
import OrderActions from '@/components/orders/OrderActions.vue'
import PayEscrowModal from '@/components/orders/PayEscrowModal.vue'
import DisputeModal from '@/components/orders/DisputeModal.vue'
import RateUserModal from '@/components/orders/RateUserModal.vue'

const route = useRoute()
const order = ref<any>(null)
const loading = ref(true)
const error = ref('')
const paying = ref(false)
const actionLoading = ref(false)
const showModal = ref(false)
const showPayModal = ref(false)
const currentUser = ref<any>(null)
const showRateModal = ref(false)
const selectedOrderId = ref<number | null>(null)

function getToken() {
  return localStorage.getItem('token_buyer') || localStorage.getItem('auth_token') || ''
}
function getCurrentUser() {
  try {
    return JSON.parse(localStorage.getItem('auth_user') || localStorage.getItem('user') || 'null')
  } catch {
    return null
  }
}

const isBuyer = computed(() => currentUser.value && order.value && currentUser.value.id === order.value.buyer_id)
const isSeller = computed(() => currentUser.value && order.value && currentUser.value.id === order.value.seller_id)

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
    const res = await axios.post(`/api/orders/${order.value.id}/escrow-pay`, {}, {
      headers: { Authorization: `Bearer ${getToken()}` }
    })
    Object.assign(order.value, res.data.order);
    showPayModal.value = false
    showToast('success', res.data.message || 'Thanh toán thành công! Tiền đang được giữ an toàn.')
  } catch (err: any) {
    showToast('error', err?.response?.data?.message || 'Không thể xác nhận thanh toán.')
  } finally {
    paying.value = false
  }
}

async function confirmOrder() {
  actionLoading.value = true
  try {
    const res = await axios.post(`/api/orders/${order.value.id}/confirm`, {}, {
      headers: { Authorization: `Bearer ${getToken()}` }
    })
    Object.assign(order.value, res.data.order);
    showToast('success', res.data.message || 'Đã xác nhận đơn hàng.')
  } catch (err: any) {
    showToast('error', err?.response?.data?.message || 'Không thể xác nhận đơn hàng.')
  } finally {
    actionLoading.value = false
  }
}

async function markShipped() {
  actionLoading.value = true
  try {
    const res = await axios.post(`/api/orders/${order.value.id}/ship`, {}, {
      headers: { Authorization: `Bearer ${getToken()}` }
    })
    Object.assign(order.value, res.data.order);
    showToast('success', res.data.message)
  } catch (err: any) {
    showToast('error', err?.response?.data?.message || 'Không thể cập nhật trạng thái.')
  } finally {
    actionLoading.value = false
  }
}

async function markDelivered() {
  actionLoading.value = true
  try {
    const res = await axios.post(`/api/orders/${order.value.id}/deliver`, {}, {
      headers: { Authorization: `Bearer ${getToken()}` }
    })
    Object.assign(order.value, res.data.order);
    showToast('success', res.data.message)
  } catch (err: any) {
    showToast('error', err?.response?.data?.message || 'Không thể cập nhật trạng thái.')
  } finally {
    actionLoading.value = false
  }
}

async function completeOrder() {
  actionLoading.value = true
  try {
    const res = await axios.post(`/api/orders/${order.value.id}/complete`, {}, {
      headers: { Authorization: `Bearer ${getToken()}` }
    })
    Object.assign(order.value, res.data.order);
    showToast('success', res.data.message)
  } catch (err: any) {
    showToast('error', err?.response?.data?.message || 'Không thể hoàn tất đơn hàng.')
  } finally {
    actionLoading.value = false
  }
}
async function confirmFreeOrder() {
  try {
    const res = await axios.post(`/api/orders/${order.value.id}/confirm-free`, {}, {
      headers: { Authorization: `Bearer ${getToken()}` }
    })
    Object.assign(order.value, res.data.order)
    showToast('success', res.data.message || 'Đơn hàng 0đ đã được xác nhận!')
  } catch (err: any) {
    showToast('error', err?.response?.data?.message || 'Không thể xác nhận đơn miễn phí.')
  }
}
function openRateModal(id: number) {
  selectedOrderId.value = id
  showRateModal.value = true
}
function handleRated() {
  if (order.value) order.value.has_rated = true
}
function handleDisputeSubmitted() {
  showToast('success', '🎫 Khiếu nại đã được gửi thành công!')
  showModal.value = false
}

onMounted(async () => {
  currentUser.value = getCurrentUser()
  await loadOrder()
})
</script>
