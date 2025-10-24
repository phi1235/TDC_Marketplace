<template>
  <div class="comparison-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">
        🔍 Search Engine Comparison Dashboard
      </h1>
      <p class="text-gray-600 mb-6">
        So sánh hiệu năng giữa Elasticsearch và Apache Solr cho hệ thống marketplace
      </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Elasticsearch</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.elasticsearch.avgResponseTime }}ms</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="flex items-center">
          <div class="p-2 bg-orange-100 rounded-lg">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Apache Solr</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.solr.avgResponseTime }}ms</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Success Rate</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.overall.successRate }}%</p>
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
            <p class="text-sm font-medium text-gray-600">Winner</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.overall.winner }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Dual Search Test -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
      <DualSearchTest />
    </div>

    <!-- Test Runner -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">🧪 Run Benchmark Tests</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Test Type</label>
          <select v-model="testConfig.type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="performance">Performance Test</option>
            <option value="vietnamese">Vietnamese Language Test</option>
            <option value="indexing">Indexing Test</option>
            <option value="all">All Tests</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Iterations</label>
          <input 
            v-model.number="testConfig.iterations" 
            type="number" 
            min="1" 
            max="100" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>
      </div>

      <div class="mt-6 flex space-x-4">
        <button 
          @click="runBenchmark" 
          :disabled="isRunning"
          class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
        >
          <svg v-if="isRunning" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isRunning ? 'Running...' : 'Run Benchmark' }}
        </button>

        <button 
          @click="refreshData" 
          class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 flex items-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Refresh Data
        </button>
      </div>
    </div>

    <!-- Performance Comparison Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">📊 Performance Comparison</h2>
      <div class="h-64 flex items-end space-x-4">
        <div class="flex flex-col items-center flex-1">
          <div 
            class="w-8 bg-blue-500 rounded-t transition-all duration-500"
            :style="{ height: `${elasticsearchBarHeight}px` }"
          ></div>
          <span class="text-xs text-gray-600 mt-2">Elasticsearch</span>
          <span class="text-xs text-gray-500">{{ stats.elasticsearch.avgResponseTime }}ms</span>
        </div>
        
        <div class="flex flex-col items-center flex-1">
          <div 
            class="w-8 bg-orange-500 rounded-t transition-all duration-500"
            :style="{ height: `${solrBarHeight}px` }"
          ></div>
          <span class="text-xs text-gray-600 mt-2">Solr</span>
          <span class="text-xs text-gray-500">{{ stats.solr.avgResponseTime }}ms</span>
        </div>
      </div>
    </div>

    <!-- Detailed Results Table -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">📋 Detailed Results</h2>
      
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Engine</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Time</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Throughput</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Success Rate</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="result in recentResults" :key="result.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="result.engine === 'elasticsearch' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'" 
                      class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ result.engine }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ result.test_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ result.response_time_avg }}ms</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ result.throughput }} QPS</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ result.success_rate }}%</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(result.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Search Quality Metrics -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">🎯 Search Quality Metrics</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h3 class="text-lg font-medium text-gray-900 mb-3">Elasticsearch</h3>
          <div class="space-y-2">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Precision:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.elasticsearch.precision }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Recall:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.elasticsearch.recall }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">F1 Score:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.elasticsearch.f1_score }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Vietnamese Score:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.elasticsearch.vietnamese_score }}%</span>
            </div>
          </div>
        </div>

        <div>
          <h3 class="text-lg font-medium text-gray-900 mb-3">Apache Solr</h3>
          <div class="space-y-2">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Precision:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.solr.precision }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Recall:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.solr.recall }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">F1 Score:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.solr.f1_score }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Vietnamese Score:</span>
              <span class="text-sm font-medium">{{ qualityMetrics.solr.vietnamese_score }}%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import DualSearchTest from '@/components/DualSearchTest.vue'

// Reactive data
const stats = ref({
  elasticsearch: { avgResponseTime: 0 },
  solr: { avgResponseTime: 0 },
  overall: { successRate: 0, winner: 'N/A' }
})

const qualityMetrics = ref({
  elasticsearch: { precision: 0, recall: 0, f1_score: 0, vietnamese_score: 0 },
  solr: { precision: 0, recall: 0, f1_score: 0, vietnamese_score: 0 }
})

const recentResults = ref([])
const isRunning = ref(false)

const testConfig = ref({
  type: 'performance',
  iterations: 5
})

// Computed properties
const elasticsearchBarHeight = computed(() => {
  const max = Math.max(stats.value.elasticsearch.avgResponseTime, stats.value.solr.avgResponseTime)
  return max > 0 ? (stats.value.elasticsearch.avgResponseTime / max) * 200 : 0
})

const solrBarHeight = computed(() => {
  const max = Math.max(stats.value.elasticsearch.avgResponseTime, stats.value.solr.avgResponseTime)
  return max > 0 ? (stats.value.solr.avgResponseTime / max) * 200 : 0
})

// Methods
const fetchSummary = async () => {
  try {
    const response = await axios.get('/api/comparison/summary')
    if (response.data.success) {
      const data = response.data.data
      stats.value = {
        elasticsearch: {
          avgResponseTime: Math.round(data.performance.elasticsearch.avg_response_time || 0)
        },
        solr: {
          avgResponseTime: Math.round(data.performance.solr.avg_response_time || 0)
        },
        overall: {
          successRate: data.performance.elasticsearch.success_rate || 0,
          winner: data.overall_winner || 'N/A'
        }
      }
      
      qualityMetrics.value = {
        elasticsearch: {
          precision: Math.round((data.quality.elasticsearch.precision || 0) * 100),
          recall: Math.round((data.quality.elasticsearch.recall || 0) * 100),
          f1_score: Math.round((data.quality.elasticsearch.f1_score || 0) * 100),
          vietnamese_score: Math.round((data.quality.elasticsearch.vietnamese_score || 0) * 100)
        },
        solr: {
          precision: Math.round((data.quality.solr.precision || 0) * 100),
          recall: Math.round((data.quality.solr.recall || 0) * 100),
          f1_score: Math.round((data.quality.solr.f1_score || 0) * 100),
          vietnamese_score: Math.round((data.quality.solr.vietnamese_score || 0) * 100)
        }
      }
    }
  } catch (error) {
    console.error('Failed to fetch summary:', error)
  }
}

const fetchRecentResults = async () => {
  try {
    const response = await axios.get('/api/comparison/recent')
    if (response.data.success) {
      recentResults.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch recent results:', error)
  }
}

const runBenchmark = async () => {
  isRunning.value = true
  try {
    const response = await axios.post('/api/comparison/test', {
      test_type: testConfig.value.type,
      options: {
        iterations: testConfig.value.iterations
      }
    })
    
    if (response.data.success) {
      // Refresh data after benchmark
      await Promise.all([fetchSummary(), fetchRecentResults()])
    }
  } catch (error) {
    console.error('Failed to run benchmark:', error)
  } finally {
    isRunning.value = false
  }
}

const refreshData = async () => {
  await Promise.all([fetchSummary(), fetchRecentResults()])
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString()
}

// Lifecycle
onMounted(() => {
  refreshData()
})
</script>

<style scoped>
.comparison-dashboard {
  padding: 1.5rem;
  background-color: #f9fafb;
  min-height: 100vh;
}

/* Animation for bars */
.bg-blue-500, .bg-orange-500 {
  transition: height 0.5s ease-in-out;
}

/* Hover effects */
.hover\:bg-gray-50:hover {
  background-color: #f9fafb;
}
</style>
