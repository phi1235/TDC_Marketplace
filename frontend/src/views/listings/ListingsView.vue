<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">🛍️ Danh sách tin rao</h1>
      
      <!-- Bộ lọc và tìm kiếm -->
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
              <option
                v-for="cat in categories"
                :key="cat.id"
                :value="cat.id"
              >
                {{ cat.icon ? cat.icon + ' ' : '' }}{{ cat.name }}
              </option>
            </select>

            <button
              @click="searchListings"
              class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
            >
              🔍 Tìm kiếm
            </button>
          </div>
        </div>
      </div>

      <!-- Danh sách tin -->
      <div
        v-if="!loading"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        <div
          v-for="listing in listings"
          :key="listing.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
          @click="goToDetail(listing.id)"
        >
          <div class="h-48 bg-gray-200 flex items-center justify-center">
            <img
              v-if="listing.images && listing.images.length"
              :src="imageUrl(listing.images[0].image_path)"
              class="object-cover w-full h-full"
            />
            <span v-else class="text-gray-500">Không có ảnh</span>
          </div>

          <div class="p-4">
            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
              {{ listing.title }}
            </h3>
            <p class="text-gray-600 text-sm mb-2 line-clamp-2">
              {{ listing.description }}
            </p>
            <div class="flex justify-between items-center">
              <span class="text-lg font-bold text-green-600">
                {{ formatPrice(listing.price) }}
              </span>
              <span class="text-sm text-gray-500">
                {{ getConditionText(listing.condition) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12 text-gray-500">
        Đang tải dữ liệu...
      </div>

      <!-- Empty -->
      <div v-if="!loading && listings.length === 0" class="text-center py-12">
        <div class="text-gray-500 text-lg">Không có tin rao nào</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { imageUrl } from '@/utils/image' // nếu có

const router = useRouter()

// State
const listings = ref<any[]>([])
const categories = ref<any[]>([])
const loading = ref(false)
const searchQuery = ref('')
const selectedCategory = ref('')

// Hàm định dạng giá
const formatPrice = (price: number) =>
  new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price)

// Điều kiện hiển thị text
const getConditionText = (c: string) => {
  const map: Record<string, string> = {
    new: 'Mới',
    like_new: 'Như mới',
    good: 'Tốt',
    fair: 'Khá',
  }
  return map[c] || 'Không xác định'
}

// Lấy danh mục thật từ API
const loadCategories = async () => {
  try {
    const res = await axios.get('/api/categories')
    categories.value = res.data
  } catch (error) {
    console.error('Lỗi tải danh mục:', error)
  }
}

// Lấy tin rao thật từ API
const loadListings = async (params = {}) => {
  loading.value = true
  try {
    const res = await axios.get('/api/listings', { params })
    listings.value = res.data.data || res.data // tuỳ backend trả về kiểu nào
  } catch (error) {
    console.error('Lỗi tải tin rao:', error)
  } finally {
    loading.value = false
  }
}

// Lọc tin theo từ khóa + danh mục
const searchListings = () => {
  const params: any = {}
  if (searchQuery.value) params.q = searchQuery.value
  if (selectedCategory.value) params.category_id = selectedCategory.value
  loadListings(params)
}

const goToDetail = (id: number) => router.push(`/listings/${id}`)

onMounted(() => {
  loadCategories()
  loadListings()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
