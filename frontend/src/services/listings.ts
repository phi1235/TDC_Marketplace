import api from './api'

export interface Listing {
  id: number
  seller_id: number
  category_id: number
  title: string
  slug: string
  description: string
  condition: 'new' | 'like_new' | 'good' | 'fair'
  price: number
  status: 'pending' | 'approved' | 'rejected' | 'sold' | 'archived'
  featured_until?: string
  view_count: number
  views_count: number // Backend uses this field name
  favorite_count: number
  created_at: string
  updated_at: string
  seller?: {
    id: number
    name: string
    email: string
    phone?: string
    avatar?: string
    seller_profile?: {
      student_id?: string
      verified_student: boolean
      rating: number
      total_ratings: number
      phone?: string
      address?: string
      total_sales?: number
    }
  }
  category?: {
    id: number
    name: string
    slug: string
    icon?: string
    description?: string
  }
  images?: Array<{
    id: number
    image_path: string
    is_primary: boolean
    sort_order?: number
    original_name?: string
    file_size?: number
    mime_type?: string
    width?: number
    height?: number
  }>
  offers?: Array<{
    id: number
    offer_price?: number
    amount?: number // Backend uses this field name
    message?: string
    status: string
    buyer?: {
      id: number
      name: string
    }
  }>
}

export interface CreateListingData {
  category_id: number
  title: string
  description: string
  condition: 'new' | 'like_new' | 'good' | 'fair'
  price: number
  images: File[]
}

export interface UpdateListingData {
  category_id?: number
  title?: string
  description?: string
  condition?: 'new' | 'like_new' | 'good' | 'fair'
  price?: number
  images?: File[]
}

export interface ListingFilters {
  category_id?: number
  condition?: string
  min_price?: number
  max_price?: number
  search?: string
  sort?: string
  order?: 'asc' | 'desc'
  page?: number
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

export const listingsService = {
  async getListings(filters: ListingFilters = {}): Promise<PaginatedResponse<Listing>> {
    const response = await api.get('/listings', { params: filters })
    return response.data
  },

  async getListing(id: number): Promise<Listing> {
    const response = await api.get(`/listings/${id}`)
    return response.data
  },

  async createListing(data: CreateListingData): Promise<{ message: string; listing: Listing }> {
    const formData = new FormData()
    
    // Add text fields
    formData.append('category_id', data.category_id.toString())
    formData.append('title', data.title)
    formData.append('description', data.description)
    formData.append('condition', data.condition)
    formData.append('price', data.price.toString())
    
    // Add images
    data.images.forEach((image, index) => {
      formData.append(`images[${index}]`, image)
    })

    const response = await api.post('/listings', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return response.data
  },

  async updateListing(id: number, data: UpdateListingData): Promise<{ message: string; listing: Listing }> {
    const formData = new FormData()
    
    // Add text fields
    if (data.category_id !== undefined) formData.append('category_id', data.category_id.toString())
    if (data.title !== undefined) formData.append('title', data.title)
    if (data.description !== undefined) formData.append('description', data.description)
    if (data.condition !== undefined) formData.append('condition', data.condition)
    if (data.price !== undefined) formData.append('price', data.price.toString())
    
    // Add images if provided
    if (data.images) {
      data.images.forEach((image, index) => {
        formData.append(`images[${index}]`, image)
      })
    }

    const response = await api.post(`/listings/${id}?_method=PUT`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return response.data
  },

  async deleteListing(id: number): Promise<{ message: string }> {
    const response = await api.delete(`/listings/${id}`)
    return response.data
  },

  async getMyListings(filters: ListingFilters = {}): Promise<PaginatedResponse<Listing>> {
    const response = await api.get('/my-listings', { params: filters })
    return response.data
  },

  async duplicateListing(id: number): Promise<{ message: string; listing: Listing }> {
    const response = await api.post(`/listings/${id}/duplicate`)
    return response.data
  },

  async toggleStatus(id: number): Promise<{ message: string; listing: Listing }> {
    const response = await api.post(`/listings/${id}/toggle-status`)
    return response.data
  },

  // Get related listings (same category)
  async getRelatedListings(id: number, categoryId: number, limit: number = 4): Promise<Listing[]> {
    const response = await api.get('/listings', {
      params: {
        category_id: categoryId,
        per_page: limit + 1, // Get one extra to exclude current listing
        sort: 'created_at',
        order: 'desc'
      }
    })
    // Filter out current listing
    return response.data.data.filter((listing: Listing) => listing.id !== id).slice(0, limit)
  }
}
