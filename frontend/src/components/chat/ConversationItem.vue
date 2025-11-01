<template>
  <div
    @click="() => $emit('click')"
    class="p-2 rounded border cursor-pointer transition-colors relative"
    :class="{
      'border-blue-500 bg-blue-50': isActive,
      'border-gray-300 bg-gray-100 font-semibold': (conversation.unread_count || 0) > 0 && !isActive,
      'hover:bg-gray-50': (conversation.unread_count || 0) === 0 && !isActive
    }"
  >
    <div class="flex items-start justify-between gap-2">
      <div class="flex-1 min-w-0">
        <div class="text-sm font-medium truncate">
          {{ getTitle() }}
          <span v-if="conversation.is_support" class="ml-1 text-xs text-yellow-600">Support</span>
        </div>
        <!-- Last message preview -->
        <div class="text-xs text-gray-600 mt-1 truncate">
          <span v-if="conversation._preview">{{ conversation._preview }}</span>
          <template v-else>
            <span v-if="conversation.last_message?.type === 'image'">📷 Ảnh</span>
            <span v-else class="truncate">{{ conversation.last_message?.content || '' }}</span>
          </template>
        </div>
        <div class="text-xs text-gray-500 mt-1">{{ formatTime(conversation.last_message_at) }}</div>
      </div>
      <!-- Unread badge -->
      <div v-if="(conversation.unread_count || 0) > 0" class="flex-shrink-0">
        <span class="bg-red-500 text-white text-xs font-bold rounded-full px-2 py-0.5 min-w-[20px] text-center">
          {{ conversation.unread_count! > 99 ? '99+' : conversation.unread_count }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Conversation {
  id: number
  is_support?: boolean
  unread_count?: number
  last_message_at?: string
  last_message?: any
  _preview?: string
  participants?: Array<{ user?: { id: number; name: string } }>
}

const props = defineProps<{
  conversation: Conversation
  isActive: boolean
  myId?: number | null
}>()

defineEmits<{
  click: []
}>()

function getTitle(): string {
  const parts = (props.conversation.participants || [])
    .map((p: any) => p.user)
    .filter(Boolean)
  const others = parts
    .filter((u: any) => u.id !== props.myId)
    .map((u: any) => u.name)
  if (others.length) return others.join(', ')
  return `#${props.conversation.id}`
}

function formatTime(s: string | null | undefined): string {
  if (!s) return '—'
  try {
    return new Date(s).toLocaleString('vi-VN')
  } catch {
    return String(s)
  }
}
</script>

