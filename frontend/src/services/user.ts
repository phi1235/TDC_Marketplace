import api from './api'
import type { User } from './auth'

export interface UpdateProfileData {
  name?: string
  email?: string
  phone?: string
  major_id?: number | null
  avatar?: File
}

export const userService = {
  getAllUsers() {
    return api.get('/users')
  },

  async updateProfile(data: UpdateProfileData): Promise<{ user: User; message: string }> {
    const formData = new FormData()
    
    if (data.name !== undefined) formData.append('name', data.name)
    if (data.email !== undefined) formData.append('email', data.email)
    if (data.phone !== undefined) formData.append('phone', data.phone)
    
    // Add major_id (can be null to unset)
    if (data.major_id !== undefined) {
      if (data.major_id === null) {
        formData.append('major_id', '')
      } else {
        formData.append('major_id', data.major_id.toString())
      }
    }
    
    if (data.avatar) {
      formData.append('avatar', data.avatar)
    }

    const response = await api.post('/user', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data
  }
}

export const getAllUsers = async (): Promise<User[]> => {
  const res = await api.get('/users')
  return res.data
}

// export const toggleUserStatus = async (id: number): Promise<void> => {
//   await api.post(`/admin/users/${id}/toggle-status`)
// }

export const searchUsers = async (keyword: string): Promise<User[]> => {
  const { data } = await api.get('/users/search', {
    params: { q: keyword }
  });
  return data;
}

export const fetchMyActivities = async (params: Record<string, any> = {}) => {
  const { data } = await api.get('/my-activities', { params })
  return data
}

//Toggle block and unblock is_active
export async function toggleUserStatus(userId: number) {
  const res = await api.post(`/admin/users/${userId}/toggle-status`)
  return res.data
}