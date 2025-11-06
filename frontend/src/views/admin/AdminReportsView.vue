<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Quản lý báo cáo</h2>
      <p class="text-gray-600 dark:text-gray-400">Xem và xử lý các báo cáo vi phạm</p>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-center">
  <!-- Bộ lọc trạng thái -->
  <select
    v-model="filters.status"
    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
  >
    <option value="">Tất cả trạng thái</option>
    <option value="pending">Chờ xử lý</option>
    <option value="reviewed">Đang xem xét</option>
    <option value="resolved">Đã xử lý</option>
    <option value="dismissed">Bị từ chối</option>
  </select>

  <!-- Bộ lọc loại -->
  <select
    v-model="filters.type"
    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
  >
    <option value="">Tất cả loại</option>
    <option value="listing">Tin rao</option>
    <option value="user">Người dùng</option>
    <option value="review">Đánh giá</option>
  </select>

  <!-- Ô tìm kiếm -->
  <input
    v-model="filters.search"
    type="text"
    placeholder="Tìm kiếm..."
    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
  />

  <!-- Nút áp dụng -->
  <button
    @click="fetchReports"
    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
  >
    Áp dụng bộ lọc
  </button>

  <!-- Export CSV -->
  <button
    @click="exportReports('csv')"
    class="flex items-center justify-center gap-2 px-4 py-2 bg-green-500 text-white rounded-md font-medium hover:bg-green-600 transition-all"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M4 12l4-4m0 0l4 4m-4-4v12" />
    </svg>
    CSV
  </button>

  <!-- Export Excel -->
  <button
    @click="exportReports('xlsx')"
    class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-md font-medium hover:bg-blue-600 transition-all"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
    </svg>
    Excel
  </button>
</div>


      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Tổng báo cáo</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Chờ xử lý</p>
              <p class="text-2xl font-bold text-yellow-600">{{ stats.pending || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Đã xử lý</p>
              <p class="text-2xl font-bold text-green-600">{{ stats.resolved || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Tỷ lệ xử lý</p>
              <p class="text-2xl font-bold text-blue-600">{{ resolveRate }}%</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="animate-pulse space-y-4">
        <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
        <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
        <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
      </div>

      <!-- Reports List -->
      <div v-else-if="reports.length > 0" class="space-y-4">
        <div v-for="report in reports" :key="report.id"
          class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md transition-shadow">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <div class="flex items-center space-x-2 mb-2">
                <span :class="getStatusBadgeClass(report.status)">
                  {{ getStatusLabel(report.status) }}
                </span>
                <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-600 rounded-full">
                  {{ getReportableTypeLabel(report.reportable_type) }}
                </span>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                {{ getReasonLabel(report.reason) }}
              </h3>
              <p class="text-sm text-gray-700 dark:text-gray-300">{{ report.description }}</p>
            </div>
          </div>

          <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
            <div class="flex items-center space-x-4">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Reporter: ID {{ report.reporter_id }}
              </div>
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ formatDate(report.created_at) }}
              </div>
            </div>
            <button @click="openConfirm(report)" :class="[
              'px-4 py-2 rounded-md text-sm font-medium transition-colors',
              getActionButtonClass(report.status)
            ]">
              {{ getActionButtonText(report.status) }}
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Không có báo cáo nào</h3>
        <p class="text-gray-600 dark:text-gray-400">Chưa có báo cáo nào phù hợp với bộ lọc</p>
      </div>
    </div>

    <!-- Confirm Modal -->
    <Teleport to="body">
      <transition name="fade">
        <div v-if="showConfirm" class="fixed inset-0 z-50 flex items-center justify-center">
          <div class="absolute inset-0 bg-black/40" @click="closeConfirm" />
          <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Xác nhận xử lý</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ confirmMessage }}</p>
            <div class="flex justify-end gap-3">
              <button @click="closeConfirm"
                class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300">Hủy</button>
              <button @click="confirmHandle" class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">Xác
                nhận</button>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>
     </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'
import { showToast } from '@/utils/toast'

interface Report {
  id: number
  reporter_id: number
  reportable_type: string
  reportable_id: number
  reason: string
  description: string
  status: string
  created_at: string
}

const loading = ref(true)
const reports = ref<Report[]>([])
// Mặc định hiển thị các báo cáo Chờ xử lý để không chiếm diện tích bởi mục đã xử lý/từ chối
const filters = ref({
  status: 'pending',
  type: '',
  search: ''
})
const stats = ref({
  total: 0,
  pending: 0,
  resolved: 0
})

const resolveRate = computed(() => {
  if (stats.value.total === 0) return 0
  return Math.round((stats.value.resolved / stats.value.total) * 100)
})

