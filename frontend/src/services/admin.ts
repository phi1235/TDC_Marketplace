import api from './api'

export interface DashboardStats {
  total_users: number
  active_listings: number
  pending_listings: number
  total_reports: number
  pending_reports: number
}

export const getDashboardStats = async (): Promise<DashboardStats> => {
  const { data } = await api.get('/admin/dashboard')
  return data
}

