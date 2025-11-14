<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
      <!-- Loading Skeleton -->
      <div v-if="loading">
        <!-- Breadcrumb Skeleton -->
        <div class="mb-8">
          <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-48 mb-4 animate-pulse"></div>
          <div class="h-8 bg-gray-300 dark:bg-gray-700 rounded w-64 mb-2 animate-pulse"></div>
          <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-96 animate-pulse"></div>
        </div>
        
        <!-- Listings Skeleton -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <ListingCardSkeleton v-for="n in 8" :key="'skeleton-' + n" />
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <div class="text-red-600 mb-4">
          <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Có lỗi xảy ra</h3>
        <p class="text-gray-600">{{ error }}</p>
      </div>

      <!-- Content -->
      <div v-else>
        <!-- Category Header -->
        <div class="mb-8">
          <div class="flex items-center text-sm text-gray-600 mb-4">
            <router-link to="/" class="hover:text-blue-600">Trang chủ</router-link>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <router-link to="/listings" class="hover:text-blue-600">Tin rao</router-link>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 font-medium">{{ category?.name || 'Danh mục' }}</span>
          </div>

          <div v-if="category" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="text-4xl mr-4">{{ getCategoryIcon(category.icon) }}</div>
              <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ category.name }}</h1>
                <p v-if="category.description" class="text-gray-600">{{ category.description }}</p>
              </div>
              <div class="text-right">
                <div class="text-3xl font-bold text-blue-600">{{ listings.length }}</div>
                <div class="text-sm text-gray-600">Tin rao</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Listings Grid -->
        <div v-if="listings.length === 0" class="text-center py-12 bg-white rounded-lg shadow-sm">
          <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Chưa có tin rao nào</h3>
          <p class="text-gray-600">Danh mục này chưa có sản phẩm nào.</p>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div v-for="listing in listings" :key="listing.id"
            class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow cursor-pointer"
            @click="goToListing(listing.id)">
            <!-- Image -->
            <div class="aspect-square bg-gray-100 relative overflow-hidden">
              <img v-if="listing.images && listing.images.length" 
                :src="buildImageUrl(listing.images[0]?.image_path)"
                :alt="listing.title" 
                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <!-- Status Badge -->
              <span v-if="listing.status === 'pending'" 
                class="absolute top-2 right-2 px-2 py-1 bg-yellow-500 text-white text-xs font-semibold rounded">
                Chờ duyệt
              </span>
            </div>

            <!-- Content -->
            <div class="p-4">
              <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-blue-600">
                {{ listing.title }}
              </h3>
              <div class="text-2xl font-bold text-blue-600 mb-2">
                {{ formatPrice(listing.price) }}
              </div>
              <div class="flex items-center justify-between text-sm text-gray-600">
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  {{ listing.location || 'TDC' }}
                </div>
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  {{ listing.views_count || 0 }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import ListingCardSkeleton from '@/components/ListingCardSkeleton.vue'

const route = useRoute()
const router = useRouter()

const categoryId = ref(route.params.id)
const category = ref<any>(null)
const listings = ref<any[]>([])
const loading = ref(true)
const error = ref('')

const loadCategoryAndListings = async () => {
  try {
    loading.value = true
    error.value = ''

    // Load category info
    const categoryRes = await axios.get(`/api/categories/${categoryId.value}`)
    category.value = categoryRes.data

    // Load listings for this category
    const listingsRes = await axios.get('/api/listings', {
      params: {
        category_id: categoryId.value,
        status: 'approved'
      }
    })
    
    listings.value = listingsRes.data.data || listingsRes.data || []
  } catch (err: any) {
    console.error('Error loading category:', err)
    error.value = err.response?.data?.message || 'Không thể tải danh mục'
  } finally {
    loading.value = false
  }
}

const getCategoryIcon = (icon: string) => {
  const icons: Record<string, string> = {
    'book': '📚',
    'academic-cap': '📖',
    'pencil': '✏️',
    'computer-desktop': '💻',
    'shopping-bag': '👕',
    'home': '🪑',
    'sport': '⚽',
    'ellipsis-horizontal': '🎒'
  }
  return icons[icon] || '📦'
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}

const buildImageUrl = (path: string) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `http://localhost:8001/storage/${path}`
}

const goToListing = (id: number) => {
  router.push(`/listings/${id}`)
}

onMounted(() => {
  loadCategoryAndListings()
})
</script>
