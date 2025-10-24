<template>
  <div class="admin-search-analytics">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
      <h3 class="text-lg font-semibold text-blue-800 mb-2">🔧 Admin Search Analytics</h3>
      <p class="text-sm text-blue-600">Thông số kỹ thuật cho việc so sánh hiệu năng search engines</p>
    </div>

    <!-- Real-time Search Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg border">
        <div class="text-center">
          <div class="text-2xl font-bold text-blue-600">{{ stats.total_searches || 0 }}</div>
          <div class="text-sm text-gray-600">Total Searches</div>
        </div>
      </div>
      <div class="bg-white p-4 rounded-lg border">
        <div class="text-center">
          <div class="text-2xl font-bold text-green-600">{{ Math.round(stats.avg_es_time || 0) }}ms</div>
          <div class="text-sm text-gray-600">ES Avg Time</div>
        </div>
      </div>
      <div class="bg-white p-4 rounded-lg border">
        <div class="text-center">
          <div class="text-2xl font-bold text-orange-600">{{ Math.round(stats.avg_solr_time || 0) }}ms</div>
          <div class="text-sm text-gray-600">Solr Avg Time</div>
        </div>
      </div>
    </div>

    <!-- Engine Performance -->
    <div class="bg-white p-4 rounded-lg border mb-6">
      <h4 class="font-semibold mb-3">⚡ Engine Performance</h4>
      <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
          <div class="text-xl font-bold text-blue-600">{{ stats.es_wins || 0 }}</div>
          <div class="text-sm text-gray-600">Elasticsearch Wins</div>
        </div>
        <div class="text-center">
          <div class="text-xl font-bold text-orange-600">{{ stats.solr_wins || 0 }}</div>
          <div class="text-sm text-gray-600">Solr Wins</div>
        </div>
      </div>
    </div>

    <!-- Popular Queries -->
    <div class="bg-white p-4 rounded-lg border">
      <h4 class="font-semibold mb-3">🔥 Popular Queries</h4>
      <div class="space-y-2">
        <div
          v-for="(query, index) in popularQueries.slice(0, 5)"
          :key="index"
          class="flex justify-between items-center p-2 bg-gray-50 rounded"
        >
          <span class="font-medium">{{ query.query }}</span>
          <span class="text-sm text-gray-600">{{ query.search_count }} searches</span>
        </div>
        <div v-if="popularQueries.length === 0" class="text-center text-gray-500 py-4">
          No search data yet
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// State
const stats = ref({})
const popularQueries = ref([])
const loading = ref(false)

// Methods
const fetchStats = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/search-analytics/stats')
    stats.value = response.data.data.stats || {}
    popularQueries.value = response.data.data.popular_queries || []
  } catch (error) {
    console.error('Failed to fetch search stats:', error)
  } finally {
    loading.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchStats()
})
</script>

<style scoped>
.admin-search-analytics {
  @apply max-w-4xl mx-auto;
}
</style>
