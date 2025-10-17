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
  favorite_count: number
  created_at: string
  updated_at: string
  seller?: {
    id: number
    name: string
    email: string
    seller_profile?: {
      student_id?: string
      verified_student: boolean
      rating: number
      total_ratings: number
    }
  }
  category?: {
    id: number
    name: string
    slug: string
  }
  images?: Array<{
    id: number
    image_path: string
    is_primary: boolean
    sort_order: number
  }>
  offers?: Array<{
    id: number
    offer_price: number
    message?: string
    status: string
    buyer: {
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
  }
}
