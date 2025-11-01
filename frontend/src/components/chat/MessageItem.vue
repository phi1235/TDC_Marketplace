<template>
  <div
    class="w-fit max-w-[80%] p-2 rounded break-words"
    :class="isMine ? 'ml-auto bg-blue-600 text-white' : 'bg-gray-100'"
  >
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
      {{ message.content }}
    </div>
    <div class="text-[11px] opacity-70 mt-1">
      {{ new Date(message.created_at).toLocaleString('vi-VN') }}
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  message: {
    id: number
    type: string
    content?: string
    created_at: string
    meta?: {
      image_url?: string
      image_name?: string
    }
  }
  isMine: boolean
}>()

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

