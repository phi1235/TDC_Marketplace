<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <!-- 🏠 Banner -->
    <section
      class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-16 px-6 text-center rounded-b-3xl shadow-lg">
      <h1 class="text-4xl font-bold mb-4">Chào mừng đến với TDC Marketplace 🎉</h1>
      <p class="text-lg opacity-90 max-w-2xl mx-auto">
        Nơi kết nối sinh viên mua bán, trao đổi đồ dùng học tập, công nghệ và nhiều hơn thế nữa.
      </p>
      <router-link to="/listings"
        class="inline-block mt-6 bg-white text-blue-600 font-semibold px-6 py-3 rounded-full shadow hover:bg-gray-100 transition">
        Xem tất cả tin rao →
      </router-link>
    </section>

    <!-- 🗂 Danh mục -->
    <section class="max-w-7xl mx-auto p-6">
      <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100 text-center">
        📂 Danh mục phổ biến
      </h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6 text-center">
        <div v-for="category in categories" :key="category.id"
          class="group bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg cursor-pointer transition transform hover:-translate-y-1"
          @click="goToCategory(category.id)">
          <div class="text-4xl mb-3">{{ category.icon || '📦' }}</div>
          <h3 class="font-semibold group-hover:text-blue-600">
            {{ category.name }}
          </h3>
        </div>
      </div>
    </section>

    <!-- 🎓 Gợi ý theo ngành của bạn -->
    <section v-if="isAuthenticated && userMajor && recommendedListings.length > 0" class="max-w-7xl mx-auto p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            🎓 Gợi ý cho ngành {{ userMajor.name }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
            {{ userMajor.icon }} Những tin rao phù hợp với ngành học của bạn
          </p>
        </div>
        <router-link to="/listings?recommended=1" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
          Xem tất cả →
        </router-link>
      </div>

      <!-- Loading Skeleton -->
      <div v-if="loadingRecommended" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <ListingCardSkeleton v-for="n in 4" :key="'rec-skeleton-' + n" />
      </div>

      <!-- Recommended Listings -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="item in recommendedListings.slice(0, 4)" :key="'rec-' + item.id"
          class="group bg-white dark:bg-gray-800 rounded-xl border-2 border-blue-200 dark:border-blue-800 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
          <!-- Badge "Gợi ý" -->
          <div class="bg-blue-600 text-white text-xs font-semibold px-3 py-1 text-center">
            ⭐ Gợi ý cho bạn
          </div>

          <!-- Ảnh -->
          <div class="relative overflow-hidden">
            <button @click="toggleFavorite(item)" class="absolute top-2 right-2 bg-white rounded-full p-1 shadow-md flex flex-col items-center">
              <span class="text-2xl transition-all duration-200 select-none"
                :class="item.is_favorite ? 'text-red-500' : 'text-gray-500'">
                {{ item.is_favorite ? '♥️' : '🤍' }}
              </span>
            </button>
            <img v-if="item.images?.length" :src="imageUrl(item.images[0].image_path)" :alt="item.title"
              class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105" />
            <div v-else class="h-48 bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
              Không có ảnh
            </div>
          </div>

          <!-- Nội dung -->
          <div class="p-4">
            <h3 class="text-lg font-semibold mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
              {{ item.title }}
            </h3>

            <p class="text-xl font-bold text-blue-600 mb-2">
              {{ formatPrice(item.price) }} VNĐ
            </p>

            <div class="flex items-center mb-2">
              <span class="text-sm text-gray-600 dark:text-gray-300">Tình trạng:</span>
              <span :class="getConditionClass(item.condition)" class="ml-2 px-2 py-1 rounded-full text-xs font-medium">
                {{ getConditionText(item.condition) }}
              </span>
            </div>

            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-300 mb-4">
              <div class="flex items-center gap-1">
                👁 {{ item.views_count }} lượt xem
              </div>
            </div>

            <router-link :to="`/listings/${item.id}`"
              class="block text-center bg-blue-600 text-white rounded-md py-2 font-medium hover:bg-blue-700 active:scale-95 transition-transform">
              Xem chi tiết
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- �🌟 Tin mới được duyệt -->
    <section class="max-w-7xl mx-auto p-6">
      <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
        🌟 Tin mới được duyệt
      </h2>

      <!-- Loading Skeleton -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <ListingCardSkeleton v-for="n in 8" :key="'skeleton-' + n" />
      </div>

      <!-- Empty -->
      <div v-else-if="listings.length === 0" class="text-center py-20 text-gray-500">
        Chưa có tin nào được duyệt hiển thị.
      </div>

      <!-- Listings -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="item in listings" :key="item.id"
          class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
          <!-- Ảnh -->
          <div class="relative overflow-hidden">
            <button @click="toggleFavorite(item)" class="absolute top-2 right-2 top-2 right-2 bg-white rounded-full p-1 shadow-md flex flex-col items-center">
                <span class="text-2xl transition-all duration-200 select-none"
                  :class="item.is_favorite ? 'text-red-500' : 'text-gray-500'">
                  {{ item.is_favorite ? '♥️' : '🤍' }}
                </span>
              </button>
            <img v-if="item.images?.length" :src="imageUrl(item.images[0].image_path)" :alt="item.title"
              class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105" />
            <div v-else class="h-48 bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
              Không có ảnh
            </div>
          </div>

          <!-- Nội dung -->
          <div class="p-4">
            <h3 class="text-lg font-semibold mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
              {{ item.title }}
            </h3>

            <p class="text-xl font-bold text-blue-600 mb-2">
              {{ formatPrice(item.price) }} VNĐ
            </p>

            <div class="flex items-center mb-2">
              <span class="text-sm text-gray-600 dark:text-gray-300">Tình trạng:</span>
              <span :class="getConditionClass(item.condition)" class="ml-2 px-2 py-1 rounded-full text-xs font-medium">
                {{ getConditionText(item.condition) }}
              </span>
            </div>

            <!-- <div class="flex items-center text-sm text-gray-600 dark:text-gray-300 mb-4">
              👁 {{ item.views_count }} lượt xem
            </div> -->
            <!-- favorite -->
            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-300 mb-4">
              <div class="flex items-center gap-1">
                👁 {{ item.views_count }} lượt xem
              </div>

              <!-- <button @click="toggleFavorite(item)">
                <span class="text-2xl transition-all duration-200 select-none"
                  :class="item.is_favorite ? 'text-red-500' : 'text-gray-500'">
                  {{ item.is_favorite ? '♥️' : '🤍' }}
                </span>
                <span class="text-xs font-medium" :class="item.is_favorite ? 'text-red-500' : 'text-gray-500'">
                  {{ item.is_favorite ? 'Đã yêu thích' : 'Chưa yêu thích' }}
                </span>
              </button> -->
            </div>

            <router-link :to="`/listings/${item.id}`"
              class="block text-center bg-blue-600 text-white rounded-md py-2 font-medium hover:bg-blue-700 active:scale-95 transition-transform">
              Xem chi tiết
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- 🦶 Footer -->
    <footer
      class="mt-16 py-8 bg-gray-100 dark:bg-gray-800 text-center text-sm text-gray-600 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
      <div class="space-x-4 mb-2">
        <router-link to="/terms" class="hover:underline">Điều khoản</router-link>
        <router-link to="/privacy-policy" class="hover:underline">Bảo mật</router-link>
        <router-link to="/faq" class="hover:underline">FAQ</router-link>
      </div>
      © {{ new Date().getFullYear() }} TDC Marketplace — All rights reserved.
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { listingsService } from '@/services/listings'
import { imageUrl } from '@/utils/image'
import axios from 'axios'
import ListingCardSkeleton from '@/components/ListingCardSkeleton.vue'
//listwish
import { wishlistService } from '@/services/wishlist'
import { useWishlistStore } from '@/stores/wishlist'
import { useAuthStore } from '@/stores/auth'
import type { Major } from '@/types/major'


