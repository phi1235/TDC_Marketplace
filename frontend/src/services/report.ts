import api from './api'

export interface Report {
  id: number
  reporter_id: number
  reportable_type: string
  reportable_id: number
  reason: string
  description: string
  status: 'pending' | 'reviewed' | 'resolved' | 'dismissed'
  admin_notes?: string
  reviewed_by?: number
  reviewed_at?: string
  created_at: string
  updated_at: string
  reportable?: any
  reporter?: {
    id: number
    name: string
    email: string
  }
}

export interface CreateReportData {
  reportable_type: string
  reportable_id: number
  reason: string
  description: string
}

export interface ReportStats {
  total_reports: number
  pending_reports: number
  reviewed_reports: number
  resolved_reports: number
  dismissed_reports: number
}

export interface ReportFilters {
  status?: string
  type?: string
  search?: string
  sort?: string
  order?: 'asc' | 'desc'
  per_page?: number
  page?: number
}

export interface PaginatedReportResponse {
  data: Report[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

export interface ReportReason {
  key: string
  label: string
}

export interface ReportableType {
  key: string
  label: string
}

export const reportService = {
  /**
   * Get all reports for the current user
   */
  async getReports(filters: ReportFilters = {}): Promise<PaginatedReportResponse> {
    const response = await api.get('/reports', { params: filters })
    return response.data
  },

  /**
   * Get a specific report
   */
  async getReport(id: number): Promise<Report> {
    const response = await api.get(`/reports/${id}`)
    return response.data
  },

  /**
   * Create a new report
   */
  async createReport(data: CreateReportData): Promise<{ message: string; report: Report }> {
    const response = await api.post('/reports', data)
    return response.data
  },

  /**
   * Get report statistics for the current user
   */
  async getStats(): Promise<ReportStats> {
    const response = await api.get('/reports-stats')
    return response.data
  },

  /**
   * Get available report reasons
   */
  async getReportReasons(): Promise<ReportReason[]> {
    const response = await api.get('/report-reasons')
    const reasons = response.data
    return Object.entries(reasons).map(([key, label]) => ({ key, label: label as string }))
  },

  /**
   * Get available reportable types
   */
  async getReportableTypes(): Promise<ReportableType[]> {
    const response = await api.get('/reportable-types')
    const types = response.data
    return Object.entries(types).map(([key, label]) => ({ key, label: label as string }))
  }
}

