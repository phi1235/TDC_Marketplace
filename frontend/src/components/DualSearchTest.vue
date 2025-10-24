<template>
  <div class="dual-search-test">
    <h2 class="text-2xl font-bold mb-6">🔍 Dual Search Test</h2>
    <p class="text-gray-600 mb-6">Test search trên cả Elasticsearch và Solr cùng lúc</p>

    <!-- Search Input -->
    <div class="mb-6">
      <div class="flex items-center space-x-4">
        <input
          type="text"
          v-model="searchQuery"
          @keyup.enter="performDualSearch"
          placeholder="Nhập từ khóa tìm kiếm..."
          class="flex-1 p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button
          @click="performDualSearch"
          :disabled="loading || !searchQuery.trim()"
          class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Searching...' : 'Search' }}
        </button>
      </div>
    </div>

    <!-- Engine Status -->
    <div class="mb-6 flex items-center space-x-6">
      <div class="flex items-center space-x-2">
        <span class="w-3 h-3 rounded-full" :class="esOnline ? 'bg-green-500' : 'bg-red-500'"></span>
        <span class="text-sm font-medium">Elasticsearch {{ esOnline ? 'Online' : 'Offline' }}</span>
      </div>
      <div class="flex items-center space-x-2">
        <span class="w-3 h-3 rounded-full" :class="solrOnline ? 'bg-green-500' : 'bg-red-500'"></span>
        <span class="text-sm font-medium">Solr {{ solrOnline ? 'Online' : 'Offline' }}</span>
      </div>
    </div>

    <!-- Results -->
    <div v-if="results" class="space-y-6">
      <!-- Performance Comparison -->
      <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold mb-2">⚡ Performance Comparison</h3>
        <div class="grid grid-cols-2 gap-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ results.comparison.es_time }}ms</div>
            <div class="text-sm text-gray-600">Elasticsearch</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ results.comparison.solr_time }}ms</div>
            <div class="text-sm text-gray-600">Solr</div>
          </div>
        </div>
        <div class="mt-2 text-center">
          <span class="text-sm text-gray-600">
            {{ results.comparison.faster_engine }} is faster by {{ results.comparison.time_difference }}ms
          </span>
        </div>
      </div>

      <!-- Results Comparison -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Elasticsearch Results -->
        <div class="bg-white border border-gray-200 rounded-lg p-4">
          <h3 class="text-lg font-semibold mb-4 flex items-center">
            <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
            Elasticsearch ({{ results.results.elasticsearch.total }} results)
          </h3>
          <div class="space-y-3">
            <div v-for="item in results.results.elasticsearch.data" :key="item._id" class="border-b pb-3 last:border-b-0">
              <h4 class="font-medium text-gray-900">{{ item._source.title }}</h4>
              <p class="text-sm text-gray-600 mt-1">{{ item._source.description }}</p>
              <p class="text-sm text-blue-600 font-medium mt-1">{{ item._source.price }} VND</p>
            </div>
          </div>
        </div>

        <!-- Solr Results -->
        <div class="bg-white border border-gray-200 rounded-lg p-4">
          <h3 class="text-lg font-semibold mb-4 flex items-center">
            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
            Solr ({{ results.results.solr.total }} results)
          </h3>
          <div class="space-y-3">
            <div v-for="item in results.results.solr.data" :key="item.id" class="border-b pb-3 last:border-b-0">
              <h4 class="font-medium text-gray-900">{{ item.title }}</h4>
              <p class="text-sm text-gray-600 mt-1">{{ item.description }}</p>
              <p class="text-sm text-green-600 font-medium mt-1">{{ item.price }} VND</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Message -->
    <div v-if="successMessage" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
          </svg>
          <p class="text-green-700 font-medium">{{ successMessage }}</p>
        </div>
        <button @click="successMessage = null" class="text-green-500 hover:text-green-700">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
          </svg>
          <p class="text-red-700 font-medium">{{ error }}</p>
        </div>
        <button @click="error = null" class="text-red-500 hover:text-red-700">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Info Message -->
    <div v-if="infoMessage" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
          <p class="text-blue-700 font-medium">{{ infoMessage }}</p>
        </div>
        <button @click="infoMessage = null" class="text-blue-500 hover:text-blue-700">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

const searchQuery = ref('')
const loading = ref(false)
const results = ref<any>(null)
const error = ref<string | null>(null)
const successMessage = ref<string | null>(null)
const infoMessage = ref<string | null>(null)
const esOnline = ref(false)
const solrOnline = ref(false)

const performDualSearch = async () => {
  if (!searchQuery.value.trim()) {
    infoMessage.value = 'Vui lòng nhập từ khóa tìm kiếm'
    setTimeout(() => { infoMessage.value = null }, 3000)
    return
  }

  loading.value = true
  error.value = null
  successMessage.value = null
  infoMessage.value = null
  results.value = null

      try {
        const response = await axios.get('http://localhost:8001/api/search-dual', {
          params: { q: searchQuery.value }
        })

    if (response.data.success) {
      results.value = response.data
      const { elasticsearch, solr } = response.data.results
      const totalResults = elasticsearch.total + solr.total
      
      if (totalResults > 0) {
        successMessage.value = `Tìm thấy ${totalResults} kết quả! Elasticsearch: ${elasticsearch.total}, Solr: ${solr.total}`
      } else {
        infoMessage.value = 'Không tìm thấy kết quả nào cho từ khóa này'
      }
      
      // Auto hide success message after 5 seconds
      setTimeout(() => { successMessage.value = null }, 5000)
    } else {
      error.value = response.data.message || 'Search failed'
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Search failed'
    console.error('Search error:', err)
  } finally {
    loading.value = false
  }
}

    const checkEngineStatus = async () => {
      try {
        const response = await axios.get('http://localhost:8001/api/search-dual/ping')
    if (response.data.success) {
      esOnline.value = response.data.engines.elasticsearch
      solrOnline.value = response.data.engines.solr
      
      // Show status message
      if (esOnline.value && solrOnline.value) {
        infoMessage.value = 'Cả hai search engine đều đang hoạt động tốt!'
        setTimeout(() => { infoMessage.value = null }, 3000)
      } else if (esOnline.value || solrOnline.value) {
        const offlineEngines = []
        if (!esOnline.value) offlineEngines.push('Elasticsearch')
        if (!solrOnline.value) offlineEngines.push('Solr')
        error.value = `${offlineEngines.join(', ')} đang offline. Kết quả có thể không chính xác.`
      } else {
        error.value = 'Cả hai search engine đều offline!'
      }
    }
  } catch (err) {
    console.error('Failed to check engine status:', err)
    error.value = 'Không thể kiểm tra trạng thái search engine'
    esOnline.value = false
    solrOnline.value = false
  }
}

onMounted(() => {
  checkEngineStatus()
})
</script>

<style scoped>
.dual-search-test {
  padding: 1.5rem;
  background-color: #f9fafb;
  min-height: 100vh;
}
</style>
