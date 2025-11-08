import { defineStore } from 'pinia'
import { userNotificationsService } from '@/services/userNotifications'

export const useNotificationStore = defineStore('notification', {
  state: () => ({
    notificationUser: {
      count: 0,
      listings: [] as any[],
      message: ''
    }
  }),
  actions: {
    async fetchNotificationUser() {
      try {
        const res = await userNotificationsService.listPending()
        this.notificationUser = res
      } catch (err) {
        console.error('Lỗi khi lấy thông báo:', err)
        this.notificationUser = { count: 0, listings: [], message: '' }
      }
    }
  }
})
