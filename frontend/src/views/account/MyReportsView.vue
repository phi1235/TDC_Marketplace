<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-8">
      <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Báo cáo của tôi</h1>
          <p class="text-gray-600 dark:text-gray-400">Xem lại các báo cáo vi phạm bạn đã gửi</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Tổng số</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_reports || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Chờ xử lý</p>
                <p class="text-2xl font-bold text-yellow-600">{{ stats.pending_reports || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Đang xem xét</p>
                <p class="text-2xl font-bold text-blue-600">{{ stats.reviewed_reports || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Đã xử lý</p>
                <p class="text-2xl font-bold text-green-600">{{ stats.resolved_reports || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Bị từ chối</p>
                <p class="text-2xl font-bold text-red-600">{{ stats.dismissed_reports || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
          <div class="flex flex-col sm:flex-row gap-4">
            <select
              v-model="filters.status"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              <option value="">Tất cả trạng thái</option>
              <option value="pending">Chờ xử lý</option>
              <option value="reviewed">Đang xem xét</option>
              <option value="resolved">Đã xử lý</option>
              <option value="dismissed">Bị từ chối</option>
            </select>

            <select
              v-model="filters.type"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              <option value="">Tất cả loại</option>
              <option value="App\\Models\\Listing">Tin rao</option>
              <option value="App\\Models\\User">Người dùng</option>
              <option value="App\\Models\\Review">Đánh giá</option>
            </select>

            <input
              v-model="filters.search"
              type="text"
              placeholder="Tìm kiếm..."
              class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
            />

            <button
              @click="fetchReports"
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Áp dụng bộ lọc
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <div class="animate-pulse space-y-4">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
          </div>
        </div>

        <!-- Reports List -->
        <div v-else-if="reports.length > 0" class="space-y-4">
          <div
            v-for="report in reports"
            :key="report.id"
            class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div class="p-6">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <div class="flex items-center space-x-2 mb-2">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                      {{ getReasonLabel(report.reason) }}
                    </h3>
                    <span :class="getStatusBadgeClass(report.status)">
                      {{ getStatusLabel(report.status) }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Loại:</span> {{ getReportableTypeLabel(report.reportable_type) }}
                  </p>
                </div>
                <button
                  @click="toggleExpanded(report.id)"
                  class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                >
                  <svg
                    class="w-5 h-5 transition-transform"
                    :class="{ 'rotate-180': expandedReports.has(report.id) }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
              </div>

              <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">{{ report.description }}</p>

              <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ formatDate(report.created_at) }}
              </div>

              <!-- Expanded Details -->
              <div v-if="expandedReports.has(report.id)" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                <div v-if="report.admin_notes" class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                  <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <div>
                      <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Ghi chú từ quản trị viên:</p>
                      <p class="text-sm text-yellow-700 dark:text-yellow-400">{{ report.admin_notes }}</p>
                    </div>
                  </div>
                </div>

                <div v-if="report.reviewed_at" class="text-xs text-gray-600 dark:text-gray-400">
                  Được xử lý lúc: {{ formatDate(report.reviewed_at) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
          <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Chưa có báo cáo nào</h3>
          <p class="text-gray-600 dark:text-gray-400">Bạn chưa gửi báo cáo vi phạm nào</p>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="mt-8 flex items-center justify-between">
          <div class="text-sm text-gray-600 dark:text-gray-400">
            Hiển thị {{ pagination.from }} đến {{ pagination.to }} trong tổng số {{ pagination.total }} báo cáo
          </div>
          <div class="flex space-x-2">
            <button
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Trước
            </button>
            <button
              v-for="page in paginationPages"
              :key="page"
              @click="changePage(page)"
              :class="[
                'px-4 py-2 rounded-md',
                page === pagination.current_page
                  ? 'bg-red-600 text-white'
                  : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'
              ]"
            >
              {{ page }}
            </button>
            <button
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Sau
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { reportService, type Report, type ReportStats } from '@/services/report'

const loading = ref(true)
const reports = ref<Report[]>([])
const stats = ref<ReportStats>({
  total_reports: 0,
  pending_reports: 0,
  reviewed_reports: 0,
  resolved_reports: 0,
  dismissed_reports: 0
})
const expandedReports = ref<Set<number>>(new Set())
const filters = ref({
  status: '',
  type: '',
  search: ''
})
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0
})

const paginationPages = computed(() => {
  const pages = []
  const current = pagination.value.current_page
  const last = pagination.value.last_page

  if (last <= 7) {
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
  } else {
    if (current <= 3) {
      for (let i = 1; i <= 5; i++) pages.push(i)
      pages.push(-1) // separator
      pages.push(last)
    } else if (current >= last - 2) {
      pages.push(1)
      pages.push(-1)
      for (let i = last - 4; i <= last; i++) pages.push(i)
    } else {
      pages.push(1)
      pages.push(-1)
      for (let i = current - 1; i <= current + 1; i++) pages.push(i)
      pages.push(-1)
      pages.push(last)
    }
  }

  return pages
})

const toggleExpanded = (id: number) => {
  if (expandedReports.value.has(id)) {
    expandedReports.value.delete(id)
  } else {
    expandedReports.value.add(id)
  }
}

const changePage = (page: number) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    pagination.value.current_page = page
    fetchReports()
  }
}

const fetchStats = async () => {
  try {
    stats.value = await reportService.getStats()
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const fetchReports = async () => {
  loading.value = true
  try {
    const response = await reportService.getReports({
      ...filters.value,
      page: pagination.value.current_page,
      per_page: pagination.value.per_page
    })
    reports.value = response.data
    pagination.value = {
      current_page: response.current_page,
      last_page: response.last_page,
      per_page: response.per_page,
      total: response.total,
      from: response.from,
      to: response.to
    }
  } catch (error) {
    console.error('Failed to fetch reports:', error)
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await Promise.all([fetchStats(), fetchReports()])
})

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
    'App\\Models\\Review': 'Đánh giá'
  }
  return typeMap[type] || type
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
</script>

