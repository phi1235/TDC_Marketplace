import api from '@/services/api'

export interface AdminListingQuery {
  page?: number
  per_page?: number
  search?: string
  status?: 'pending' | 'approved' | 'rejected'
}

export interface BulkActionPayload {
  action: 'approve' | 'reject' | 'delete'
  ids: number[]
  admin_notes?: string
  rejection_reason?: string
}

export const adminListingsService = {
  async list(params: AdminListingQuery = {}) {
    // Nếu có status=pending, gọi API pending riêng
    if (params.status === 'pending') {
      const res = await api.get('/admin/listings/pending', { params })
      return res.data
    }
    // Các trường hợp khác gọi API all listings
    const res = await api.get('/admin/listings', { params })
    return res.data
  },
  async approve(id: number, admin_notes?: string) {
    const res = await api.post(`/admin/listings/${id}/approve`, { admin_notes })
    return res.data
  },
  async reject(id: number, rejection_reason: string, admin_notes?: string) {
    const res = await api.post(`/admin/listings/${id}/reject`, { rejection_reason, admin_notes })
    return res.data
  },
  async bulkAction(payload: BulkActionPayload) {
    const res = await api.post('/admin/listings/bulk-action', payload)
    return res.data
  },
  async stats() {
    const res = await api.get('/admin/listings/stats')
    return res.data
  },
  async toggleActive(id: number) {
    // use public listing toggle; admin should be authorized
    const res = await api.post(`/listings/${id}/toggle-status`)
    return res.data
  },
  async deleteOne(id: number) {
    // use admin bulk delete for single id
    const res = await api.post('/admin/listings/bulk-action', { action: 'delete', listing_ids: [id] })
    return res.data
  },
}


