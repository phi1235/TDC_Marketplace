import api from './api'

export interface Category {
  id: number
  name: string
  slug: string
  description?: string
  icon?: string
  is_active: boolean
  created_at: string
  updated_at: string
}

export const categoriesService = {
  async getCategories(): Promise<Category[]> {
    const response = await api.get('/categories')
    return response.data
  },

  async getCategory(id: number): Promise<Category> {
    const response = await api.get(`/categories/${id}`)
    return response.data
  }
}