const listings = ref([])
const categories = ref([])
const loading = ref(false)
const recommendedListings = ref([])
const loadingRecommended = ref(false)

const authStore = useAuthStore()
const isAuthenticated = computed(() => authStore.isAuthenticated)
const userMajor = computed(() => authStore.user?.major as Major | undefined)

const loadCategories = async () => {
  try {
    const res = await axios.get('/api/categories')
    categories.value = res.data.slice(0, 6) // chỉ lấy 6 danh mục đầu
  } catch (error) {
    console.error('Lỗi tải danh mục:', error)
  }
}

const loadPublicListings = async () => {
  loading.value = true
  try {
    // Add delay to see skeleton loading (REMOVE IN PRODUCTION)
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    const res = await listingsService.getPublicListings()
    listings.value = res.data.slice(0, 16)
  } catch (error) {
    console.error('Lỗi tải tin:', error)
  } finally {
    loading.value = false
  }
}

const loadRecommendedListings = async () => {
  if (!isAuthenticated.value || !userMajor.value) return
  
  loadingRecommended.value = true
  try {
    const res = await listingsService.getRecommendedListings()
    recommendedListings.value = res.data || res
  } catch (error) {
    console.error('Lỗi tải gợi ý:', error)
  } finally {
    loadingRecommended.value = false
  }
}

const formatPrice = (price: number) => new Intl.NumberFormat('vi-VN').format(price)

const getConditionClass = (condition: string) => {
  const classes: Record<string, string> = {
    new: 'bg-green-100 text-green-800',
    like_new: 'bg-blue-100 text-blue-800',
    good: 'bg-yellow-100 text-yellow-800',
    fair: 'bg-red-100 text-red-800'
  }
  return classes[condition] || 'bg-gray-100 text-gray-800'
}

const getConditionText = (condition: string) => {
  const texts: Record<string, string> = {
    new: 'Mới',
    like_new: 'Như mới',
    good: 'Tốt',
    fair: 'Khá'
  }
  return texts[condition] || 'Không xác định'
}

const goToCategory = (id: number) => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
  window.location.href = `/category/${id}`
}

onMounted(async () => {
  await loadCategories()
  await loadPublicListings()
  await loadRecommendedListings()
  await loadWishlistStatus()
})




// Favorite
// const toggleFavorite = (item: any) => {
//   item.is_favorite = !item.is_favorite
// }

const wishlistStore = useWishlistStore()

const toggleFavorite = async (item: any) => {
  try {
    const res = await wishlistService.toggleWishlist(item.id)

    // cập nhật số lượng tổng wishlist
    wishlistStore.setCount(res.total ?? wishlistStore.count + (res.is_favorited ? 1 : -1))

    // cập nhật trạng thái tim trên UI
    item.is_favorite = res.is_favorited
  } catch (err) {
    console.error('Lỗi toggle wishlist:', err)
  }
}

const loadWishlistStatus = async () => {
  const res = await wishlistService.getWishlist()
  const wishlistData = Array.isArray(res) ? res : res.data

  wishlistStore.setCount(wishlistData.length)

  listings.value.forEach(l => {
    l.is_favorite = wishlistData.some((w: any) => w.listing_id === l.id)
  })
}


</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
