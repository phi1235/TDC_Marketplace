<template>
  <div class="bg-white border rounded p-3 md:col-span-1">
    <div class="flex items-center justify-between mb-3">
      <div class="font-semibold">Cuộc trò chuyện</div>
      <button class="text-sm text-blue-600" @click="() => $emit('refresh')">Làm mới</button>
    </div>
    <div class="space-y-2 max-h-[70vh] overflow-auto">
      <ConversationItem
        v-for="conversation in conversations"
        :key="conversation.id"
        :conversation="conversation"
        :is-active="activeConversationId === conversation.id"
        :my-id="myId"
        @click="$emit('select', conversation)"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import ConversationItem from './ConversationItem.vue'

interface Conversation {
  id: number
  is_support?: boolean
  unread_count?: number
  last_message_at?: string
  last_message?: any
  _preview?: string
  participants?: any[]
}

defineProps<{
  conversations: Conversation[]
  activeConversationId?: number | null
  myId?: number | null
}>()

defineEmits<{
  refresh: []
  select: [conversation: Conversation]
}>()
</script>

