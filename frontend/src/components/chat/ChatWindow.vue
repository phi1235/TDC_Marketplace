<template>
  <div class="bg-white border rounded md:col-span-2 flex flex-col h-[75vh]">
    <ChatHeader :conversation="activeConversation" :my-id="myId" />
    
    <MessageList
      :messages="messages"
      :my-id="myId"
      :is-typing="isTyping"
      @image-click="handleImageClick"
      ref="messageListRef"
    />
    
    <MessageInput
      :draft="draft"
      :selected-image="selectedImage"
      :selected-image-preview="selectedImagePreview"
      @update:draft="(val) => $emit('update:draft', val)"
      @image-select="handleImageSelect"
      @clear-image="$emit('clear-image')"
      @send="$emit('send')"
      @typing="$emit('typing')"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ChatHeader from './ChatHeader.vue'
import MessageList from './MessageList.vue'
import MessageInput from './MessageInput.vue'

interface Conversation {
  id: number
  participants?: Array<{ user?: { id: number; name: string } }>
}

defineProps<{
  activeConversation: Conversation | null
  messages: any[]
  myId?: number | null
  isTyping: boolean
  draft: string
  selectedImage: File | null
  selectedImagePreview: string
}>()

const emit = defineEmits<{
  'update:draft': [value: string]
  'image-select': [file: File]
  'clear-image': []
  'send': []
  'typing': []
  'image-click': [url: string]
}>()

const messageListRef = ref<InstanceType<typeof MessageList> | null>(null)

function handleImageSelect(file: File) {
  emit('image-select', file)
}

function handleImageClick(url: string) {
  emit('image-click', url)
}

defineExpose({
  scrollToBottom: () => messageListRef.value?.scrollToBottom()
})
</script>

