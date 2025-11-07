<template>
  <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-3">
    <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
      ⚙️ Trạng thái & hành động
    </h3>

    <!-- Buyer -->
    <template v-if="isBuyer">
      <button
        v-if="order.status === 'pending'"
        @click="$emit('open-pay')"
        class="w-full px-4 py-3 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 active:scale-95 transition-all"
      >
        💳 Thanh toán ngay (Escrow)
      </button>

      <p v-else-if="order.status === 'paid'" class="text-sm text-gray-600">
        ✅ Bạn đã thanh toán. Tiền đang được giữ an toàn, chờ người bán xác nhận và giao hàng.
      </p>

      <button
        v-else-if="order.status === 'shipped'"
        @click="$emit('delivered')"
        :disabled="actionLoading"
        class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:scale-95 transition-all"
      >
        ✅ Tôi đã nhận được hàng
      </button>

      <button
        v-else-if="order.status === 'delivered'"
        @click="$emit('complete')"
        :disabled="actionLoading"
        class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 active:scale-95 transition-all"
      >
        🎉 Hoàn tất đơn & giải phóng tiền
      </button>

     <div v-else-if="order.status === 'completed'" class="text-center space-y-3">
  <p class="text-green-700 font-semibold text-sm">
    🎉 Đơn hàng đã hoàn tất. Tiền đã được chuyển cho người bán.
  </p>

  <button
    v-if="isBuyer && !order.has_rated"
    @click="$emit('rate', order.id)"
    class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-sm"
  >
    ⭐ Đánh giá người bán
  </button>

  <p v-else-if="order.has_rated" class="text-gray-500 text-sm italic">
    Bạn đã đánh giá người bán.
  </p>
</div>

      <p v-else class="text-sm text-gray-600">Trạng thái hiện tại: {{ getStatusText(order.status) }}</p>
    </template>

    <!-- Seller -->
    <template v-else-if="isSeller">
      <button
        v-if="order.status === 'paid'"
        @click="$emit('confirm')"
        :disabled="actionLoading"
        class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 active:scale-95 transition-all"
      >
        ✅ Xác nhận đơn hàng
      </button>

      <button
        v-else-if="order.status === 'confirmed'"
        @click="$emit('ship')"
        :disabled="actionLoading"
        class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:scale-95 transition-all"
      >
        🚚 Đánh dấu đang giao
      </button>

      <p v-else-if="order.status === 'shipped'" class="text-sm text-gray-600">
        📦 Bạn đã đánh dấu đang giao. Chờ người mua xác nhận.
      </p>

      <p v-else-if="order.status === 'completed'" class="text-green-700 font-semibold text-center text-sm">
        🎉 Đơn hàng đã hoàn tất. Bạn đã nhận được tiền từ escrow.
      </p>

      <p v-else class="text-sm text-gray-600">Trạng thái hiện tại: {{ getStatusText(order.status) }}</p>
    </template>

    <!-- Others -->
    <template v-else>
      <p class="text-sm text-gray-500">Bạn không phải người mua hoặc người bán của đơn này.</p>
    </template>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  order: any
  isBuyer: boolean
  isSeller: boolean
  actionLoading: boolean
}>()

const emits = defineEmits(['open-pay', 'confirm', 'ship', 'delivered', 'complete'])

function getStatusText(status: string) {
  return {
    pending: 'Chờ thanh toán',
    confirmed: 'Đã xác nhận',
    paid: 'Đã thanh toán',
    shipped: 'Đang giao',
    delivered: 'Đã giao',
    completed: 'Hoàn tất'
  }[status] || 'Không xác định'
}
</script>
