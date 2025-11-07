<template>
  <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
      🧍‍♂️ Thông tin người bán
    </h3>
    <div class="flex items-center gap-3 mb-3">
      <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-lg font-bold">
        {{ getInitials(seller?.name) }}
      </div>
      <div>
        <p class="font-semibold text-gray-900">{{ seller?.name || 'Không có tên' }}</p>
        <p class="text-xs text-gray-500">{{ seller?.email || 'Không có email' }}</p>
      </div>
    </div>
    <p class="text-sm">
      <strong>Số điện thoại:</strong> {{ seller?.phone || 'Không có dữ liệu' }}
    </p>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ seller: any }>()

function getInitials(name: string = '') {
  return name
    ? name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .slice(0, 2)
        .toUpperCase()
    : 'U'
}
</script>
