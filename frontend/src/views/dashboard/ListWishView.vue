<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Danh sách yêu thích</h1>

    <div class="overflow-x-auto shadow-lg rounded-lg bg-white">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày cập nhật</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="wish in wishes.data" :key="wish.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ wish.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ formatDate(wish.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ formatDate(wish.updated_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                Xem
              </button>
            </td>
          </tr>
          <tr v-if="wishes.data.length === 0">
            <td colspan="4" class="px-6 py-4 text-center text-gray-400">Chưa có sản phẩm yêu thích</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center space-x-2">
      <button
        v-for="link in wishes.links"
        :key="link.label"
        :disabled="!link.url"
        class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition"
        @click="getWishes(link.url)"
      >
        <span v-html="link.label"></span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

interface Wish {
  id: number
  created_at: string
  updated_at: string
}

interface Pagination {
  data: Wish[]
  links: any[]
}

const wishes = ref<Pagination>({ data: [], links: [] })

const getWishes = async (url = 'http://localhost:8001/api/wishes') => {
  try {
    const res = await axios.get(url.startsWith('http') ? url : `http://localhost:8001${url}`)
    wishes.value = res.data
  } catch (error) {
    console.error('Error fetching wishes:', error)
  }
}

// Format date đẹp hơn
const formatDate = (dateStr: string) => {
  const d = new Date(dateStr)
  return d.toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

onMounted(() => {
  getWishes()
})
</script>

<style scoped>
/* Không cần thêm nhiều, Tailwind đã đủ */
</style>
