<template>
  <section class="mt-10">
    <h3 class="text-xl font-bold mb-4 text-gray-900">Tin rao tương tự</h3>
    <div v-if="loading" class="text-gray-500 italic">Đang tải...</div>
    <div v-else-if="error" class="text-red-600">{{ error }}</div>

    <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <ListingCard v-for="item in listings" :key="item.id" :listing="item" />
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import ListingCard from '@/components/ListingCard.vue'

const props = defineProps<{ listingId: number }>()

const listings = ref([])
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const res = await axios.get(`/api/listings/${props.listingId}/related`)
    listings.value = res.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Không thể tải tin rao tương tự'
  } finally {
    loading.value = false
  }
})
</script>
