import api from './api'

export const userNotificationsService = {
  async list() {
    const res = await api.get('/notifications')
    return res.data
  }
}
