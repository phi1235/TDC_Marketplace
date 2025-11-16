import api from './api'
import type { Major, CreateMajorData, UpdateMajorData, MajorWithRelations } from '@/types/major'

/**
 * Major Service
 * Handles all API calls related to majors (academic departments)
 */
export const majorService = {
  /**
   * Get all active majors
   * Public endpoint - no auth required
   */
  async getMajors(): Promise<Major[]> {
    const response = await api.get('/majors')
    return response.data.data || response.data
  },

  /**
   * Get single major by ID with relationships
   * @param id - Major ID
   */
  async getMajor(id: number): Promise<MajorWithRelations> {
    const response = await api.get(`/majors/${id}`)
    return response.data.data || response.data
  },

  /**
   * Create new major (Admin only)
   * @param data - Major creation data
   */
  async createMajor(data: CreateMajorData): Promise<Major> {
    const response = await api.post('/majors', data)
    return response.data.data || response.data
  },

  /**
   * Update existing major (Admin only)
   * @param id - Major ID
   * @param data - Major update data
   */
  async updateMajor(id: number, data: UpdateMajorData): Promise<Major> {
    const response = await api.put(`/majors/${id}`, data)
    return response.data.data || response.data
  },

  /**
   * Delete major (Admin only)
   * Note: Backend prevents deletion if major has users or listings
   * @param id - Major ID
   */
  async deleteMajor(id: number): Promise<void> {
    await api.delete(`/majors/${id}`)
  }
}

// Export individual functions for convenience
export const getMajors = () => majorService.getMajors()
export const getMajor = (id: number) => majorService.getMajor(id)
export const createMajor = (data: CreateMajorData) => majorService.createMajor(data)
export const updateMajor = (id: number, data: UpdateMajorData) => majorService.updateMajor(id, data)
export const deleteMajor = (id: number) => majorService.deleteMajor(id)

export default majorService
