<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="close"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 transition-opacity"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div
            class="relative bg-white rounded-lg shadow-xl max-w-lg w-full"
            @click.stop
          >
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">Liên hệ người bán</h3>
              <button
                @click="close"
                class="text-gray-400 hover:text-gray-500 transition-colors"
                aria-label="Close"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Content -->
            <div class="px-6 py-4">
              <!-- Listing Info -->
              <div class="flex items-start space-x-3 mb-6 p-3 bg-gray-50 rounded-lg">
                <img
                  v-if="listing.images?.[0]"
                  :src="getImageUrl(listing.images[0].image_path)"
                  :alt="listing.title"
                  class="w-16 h-16 object-cover rounded"
                />
                <div class="flex-1 min-w-0">
                  <h4 class="font-medium text-gray-900 truncate">{{ listing.title }}</h4>
                  <p class="text-lg font-bold text-blue-600">{{ formatPrice(listing.price) }}</p>
                </div>
              </div>

              <!-- Seller Info -->
              <div class="mb-6">
                <div class="flex items-center space-x-3 mb-3">
                  <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                    {{ getInitials(seller.name) }}
                  </div>
                  <div>
                    <div class="flex items-center space-x-2">
                      <span class="font-medium text-gray-900">{{ seller.name }}</span>
                      <span
                        v-if="seller.seller_profile?.verified_student"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
                      >
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Đã xác thực
                      </span>
                    </div>
                    <div v-if="seller.seller_profile" class="flex items-center text-sm text-gray-600">
                      <div class="flex items-center mr-2">
                        <svg
                          v-for="i in 5"
                          :key="i"
                          class="w-3 h-3"
                          :class="i <= Math.round(seller.seller_profile.rating) ? 'text-yellow-400' : 'text-gray-300'"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                      </div>
                      {{ formatRating(seller.seller_profile.rating) }}
                    </div>
                  </div>
                </div>

                <!-- Contact Details -->
                <div class="space-y-2 p-3 bg-blue-50 rounded-lg">
                  <div v-if="seller.email" class="flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <a :href="`mailto:${seller.email}`" class="text-blue-700 hover:underline">{{ seller.email }}</a>
                  </div>
                  <div v-if="seller.phone || seller.seller_profile?.phone" class="flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <a :href="`tel:${seller.phone || seller.seller_profile?.phone}`" class="text-blue-700 hover:underline">
                      {{ seller.phone || seller.seller_profile?.phone }}
                    </a>
                  </div>
                </div>
              </div>

              <!-- Quick Message Form -->
              <form @submit.prevent="sendMessage" class="space-y-4">
                <div>
                  <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                    Tin nhắn (tùy chọn)
                  </label>
                  <textarea
                    id="message"
                    v-model="message"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Xin chào, tôi quan tâm đến sản phẩm của bạn..."
                  ></textarea>
                </div>

                <!-- Quick Message Templates -->
                <div class="space-y-2">
                  <p class="text-sm font-medium text-gray-700">Tin nhắn mẫu:</p>
                  <div class="flex flex-wrap gap-2">
                    <button
                      v-for="template in messageTemplates"
                      :key="template"
                      type="button"
                      @click="message = template"
                      class="px-3 py-1 text-xs border border-gray-300 rounded-full hover:bg-gray-50 transition-colors"
                    >
                      {{ template }}
                    </button>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-4">
                  <button
                    type="button"
                    @click="close"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Đóng
                  </button>
                  <button
                    type="submit"
                    :disabled="isSending"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                  >
                    <svg v-if="isSending" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isSending ? 'Đang gửi...' : 'Gửi tin nhắn' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { showToast } from '@/utils/toast'

interface Listing {
  id: number
  title: string
  price: number
  images?: Array<{
    image_path: string
  }>
}

interface Seller {
  id: number
  name: string
  email: string
  phone?: string
  seller_profile?: {
    verified_student: boolean
    rating: number
    phone?: string
  }
}

interface Props {
  isOpen: boolean
  listing: Listing
  seller: Seller
}

defineProps<Props>()

const emit = defineEmits<{
  close: []
  send: [message: string]
}>()

const message = ref('')
const isSending = ref(false)

const messageTemplates = [
  'Sản phẩm còn không?',
  'Có thể thương lượng giá không?',
  'Khi nào có thể gặp?',
  'Tình trạng thực tế như thế nào?'
]

const getImageUrl = (path: string) => {
  const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
  return `${baseUrl}/storage/${path}`
}

const formatPrice = (price: number) => {
  if (price === 0) return 'Miễn phí'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
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

const close = () => {
  emit('close')
  message.value = ''
}

const sendMessage = async () => {
  isSending.value = true
  
  try {
    // Simulate API call - replace with actual API call later
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    emit('send', message.value)
    showToast('success', 'Tin nhắn đã được gửi!')
    close()
  } catch (error) {
    showToast('error', 'Không thể gửi tin nhắn. Vui lòng thử lại.')
  } finally {
    isSending.value = false
  }
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
  transition: transform 0.3s ease;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
  transform: scale(0.95);
}
</style>

