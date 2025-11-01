import axios from 'axios'
const API = import.meta.env.VITE_API_BASE_URL || 'http://localhost/api'

export const pickupApi = {
  list(params?: { search?: string; campus?: string; page?: number }) {
    return axios.get(`${API}/pickup-points`, { params })
  },
  listingList(listingId: number) {
    return axios.get(`${API}/listings/${listingId}/pickup-points`)
  },
  listingSync(listingId: number, ids: number[]) {
    return axios.post(`${API}/listings/${listingId}/pickup-points/sync`, { pickup_point_ids: ids })
  },
  setOrderPickup(orderId: number, payload: { pickup_point_id: number; pickup_scheduled_at?: string; pickup_note?: string }) {
    return axios.post(`${API}/orders/${orderId}/pickup`, payload)
  }
}
