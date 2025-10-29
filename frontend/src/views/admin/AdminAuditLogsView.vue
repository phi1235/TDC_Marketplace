<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Lịch sử hoạt động</h1>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-4 grid grid-cols-1 md:grid-cols-5 gap-3 items-center">
      <input v-model="filters.search" @keyup.enter="load" placeholder="Tìm theo action, IP, user-agent" class="border rounded px-3 py-2" />
      <input v-model.number="filters.user_id" @keyup.enter="load" type="number" placeholder="User ID" class="border rounded px-3 py-2" />
      <input v-model="filters.auditable_type" @keyup.enter="load" placeholder="Auditable type (e.g. App\\Models\\Listing)" class="border rounded px-3 py-2" />
      <input v-model="filters.action" @keyup.enter="load" placeholder="Action (created, updated, ...)" class="border rounded px-3 py-2" />
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
              <th class="py-2 px-4">User</th>
              <th class="py-2 px-4">Action</th>
              <th class="py-2 px-4">Auditable</th>
              <th class="py-2 px-4">IP</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in logs" :key="log.id" class="border-b hover:bg-gray-50">
              <td class="py-2 px-4">{{ new Date(log.created_at).toLocaleString('vi-VN') }}</td>
              <td class="py-2 px-4">{{ log.user?.name || 'Hệ thống' }}</td>
              <td class="py-2 px-4 font-medium">{{ log.action }}</td>
              <td class="py-2 px-4">{{ log.auditable_type }}#{{ log.auditable_id }}</td>
              <td class="py-2 px-4">{{ log.ip_address || '-' }}</td>
            </tr>
          </tbody>
        </table>
        <div v-if="!logs.length" class="p-4 text-gray-500">Không có dữ liệu.</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { adminListingsService } from '@/services/adminListings'

const logs = ref<any[]>([])
const loading = ref(false)
const filters = ref({ search: '', action: '', user_id: undefined as number | undefined, auditable_type: '' })

async function load() {
  loading.value = true
  try {
    const res = await adminListingsService.auditLogs({ ...filters.value, per_page: 20 })
    logs.value = res.data || []
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>


