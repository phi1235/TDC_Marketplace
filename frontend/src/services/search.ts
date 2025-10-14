import api from './api'
import type { Listing, PaginatedResponse } from './listings'

export interface SearchFilters {
  q: string
  category_id?: number
  condition?: string
  min_price?: number
  max_price?: number
  page?: number
}

export interface SearchSuggestion {
  id: number
  title: string
}

export const searchService = {
  async search(filters: SearchFilters): Promise<PaginatedResponse<Listing>> {
    const response = await api.get('/search', { params: filters })
    return response.data
  },

  async getSuggestions(query: string): Promise<SearchSuggestion[]> {
    if (!query || query.length < 2) {
      return []
    }
    
    const response = await api.get('/search/suggestions', { 
      params: { q: query } 
    })
    return response.data
  }
}
