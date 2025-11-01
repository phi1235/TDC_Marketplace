<template>
  <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4 h-full">
    <!-- Sidebar conversations -->
    <ConversationList
      :conversations="Array.isArray(conversations) ? conversations : (conversations.data || [])"
      :active-conversation-id="activeConversation?.id"
      :my-id="myId"
      @refresh="loadConversations"
      @select="handleSelectConversation"
    />

    <!-- Chat window -->
    <ChatWindow
      v-if="activeConversation"
      :active-conversation="activeConversation"
      :messages="messages"
      :my-id="myId"
      :is-typing="isTyping"
      :draft="draft"
      :selected-image="selectedImage"
      :selected-image-preview="selectedImagePreview"
      @update:draft="draft = $event"
      @image-select="handleImageSelect"
      @clear-image="clearImage"
      @send="handleSend"
      @typing="notifyTyping"
      @image-click="openImageModal"
      ref="chatWindowRef"
    />
    <div v-else class="bg-white border rounded md:col-span-2 flex items-center justify-center">
      <p class="text-gray-500">Chọn một cuộc trò chuyện để bắt đầu</p>
    </div>
  </div>

  <!-- Image Modal -->
  <ImageModal :image-url="imageModalUrl" @close="imageModalUrl = null" />
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import ConversationList from '@/components/chat/ConversationList.vue'
import ChatWindow from '@/components/chat/ChatWindow.vue'
import ImageModal from '@/components/chat/ImageModal.vue'
import { useChat } from '@/composables/useChat'
import { chatService } from '@/services/chat'

const {
  conversations,
  activeConversation,
  messages,
  draft,
  selectedImage,
  selectedImagePreview,
  imageModalUrl,
  isTyping,
  myId,
  loadConversations,
  openConversation,
  send,
  handleImageSelect,
  clearImage,
  openImageModal,
  notifyTyping,
  initializeChat,
  cleanup,
  route
} = useChat()

const chatWindowRef = ref<InstanceType<typeof ChatWindow> | null>(null)

function scrollToBottom() {
  chatWindowRef.value?.scrollToBottom()
}

async function handleSelectConversation(conversation: any) {
  await openConversation(conversation, scrollToBottom)
}

async function handleSend() {
  await send(scrollToBottom)
}

onMounted(async () => {
  initializeChat()
  await loadConversations()
  
  // Auto open conversations based on route query
  if (route.query.support === '1') {
    const convo = await chatService.startSupport()
    await loadConversations()
    await openConversation(convo, scrollToBottom)
    return
  }
  
  if (route.query.user_id) {
    const uid = Number(route.query.user_id)
    const convo = await chatService.start(uid)
    await loadConversations()
    await openConversation(convo, scrollToBottom)
    return
  }
  
  if (conversations.value.length) {
    await openConversation(conversations.value[0], scrollToBottom)
  }
})

onBeforeUnmount(() => {
  cleanup()
})
</script>
