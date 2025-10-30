<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Danh sách tin rao</h1>
      
      <!-- Search and Filters -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm kiếm tin rao..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div class="flex gap-2">
            <select
              v-model="selectedCategory"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Tất cả danh mục</option>
              <option value="1">Sách giáo khoa</option>
              <option value="2">Điện tử</option>
              <option value="3">Đồ dùng học tập</option>
            </select>
            <button
              @click="searchListings"
              class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
            >
              Tìm kiếm
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="n in 6" :key="n" class="bg-white rounded-lg shadow-md overflow-hidden animate-pulse">
          <div class="h-48 bg-gray-300"></div>
          <div class="p-4">
            <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
            <div class="h-5 bg-gray-300 rounded w-1/3"></div>
          </div>
        </div>
      </div>

      <!-- Listings Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="listing in listings"
          :key="listing.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
          @click="goToDetail(listing.id)"
        >
          <div class="h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
            <img 
              v-if="listing.images && listing.images.length > 0" 
              :src="listing.images[0].url || listing.images[0].path" 
              :alt="listing.title"
              class="w-full h-full object-cover"
            />
            <span v-else class="text-gray-500">Ảnh sản phẩm</span>
          </div>
          <div class="p-4">
            <h3 class="font-semibold text-gray-900 mb-2">{{ listing.title }}</h3>
            <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ listing.description }}</p>
            <div class="flex justify-between items-center">
              <span class="text-lg font-bold text-green-600">{{ formatPrice(listing.price) }}</span>
              <span class="text-sm text-gray-500">{{ listing.condition }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && listings.length === 0" class="text-center py-12">
        <div class="text-gray-500 text-lg">Không có tin rao nào</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

interface Listing {
  id: number
  title: string
  description: string
  price: number
  condition: string
  images?: any[]
}

const router = useRouter()
const searchQuery = ref('')
const selectedCategory = ref('')
const listings = ref<Listing[]>([])
const loading = ref(false)

const loadListings = async () => {
  loading.value = true
  try {
    const response = await api.get('/listings')
    listings.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Error loading listings:', error)
    listings.value = []
  } finally {
    loading.value = false
  }
}

const searchListings = () => {
  console.log('Searching:', searchQuery.value, selectedCategory.value)
  loadListings() // Reload với filter
}

const goToDetail = (id: number) => {
  router.push(`/listings/${id}`)
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}

onMounted(() => {
  loadListings()
})
</script>
