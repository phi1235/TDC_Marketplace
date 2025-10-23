<template>
  <div class="p-6 max-w-7xl mx-auto">
    <h2 class="text-xl font-bold mb-4">🔍 Tìm kiếm sản phẩm</h2>

    <!-- Ô nhập -->
    <div class="relative flex gap-3 mb-6">
      <div class="flex-1 relative">
        <input
          v-model="keyword"
          type="text"
          autocomplete="off"
          placeholder="Nhập từ khóa..."
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
          @focus="showDropdown = true"
          @blur="hideDropdown"
          @input="handleInput"
          @compositionstart="isComposing = true"
          @compositionend="handleCompositionEnd"
          @keydown.enter.prevent="handleEnter"
          @keydown.down.prevent="moveDown"
          @keydown.up.prevent="moveUp"
        />

        <!-- Dropdown -->
        <ul
          v-if="showDropdown && (loadingSuggest || suggestions.length)"
          class="absolute left-0 top-full z-10 w-full bg-white border border-gray-200 rounded-lg shadow-md mt-1 max-h-60 overflow-auto"
        >
          <li v-if="loadingSuggest" class="px-4 py-2 text-gray-400 italic">Đang gợi ý...</li>

          <li
            v-for="(item, i) in suggestions"
            :key="i"
            class="px-4 py-2 cursor-pointer transition"
            :class="i === selectedIndex ? 'bg-blue-100 font-semibold' : 'hover:bg-blue-50'"
            @mousedown.prevent="selectSuggestion(item)"
          >
            <span v-html="highlight(item)"></span>
          </li>
        </ul>
      </div>

      <button
        @click="searchProducts"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
      >
        Tìm kiếm
      </button>
    </div>

    <!-- 🔎 Lịch sử tìm kiếm -->
    <div v-if="history.length" class="mb-4">
      <div class="flex items-center justify-between mb-2">
        <h4 class="font-semibold">🔎 Lịch sử tìm kiếm gần đây</h4>
        <button
          @click="clearHistory"
          class="text-sm text-red-600 hover:text-red-700 hover:underline transition"
        >
          🗑 Xóa lịch sử
        </button>
      </div>

      <ul class="flex flex-wrap gap-2">
        <li
          v-for="(h, i) in history"
          :key="i + h.keyword"
          class="bg-gray-100 px-3 py-1 rounded cursor-pointer hover:bg-gray-200"
          @click="selectSuggestion(h.keyword)"
          :title="new Date(h.timestamp).toLocaleString() + ' • ' + h.results_count + ' kết quả'"
        >
          {{ h.keyword }}
        </li>
      </ul>
    </div>

    <!-- Loading shimmer -->
    <transition-group name="fade" tag="div" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div
        v-if="loading"
        v-for="n in 6"
        :key="'skeleton-' + n"
        class="border rounded-lg shadow-sm p-4 animate-pulse"
      >
        <div class="bg-gray-300 h-40 w-full rounded-lg mb-3 shimmer"></div>
        <div class="h-4 bg-gray-300 rounded w-3/4 mb-2 shimmer"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2 mb-2 shimmer"></div>
        <div class="h-5 bg-gray-400 rounded w-1/3 shimmer"></div>
      </div>

      <!-- Kết quả -->
      <div
        v-else
        v-for="item in results"
        :key="item._id"
        class="border rounded-lg shadow-sm hover:shadow-md transition p-4 fade-item"
      >
        <img
          :src="item._source.image || 'https://picsum.photos/300/200'"
          class="w-full h-40 object-cover rounded-lg mb-3"
        />
        <h3 class="font-semibold text-lg mb-1">{{ item._source.title }}</h3>
        <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ item._source.description }}</p>
        <p class="font-bold text-blue-600">{{ formatPrice(item._source.price) }}₫</p>
      </div>
    </transition-group>

    <div v-if="!loading && !results.length" class="text-gray-500 mt-4 text-center">
      Không tìm thấy sản phẩm nào.
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const keyword = ref('')
const results = ref([])
const suggestions = ref([])
const history = ref([])
const showDropdown = ref(false)
const loading = ref(false)
const loadingSuggest = ref(false)
const isComposing = ref(false)
const selectedIndex = ref(-1)

