import api from './api'

export interface WishlistItem {
  id: number
  user_id: number
  listing_id: number
  created_at: string
  updated_at: string
  listing: {
    id: number
    title: string
    price: number
    currency: string
    condition_grade: string
    view_count: number
    images?: Array<{
      id: number
      file_path: string
      is_primary: boolean
    }>
    seller?: {
      id: number
      name: string
    }
    category?: {
      id: number
      name: string
    }
  }
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

export const wishlistService = {
  async getWishlist(filters: { page?: number } = {}): Promise<PaginatedResponse<WishlistItem>> {
    const response = await api.get('/wishlists', { params: filters })
    return response.data
  },

  async toggleWishlist(listingId: number): Promise<{ message: string; is_favorited: boolean }> {
    const response = await api.post(`/wishlists/${listingId}/toggle`)
    return response.data
  },

  async checkWishlist(listingId: number): Promise<{ is_favorited: boolean }> {
    const response = await api.get(`/wishlists/${listingId}/check`)
    return response.data
  }
}
