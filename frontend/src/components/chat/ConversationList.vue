<template>
  <div class="bg-white border rounded p-3 md:col-span-1">
    <div class="flex items-center justify-between mb-3">
      <div class="font-semibold">Cuộc trò chuyện</div>
      <button class="text-sm text-blue-600" @click="() => $emit('refresh')">Làm mới</button>
    </div>
    
    <!-- Button to start support chat -->
    <button
      v-if="!hasSupportConversation"
      @click="handleStartSupport"
      class="w-full mb-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
    >
      <span>🤖</span>
      <span>Bắt đầu chat hỗ trợ với AI</span>
    </button>
    
    <div class="space-y-2 max-h-[70vh] overflow-auto">
      <ConversationItem
        v-for="conversation in conversations"
        :key="conversation.id"
        :conversation="conversation"
        :is-active="activeConversationId === conversation.id"
        :my-id="myId"
        @click="$emit('select', conversation)"
      />
      <div v-if="conversations.length === 0" class="text-center text-gray-500 py-8 text-sm">
        Chưa có cuộc trò chuyện nào
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import ConversationItem from './ConversationItem.vue'
import { chatService } from '@/services/chat'

interface Conversation {
  id: number
  is_support?: boolean
  unread_count?: number
  last_message_at?: string
  last_message?: any
  _preview?: string
  participants?: any[]
}

const props = defineProps<{
  conversations: Conversation[]
  activeConversationId?: number | null
  myId?: number | null
}>()

const emit = defineEmits<{
  refresh: []
  select: [conversation: Conversation]
}>()

const hasSupportConversation = computed(() => {
  return props.conversations.some((c: Conversation) => c.is_support)
})

async function handleStartSupport() {
  try {
    const convo = await chatService.startSupport()
    emit('refresh')
    emit('select', convo)
  } catch (error: any) {
    alert(error.response?.data?.message || 'Không thể tạo cuộc trò chuyện hỗ trợ')
  }
}
</script>

