import api from './api'

export const pickupApi = {
  list(params?: { search?: string; campus?: string; page?: number }) {
    return api.get('/pickup-points', { params })
  },
  listingList(listingId: number) {
    return api.get(`/listings/${listingId}/pickup-points`)
  },
  listingSync(listingId: number, ids: number[]) {
    return api.post(`/listings/${listingId}/pickup-points/sync`, { pickup_point_ids: ids })
  },
  setOrderPickup(orderId: number, payload: { pickup_point_id: number; pickup_scheduled_at?: string; pickup_note?: string }) {
    return api.post(`/orders/${orderId}/pickup`, payload)
  }
}
