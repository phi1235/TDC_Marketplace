import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useSearchStore = defineStore('search', () => {
  // State
  const searchQuery = ref('')
  const searchResults = ref<any[]>([])
  const selectedEngine = ref('elasticsearch')
  const isLoading = ref(false)
  const searchHistory = ref<string[]>([])
  const error = ref<string | null>(null)

  // Getters
  const hasResults = computed(() => searchResults.value.length > 0)
  const hasQuery = computed(() => searchQuery.value.trim().length > 0)

  // Actions
  const setSearchQuery = (query: string) => {
    searchQuery.value = query
  }

  const setSelectedEngine = (engine: string) => {
    selectedEngine.value = engine
  }

  const performSearch = async (query: string, engine?: string) => {
    if (!query.trim()) {
      searchResults.value = []
      return
    }

    isLoading.value = true
    error.value = null

    try {
      const searchEngine = engine || selectedEngine.value
      const endpoint = searchEngine === 'elasticsearch' ? '/api/search-es' : '/api/search-solr'
      
      const response = await axios.get(endpoint, {
        params: { q: query }
      })

      searchResults.value = response.data.data || []
      
      // Add to history if not already present
      if (!searchHistory.value.includes(query)) {
        searchHistory.value.unshift(query)
        // Keep only last 10 searches
        if (searchHistory.value.length > 10) {
          searchHistory.value = searchHistory.value.slice(0, 10)
        }
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Search failed'
      searchResults.value = []
    } finally {
      isLoading.value = false
    }
  }

  const clearResults = () => {
    searchResults.value = []
    error.value = null
  }

  const clearHistory = () => {
    searchHistory.value = []
  }

  const removeFromHistory = (query: string) => {
    const index = searchHistory.value.indexOf(query)
    if (index > -1) {
      searchHistory.value.splice(index, 1)
    }
  }

  return {
    // State
    searchQuery,
    searchResults,
    selectedEngine,
    isLoading,
    searchHistory,
    error,
    
    // Getters
    hasResults,
    hasQuery,
    
    // Actions
    setSearchQuery,
    setSelectedEngine,
    setSearchEngine: setSelectedEngine, // Alias for compatibility
    performSearch,
    clearResults,
    clearHistory,
    removeFromHistory
  }
})
