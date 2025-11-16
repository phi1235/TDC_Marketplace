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
  },

  async createCategory(data: Partial<Category>): Promise<Category> {
    const response = await api.post('/categories', data)
    return response.data
  },

  async updateCategory(id: number, data: Partial<Category>): Promise<Category> {
    const response = await api.put(`/categories/${id}`, data)
    return response.data
  },

  async deleteCategory(id: number): Promise<void> {
    await api.delete(`/categories/${id}`)
  }
}
