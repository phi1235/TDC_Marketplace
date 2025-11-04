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