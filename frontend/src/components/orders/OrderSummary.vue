<template>
  <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl shadow-sm p-6">
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

    <div class="space-y-2 text-sm sm:text-base">
      <p><strong>Mã đơn:</strong> <span class="font-mono text-blue-600">{{ order.order_number }}</span></p>
      <p><strong>Sản phẩm:</strong> {{ order.product_title || 'Không có tiêu đề' }}</p>
      <p><strong>Giá:</strong> {{ formatPrice(order.total_amount || order.product_price) }}</p>
      <p><strong>Ngày đặt hàng:</strong> {{ formatDate(order.created_at) }}</p>
      <p><strong>Trạng thái:</strong>
        <span class="px-3 py-1 rounded-full text-xs sm:text-sm font-medium" :class="statusColor(order.status)">
          {{ getStatusText(order.status) }}
        </span>
      </p>

      <div v-if="order.escrow_account || order.escrowAccount" class="mt-3 bg-blue-50 border border-blue-100 rounded-lg p-3 text-sm">
        <p class="font-semibold text-blue-800 mb-1">💳 Thanh toán đảm bảo (Escrow)</p>
        <p class="text-blue-700">
          Số tiền giữ: <strong>{{ formatPrice(order.total_amount) }}</strong> –
          Trạng thái: {{ (order.escrowAccount || order.escrow_account)?.status || 'holding' }}
        </p>
      </div>

      <div v-if="order.shipping_address" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-3">
        <h3 class="font-semibold text-gray-800 mb-1">📦 Địa chỉ giao hàng</h3>
        <p class="text-gray-700">{{ order.shipping_address }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ order: any }>()

function formatPrice(price: number) {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Number(price || 0))
}
function formatDate(date: string) {
  const d = new Date(date)
  return d.toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}
function getStatusText(status: string) {
  return { pending: 'Chờ thanh toán', confirmed: 'Đã xác nhận', paid: 'Đã thanh toán', shipped: 'Đang giao', delivered: 'Đã giao', completed: 'Hoàn tất' }[status] || 'Không xác định'
}
function statusColor(status: string) {
  return { pending: 'bg-yellow-100 text-yellow-800', confirmed: 'bg-blue-100 text-blue-800', paid: 'bg-green-100 text-green-800', shipped: 'bg-indigo-100 text-indigo-800', delivered: 'bg-cyan-100 text-cyan-800', completed: 'bg-gray-100 text-gray-700' }[status] || 'bg-gray-100 text-gray-600'
}
</script>
