<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
    <!-- Image -->
    <div class="aspect-w-16 aspect-h-12 bg-gray-200">
      <img
        v-if="listing.images && listing.images.length > 0"
        :src="listing.images[0].image_path"
        :alt="listing.title"
        class="w-full h-48 object-cover"
      />
      <div v-else class="w-full h-48 bg-gray-200 flex items-center justify-center">
        <PhotoIcon class="h-12 w-12 text-gray-400" />
      </div>
    </div>

    <!-- Content -->
    <div class="p-4">
      <!-- Title -->
      <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
        {{ listing.title }}
      </h3>

      <!-- Price -->
      <div class="flex items-center justify-between mb-2">
        <span class="text-xl font-bold text-blue-600">
          {{ formatPrice(listing.price) }} {{ listing.currency }}
        </span>
        <span
          v-if="listing.original_price"
          class="text-sm text-gray-500 line-through"
        >
          {{ formatPrice(listing.original_price) }}
        </span>
      </div>

      <!-- Condition -->
      <div class="flex items-center mb-2">
        <span class="text-sm text-gray-600">Tình trạng:</span>
        <span
          :class="getConditionClass(listing.condition_grade)"
          class="ml-2 px-2 py-1 rounded-full text-xs font-medium"
        >
          {{ getConditionText(listing.condition_grade) }}
        </span>
      </div>

      <!-- Seller Info -->
      <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
        <div class="flex items-center">
          <UserIcon class="h-4 w-4 mr-1" />
          <span>{{ listing.seller?.name }}</span>
        </div>
        <div class="flex items-center">
          <EyeIcon class="h-4 w-4 mr-1" />
          <span>{{ listing.view_count }}</span>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-between">
        <router-link
          :to="`/listings/${listing.id}`"
          class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors"
        >
          Xem chi tiết
        </router-link>
        
        <button
          v-if="isAuthenticated"
          @click="toggleWishlist"
          :class="[
            'ml-2 p-2 rounded-md transition-colors',
            isInWishlist
              ? 'bg-red-100 text-red-600 hover:bg-red-200'
              : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
          ]"
        >
          <HeartIcon
            :class="[
              'h-5 w-5',
              isInWishlist ? 'fill-current' : ''
            ]"
          />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useWishlistStore } from '@/stores/wishlist'
import {
  PhotoIcon,
  UserIcon,
  EyeIcon,
  HeartIcon
} from '@heroicons/vue/24/outline'

interface Listing {
  id: number
  title: string
  price: number
  original_price?: number
  currency: string
  condition_grade: string
  view_count: number
  images?: Array<{ image_path: string }>
  seller?: { name: string }
}

const props = defineProps<{
  listing: Listing
}>()

const authStore = useAuthStore()
const wishlistStore = useWishlistStore()

const isAuthenticated = computed(() => authStore.isAuthenticated)
const isInWishlist = computed(() => 
  wishlistStore.wishlistItems.some(item => item.listing_id === props.listing.id)
)

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('vi-VN').format(price)
}

const getConditionClass = (grade: string) => {
  const classes = {
    A: 'bg-green-100 text-green-800',
    B: 'bg-blue-100 text-blue-800',
    C: 'bg-yellow-100 text-yellow-800',
    D: 'bg-red-100 text-red-800'
  }
  return classes[grade as keyof typeof classes] || 'bg-gray-100 text-gray-800'
}

const getConditionText = (grade: string) => {
  const texts = {
    A: 'Mới',
    B: 'Tốt',
    C: 'Khá',
    D: 'Cũ'
  }
  return texts[grade as keyof typeof texts] || 'Không xác định'
}

const toggleWishlist = async () => {
  if (!isAuthenticated.value) {
    // Redirect to login
    return
  }
  
  try {
    if (isInWishlist.value) {
      await wishlistStore.removeFromWishlist(props.listing.id)
    } else {
      await wishlistStore.addToWishlist(props.listing.id)
    }
  } catch (error) {
    console.error('Error toggling wishlist:', error)
  }
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
