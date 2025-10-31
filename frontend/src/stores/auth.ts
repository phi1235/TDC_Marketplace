import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
//import api from '@/lib/api';

interface User {
  id: number
  name: string
  email: string
  role: 'user' | 'admin'
  phone?: string
  email_verified_at?: string
}

interface LoginCredentials {
  email: string
  password: string
}

interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
  otp_code?: string
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(
    (() => {
      try {
        const raw = localStorage.getItem('user')
        return raw ? (JSON.parse(raw) as User) : null
      } catch {
        return null
      }
    })()
  )
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  // Set api default authorization header for current session (interceptor also handles this)
  if (token.value) {
    ; (api.defaults.headers as any).Authorization = `Bearer ${token.value}`
  }

  async function login(credentials: LoginCredentials) {
    try {
      loading.value = true
      const response = await api.post('/auth/login', credentials)

      token.value = response.data.token
      user.value = response.data.user

      localStorage.setItem('auth_token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))
        ; (api.defaults.headers as any).Authorization = `Bearer ${token.value}`

      return { success: true }
    } catch (error: any) {
      return {
        success: false,
        error: error.response?.data?.message || 'Login failed'
      }
    } finally {
      loading.value = false
    }
  }

  async function register(data: RegisterData) {
    try {
      loading.value = true
      const response = await api.post('/auth/register', data)

      token.value = response.data.token
      user.value = response.data.user

      localStorage.setItem('auth_token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))
        ; (api.defaults.headers as any).Authorization = `Bearer ${token.value}`

      return { success: true }
    } catch (error: any) {
      return {
        success: false,
        error: error.response?.data?.message || 'Registration failed',
        errors: error.response?.data?.errors
      }
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await api.post('/auth/logout')
    } catch (error) {
      // Continue with logout even if API call fails
    }

    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    delete (api.defaults.headers as any).Authorization
  }

  async function fetchUser() {
    if (!token.value) return

    try {
      const response = await api.get('/auth/me')
      user.value = response.data
      localStorage.setItem('user', JSON.stringify(user.value))
    } catch (error) {
      // Token might be invalid, logout
      await logout()
    }
  }

  async function sendEduOtp(payload: { email: string }) {
    try {
      loading.value = true
      // Gọi API gửi mã OTP đến email sinh viên
      // Đổi URL này nếu backend của bạn khác: '/students/send-otp' ...
      const res = await api.post('/auth/send-otp', payload)
      return { success: true, data: res.data }
    } catch (error: any) {
      // Chuẩn hóa lỗi trả về để FE dễ hiển thị
      if (error.response?.status === 422) {
        return { success: false, errors: error.response.data?.errors }
      }
      return {
        success: false,
        error: error.response?.data?.message || 'Gửi OTP thất bại'
      }
    } finally {
      loading.value = false
    }
  }
  return {
    user,
    token,
    loading,
    isAuthenticated,
    isAdmin,
    login,
    register,
    logout,
    fetchUser,
    sendEduOtp,      // <-- thêm
    // verifyEduOtp, // <-- nếu dùng
  }
})

//wish list
