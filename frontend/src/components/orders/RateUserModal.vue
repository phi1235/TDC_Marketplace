<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96 shadow-lg animate-fadeIn">
      <h2 class="text-lg font-semibold mb-4">⭐ Đánh giá người bán</h2>

      <!-- Sao -->
      <div class="flex justify-center mb-4 space-x-1">
        <button
          v-for="i in 5"
          :key="i"
          type="button"
          @click="setRating(i)"
          class="focus:outline-none"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            class="w-8 h-8 transition-colors duration-150"
            :class="i <= rating ? 'text-yellow-400' : 'text-gray-300'"
          >
            <path
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 
             3.292a1 1 0 00.95.69h3.462c.969 0 
             1.371 1.24.588 1.81l-2.8 
             2.034a1 1 0 00-.364 1.118l1.07 
             3.292c.3.921-.755 
             1.688-1.54 1.118l-2.8-2.034a1 
             1 0 00-1.175 0l-2.8 
             2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 
             1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 
             1 0 00.951-.69l1.07-3.292z"
            />
          </svg>
        </button>
      </div>

      <!-- Bình luận -->
      <textarea
        v-model="comment"
        placeholder="Nhập nhận xét của bạn (tuỳ chọn)"
        class="w-full border rounded-lg p-2 text-sm mb-4"
      ></textarea>

      <div class="flex justify-end space-x-2">
        <button @click="$emit('close')" class="px-3 py-1 border rounded-md text-gray-700">
          Huỷ
        </button>
        <button
          @click="submitRating"
          :disabled="loading"
          class="px-4 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-2"
        >
          <svg
            v-if="loading"
            class="animate-spin h-4 w-4 text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
            />
          </svg>
          <span>{{ loading ? 'Đang gửi...' : 'Gửi đánh giá' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import { showToast } from '@/utils/toast'

const props = defineProps({
  isOpen: Boolean,
  orderId: Number,
})
const emit = defineEmits(['close', 'submitted'])

const rating = ref(5)
const comment = ref('')
const loading = ref(false)

function setRating(value: number) {
  rating.value = value
}

async function submitRating() {
  if (!props.orderId) return
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token') || localStorage.getItem('token_buyer')
    await axios.post(
      '/api/ratings',
      {
        order_id: props.orderId,
        stars: rating.value,
        comment: comment.value,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    )
    showToast('success', '🎉 Đã gửi đánh giá thành công!')
    emit('submitted')
    emit('close')
  } catch (err: any) {
    showToast('error', err.response?.data?.message || 'Không thể gửi đánh giá.')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