let debounceTimer = null
let searchTimer = null
let abortController = null
const cache = new Map()

// 🧠 Input realtime
const handleInput = () => {
  if (isComposing.value) return
  showDropdown.value = true
  selectedIndex.value = -1
  debounceSuggest()
}

const handleCompositionEnd = () => {
  isComposing.value = false
  debounceSuggest()
}

const debounceSuggest = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchSuggestions, 120)
}

const fetchSuggestions = async () => {
  const q = keyword.value.trim()
  if (!q) {
    suggestions.value = []
    return
  }

  if (cache.has('suggest_' + q)) {
    suggestions.value = cache.get('suggest_' + q)
    return
  }

  if (abortController) abortController.abort()
  abortController = new AbortController()

  loadingSuggest.value = true
  try {
    const res = await fetch(`http://localhost:8001/api/search-es/suggest?q=${encodeURIComponent(q)}`, {
      signal: abortController.signal,
    })
    const data = await res.json()
    suggestions.value = data.suggestions || []
    cache.set('suggest_' + q, suggestions.value)
  } catch (e) {
    if (e.name !== 'AbortError') console.error('Suggest error:', e)
  } finally {
    loadingSuggest.value = false
  }
}

watch(keyword, (newVal) => {
  clearTimeout(searchTimer)
  if (!newVal.trim()) {
    results.value = []
    return
  }
  if (newVal.trim().length < 2) return
  searchTimer = setTimeout(searchProducts, 300)
})

// 🔎 API chính
const searchProducts = async () => {
  const q = keyword.value.trim()
  if (!q) return

  if (cache.has('search_' + q)) {
    results.value = cache.get('search_' + q)
    return
  }

  loading.value = true
  showDropdown.value = false

  try {
    const res = await fetch(`http://localhost:8001/api/search-es?q=${encodeURIComponent(q)}`)
    const data = await res.json()
    results.value = data.data || []
    cache.set('search_' + q, results.value)
    fetchHistory()
  } catch (err) {
    console.error('Search error:', err)
  } finally {
    setTimeout(() => (loading.value = false), 200)
  }
}

// 📜 Lấy & xóa lịch sử
const fetchHistory = async () => {
  try {
    const res = await fetch('http://localhost:8001/api/search-es/history', { credentials: 'include' })
    const data = await res.json()
    history.value = data.history || []
  } catch (e) {
    console.error('History error:', e)
  }
}

const clearHistory = async () => {
  if (!confirm('Bạn có chắc muốn xóa toàn bộ lịch sử tìm kiếm?')) return
  try {
    const res = await fetch('http://localhost:8001/api/search-es/history/clear', {
      method: 'DELETE',
      credentials: 'include',
    })
    const data = await res.json()
    if (data.success) {
      history.value = []
      alert('🗑 Đã xóa lịch sử tìm kiếm!')
    } else {
      alert('❌ Xóa thất bại, thử lại sau.')
    }
  } catch (e) {
    console.error('Clear history error:', e)
  }
}

// ⌨️ Điều hướng phím
const moveDown = () => {
  if (!suggestions.value.length) return
  selectedIndex.value = (selectedIndex.value + 1) % suggestions.value.length
  keyword.value = suggestions.value[selectedIndex.value]
}

const moveUp = () => {
  if (!suggestions.value.length) return
  selectedIndex.value = (selectedIndex.value - 1 + suggestions.value.length) % suggestions.value.length
  keyword.value = suggestions.value[selectedIndex.value]
}

const handleEnter = () => {
  if (selectedIndex.value >= 0 && suggestions.value[selectedIndex.value]) {
    selectSuggestion(suggestions.value[selectedIndex.value])
  } else {
    searchProducts()
  }
}

const selectSuggestion = (item) => {
  keyword.value = item
  showDropdown.value = false
  searchProducts()
}

const hideDropdown = () => setTimeout(() => (showDropdown.value = false), 200)

const highlight = (text) => {
  const q = keyword.value
  if (!q) return text
  const regex = new RegExp(`(${q})`, 'gi')
  return text.replace(regex, '<mark class="bg-yellow-200">$1</mark>')
}

const formatPrice = (p) => Number(p).toLocaleString('vi-VN')

onMounted(fetchHistory)
</script>
