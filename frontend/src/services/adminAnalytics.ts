import api from '@/services/api'

export const adminAnalyticsService = {
  async overview(params: Record<string, any> = {}) {
    const res = await api.get('/admin/analytics/overview', { params })
    return res.data
  }
}


