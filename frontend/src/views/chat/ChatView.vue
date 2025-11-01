<template>
  <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4 h-full">
    <!-- Sidebar conversations -->
    <div class="bg-white border rounded p-3 md:col-span-1">
      <div class="flex items-center justify-between mb-3">
        <div class="font-semibold">Cuộc trò chuyện</div>
        <button class="text-sm text-blue-600" @click="loadConversations">Làm mới</button>
      </div>
      <div class="space-y-2 max-h-[70vh] overflow-auto">
        <div v-for="c in (conversations.data || conversations)" :key="c.id" @click="openConversation(c)"
             class="p-2 rounded border cursor-pointer transition-colors relative"
             :class="{ 
               'border-blue-500 bg-blue-50': activeConversation?.id === c.id,
               'border-gray-300 bg-gray-100 font-semibold': (c.unread_count || 0) > 0 && activeConversation?.id !== c.id,
               'hover:bg-gray-50': (c.unread_count || 0) === 0 && activeConversation?.id !== c.id
             }">
          <div class="flex items-start justify-between gap-2">
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium truncate">
                {{ getConvoTitle(c) }} 
                <span v-if="c.is_support" class="ml-1 text-xs text-yellow-600">Support</span>
              </div>
              <!-- Last message preview -->
              <div class="text-xs text-gray-600 mt-1 truncate">
                <span v-if="c._preview">{{ c._preview }}</span>
                <template v-else>
                  <span v-if="c.last_message?.type === 'image'">📷 Ảnh</span>
                  <span v-else class="truncate">{{ c.last_message?.content || '' }}</span>
                </template>
              </div>
              <div class="text-xs text-gray-500 mt-1">{{ formatTime(c.last_message_at) }}</div>
            </div>
            <!-- Unread badge -->
            <div v-if="(c.unread_count || 0) > 0" class="flex-shrink-0">
              <span class="bg-red-500 text-white text-xs font-bold rounded-full px-2 py-0.5 min-w-[20px] text-center">
                {{ c.unread_count > 99 ? '99+' : c.unread_count }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat window -->
    <div class="bg-white border rounded md:col-span-2 flex flex-col h-[75vh]">
      <div class="p-3 border-b font-semibold flex items-center justify-between">
        <div>         
          <span v-if="activeConversation">
            {{ activeConversation.participants?.find((p:any) => p.user?.id !== myId)?.user?.name || ('#' + activeConversation.id) }}
          </span>
        </div>
        <div class="text-sm text-gray-500" v-if="activeConversation && activeConversation.participants">
          {{ activeConversation.participants.map((p:any)=>p.user?.name).join(', ') }}
        </div>
      </div>
      <div class="flex-1 overflow-auto overflow-x-hidden p-3 space-y-2" ref="scrollRef">
        <div v-for="m in messages" :key="m.id" class="w-fit max-w-[80%] p-2 rounded break-words" :class="m.sender_id === myId ? 'ml-auto bg-blue-600 text-white' : 'bg-gray-100'">
          <!-- Image message -->
          <div v-if="m.type === 'image' && m.meta?.image_url" class="mb-1">
            <img :src="getImageUrl(m.meta.image_url)" :alt="m.meta.image_name || 'Image'" class="max-w-full max-h-[300px] rounded object-contain cursor-pointer" @click="openImageModal(m.meta.image_url)" />
          </div>
          <!-- Text content -->
          <div v-if="m.content" class="text-sm whitespace-pre-wrap break-words break-all">{{ m.content }}</div>
          <div class="text-[11px] opacity-70 mt-1">{{ new Date(m.created_at).toLocaleString('vi-VN') }}</div>
        </div>
        <!-- Typing indicator -->
        <div v-if="isTyping" class="w-fit max-w-[80%] p-2 rounded bg-gray-100 inline-flex items-center gap-1">
          <span class="typing-dot animate-bounce" style="animation-delay:0ms">•</span>
          <span class="typing-dot animate-bounce" style="animation-delay:100ms">•</span>
          <span class="typing-dot animate-bounce" style="animation-delay:200ms">•</span>
        </div>
      </div>
      <div class="p-3 border-t">
        <!-- Image preview -->
        <div v-if="selectedImage" class="mb-2 relative inline-block">
          <img :src="selectedImagePreview" alt="Preview" class="max-h-32 rounded border" />
          <button @click="clearImage" class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">×</button>
        </div>
        <div class="flex gap-2 items-end">
          <input type="file" ref="fileInputRef" accept="image/*" @change="handleImageSelect" class="hidden" />
          <button @click="triggerFileInput" class="bg-gray-200 hover:bg-gray-300 rounded px-3 py-2 text-gray-700">📷</button>
          <textarea v-model="draft" @focus="notifyTyping" @input="autoResize" @keydown.enter.exact.prevent="send" ref="textareaRef" placeholder="Nhập tin nhắn..." class="flex-1 border rounded px-3 py-2 min-h-[40px] max-h-[120px] resize-none overflow-y-auto" rows="1"></textarea>
          <button @click="send" class="bg-blue-600 text-white rounded px-4 py-2 h-fit">Gửi</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Image Modal -->
  <div v-if="imageModalUrl" @click="imageModalUrl = null" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center cursor-pointer">
    <img :src="imageModalUrl" alt="Full size" class="max-w-[90vw] max-h-[90vh] object-contain" @click.stop />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'
import { chatService } from '@/services/chat'
import { useAuthStore } from '@/stores/auth'
import { initEcho, getEcho } from '@/utils/echo'
import { useRoute } from 'vue-router'

const conversations = ref<any[]>([])
const activeConversation = ref<any|null>(null)
const messages = ref<any[]>([])
const draft = ref('')
const scrollRef = ref<HTMLDivElement|null>(null)
const textareaRef = ref<HTMLTextAreaElement|null>(null)
const fileInputRef = ref<HTMLInputElement|null>(null)
const selectedImage = ref<File|null>(null)
const selectedImagePreview = ref<string>('')
const imageModalUrl = ref<string|null>(null)
const authStore = useAuthStore()
const myId = authStore.user?.id
const route = useRoute()
let currentChannel: any = null
let pollTimer: number | undefined
const isTyping = ref(false)
let typingTimer: number | undefined
const subscribedChannels = new Set<number>() // Track subscribed channels
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
        // Always update last message preview and ordering
        const updates: any = { last_message: e, last_message_at: e.created_at, _preview: (e.type === 'image' ? '📷 Ảnh' : (e.content || '')) }
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
    // Remove then unshift to top to guarantee rerender and ordering
    conversations.value.splice(idx, 1)
    conversations.value.unshift(updated)
    // Force reactive update of the array reference
    conversations.value = [...conversations.value]
  }
}

