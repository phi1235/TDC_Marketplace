import api from './api'

export const adminNotificationsService = {
  async list() {
    const res = await api.get('/dashboard/notifications') // user xem thông báo
    return res.data
  },

  async create(data) {
    // 🔧 sửa dòng này
    const res = await api.post('/dashboard/notifications', data)
    return res.data
  },
}
