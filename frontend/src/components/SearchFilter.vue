<template>
  <div class="container mx-auto px-4 py-10">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <!-- 🔍 Bộ lọc -->
      <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Tìm kiếm & Bộ lọc</h2>

        <!-- Tìm kiếm -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-600 mb-1">Từ khóa</label>
          <input
            v-model="filters.keyword"
            type="text"
            placeholder="Nhập tên sản phẩm..."
            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
          />
        </div>

        <!-- Danh mục -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-600 mb-1">Danh mục</label>
          <select
            v-model="filters.category"
            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
          >
            <option value="">Tất cả</option>
            <option v-for="cat in categories" :key="cat">{{ cat }}</option>
          </select>
        </div>

        <!-- Khoảng giá -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-600 mb-1">Khoảng giá (VNĐ)</label>
          <input
            type="range"
            min="0"
            max="1000000"
            step="50000"
            v-model="filters.price"
            class="w-full accent-blue-500"
          />
          <p class="text-sm text-gray-600 mt-2">
            Dưới {{ Number(filters.price).toLocaleString() }}₫
          </p>
        </div>

        <!-- Sắp xếp -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-600 mb-1">Sắp xếp</label>
          <select
            v-model="filters.sort"
            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
          >
            <option value="newest">Mới nhất</option>
            <option value="asc">Giá tăng dần</option>
            <option value="desc">Giá giảm dần</option>
          </select>
        </div>

        <!-- Nút -->
        <button
          @click="applyFilters"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition"
        >
          Áp dụng bộ lọc
        </button>
      </div>

      <!-- 🧾 Kết quả -->
      <div class="md:col-span-3">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
          Kết quả: {{ filteredItems.length }} sản phẩm
        </h2>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="item in filteredItems"
            :key="item.id"
            class="bg-white border border-gray-100 shadow-sm rounded-xl overflow-hidden hover:shadow-lg transition"
          >
            <img :src="item.image" alt="Sản phẩm" class="w-full h-48 object-cover" />
            <div class="p-4">
              <h3 class="font-semibold text-gray-800 truncate">{{ item.name }}</h3>
              <p class="text-sm text-gray-500">{{ item.category }}</p>
              <p class="text-blue-600 font-bold mt-2">{{ item.price.toLocaleString() }}₫</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const filters = ref({
  keyword: '',
  category: '',
  price: 1000000,
  sort: 'newest'
})

const categories = ['Sách giáo khoa', 'Điện tử', 'Đồ dùng học tập', 'Quần áo']

const items = ref([
  { id: 1, name: 'Sách Toán 12', category: 'Sách giáo khoa', price: 40000, image: 'https://picsum.photos/300/200?1' },
  { id: 2, name: 'Laptop Dell cũ', category: 'Điện tử', price: 3500000, image: 'https://picsum.photos/300/200?2' },
  { id: 3, name: 'Bút bi Thiên Long', category: 'Đồ dùng học tập', price: 5000, image: 'https://picsum.photos/300/200?3' },
  { id: 4, name: 'Áo khoác Khoa CNTT', category: 'Quần áo', price: 120000, image: 'https://picsum.photos/300/200?4' }
])

const filteredItems = computed(() => {
  let results = items.value.filter(i =>
    i.name.toLowerCase().includes(filters.value.keyword.toLowerCase())
  )

  if (filters.value.category) {
    results = results.filter(i => i.category === filters.value.category)
  }

  results = results.filter(i => i.price <= filters.value.price)

  if (filters.value.sort === 'asc') results.sort((a, b) => a.price - b.price)
  if (filters.value.sort === 'desc') results.sort((a, b) => b.price - a.price)

  return results
})

const applyFilters = () => {
  console.log('Filters applied:', filters.value)
}
</script>

<style scoped>
/* Một số hiệu ứng nhẹ */
input[type="range"] {
  cursor: pointer;
}
</style>
