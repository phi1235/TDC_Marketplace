import { defineStore } from 'pinia'
import axios from 'axios'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
  }),
  getters: {
    isAdmin: (state) => state.user?.role === 'admin',
  },
  actions: {
    async fetchCurrentUser() {
        try {
        const res = await axios.get('http://127.0.0.1:8001/api/user', {
            headers: { Accept: 'application/json' },
            withCredentials: true
        })
        this.user = res.data
        } catch (err) {
        if (err.response?.status === 401) {
            console.log('Chưa login, redirect tới login SPA')
            // ví dụ: router.push('/login')
        } else {
            console.error('API error:', err)
        }
    }

    },
  },
})
