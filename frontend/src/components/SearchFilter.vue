<template>
  <div class="p-6 max-w-7xl mx-auto">
    <h2 class="text-xl font-bold mb-6">
      🔍 Kết quả tìm kiếm cho:
      <span class="text-blue-600">{{ keyword }}</span>
      <span v-if="engine" class="ml-2 text-gray-500 text-sm">
        ({{ engineLabel }})
      </span>
    </h2>

    <!-- 🌀 Loading Skeleton -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div v-for="n in 6" :key="'skeleton-' + n" class="border rounded-lg shadow-sm p-4 animate-pulse">
        <div class="bg-gray-300 h-40 w-full rounded-lg mb-3 shimmer"></div>
        <div class="h-4 bg-gray-300 rounded w-3/4 mb-2 shimmer"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2 mb-2 shimmer"></div>
        <div class="h-5 bg-gray-400 rounded w-1/3 shimmer"></div>
      </div>
    </div>

    <!-- 🧩 Compare Mode -->
    <div v-else-if="engine === 'compare'" class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Elasticsearch -->
      <div>
        <h3 class="text-blue-600 font-semibold text-lg mb-3 flex items-center justify-between">
          <span>Elasticsearch</span>
          <small class="text-gray-500 text-sm">
            {{ results[0]?.data?.total || 0 }} kết quả – {{ results[0]?.data?.time_ms || 0 }}ms
          </small>
        </h3>

        <div v-for="item in results[0]?.data?.results" :key="item._id"
          class="border rounded-lg shadow-sm hover:shadow-md transition p-4 mb-4 cursor-pointer"
          @click="goToListingDetail(item)">
          >
          <img :src="getImage(item)" class="w-full h-40 object-cover rounded-lg mb-3" />
          <h3 class="font-semibold text-lg mb-1">{{ getTitle(item) }}</h3>
          <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ getDescription(item) }}</p>
          <p class="font-bold text-blue-600">{{ formatPrice(getPrice(item)) }}₫</p>
        </div>
      </div>

      <!-- Solr -->
      <div>
        <h3 class="text-orange-600 font-semibold text-lg mb-3 flex items-center justify-between">
          <span>Solr</span>
          <small class="text-gray-500 text-sm">
            {{ results[1]?.data?.total || 0 }} kết quả – {{ results[1]?.data?.time_ms || 0 }}ms
          </small>
        </h3>

        <div v-for="item in results[1]?.data?.results" :key="item.id"
          class="border rounded-lg shadow-sm hover:shadow-md transition p-4 mb-4">
          <img :src="getImage(item)" class="w-full h-40 object-cover rounded-lg mb-3" />
          <h3 class="font-semibold text-lg mb-1">{{ getTitle(item) }}</h3>
          <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ getDescription(item) }}</p>
          <p class="font-bold text-blue-600">{{ formatPrice(getPrice(item)) }}₫</p>
        </div>
      </div>
    </div>

    <!-- 🧾 Normal Mode (ES hoặc Solr) -->
    <transition-group v-else name="fade" tag="div" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div v-for="item in results" :key="item.id || item._id"
        class="border rounded-lg shadow-sm hover:shadow-md transition p-4 fade-item cursor-pointer"
        @click="goToListingDetail(item)">
        <img :src="getImage(item)" class="w-full h-40 object-cover rounded-lg mb-3" />
        <h3 class="font-semibold text-lg mb-1">{{ getTitle(item) }}</h3>
        <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ getDescription(item) }}</p>
        <p class="font-bold text-blue-600">{{ formatPrice(getPrice(item)) }}₫</p>
      </div>
    </transition-group>

    <!-- Empty -->
    <div v-if="!loading && !results.length" class="text-gray-500 mt-4 text-center">
      Không tìm thấy sản phẩm nào.
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter() // 👈 thêm dòng này
const keyword = ref('')
const results = ref<any[]>([])
const loading = ref(false)
const cache = new Map()
const engine = ref('es') // default engine

// 🏷 Label engine hiển thị
const engineLabel = computed(() => {
  switch (engine.value) {
    case 'solr':
      return 'Solr Search'
    case 'es':
      return 'Elasticsearch'
    case 'compare':
      return 'So sánh hai engine'
    default:
      return ''
  }
})

// 🔍 Gọi API phù hợp engine
const searchProducts = async () => {
  const q = keyword.value.trim()
  if (!q) return

  const cacheKey = `${engine.value}_${q}`
  if (cache.has(cacheKey)) {
    results.value = cache.get(cacheKey)
    return
  }

  loading.value = true
  try {
    let url = ''
    if (engine.value === 'compare') {
      url = `http://localhost:8001/api/search-compare?q=${encodeURIComponent(q)}`
    } else if (engine.value === 'solr') {
      url = `http://localhost:8001/api/search-solr?q=${encodeURIComponent(q)}`
    } else {
      url = `http://localhost:8001/api/search-es?q=${encodeURIComponent(q)}`
    }

    const res = await fetch(url)
    const data = await res.json()

    // ⚡️ Gán dữ liệu theo engine
    if (engine.value === 'compare') {
      results.value = [
        { label: 'Elasticsearch', data: data.elasticsearch },
        { label: 'Solr', data: data.solr },
      ]
    } else if (engine.value === 'solr') {
      results.value = data.results || []
    } else {
      results.value = data.data || []
    }

    cache.set(cacheKey, results.value)
  } catch (err) {
    console.error('Search error:', err)
  } finally {
    loading.value = false
  }
}

// 💰 Format hiển thị giá
const formatPrice = (p: any) => {
  const num = Number(p)
  return isNaN(num) ? 0 : num.toLocaleString('vi-VN')
}

// 📦 Xử lý field khác nhau giữa ES và Solr
const getTitle = (item: any) => {
  if (item._source) return item._source.title
  if (Array.isArray(item.title)) return item.title[0]
  return item.title || 'Không rõ'
}

const getDescription = (item: any) => {
  if (item._source) return item._source.description
  if (Array.isArray(item.description)) return item.description[0]
  return item.description || ''
}

const getImage = (item: any) => {
  if (item._source?.image) return item._source.image
  if (Array.isArray(item.image) && item.image.length) return item.image[0]
  return 'https://picsum.photos/300/200'
}

const getPrice = (item: any) => {
  if (item._source?.price) return item._source.price
  if (Array.isArray(item.price)) return item.price[0]
  return 0
}
const goToListingDetail = (item: any) => {
  // Lấy ID cho cả ES và Solr
  const id = item._id || item.id
  if (!id) return

  // Điều hướng đến trang chi tiết
  router.push(`/listings/${id}`)
}

// 🎬 Lifecycle
onMounted(() => {
  if (route.query.q) {
    keyword.value = String(route.query.q)
    engine.value = String(route.query.engine || 'es')
    searchProducts()
  }
})

// 🔁 Watch URL params
watch(
  () => [route.query.q, route.query.engine],
  ([newQ, newEngine]) => {
    if (newQ) {
      keyword.value = String(newQ)
      engine.value = String(newEngine || 'es')
      searchProducts()
    }
  }
)
</script>

<style scoped>
mark {
  background-color: #fef08a;
  color: inherit;
}

.shimmer {
  animation: shimmer 1.5s infinite linear;
  background: linear-gradient(90deg,
      #f3f4f6 25%,
      #e5e7eb 50%,
      #f3f4f6 75%);
  background-size: 200% 100%;
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }

  100% {
    background-position: 200% 0;
  }
}
.fade-item:hover {
  transform: translateY(-3px);
  transition: all 0.2s ease;
}
</style>
