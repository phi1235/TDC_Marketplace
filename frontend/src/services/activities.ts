import api from '@/services/api'

export async function fire(event_name: string, metadata: Record<string, any> = {}) {
  try {
    await api.post('/activities', { event_name, metadata })
  } catch (e) {
    // ignore client-side errors to avoid blocking UI
    console.warn('activity fire failed', e)
  }
}


