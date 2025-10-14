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

      <!-- Listings Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="listing in listings"
          :key="listing.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
          @click="goToDetail(listing.id)"
        >
          <div class="h-48 bg-gray-200 flex items-center justify-center">
            <span class="text-gray-500">Ảnh sản phẩm</span>
          </div>
          <div class="p-4">
            <h3 class="font-semibold text-gray-900 mb-2">{{ listing.title }}</h3>
            <p class="text-gray-600 text-sm mb-2">{{ listing.description }}</p>
            <div class="flex justify-between items-center">
              <span class="text-lg font-bold text-green-600">{{ formatPrice(listing.price) }}</span>
              <span class="text-sm text-gray-500">{{ listing.condition }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="listings.length === 0" class="text-center py-12">
        <div class="text-gray-500 text-lg">Không có tin rao nào</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const searchQuery = ref('')
const selectedCategory = ref('')
const listings = ref([
  {
    id: 1,
    title: 'Sách Toán 12',
    description: 'Sách giáo khoa Toán 12, tình trạng tốt',
    price: 50000,
    condition: 'Tốt'
  },
  {
    id: 2,
    title: 'Laptop Dell Inspiron',
    description: 'Laptop cũ, còn bảo hành 6 tháng',
    price: 5000000,
    condition: 'Khá'
  },
  {
    id: 3,
    title: 'Bút bi Parker',
    description: 'Bút bi cao cấp, mới 90%',
    price: 150000,
    condition: 'Rất tốt'
  }
])

const searchListings = () => {
  console.log('Searching:', searchQuery.value, selectedCategory.value)
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
  // Load sample data
})
</script>