function getConvoTitle(c: any): string {
  const parts = (c.participants || []).map((p: any) => p.user).filter(Boolean)
  // Title as names except myself
  const others = parts.filter((u: any) => u.id !== myId).map((u: any) => u.name)
  if (others.length) return others.join(', ')
  // fallback
  return `#${c.id}`
}

function formatTime(s: string | null | undefined): string {
  if (!s) return '—'
  try { return new Date(s).toLocaleString('vi-VN') } catch { return String(s) }
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
      // Dedupe: skip messages that already exist (can happen if send() pushed earlier)
      const existingIds = new Set(messages.value.map((m: any) => m.id))
      const dedupNews = news.filter((m: any) => !existingIds.has(m.id))
      if (dedupNews.length) {
        messages.value.push(...dedupNews)
      }
      // Update sidebar preview for active conversation as fallback (when WS missed)
      const latest = (dedupNews.length ? dedupNews : news)[(dedupNews.length ? dedupNews : news).length - 1]
      updateConversationInList(activeConversation.value.id, {
        last_message: latest,
        last_message_at: latest.created_at,
        _preview: (latest.type === 'image' ? '📷 Ảnh' : (latest.content || ''))
      })
      // Mark as read for active conv
      try { await chatService.markAsRead(activeConversation.value.id) } catch {}
      await nextTick(); scrollToBottom()
    }
  }, 2500)
}

async function openConversation(c: any) {
  // Unsubscribe previous channel (only typing whisper, keep global message listeners)
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
  scrollToBottom()
  
  // Mark as read
  if (c.unread_count > 0) {
    try {
      await chatService.markAsRead(c.id)
      // Update conversation in list to clear unread count
      updateConversationInList(c.id, { unread_count: 0 })
      // Also update activeConversation
      if (activeConversation.value?.id === c.id) {
        activeConversation.value.unread_count = 0
      }
    } catch (e) {
      console.error('Failed to mark as read:', e)
    }
  }
  
  // Subscribe to WebSocket channel (Echo sẽ tự thêm prefix private-)
  const echo = initEcho()
  currentChannel = echo.private(`chat.${c.id}`)
    .listenForWhisper('typing', (e: any) => {
      if (e && e.user_id !== myId) {
        isTyping.value = true
        if (typingTimer) window.clearTimeout(typingTimer)
        typingTimer = window.setTimeout(() => (isTyping.value = false), 2000)
      }
    })

  // Fallback: polling to fetch missed events while chatting
  startPolling()
}

