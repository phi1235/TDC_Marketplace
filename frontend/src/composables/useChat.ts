import { ref, nextTick } from 'vue'
import { chatService } from '@/services/chat'
import { useAuthStore } from '@/stores/auth'
import { initEcho, getEcho } from '@/utils/echo'
import { useRoute } from 'vue-router'

export function useChat() {
  const conversations = ref<any[]>([])
  const activeConversation = ref<any | null>(null)
  const messages = ref<any[]>([])
  const draft = ref('')
  const selectedImage = ref<File | null>(null)
  const selectedImagePreview = ref<string>('')
  const imageModalUrl = ref<string | null>(null)
  const isTyping = ref(false)
  
  const authStore = useAuthStore()
  const route = useRoute()
  const myId = authStore.user?.id
  
  let currentChannel: any = null
  let pollTimer: number | undefined
  let typingTimer: number | undefined
  const subscribedChannels = new Set<number>()
  let userChannelSubscribed = false

  // Subscribe to all conversations for real-time updates
  function subscribeToAllConversations() {
    const echo = getEcho()
    if (!echo) return
    
    // Unsubscribe from channels that no longer exist
    subscribedChannels.forEach((convoId) => {
      if (!conversations.value.find((c: any) => c.id === convoId)) {
        echo.leave(`chat.${convoId}`)
        subscribedChannels.delete(convoId)
      }
    })
    
    // Listen to all conversations
    conversations.value.forEach((c: any) => {
      if (subscribedChannels.has(c.id)) return // Already subscribed
      
      echo.private(`chat.${c.id}`)
        .listen('.MessageSent', (e: any) => {
          const updates: any = {
            last_message: e,
            last_message_at: e.created_at,
            _preview: (e.type === 'image' ? '📷 Ảnh' : (e.content || ''))
          }
          if (activeConversation.value?.id !== c.id && e.sender_id !== myId) {
            const conv = conversations.value.find((conv: any) => conv.id === c.id)
            updates.unread_count = ((conv?.unread_count || 0) + 1)
          }
          updateConversationInList(c.id, updates)
        })
      subscribedChannels.add(c.id)
    })
  }

  async function loadConversations() {
    const res = await chatService.listConversations()
    conversations.value = Array.isArray(res) ? res : (res.data || [])
    // Sort by last_message_at descending
    conversations.value.sort((a: any, b: any) => {
      const timeA = a.last_message_at ? new Date(a.last_message_at).getTime() : 0
      const timeB = b.last_message_at ? new Date(b.last_message_at).getTime() : 0
      return timeB - timeA
    })
    // Re-subscribe to all conversations
    subscribeToAllConversations()
    // Ensure support conversation exists for new users
    const hasSupport = conversations.value.some((c: any) => c.is_support)
    if (!hasSupport) {
      try {
        await chatService.startSupport()
        await loadConversations() // Reload to get updated list
      } catch (e) {}
    }
  }

  function updateConversationInList(conversationId: number, updates: any) {
    const idx = conversations.value.findIndex((c: any) => c.id === conversationId)
    if (idx >= 0) {
      const updated = { ...conversations.value[idx], ...updates }
      conversations.value.splice(idx, 1)
      conversations.value.unshift(updated)
      conversations.value = [...conversations.value]
    }
  }

  function startPolling() {
    if (pollTimer) window.clearInterval(pollTimer)
    pollTimer = window.setInterval(async () => {
      if (!activeConversation.value) return
      const lastId = messages.value.length ? messages.value[messages.value.length - 1].id : 0
      const res = await chatService.messages(activeConversation.value.id, { per_page: 20 })
      const arr = (res.data && Array.isArray(res.data)) ? res.data : res
      const sorted = [...arr].reverse()
      const news = sorted.filter((m: any) => m.id > lastId)
      if (news.length) {
        const existingIds = new Set(messages.value.map((m: any) => m.id))
        const dedupNews = news.filter((m: any) => !existingIds.has(m.id))
        if (dedupNews.length) {
          messages.value.push(...dedupNews)
        }
        const latest = (dedupNews.length ? dedupNews : news)[(dedupNews.length ? dedupNews : news).length - 1]
        updateConversationInList(activeConversation.value.id, {
          last_message: latest,
          last_message_at: latest.created_at,
          _preview: (latest.type === 'image' ? '📷 Ảnh' : (latest.content || ''))
        })
        try {
          await chatService.markAsRead(activeConversation.value.id)
        } catch {}
      }
    }, 2500)
  }

  async function openConversation(c: any, scrollToBottom?: () => void) {
    // Unsubscribe previous channel
    if (currentChannel && activeConversation.value) {
      try {
        currentChannel.stopListeningForWhisper && currentChannel.stopListeningForWhisper('typing')
      } catch {}
      currentChannel = null
    }
    
    activeConversation.value = c
    const res = await chatService.messages(c.id, { per_page: 50 })
    messages.value = (res.data && Array.isArray(res.data)) ? [...res.data].reverse() : [...(res || [])].reverse()
    await nextTick()
    scrollToBottom?.()
    
    // Mark as read
    if (c.unread_count > 0) {
      try {
        await chatService.markAsRead(c.id)
        updateConversationInList(c.id, { unread_count: 0 })
        if (activeConversation.value?.id === c.id) {
          activeConversation.value.unread_count = 0
        }
      } catch (e) {
        console.error('Failed to mark as read:', e)
      }
    }
    
    // Subscribe to WebSocket channel
    const echo = initEcho()
    currentChannel = echo.private(`chat.${c.id}`)
      .listenForWhisper('typing', (e: any) => {
        if (e && e.user_id !== myId) {
          isTyping.value = true
          if (typingTimer) window.clearTimeout(typingTimer)
          typingTimer = window.setTimeout(() => (isTyping.value = false), 2000)
        }
      })

    startPolling()
  }

  function handleImageSelect(file: File) {
    if (file.size > 5 * 1024 * 1024) {
      alert('Ảnh không được vượt quá 5MB')
      return
    }
    selectedImage.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      selectedImagePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }

  function clearImage() {
    selectedImage.value = null
    selectedImagePreview.value = ''
  }

  function openImageModal(url: string) {
    imageModalUrl.value = url.startsWith('http') ? url
      : url.startsWith('/storage/') ? url
      : url.includes('storage/') ? `/storage/${url.includes('/storage/') ? url.split('/storage/')[1] : url}`
      : url.startsWith('/') ? url : `/${url}`
  }

  async function send(scrollToBottom?: () => void) {
    if (!activeConversation.value) return
    if (!draft.value.trim() && !selectedImage.value) return
    
    try {
      const payload: any = {}
      if (selectedImage.value) {
        payload.image = selectedImage.value
        payload.type = 'image'
      }
      if (draft.value.trim()) {
        payload.content = draft.value
      }
      
      const msg = await chatService.send(activeConversation.value.id, payload)
      if (!messages.value.find((m: any) => m.id === msg.id)) {
        messages.value.push(msg)
      }
      updateConversationInList(activeConversation.value.id, {
        last_message: msg,
        last_message_at: msg.created_at
      })
      draft.value = ''
      clearImage()
      await nextTick()
      scrollToBottom?.()
    } catch (error: any) {
      alert(error.response?.data?.message || 'Gửi tin nhắn thất bại')
    }
  }

  function notifyTyping() {
    try {
      const echo = getEcho()
      if (!echo || !activeConversation.value) return
      echo.private(`chat.${activeConversation.value.id}`).whisper('typing', { user_id: myId })
    } catch {}
  }

  function initializeChat() {
    const echo = initEcho()
    
    // Subscribe to user-level channel
    if (authStore.user?.id && !userChannelSubscribed) {
      echo.private(`user.${authStore.user.id}`)
        .listen('.MessageSent', (e: any) => {
          const preview = e.type === 'image' ? '📷 Ảnh' : (e.content || '')
          const conv = conversations.value.find((c: any) => c.id === e.conversation_id)
          if (!conv) {
            loadConversations()
            return
          }
          const updates: any = {
            last_message: e,
            last_message_at: e.created_at,
            _preview: preview,
          }
          if (activeConversation.value?.id !== e.conversation_id && e.sender_id !== myId) {
            updates.unread_count = ((conv?.unread_count || 0) + 1)
          }
          updateConversationInList(e.conversation_id, updates)
          loadConversations()
        })
      userChannelSubscribed = true
    }
    
    // Re-subscribe after websocket reconnects
    try {
      // @ts-ignore
      const conn = echo.connector?.pusher?.connection
      if (conn && typeof conn.bind === 'function') {
        conn.bind('state_change', (states: any) => {
          if (states?.current === 'connected') {
            subscribeToAllConversations()
          }
        })
      }
    } catch {}
  }

  function cleanup() {
    const echo = getEcho()
    if (echo) {
      if (currentChannel && activeConversation.value) {
        echo.leave(`chat.${activeConversation.value.id}`)
      }
      subscribedChannels.forEach((convoId) => {
        echo.leave(`chat.${convoId}`)
      })
      subscribedChannels.clear()
    }
    if (pollTimer) window.clearInterval(pollTimer)
  }

  return {
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
  }
}

