<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Chi tiết tin rao</h1>
      
      <div class="bg-white rounded-lg shadow-md p-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Product Images -->
          <div>
            <div class="h-96 bg-gray-200 rounded-lg flex items-center justify-center mb-4">
              <span class="text-gray-500 text-lg">Ảnh sản phẩm</span>
            </div>
            <div class="grid grid-cols-4 gap-2">
              <div class="h-20 bg-gray-200 rounded cursor-pointer"></div>
              <div class="h-20 bg-gray-200 rounded cursor-pointer"></div>
              <div class="h-20 bg-gray-200 rounded cursor-pointer"></div>
              <div class="h-20 bg-gray-200 rounded cursor-pointer"></div>
            </div>
          </div>

          <!-- Product Info -->
          <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ listing.title }}</h2>
            <div class="text-3xl font-bold text-green-600 mb-4">{{ formatPrice(listing.price) }}</div>
            
            <div class="space-y-4 mb-6">
              <div>
                <span class="font-semibold">Tình trạng:</span>
                <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ listing.condition }}</span>
              </div>
              <div>
                <span class="font-semibold">Danh mục:</span>
                <span class="ml-2">{{ listing.category }}</span>
              </div>
              <div>
                <span class="font-semibold">Người bán:</span>
                <span class="ml-2">{{ listing.seller }}</span>
              </div>
            </div>

            <div class="space-y-4">
              <button class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Liên hệ người bán
              </button>
              <button class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors">
                Thêm vào yêu thích
              </button>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="mt-8 pt-8 border-t">
          <h3 class="text-xl font-semibold text-gray-900 mb-4">Mô tả chi tiết</h3>
          <p class="text-gray-700 leading-relaxed">{{ listing.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const listing = ref({
  id: 1,
  title: 'Sách Toán 12',
  description: 'Sách giáo khoa Toán 12, tình trạng tốt. Sách được sử dụng cẩn thận, không có ghi chú hay đánh dấu. Phù hợp cho học sinh lớp 12.',
  price: 50000,
  condition: 'Tốt',
  category: 'Sách giáo khoa',
  seller: 'Nguyễn Văn A'
})

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}

onMounted(() => {
  const id = route.params.id
  console.log('Loading listing:', id)
})
</script>
