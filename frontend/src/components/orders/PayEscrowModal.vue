<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg animate-fadeIn space-y-4">
      <h2 class="text-xl font-semibold text-gray-800">💳 Thanh toán đơn hàng</h2>

      <p class="text-sm text-gray-600">
        Quét mã QR / chuyển khoản theo thông tin bên dưới. Sau khi thanh toán xong, bấm
        <strong>"Tôi đã thanh toán"</strong> để hệ thống tạo escrow và cập nhật trạng thái.
      </p>

      <!-- QR Code -->
      <div class="border rounded-lg p-4 text-center space-y-3">
        <!-- ✅ Nếu có ảnh QR -->
        <div v-if="qrSrc" class="flex justify-center">
          <img
            :src="qrSrc"
            alt="QR Code thanh toán"
            class="w-48 h-48 rounded-lg border shadow-sm object-contain"
          />
        </div>

        <!-- ❌ Nếu chưa có ảnh QR -->
        <div
          v-else
          class="w-48 h-48 mx-auto bg-gray-200 flex items-center justify-center text-gray-500 text-xs rounded-lg"
        >
          QR CODE / MÃ THANH TOÁN
        </div>

        <!-- 💬 Thông tin -->
        <p class="text-xs text-gray-600">
          Nội dung chuyển khoản: <strong>{{ order?.order_number }}</strong>
        </p>
        <p class="text-xs text-gray-600">
          Số tiền: <strong>{{ formatPrice(order?.total_amount || order?.product_price) }}</strong>
        </p>
      </div>

      <!-- Nút hành động -->
      <div class="flex justify-end gap-3 pt-2">
        <button
          @click="$emit('close')"
          class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 text-sm"
        >
          Đóng
        </button>

        <button
          @click="$emit('confirm')"
          :disabled="loading"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 active:scale-95 text-sm flex items-center gap-2"
        >
          <svg
            v-if="loading"
            class="animate-spin h-4 w-4 text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
            />
          </svg>
          <span>{{ loading ? 'Đang xác nhận...' : 'Tôi đã thanh toán' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{ order: any; loading: boolean; qrUrl?: string }>()
const emits = defineEmits(['close', 'confirm'])

// ✅ Ưu tiên: prop qrUrl → order.qr_url → fallback QR tạm để test
const qrSrc = computed(() => {
  return (
    props.qrUrl ||
    props.order?.qr_url ||
    `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=Thanh%20toan%20don%20hang%20${props.order?.order_number || 'TEST'}`
  )
})

function formatPrice(price: number) {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(Number(price || 0))
}
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
