<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg animate-fadeIn">
      <h2 class="text-xl font-semibold mb-4 text-gray-800">🧾 Gửi khiếu nại</h2>
      <textarea
        v-model="reason"
        rows="4"
        placeholder="Nhập lý do khiếu nại (tối thiểu 20 ký tự)..."
        class="w-full border rounded-lg p-3 text-gray-700 focus:ring-2 focus:ring-red-400 focus:outline-none"
      ></textarea>
      <div class="mt-5 flex justify-end gap-3">
        <button @click="$emit('close')" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
          Hủy
        </button>
        <button
          @click="submit"
          :disabled="loading"
          class="px-5 py-2 rounded-lg text-white flex items-center justify-center gap-2 shadow transition-all duration-200"
          :class="loading ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 active:scale-95'"
        >
          <svg
            v-if="loading"
            class="animate-spin h-5 w-5 text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
          </svg>
          <span>{{ loading ? 'Đang gửi...' : 'Gửi khiếu nại' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import { showToast } from '@/utils/toast'

const props = defineProps<{ order: any }>()
const emits = defineEmits(['close', 'submitted'])
const reason = ref('')
const loading = ref(false)

function getToken() {
  return localStorage.getItem('token_buyer') || localStorage.getItem('auth_token') || ''
}

async function submit() {
  if (reason.value.trim().length < 20) {
    showToast('error', '⚠️ Lý do khiếu nại phải có ít nhất 20 ký tự.')
    return
  }
  loading.value = true
  try {
    const res = await axios.post(
      '/api/disputes',
      { listing_id: props.order.listing_id, reason: reason.value },
      { headers: { Authorization: `Bearer ${getToken()}`, Accept: 'application/json' } }
    )
    showToast('success', res.data.message || '🎫 Khiếu nại đã được gửi thành công!')
    emits('submitted')
  } catch (err: any) {
    showToast('error', err?.response?.data?.message || 'Không thể gửi khiếu nại.')
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
