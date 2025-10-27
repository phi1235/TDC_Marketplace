import api from './api'
import type { User } from './auth'

// Re-export User type for convenience
export type { User }

export const userService = {
  getAllUsers() {
    return api.get('/users')
  }
}

export const getAllUsers = async (): Promise<User[]> => {
  const res = await api.get('/users')
  return res.data
}

export const toggleUserStatus = async (id: number): Promise<void> => {
  await api.post(`/admin/users/${id}/toggle-status`)
}

export const searchUsers = async (keyword: string): Promise<User[]> => {
  const { data } = await api.get('/users/search', {
    params: { q: keyword }
  });
  return data;
}