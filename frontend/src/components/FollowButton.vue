<template>
  <button
    @click="handleFollow"
    :disabled="!authStore.isAuthenticated || loading"
    class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600 disabled:bg-gray-400"
  >
    {{ loading ? 'Loading...' : isFollowing ? 'Unfollow' : 'Follow' }}
  </button>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

// Props từ component cha
interface Props {
  sellerId: number
}
const props = defineProps<Props>()

const authStore = useAuthStore()

const isFollowing = ref(false)
const loading = ref(false)

// Lấy trạng thái follow khi mount
const fetchFollowStatus = async () => {
  if (!authStore.isAuthenticated) return

  try {
    loading.value = true
    const res = await api.get(`/follow-sellers/${props.sellerId}/status`)
    // Backend trả về { isFollowing: true/false }
    isFollowing.value = res.data.isFollowing
  } catch (err) {
    console.error('Error fetching follow status:', err)
  } finally {
    loading.value = false
  }
}

// Follow / Unfollow
const handleFollow = async () => {
  if (!authStore.isAuthenticated) {
    alert('Bạn phải đăng nhập để follow seller')
    return
  }

  try {
    loading.value = true

    if (isFollowing.value) {
      await api.delete(`/follow-sellers/${props.sellerId}`)
      isFollowing.value = false
    } else {
      await api.post('/follow-sellers', { seller_id: props.sellerId })
      isFollowing.value = true
    }
  } catch (err) {
    console.error(err)
    alert('Có lỗi xảy ra khi follow/unfollow seller')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchFollowStatus()
})
</script>
