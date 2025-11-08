import api from './api'

export const userNotificationsService = {
  async list() {
    const res = await api.get('/notifications')
    return res.data
  },
  async listPending() {
    // Gọi BE /my-listings/pending
    const res = await api.get('/my-listings/pending')
    return res.data // { count, listings, message }
  }
}
