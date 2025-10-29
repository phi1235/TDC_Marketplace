import api from '@/services/api'

export const chatService = {
  async listConversations(params: Record<string, any> = {}) {
    const res = await api.get('/chat/conversations', { params })
    return res.data
  },
  async start(user_id: number, is_support = false) {
    const res = await api.post('/chat/start', { user_id, is_support })
    return res.data
  },
  async startSupport() {
    const res = await api.post('/chat/start-support')
    return res.data
  },
  async messages(conversationId: number, params: Record<string, any> = {}) {
    const res = await api.get(`/chat/conversations/${conversationId}/messages`, { params })
    return res.data
  },
  async send(conversationId: number, payload: { type?: string; content?: string; meta?: any, image?: File }) {
    const formData = new FormData()
    if (payload.image) {
      formData.append('image', payload.image)
    }
    if (payload.type) formData.append('type', payload.type)
    if (payload.content) formData.append('content', payload.content)
    if (payload.meta) formData.append('meta', JSON.stringify(payload.meta))

    const res = await api.post(`/chat/conversations/${conversationId}/messages`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    return res.data
  },
  async markAsRead(conversationId: number) {
    const res = await api.post(`/chat/conversations/${conversationId}/mark-read`)
    return res.data
  },
}


