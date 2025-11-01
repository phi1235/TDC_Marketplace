<template>
  <div
    v-if="imageUrl"
    @click="$emit('close')"
    class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center cursor-pointer"
  >
    <img
      :src="getImageUrl(imageUrl)"
      alt="Full size"
      class="max-w-[90vw] max-h-[90vh] object-contain"
      @click.stop
    />
  </div>
</template>

<script setup lang="ts">
import { getImageUrl } from '@/utils/image'
import { CHAT_CONSTANTS } from '@/constants/chat'

defineProps<{
  imageUrl: string | null
}>()

defineEmits<{
  close: []
}>()
</script>

