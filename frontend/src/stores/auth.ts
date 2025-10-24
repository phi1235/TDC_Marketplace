import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

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
}

export const useAuthStore = defineStore('auth', () => {
  // Try to restore user from localStorage
  const savedUser = localStorage.getItem('auth_user')
  console.log('Auth - Initial load:', {
    savedUser: savedUser,
    parsedUser: savedUser ? JSON.parse(savedUser) : null
  })
  
  let parsedUser = null
  if (savedUser) {
    const temp = JSON.parse(savedUser)
    // Handle nested user structure from old data
    parsedUser = temp.user ? temp.user : temp
  }
  
  const user = ref<User | null>(parsedUser)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => {
    // Debug logging
    console.log('Auth - isAdmin computed:', {
      user: user.value,
      role: user.value?.role,
      isAdmin: user.value?.role === 'admin'
    })
    // Ensure user data is available before checking role
    return user.value?.role === 'admin'
  })

  // Set api default authorization header for current session (interceptor also handles this)
  if (token.value) {
    console.log('Auth - Token found, setting up API header')
    ;(api.defaults.headers as any).Authorization = `Bearer ${token.value}`
    // Auto fetch user if token exists but user is null
    if (!user.value) {
      console.log('Auth - No user data, fetching...')
      fetchUser()
    } else {
      console.log('Auth - User data already exists:', user.value)
    }
  } else {
    console.log('Auth - No token found')
  }

  async function login(credentials: LoginCredentials) {
    try {
      loading.value = true
      const response = await api.post('/auth/login', credentials)
      
      token.value = response.data.token
      user.value = response.data.user
      
      localStorage.setItem('auth_token', token.value)
      localStorage.setItem('auth_user', JSON.stringify(user.value)) // Store user directly, not nested
      ;(api.defaults.headers as any).Authorization = `Bearer ${token.value}`
      
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
      localStorage.setItem('auth_user', JSON.stringify(user.value)) // Store user directly, not nested
      ;(api.defaults.headers as any).Authorization = `Bearer ${token.value}`
      
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
    localStorage.removeItem('auth_user')
    delete (api.defaults.headers as any).Authorization
  }

  async function fetchUser() {
    if (!token.value) return

    try {
      console.log('Auth - fetchUser: Starting fetch...')
      const response = await api.get('/auth/me')
      user.value = response.data.user // Extract user from response.data.user
      localStorage.setItem('auth_user', JSON.stringify(user.value))
      console.log('Auth - fetchUser: Success:', user.value)
    } catch (error: any) {
      console.log('Auth - fetchUser: Error:', error.response?.status)
      // Only logout if token is explicitly invalid (401)
      if (error.response?.status === 401) {
        await logout()
      }
      // For other errors (network, 500, etc.), keep user logged in
    }
  }

  async function refreshUser() {
    if (token.value && user.value) {
      await fetchUser()
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
    refreshUser,
  }
})

//wish list