function scrollToBottom() {
  const el = scrollRef.value
  if (!el) return
  el.scrollTop = el.scrollHeight
}

function autoResize() {
  notifyTyping()
  const el = textareaRef.value
  if (!el) return
  el.style.height = '0'
  el.style.height = Math.min(el.scrollHeight, 120) + 'px'
}

function triggerFileInput() {
  fileInputRef.value?.click()
}

function handleImageSelect(e: Event) {
  const input = e.target as HTMLInputElement
  const file = input.files?.[0]
  if (!file) return
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
  // Reset input
  if (input) input.value = ''
}

function clearImage() {
  selectedImage.value = null
  selectedImagePreview.value = ''
}

function getImageUrl(url: string): string {
  if (url.startsWith('http')) return url
  // Storage files are served from /storage/ (not /api/storage/)
  if (url.startsWith('/storage/')) return url
  // If relative path starting with storage/, use it as-is
  if (url.includes('storage/')) {
    const path = url.includes('/storage/') ? url.split('/storage/')[1] : url
    return `/storage/${path}`
  }
  return url.startsWith('/') ? url : `/${url}`
}

function openImageModal(url: string) {
  imageModalUrl.value = getImageUrl(url)
}

async function send() {
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
    // Guard: avoid duplicate append if polling already fetched this message
    if (!messages.value.find((m: any) => m.id === msg.id)) {
      messages.value.push(msg)
    }
    // Update conversation in list with new last message
    updateConversationInList(activeConversation.value.id, {
      last_message: msg,
      last_message_at: msg.created_at
    })
    draft.value = ''
    clearImage()
    // Reset textarea height after sending
    if (textareaRef.value) {
      textareaRef.value.style.height = 'auto'
    }
    await nextTick(); scrollToBottom()
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

onMounted(async () => {
  const echo = initEcho() // Initialize Echo on mount
  await loadConversations()
  
  // Subscribe to all conversations for real-time updates
  subscribeToAllConversations()
  // Subscribe to user-level channel to update sidebar for any new message
  if (authStore.user?.id && !userChannelSubscribed) {
    echo.private(`user.${authStore.user.id}`)
      .listen('.MessageSent', (e: any) => {
        const preview = e.type === 'image' ? '📷 Ảnh' : (e.content || '')
        const conv = conversations.value.find((c: any) => c.id === e.conversation_id)
        if (!conv) {
          // If the conversation is not present (new or not loaded), reload the list
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
        // Ensure consistency with backend-calculated fields/order (like manual refresh)
        loadConversations()
      })
    userChannelSubscribed = true
  }
  // Re-subscribe after websocket reconnects
  try {
    // @ts-ignore - access pusher internals
    const conn = echo.connector?.pusher?.connection
    if (conn && typeof conn.bind === 'function') {
      conn.bind('state_change', (states: any) => {
        if (states?.current === 'connected') {
          subscribeToAllConversations()
        }
      })
    }
  } catch {}
  
  // Tự động mở chat support với admin
  if (route.query.support === '1') {
    const convo = await chatService.startSupport()
    await loadConversations()
    subscribeToAllConversations() // Re-subscribe after reload
    openConversation(convo)
    return
  }
  // Tự động mở chat với user cụ thể
  if (route.query.user_id) {
    const uid = Number(route.query.user_id)
    const convo = await chatService.start(uid)
    await loadConversations()
    subscribeToAllConversations() // Re-subscribe after reload
    openConversation(convo)
    return
  }
  if (conversations.value.length) openConversation(conversations.value[0])
})

// In case auth loads after mount, subscribe to user channel when ready
watch(() => authStore.user?.id, (val) => {
  if (!val) return
  const echo = getEcho() || initEcho()
  if (userChannelSubscribed) return
  echo.private(`user.${val}`)
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
      // Ensure consistency similar to manual refresh
      loadConversations()
    })
  userChannelSubscribed = true
})

onBeforeUnmount(() => {
  const echo = getEcho()
  if (echo) {
    if (currentChannel && activeConversation.value) {
      echo.leave(`chat.${activeConversation.value.id}`)
    }
    // Unsubscribe from all channels
    subscribedChannels.forEach((convoId) => {
      echo.leave(`chat.${convoId}`)
    })
    subscribedChannels.clear()
  }
  if (pollTimer) window.clearInterval(pollTimer)
})
</script>


