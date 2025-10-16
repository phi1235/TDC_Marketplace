import api from './api'

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
