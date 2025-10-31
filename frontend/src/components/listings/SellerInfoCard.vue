<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin người bán</h3>

    <div class="flex items-start space-x-4">
      <!-- Avatar -->
      <div class="flex-shrink-0">
        <div
          class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xl font-bold">
          {{ getInitials(seller.name) }}
        </div>
      </div>

      <!-- Seller Info -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center space-x-2 mb-1">
          <h4 class="text-lg font-semibold text-gray-900 truncate">{{ seller.name }}</h4>

          <span v-if="seller.seller_profile?.verified_student"
            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
            title="Sinh viên đã xác thực">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd" />
            </svg>
            Đã xác thực
          </span>
        </div>

        <!-- ⭐ Rating (click để mở modal) -->
        <div class="flex items-center space-x-2 mb-2 cursor-pointer" @click="openRatingModal">
          <div class="flex items-center">
            <!-- Full stars -->
            <svg
              v-for="i in Math.floor(seller.rating || 0)"
              :key="'full-' + i"
              class="w-4 h-4 text-yellow-400"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
              />
            </svg>

            <!-- Half star -->
            <svg
              v-if="(seller.rating || 0) % 1 >= 0.5"
              class="w-4 h-4 text-yellow-400"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <defs>
                <linearGradient id="halfStar" x1="0" x2="100%" y1="0" y2="0">
                  <stop offset="50%" stop-color="currentColor" />
                  <stop offset="50%" stop-color="#E5E7EB" />
                </linearGradient>
              </defs>
              <path
                fill="url(#halfStar)"
                d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.78 1.401 8.171L12 18.896l-7.335 3.866 1.4-8.171L.132 9.211l8.2-1.193L12 .587z"
              />
            </svg>

            <!-- Empty stars -->
            <svg
              v-for="i in 5 - Math.ceil(seller.rating || 0)"
              :key="'empty-' + i"
              class="w-4 h-4 text-gray-300"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
              />
            </svg>
          </div>

          <span class="text-sm text-gray-600 hover:text-blue-600">
            {{ formatRating(seller.rating) }}
            <span class="text-gray-400">({{ seller.total_ratings || 0 }} đánh giá)</span>
          </span>
        </div>

        <!-- 💬 Modal xem đánh giá -->
        <Transition name="fade">
          <div
            v-if="showRatingModal"
            class="fixed inset-0 z-50 bg-black bg-opacity-40 flex items-center justify-center p-4"
          >
            <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 relative">
              <button
                @click="showRatingModal = false"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-700"
              >
                ✖
              </button>

              <h3 class="text-lg font-semibold mb-4">
                Đánh giá của người mua
              </h3>

              <div v-if="loadingRatings" class="text-gray-500 italic">Đang tải...</div>

              <div v-else-if="ratings.length === 0" class="text-gray-500 italic">
                Chưa có đánh giá nào.
              </div>

              <div v-else class="space-y-4 max-h-80 overflow-y-auto pr-2">
                <div
                  v-for="rating in ratings"
                  :key="rating.id"
                  class="border-b pb-3 last:border-none"
                >
                  <div class="flex items-center justify-between mb-1">
                    <span class="font-medium text-gray-800">{{ rating.from_user.name }}</span>
                    <span class="text-yellow-500">
                      <span v-for="i in 5" :key="i">
                        {{ i <= rating.stars ? '★' : '☆' }}
                      </span>
                    </span>
                  </div>

                  <p class="text-sm text-gray-600 mb-1">{{ rating.comment || 'Không có bình luận.' }}</p>

                  <p class="text-xs text-gray-400">
                    {{ formatDate(rating.created_at) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </Transition>

        <!-- Stats -->
        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            {{ seller.total_sales ?? 0 }} đã bán
          </div>
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Tham gia {{ formatJoinDate(seller.created_at) }}
          </div>
        </div>

        <!-- Contact Info -->
        <div v-if="showContactInfo" class="space-y-2 mb-4 p-3 bg-gray-50 rounded-lg">
          <div v-if="seller.email" class="flex items-center text-sm">
            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span class="text-gray-700">{{ seller.email }}</span>
          </div>

          <div v-if="seller.phone || seller.seller_profile?.phone" class="flex items-center text-sm">
            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            <span class="text-gray-700">{{ seller.phone || seller.seller_profile?.phone }}</span>
          </div>
        </div>

      
      </div>
    </div>
    <!-- Action Buttons -->
<div class="flex w-full mt-3 gap-2 ">
  <!-- Nút Liên hệ -->
  <button
    @click="$emit('contact')"
    class="flex-1 inline-flex items-center justify-center gap-1  py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
  >
    <svg
      class="w-5 h-5"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
      />
    </svg>
    Liên hệ người bán
  </button>

  <!-- Nút Xem thông tin -->
  <button
    v-if="!showContactInfo"
    @click="toggleContactInfo"
    class="flex-1 inline-flex items-center justify-center gap-1  py-3 text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 rounded-md shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
  >
    <svg
      class="w-5 h-5"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
      />
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
      />
    </svg>
    Xem thông tin
  </button>
</div>

  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'

const showRatingModal = ref(false)
const ratings = ref<any[]>([])
const loadingRatings = ref(false)

interface Seller {
  id: number
  name: string
  email: string
  phone?: string
  avatar?: string
  created_at: string
  total_sales?: number
  rating?: number
  total_ratings?: number
  seller_profile?: {
    verified_student: boolean
  }
}

const props = defineProps<{
  seller: Seller
}>()

defineEmits<{
  contact: []
}>()

// modal mở + fetch danh sách đánh giá
const openRatingModal = async () => {
  showRatingModal.value = true
  loadingRatings.value = true
  try {
    const res = await axios.get(`/api/ratings/user/${props.seller.id}`)
    ratings.value = res.data
  } catch (err) {
    console.error('Lỗi khi lấy đánh giá:', err)
  } finally {
    loadingRatings.value = false
  }
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleString('vi-VN', {
    dateStyle: 'short',
    timeStyle: 'short'
  })
}

const showContactInfo = ref(false)

const toggleContactInfo = () => {
  showContactInfo.value = !showContactInfo.value
}

const getInitials = (name: string) => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const formatRating = (rating: number | string | null | undefined) => {
  if (!rating) return '0.0'
  const num = typeof rating === 'string' ? parseFloat(rating) : rating
  return isNaN(num) ? '0.0' : num.toFixed(1)
}

// "2 ngày trước" / "5 tháng trước" / "1 năm trước"
const formatJoinDate = (dateString: string) => {
  if (!dateString) return 'chưa rõ'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return 'chưa rõ'

  const now = new Date()
  const diffTime = Math.abs(now.getTime() - date.getTime())
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays < 30) return `${diffDays} ngày trước`
  if (diffDays < 365) return `${Math.floor(diffDays / 30)} tháng trước`
  return `${Math.floor(diffDays / 365)} năm trước`
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
