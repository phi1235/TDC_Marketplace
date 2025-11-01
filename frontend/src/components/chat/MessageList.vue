<template>
  <div class="flex-1 overflow-auto overflow-x-hidden p-3 space-y-2" ref="scrollContainerRef">
    <MessageItem
      v-for="message in messages"
      :key="message.id"
      :message="message"
      :is-mine="message.sender_id === myId"
      @image-click="$emit('image-click', $event)"
    />
    <TypingIndicator v-if="isTyping" />
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick, watch } from 'vue'
import MessageItem from './MessageItem.vue'
import TypingIndicator from './TypingIndicator.vue'

const props = defineProps<{
  messages: any[]
  myId?: number | null
  isTyping: boolean
}>()

defineEmits<{
  'image-click': [url: string]
}>()

const scrollContainerRef = ref<HTMLDivElement | null>(null)

function scrollToBottom() {
  const el = scrollContainerRef.value
  if (!el) return
  nextTick(() => {
    el.scrollTop = el.scrollHeight
  })
}

// Auto scroll when new messages arrive
watch(() => props.messages.length, () => {
  scrollToBottom()
}, { flush: 'post' })

defineExpose({
  scrollToBottom
})
</script>

