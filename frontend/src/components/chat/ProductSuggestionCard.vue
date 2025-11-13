<template>
  <div
    class="border rounded-lg p-2 bg-white shadow-sm flex gap-3 cursor-pointer hover:border-blue-400 transition"
    @click="openProduct"
  >
    <img
      :src="thumbnailSrc"
      :alt="product.title"
      class="w-16 h-16 object-cover rounded-md border bg-gray-50"
    />
    <div class="flex-1 min-w-0">
      <p class="text-sm font-semibold text-gray-800 line-clamp-2">
        {{ product.title }}
      </p>
      <p class="text-xs text-gray-500" v-if="product.category">
        {{ product.category }}
      </p>
      <p class="text-sm font-bold text-blue-600 mt-1">
        {{ formattedPrice }}
      </p>
      <p class="text-xs text-blue-500 mt-1">
        Nhấn để xem chi tiết →
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import type { ProductSuggestion } from '@/types/chat'

const props = defineProps<{
  product: ProductSuggestion
}>()

const router = useRouter()

const formattedPrice = computed(() => {
  if (props.product.price === undefined || props.product.price === null) {
    return 'Giá liên hệ'
  }
  try {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND',
      maximumFractionDigits: 0,
    }).format(props.product.price)
  } catch {
    return `${props.product.price.toLocaleString('vi-VN')} đ`
  }
})

const thumbnailSrc = computed(() => {
  return props.product.thumbnail || 'https://via.placeholder.com/80x80?text=TDC'
})

function openProduct() {
  const target = props.product.url || `/listings/${props.product.id}`

  if (target.startsWith('http')) {
    window.open(target, '_blank')
    return
  }

  if (target.startsWith('/')) {
    router.push(target)
    return
  }

  if (props.product.id) {
    router.push({ name: 'listing-detail', params: { id: props.product.id } })
  }
}
</script>
