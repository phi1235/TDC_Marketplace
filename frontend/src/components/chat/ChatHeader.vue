<template>
  <div class="p-3 border-b font-semibold flex items-center justify-between">
    <div>
      <span v-if="conversation">
        {{ getConversationTitle() }}
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
  participants?: Array<{ user?: { id: number; name: string } }>
}

const props = defineProps<{
  conversation: Conversation | null
  myId?: number | null
}>()

function getConversationTitle(): string {
  if (!props.conversation) return ''
  const other = props.conversation.participants?.find(
    (p: any) => p.user?.id !== props.myId
  )?.user?.name
  return other || `#${props.conversation.id}`
}
</script>

