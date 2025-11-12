<template>
  <div class="p-3 border-b font-semibold flex items-center justify-between">
    <div class="flex items-center gap-2">
      <span v-if="conversation">
        {{ getConversationTitle() }}
      </span>
      <!-- AI Assistant Badge for support conversations -->
      <span 
        v-if="conversation?.is_support" 
        class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium"
      >
        🤖 AI Assistant
      </span>
    </div>
    <div class="text-sm text-gray-500" v-if="conversation?.participants">
      {{ conversation.participants.map((p: any) => p.user?.name).join(', ') }}
    </div>
  </div>
</template>

<script setup lang="ts">
interface Conversation {
  id: number
  is_support?: boolean
  participants?: Array<{ user?: { id: number; name: string } }>
}

const props = defineProps<{
  conversation: Conversation | null
  myId?: number | null
}>()

function getConversationTitle(): string {
  if (!props.conversation) return ''
  
  // For support conversations, show "Hỗ trợ"
  if (props.conversation.is_support) {
    return 'Hỗ trợ'
  }
  
  const other = props.conversation.participants?.find(
    (p: any) => p.user?.id !== props.myId
  )?.user?.name
  return other || `#${props.conversation.id}`
}
</script>

