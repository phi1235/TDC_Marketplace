<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Hoạt động của tôi</h1>

    <div class="bg-white rounded-lg shadow-sm border p-4 mb-4 grid grid-cols-1 md:grid-cols-4 gap-3 items-center">
      <input v-model="filters.action" @keyup.enter="load" placeholder="Action (vd: login_success)" class="border rounded px-3 py-2" />
      <div class="text-sm text-gray-500 md:col-span-2">Tìm kiếm theo action.</div>
      <div class="flex justify-end">
        <button @click="load" class="bg-blue-600 text-white rounded px-5 py-2">Lọc</button>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border">
      <div v-if="loading" class="p-4 text-gray-500">Đang tải...</div>
      <div v-else class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500 border-b">
              <th class="py-2 px-4">Thời gian</th>
              <th class="py-2 px-4">Action</th>
              <th class="py-2 px-4">IP</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in logs" :key="log.id" class="border-b">
              <td class="py-2 px-4">{{ new Date(log.created_at).toLocaleString('vi-VN') }}</td>
              <td class="py-2 px-4">{{ log.action }}</td>
              <td class="py-2 px-4">{{ log.ip_address || '-' }}</td>
            </tr>
          </tbody>
        </table>
        <div v-if="!logs.length" class="p-4 text-gray-500">Chưa có hoạt động.</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchMyActivities } from '@/services/user'

const logs = ref<any[]>([])
const loading = ref(false)
const filters = ref({ action: '' })

async function load() {
  loading.value = true
  try {
    const res = await fetchMyActivities({ ...filters.value, per_page: 20 })
    logs.value = res.data || []
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>


