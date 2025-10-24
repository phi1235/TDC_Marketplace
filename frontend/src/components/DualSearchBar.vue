<template>
  <div class="dual-search-bar">
    <!-- Search Input -->
    <div class="relative flex gap-2">
      <div class="flex-1 relative">
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="placeholder"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
          @keyup.enter="handleSearch"
          @input="handleInput"
        />
        
        <!-- Loading indicator -->
        <div v-if="isLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
          <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
        </div>
      </div>

      <button
        @click="handleSearch"
        :disabled="!searchQuery.trim() || isLoading"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
      >
        {{ isLoading ? 'Searching...' : 'Search' }}
      </button>
    </div>

    <!-- Engine Status -->
    <div v-if="showEngineStatus" class="flex items-center gap-4 mt-2 text-sm">
      <div class="flex items-center gap-1">
        <span class="w-2 h-2 rounded-full" :class="enginesStatus.elasticsearch ? 'bg-green-500' : 'bg-red-500'"></span>
        <span>Elasticsearch {{ enginesStatus.elasticsearch ? 'Online' : 'Offline' }}</span>
      </div>
      <div class="flex items-center gap-1">
        <span class="w-2 h-2 rounded-full" :class="enginesStatus.solr ? 'bg-green-500' : 'bg-red-500'"></span>
        <span>Solr {{ enginesStatus.solr ? 'Online' : 'Offline' }}</span>
      </div>
    </div>

    <!-- User-friendly search results -->
    <div v-if="searchResult && showResults && !isAdmin" class="mt-4">
      <div class="bg-white border rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2">
          🔍 Kết quả tìm kiếm ({{ getBestResults().length }} kết quả)
        </h3>
        <div class="space-y-2 max-h-60 overflow-y-auto">
          <div
            v-for="item in getBestResults().slice(0, 10)"
            :key="item.id"
            class="p-3 bg-gray-50 rounded text-sm hover:bg-gray-100 transition"
          >
            <div class="font-medium">{{ item.title }}</div>
            <div class="text-gray-600 text-xs">{{ item.description?.substring(0, 100) }}...</div>
            <div class="text-green-600 font-semibold mt-1">{{ formatPrice(item.price) }} VND</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Results Comparison - Only show for admin -->
    <div v-if="searchResult && showResults && isAdmin" class="mt-4 space-y-4">
      <!-- Performance Comparison -->
      <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold mb-2">⚡ Performance Comparison</h3>
        <div class="grid grid-cols-2 gap-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ searchResult.comparison.es_time }}ms</div>
            <div class="text-sm text-gray-600">Elasticsearch</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ searchResult.comparison.solr_time }}ms</div>
            <div class="text-sm text-gray-600">Solr</div>
          </div>
        </div>
        <div class="text-center mt-2">
          <span class="text-sm font-medium">
            {{ searchResult.comparison.winner === 'elasticsearch' ? 'Elasticsearch' : 'Solr' }} 
            is faster by {{ searchResult.comparison.time_difference }}ms
          </span>
        </div>
      </div>

      <!-- Results Comparison -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Elasticsearch Results -->
        <div class="border rounded-lg p-4">
          <h4 class="font-semibold text-blue-600 mb-2">
            Elasticsearch ({{ searchResult.elasticsearch.count }} results)
          </h4>
          <div class="space-y-2 max-h-60 overflow-y-auto">
            <div
              v-for="item in searchResult.elasticsearch.data.slice(0, 5)"
              :key="`es-${item.id}`"
              class="p-2 bg-blue-50 rounded text-sm"
            >
              <div class="font-medium">{{ item.title }}</div>
              <div class="text-gray-600 text-xs">{{ item.description?.substring(0, 100) }}...</div>
            </div>
          </div>
        </div>

        <!-- Solr Results -->
        <div class="border rounded-lg p-4">
          <h4 class="font-semibold text-green-600 mb-2">
            Solr ({{ searchResult.solr.count }} results)
          </h4>
          <div class="space-y-2 max-h-60 overflow-y-auto">
            <div
              v-for="item in searchResult.solr.data.slice(0, 5)"
              :key="`solr-${item.id}`"
              class="p-2 bg-green-50 rounded text-sm"
            >
              <div class="font-medium">{{ item.title }}</div>
              <div class="text-gray-600 text-xs">{{ item.description?.substring(0, 100) }}...</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Admin Analytics Component -->
    <AdminSearchAnalytics v-if="isAdmin && showAnalytics" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { dualSearchService } from '@/services/dualSearch'
import { useAuthStore } from '@/stores/auth'
import AdminSearchAnalytics from './AdminSearchAnalytics.vue'

interface Props {
  placeholder?: string
  showEngineStatus?: boolean
  showResults?: boolean
  showAnalytics?: boolean
  autoSearch?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Tìm kiếm sản phẩm...',
  showEngineStatus: true,
  showResults: true,
  showAnalytics: true,
  autoSearch: false
})

const emit = defineEmits(['search', 'results'])

// State
const searchQuery = ref('')
const isLoading = ref(false)
const searchResult = ref(null)
const enginesStatus = ref({ elasticsearch: false, solr: false })
const auth = useAuthStore()

// Computed
const analytics = computed(() => dualSearchService.getSearchAnalytics())
const averageTimes = computed(() => dualSearchService.getAverageResponseTime())
const winningEngine = computed(() => dualSearchService.getWinningEngine())
const isAdmin = computed(() => auth.user?.role === 'admin')

// Methods
const handleInput = () => {
  if (props.autoSearch && searchQuery.value.length > 2) {
    handleSearch()
  }
}

const handleSearch = async () => {
  if (!searchQuery.value.trim()) return

  isLoading.value = true
  try {
    const result = await dualSearchService.performDualSearch(searchQuery.value)
    searchResult.value = result
    
    emit('search', result)
    emit('results', result.elasticsearch.data, result.solr.data)
  } catch (error) {
    console.error('Dual search failed:', error)
  } finally {
    isLoading.value = false
  }
}

const checkEnginesStatus = async () => {
  enginesStatus.value = await dualSearchService.checkEnginesStatus()
}

const getBestResults = () => {
  if (!searchResult.value) return []
  
  // Return results from the winning engine
  const winner = searchResult.value.comparison.winner
  if (winner === 'elasticsearch') {
    return searchResult.value.elasticsearch.data
  } else {
    return searchResult.value.solr.data
  }
}

const formatPrice = (price) => {
  return Number(price).toLocaleString('vi-VN')
}

// Lifecycle
onMounted(() => {
  checkEnginesStatus()
})
</script>

<style scoped>
.dual-search-bar {
  @apply w-full;
}
</style>
