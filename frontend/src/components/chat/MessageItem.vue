<template>
  <div
    class="w-fit max-w-[80%] p-2 rounded break-words"
    :class="getMessageClasses()"
  >
    <!-- AI Badge -->
    <div v-if="message.is_ai" class="flex items-center gap-1 mb-1">
      <span class="text-xs font-semibold text-blue-600">🤖 AI Assistant</span>
    </div>
    
    <!-- Image message -->
    <div v-if="message.type === 'image' && message.meta?.image_url" class="mb-1">
      <img
        :src="getImageUrl(message.meta.image_url)"
        :alt="message.meta.image_name || 'Image'"
        class="max-w-full max-h-[300px] rounded object-contain cursor-pointer"
        @click="$emit('image-click', message.meta.image_url)"
      />
    </div>
    <!-- Text content -->
    <div v-if="message.content" class="text-sm whitespace-pre-wrap break-words break-all">
      <template v-for="(segment, index) in parsedContent" :key="index">
        <template v-if="segment.type === 'link'">
          <a
            :href="segment.value"
            class="text-blue-600 underline break-all"
            target="_blank"
            rel="noopener noreferrer"
          >
            {{ segment.value }}
          </a>
        </template>
        <template v-else>
          {{ segment.value }}
        </template>
      </template>
    </div>

    <!-- Product suggestions -->
    <div v-if="hasProductSuggestions" class="mt-2 grid gap-2">
      <ProductSuggestionCard
        v-for="product in productSuggestions"
        :key="`${message.id}-product-${product.id}`"
        :product="product"
      />
    </div>
    <div class="text-[11px] opacity-70 mt-1">
      {{ new Date(message.created_at).toLocaleString('vi-VN') }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import ProductSuggestionCard from './ProductSuggestionCard.vue'
import type { Message, ProductSuggestion } from '@/types/chat'

const props = defineProps<{ message: Message; isMine: boolean }>()

type ContentSegment = { type: 'text' | 'link'; value: string }

const productSuggestions = computed<ProductSuggestion[]>(() => props.message.meta?.products ?? [])
const hasProductSuggestions = computed(() => productSuggestions.value.length > 0)

const parsedContent = computed<ContentSegment[]>(() => {
  const content = props.message.content
  if (!content) return []

  const segments: ContentSegment[] = []
  const urlRegex = /(https?:\/\/[^\s]+)/gi
  let lastIndex = 0

  content.replace(urlRegex, (match, offset: number) => {
    if (offset > lastIndex) {
      segments.push({ type: 'text', value: content.slice(lastIndex, offset) })
    }
    segments.push({ type: 'link', value: match })
    lastIndex = offset + match.length
    return match
  })

  if (lastIndex < content.length) {
    segments.push({ type: 'text', value: content.slice(lastIndex) })
  }

  return segments.length ? segments : [{ type: 'text', value: content }]
})

function getMessageClasses(): string {
  if (props.message.is_ai) {
    return 'bg-blue-50 border border-blue-200 text-gray-800'
  }
  return props.isMine ? 'ml-auto bg-blue-600 text-white' : 'bg-gray-100'
}

defineEmits<{
  'image-click': [url: string]
}>()

function getImageUrl(url: string): string {
  if (url.startsWith('http')) return url
  if (url.startsWith('/storage/')) return url
  if (url.includes('storage/')) {
    const path = url.includes('/storage/') ? url.split('/storage/')[1] : url
    return `/storage/${path}`
  }
  return url.startsWith('/') ? url : `/${url}`
}
</script>