const fetchReports = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/reports', { params: filters.value })
    reports.value = response.data.data || []
    // Update stats based on fetched data
    stats.value = {
      total: response.data.total ?? reports.value.length,
      pending: reports.value.filter(r => r.status === 'pending').length,
      resolved: reports.value.filter(r => r.status === 'resolved').length
    }
  } catch (error) {
    console.error('Failed to fetch reports:', error)
    showToast('error', 'Không thể tải danh sách báo cáo')
  } finally {
    loading.value = false
  }
}

const handleReport = async (report: Report) => {
  try {
    await api.post(`/admin/reports/${report.id}/handle`, {
      action: report.status === 'pending' ? 'accept' : report.status === 'reviewed' ? 'resolve' : 'reject'
    })
    showToast('success', 'Đã xử lý báo cáo thành công')
    await fetchReports()
  } catch (error) {
    console.error('Failed to handle report:', error)
    showToast('error', 'Không thể xử lý báo cáo')
  }
}

// Professional confirm instead of window.confirm
const showConfirm = ref(false)
const confirmTarget = ref<Report | null>(null)
const confirmMessage = ref('')

const openConfirm = (report: Report) => {
  confirmTarget.value = report
  const nextAction = report.status === 'pending' ? 'chuyển sang Đang xem xét' : report.status === 'reviewed' ? 'đánh dấu Đã xử lý' : 'từ chối'
  confirmMessage.value = `Bạn có chắc muốn ${nextAction} báo cáo này?`
  showConfirm.value = true
}

const closeConfirm = () => {
  showConfirm.value = false
  confirmTarget.value = null
}

const confirmHandle = async () => {
  if (!confirmTarget.value) return
  const r = confirmTarget.value
  showConfirm.value = false
  await handleReport(r)
}

const getStatusLabel = (status: string) => {
  const statusMap: Record<string, string> = {
    pending: 'Chờ xử lý',
    reviewed: 'Đang xem xét',
    resolved: 'Đã xử lý',
    dismissed: 'Bị từ chối'
  }
  return statusMap[status] || status
}

const getStatusBadgeClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    reviewed: 'px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    resolved: 'px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    dismissed: 'px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
  }
  return classes[status] || ''
}

const getReportableTypeLabel = (type: string) => {
  const typeMap: Record<string, string> = {
    'App\\Models\\Listing': 'Tin rao',
    'App\\Models\\User': 'Người dùng',
    'App\\Models\\Review': 'Đánh giá',
    listing: 'Tin rao',
    user: 'Người dùng',
    review: 'Đánh giá'
  }
  return typeMap[type] || type
}

const getReasonLabel = (reason: string) => {
  const reasonMap: Record<string, string> = {
    fraud: 'Lừa đảo',
    fake_product: 'Hàng giả',
    spam: 'Spam',
    inappropriate_content: 'Nội dung không phù hợp',
    price_manipulation: 'Thao túng giá',
    fake_reviews: 'Đánh giá giả',
    harassment: 'Quấy rối',
    copyright_violation: 'Vi phạm bản quyền',
    other: 'Khác'
  }
  return reasonMap[reason] || reason
}

const getActionButtonText = (status: string) => {
  const textMap: Record<string, string> = {
    pending: 'Xem xét',
    reviewed: 'Đã xử lý',
    resolved: 'Đã xử lý',
    dismissed: 'Bị từ chối'
  }
  return textMap[status] || 'Xem'
}

const getActionButtonClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-600 hover:bg-yellow-700 text-white',
    reviewed: 'bg-blue-600 hover:bg-blue-700 text-white',
    resolved: 'bg-green-600 hover:bg-green-700 text-white',
    dismissed: 'bg-red-600 hover:bg-red-700 text-white'
  }
  return classes[status] || 'bg-gray-600 hover:bg-gray-700 text-white'
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
const exportReports = async (format: 'csv' | 'xlsx') => {
  try {
    const params = new URLSearchParams({ ...filters.value, format });
    const response = await api.get(`/admin/reports/export?${params.toString()}`, {
      responseType: 'blob',
    });

    const blob = new Blob([response.data], {
      type:
        format === 'csv'
          ? 'text/csv'
          : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    });

    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `reports_${new Date().toISOString().slice(0, 19)}.${format}`;
    link.click();

    showToast('success', 'Đã xuất dữ liệu báo cáo thành công');
  } catch (error) {
    console.error('Export failed', error);
    showToast('error', 'Không thể xuất dữ liệu báo cáo');
  }
};

onMounted(() => {
  fetchReports()
})
</script>
