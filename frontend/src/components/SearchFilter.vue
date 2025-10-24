<template>
  <div class="p-6 max-w-7xl mx-auto">
    <h2 class="text-xl font-bold mb-4">
      🔍 Kết quả tìm kiếm cho: <span class="text-blue-600">{{ keyword }}</span>
    </h2>

    <transition-group name="fade" tag="div" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div v-if="loading" v-for="n in 6" :key="'skeleton-' + n" class="border rounded-lg shadow-sm p-4 animate-pulse">
        <div class="bg-gray-300 h-40 w-full rounded-lg mb-3 shimmer"></div>
        <div class="h-4 bg-gray-300 rounded w-3/4 mb-2 shimmer"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2 mb-2 shimmer"></div>
        <div class="h-5 bg-gray-400 rounded w-1/3 shimmer"></div>
      </div>

      <div v-else v-for="item in results" :key="item._id" class="border rounded-lg shadow-sm hover:shadow-md transition p-4 fade-item">
        <img :src="item._source.image || 'https://picsum.photos/300/200'" class="w-full h-40 object-cover rounded-lg mb-3" />
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

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const keyword = ref('')
const results = ref<any[]>([])
const loading = ref(false)
const cache = new Map()

const searchProducts = async () => {
  const q = keyword.value.trim()
  if (!q) return

  if (cache.has('search_' + q)) {
    results.value = cache.get('search_' + q)
    return
  }

  loading.value = true
  try {
    const res = await fetch(`http://localhost:8001/api/search-es?q=${encodeURIComponent(q)}`)
    const data = await res.json()
    results.value = data.data || []
    cache.set('search_' + q, results.value)
  } catch (err) {
    console.error('Search error:', err)
  } finally {
    setTimeout(() => (loading.value = false), 200)
  }
}

const formatPrice = (p: number) => Number(p).toLocaleString('vi-VN')

onMounted(() => {
  if (route.query.q) {
    keyword.value = String(route.query.q)
    searchProducts()
  }
})

watch(
  () => route.query.q,
  (newQ) => {
    if (newQ) {
      keyword.value = String(newQ)
      searchProducts()
    }
  }
)
</script>
