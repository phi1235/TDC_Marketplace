<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin người bán</h3>
    
    <div class="flex items-start space-x-4">
      <!-- Avatar -->
      <div class="flex-shrink-0">
        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xl font-bold">
          {{ getInitials(seller.name) }}
        </div>
      </div>

      <!-- Seller Info -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center space-x-2 mb-1">
          <h4 class="text-lg font-semibold text-gray-900 truncate">{{ seller.name }}</h4>
          <span
            v-if="seller.seller_profile?.verified_student"
            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
            title="Sinh viên đã xác thực"
          >
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Đã xác thực
          </span>
        </div>

        <!-- Rating -->
        <div v-if="seller.seller_profile" class="flex items-center space-x-2 mb-2">
          <div class="flex items-center">
            <svg
              v-for="i in 5"
              :key="i"
              class="w-4 h-4"
              :class="i <= Math.round(seller.seller_profile.rating) ? 'text-yellow-400' : 'text-gray-300'"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
          </div>
          <span class="text-sm text-gray-600">
            {{ formatRating(seller.seller_profile.rating) }} 
            <span class="text-gray-400">({{ seller.seller_profile.total_ratings || 0 }} đánh giá)</span>
          </span>
        </div>

        <!-- Stats -->
        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            {{ seller.seller_profile?.total_sales || 0 }} đã bán
          </div>
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Tham gia {{ formatJoinDate(seller.created_at) }}
          </div>
        </div>

        <!-- Contact Info -->
        <div v-if="showContactInfo" class="space-y-2 mb-4 p-3 bg-gray-50 rounded-lg">
          <div v-if="seller.email" class="flex items-center text-sm">
            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span class="text-gray-700">{{ seller.email }}</span>
          </div>
          <div v-if="seller.phone || seller.seller_profile?.phone" class="flex items-center text-sm">
            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            <span class="text-gray-700">{{ seller.phone || seller.seller_profile?.phone }}</span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-2">
          <button
            @click="$emit('contact')"
            class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Liên hệ người bán
          </button>
          
          <button
            v-if="!showContactInfo"
            @click="toggleContactInfo"
            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Xem thông tin
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

interface Seller {
  id: number
  name: string
  email: string
  phone?: string
  avatar?: string
  created_at: string
  seller_profile?: {
    verified_student: boolean
    rating: number
    total_ratings: number
    phone?: string
    address?: string
    total_sales?: number
  }
}

interface Props {
  seller: Seller
}

defineProps<Props>()
defineEmits<{
  contact: []
}>()

const showContactInfo = ref(false)

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

const formatJoinDate = (dateString: string) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now.getTime() - date.getTime())
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays < 30) {
    return `${diffDays} ngày trước`
  } else if (diffDays < 365) {
    const months = Math.floor(diffDays / 30)
    return `${months} tháng trước`
  } else {
    const years = Math.floor(diffDays / 365)
    return `${years} năm trước`
  }
}

const toggleContactInfo = () => {
  showContactInfo.value = !showContactInfo.value
}
</script>

