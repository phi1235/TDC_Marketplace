<template>
  <div class="search-analytics-dashboard">
    <!-- Header -->
    <div class="dashboard-header mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">
        📊 Search Analytics Dashboard
      </h1>
      <p class="text-gray-600">
        Thống kê và phân tích hiệu năng tìm kiếm trong hệ thống
      </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Searches</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total_searches || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Elasticsearch Avg</p>
            <p class="text-2xl font-semibold text-gray-900">{{ Math.round(stats.avg_es_time || 0) }}ms</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="flex items-center">
          <div class="p-2 bg-orange-100 rounded-lg">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Solr Avg</p>
            <p class="text-2xl font-semibold text-gray-900">{{ Math.round(stats.avg_solr_time || 0) }}ms</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="flex items-center">
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Winning Engine</p>
            <p class="text-2xl font-semibold text-gray-900">{{ getWinningEngine() }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <!-- Performance Comparison Chart -->
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <h3 class="text-lg font-semibold mb-4">⚡ Performance Comparison</h3>
        <div class="h-64 flex items-center justify-center">
          <div class="text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2">{{ Math.round(stats.avg_es_time || 0) }}ms</div>
            <div class="text-sm text-gray-600 mb-4">Elasticsearch Average</div>
            <div class="text-4xl font-bold text-orange-600 mb-2">{{ Math.round(stats.avg_solr_time || 0) }}ms</div>
            <div class="text-sm text-gray-600">Solr Average</div>
          </div>
        </div>
      </div>

      <!-- Engine Wins Chart -->
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <h3 class="text-lg font-semibold mb-4">🏆 Engine Wins</h3>
        <div class="h-64 flex items-center justify-center">
          <div class="text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2">{{ stats.es_wins || 0 }}</div>
            <div class="text-sm text-gray-600 mb-4">Elasticsearch Wins</div>
            <div class="text-4xl font-bold text-orange-600 mb-2">{{ stats.solr_wins || 0 }}</div>
            <div class="text-sm text-gray-600">Solr Wins</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Popular Queries -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h3 class="text-lg font-semibold mb-4">🔥 Popular Search Queries</h3>
      <div class="space-y-2">
        <div
          v-for="(query, index) in popularQueries"
          :key="index"
          class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
        >
          <span class="font-medium">{{ query.query }}</span>
          <span class="text-sm text-gray-600">{{ query.search_count }} searches</span>
        </div>
        <div v-if="popularQueries.length === 0" class="text-center text-gray-500 py-8">
          No search data available yet
        </div>
      </div>
    </div>

    <!-- Performance Trends -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
      <h3 class="text-lg font-semibold mb-4">📈 Performance Trends</h3>
      <div class="space-y-4">
        <div
          v-for="(trend, index) in performanceTrends"
          :key="index"
          class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
        >
          <div>
            <div class="font-medium">{{ new Date(trend.date).toLocaleDateString() }}</div>
            <div class="text-sm text-gray-600">{{ trend.search_count }} searches</div>
          </div>
          <div class="text-right">
            <div class="text-sm text-blue-600">ES: {{ Math.round(trend.avg_es_time) }}ms</div>
            <div class="text-sm text-orange-600">Solr: {{ Math.round(trend.avg_solr_time) }}ms</div>
          </div>
        </div>
        <div v-if="performanceTrends.length === 0" class="text-center text-gray-500 py-8">
          No performance data available yet
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

// State
const stats = ref<any>({})
const popularQueries = ref<any[]>([])
const performanceTrends = ref<any[]>([])
const loading = ref(false)

// Methods
const fetchAnalytics = async () => {
  loading.value = true
  try {
    const [statsResponse, queriesResponse, trendsResponse] = await Promise.all([
      axios.get('/api/search-analytics/stats'),
      axios.get('/api/search-analytics/stats'),
      axios.get('/api/search-analytics/performance')
    ])

    stats.value = statsResponse.data.data.stats || {}
    popularQueries.value = statsResponse.data.data.popular_queries || []
    performanceTrends.value = trendsResponse.data.data || []
  } catch (error) {
    console.error('Failed to fetch analytics:', error)
  } finally {
    loading.value = false
  }
}

const getWinningEngine = () => {
  const esWins = stats.value.es_wins || 0
  const solrWins = stats.value.solr_wins || 0
  
  if (esWins > solrWins) return 'Elasticsearch'
  if (solrWins > esWins) return 'Solr'
  return 'Tie'
}

// Lifecycle
onMounted(() => {
  fetchAnalytics()
})
</script>

<style scoped>
.search-analytics-dashboard {
  @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8;
}
</style>
