<template>
  <div
    v-if="order.status === 'delivered' || order.status === 'completed'"
    class="bg-white border border-gray-200 rounded-xl shadow-sm p-5"
  >
    <h3 class="font-semibold text-gray-800 mb-3">⚠️ Khiếu nại giao dịch</h3>
    <p class="text-sm text-gray-600 mb-4">
      Gặp sự cố? Gửi khiếu nại để ban quản trị hỗ trợ.
    </p>
    <button
      @click="$emit('open-dispute')"
      class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all"
    >
      🧾 Gửi khiếu nại
    </button>
  </div>
  <div v-else class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 text-gray-500 italic text-xs">
    ⚠️ Khiếu nại chỉ khả dụng sau khi đơn hàng được giao hoặc hoàn tất.
  </div>
</template>

<script setup lang="ts">
defineProps<{ order: any }>()
</script>
