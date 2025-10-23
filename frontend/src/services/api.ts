import axios from 'axios'

// In dev, prefer hitting Vite's proxy at /api. You can override with VITE_API_URL.
const API_BASE_URL = import.meta.env.VITE_API_URL || '/api'

// Create axios instance
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    console.log('🌐 [API Request]', config.method?.toUpperCase(), config.url)
    console.log('📍 Full URL:', config.baseURL + config.url)
    if (config.data instanceof FormData) {
      console.log('📦 FormData detected')
      for (let pair of config.data.entries()) {
        console.log('  -', pair[0], ':', pair[1])
      }
    } else {
      console.log('📦 Data:', config.data)
    }
    
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
      console.log('🔑 Auth token added')
    }
    return config
  },
  (error) => {
    console.error('❌ Request error:', error)
    return Promise.reject(error)
  }
)

// Response interceptor to handle auth errors
api.interceptors.response.use(
  (response) => {
    console.log('✅ [API Response]', response.status, response.config.url)
    console.log('📦 Response data:', response.data)
    return response
  },
  (error) => {
    console.error('❌ [API Error]', error.response?.status, error.config?.url)
    console.error('📦 Error data:', error.response?.data)
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default api
