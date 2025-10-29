<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Quản trị hệ thống</h1>
      
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Tổng người dùng</h3>
          <div class="text-3xl font-bold text-blue-600">1,234</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Tin rao</h3>
          <div class="text-3xl font-bold text-green-600">567</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Chờ duyệt</h3>
          <div class="text-3xl font-bold text-yellow-600">23</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Báo cáo</h3>
          <div class="text-3xl font-bold text-red-600">12</div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Thao tác nhanh</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <button class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
            Quản lý người dùng
          </button>
          <button class="bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors">
            Duyệt tin rao
          </button>
          <button class="bg-yellow-600 text-white px-4 py-3 rounded-lg hover:bg-yellow-700 transition-colors">
            Xử lý báo cáo
          </button>
          <button class="bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition-colors">
            Thống kê
          </button>
        </div>
      </div>

      <!-- Recent Activity (Audit Logs) -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Hoạt động gần đây</h3>
        <div v-if="loading" class="text-gray-500">Đang tải...</div>
        <div v-else>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="text-left text-gray-500 border-b">
                  <th class="py-2 pr-4">Thời gian</th>
                  <th class="py-2 pr-4">Người dùng</th>
                  <th class="py-2 pr-4">Hành động</th>
                  <th class="py-2 pr-4">Đối tượng</th>
                  <th class="py-2">IP</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="log in logs" :key="log.id" class="border-b hover:bg-gray-50">
                  <td class="py-2 pr-4">{{ new Date(log.created_at).toLocaleString('vi-VN') }}</td>
                  <td class="py-2 pr-4">{{ log.user?.name || 'Hệ thống' }}</td>
                  <td class="py-2 pr-4 font-medium">{{ log.action }}</td>
                  <td class="py-2 pr-4">{{ log.auditable_type }}#{{ log.auditable_id }}</td>
                  <td class="py-2">{{ log.ip_address || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="!logs.length" class="text-gray-500">Chưa có hoạt động.</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { adminListingsService } from '@/services/adminListings'

const logs = ref<any[]>([])
const loading = ref(false)

onMounted(async () => {
  try {
    loading.value = true
    const res = await adminListingsService.auditLogs({ per_page: 10 })
    logs.value = res.data || []
  } finally {
    loading.value = false
  }
})
</script>
