import api from './api'

export interface LoginData {
  email: string
  password: string
  remember?: boolean
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
  agree_terms: boolean
}

export interface User {
  id: number
  name: string
  email: string
  role: string
  phone?: string
  avatar?: string
  email_verified_at?: string
  phone_verified_at?: string
  is_active: boolean
  last_login_at?: string
  login_count: number
  created_at: string
  updated_at: string
  seller_profile?: {
    student_id?: string
    verified_student: boolean
    rating: number
    total_ratings: number
    total_sales: number
    total_revenue: number
    bio?: string
    academic_year?: string
    major?: string
  }
}

export interface AuthResponse {
  message: string
  user: User
  token: string
}

export const authService = {
  async login(data: LoginData): Promise<AuthResponse> {
    const response = await api.post('/auth/login', data)
    return response.data
  },

  async register(data: RegisterData): Promise<AuthResponse> {
    const response = await api.post('/auth/register', data)
    return response.data
  },

  async logout(): Promise<void> {
    await api.post('/auth/logout')
  },

  async me(): Promise<{ user: User }> {
    const response = await api.get('/auth/me')
    return response.data
  },

  async refreshToken(): Promise<AuthResponse> {
    const response = await api.post('/auth/refresh')
    return response.data
  }
}
