<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Quản lý Ngành học</h1>
          <p class="mt-2 text-sm text-gray-600">
            Quản lý danh sách các ngành học tại TDC
          </p>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Thêm ngành học
        </button>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      <p class="mt-4 text-gray-600">Đang tải dữ liệu...</p>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <div class="flex">
        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Lỗi tải dữ liệu</h3>
          <p class="mt-1 text-sm text-red-700">{{ error }}</p>
        </div>
      </div>
    </div>

    <!-- Majors table -->
    <div v-else class="bg-white shadow-md rounded-lg overflow-hidden">
      <div v-if="majors.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có ngành học</h3>
        <p class="mt-1 text-sm text-gray-500">Bắt đầu bằng cách tạo ngành học mới.</p>
        <div class="mt-6">
          <button
            @click="openCreateModal"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Thêm ngành học
          </button>
        </div>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngành học</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thống kê</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="major in sortedMajors" :key="major.id" class="hover:bg-gray-50">
            <!-- Display Order -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ major.display_order }}
            </td>

            <!-- Name with Icon -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <span class="text-2xl mr-2">{{ major.icon }}</span>
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ major.name }}</div>
                  <div class="text-sm text-gray-500">{{ major.slug }}</div>
                </div>
              </div>
            </td>

            <!-- Description -->
            <td class="px-6 py-4 text-sm text-gray-500">
              <div class="max-w-xs truncate" :title="major.description">
                {{ major.description || '—' }}
              </div>
            </td>

            <!-- Status -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  major.is_active
                    ? 'bg-green-100 text-green-800'
                    : 'bg-gray-100 text-gray-800',
                  'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                ]"
              >
                {{ major.is_active ? 'Hoạt động' : 'Ẩn' }}
              </span>
            </td>

            <!-- Stats -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <div>
                <span class="font-medium">{{ major.users_count || 0 }}</span> user
              </div>
              <div>
                <span class="font-medium">{{ major.listings_count || 0 }}</span> listing
              </div>
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openEditModal(major)"
                class="text-blue-600 hover:text-blue-900 mr-4"
                title="Chỉnh sửa"
              >
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button
                @click="confirmDelete(major)"
                class="text-red-600 hover:text-red-900"
                title="Xóa"
              >
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Major Form Modal -->
    <MajorFormModal
      v-if="showModal"
      :major="editingMajor"
      @close="closeModal"
      @success="handleSuccess"
    />

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed z-50 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeDeleteModal"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Xóa ngành học</h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Bạn có chắc chắn muốn xóa ngành học <strong>{{ deletingMajor?.name }}</strong>?
                  </p>
                  <p class="mt-2 text-sm text-red-600">
                    Lưu ý: Không thể xóa nếu có user hoặc listing đang sử dụng ngành học này.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="handleDelete"
              :disabled="deleting"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ deleting ? 'Đang xóa...' : 'Xóa' }}
            </button>
            <button
              @click="closeDeleteModal"
              :disabled="deleting"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Hủy
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Toast -->
    <div
      v-if="successMessage"
      class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center"
    >
      <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
      {{ successMessage }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { getMajors, deleteMajor } from '@/services/majors'
import type { Major } from '@/types/major'
import MajorFormModal from '@/components/admin/MajorFormModal.vue'

// State
const majors = ref<Major[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const showModal = ref(false)
const editingMajor = ref<Major | null>(null)
const showDeleteModal = ref(false)
const deletingMajor = ref<Major | null>(null)
const deleting = ref(false)
const successMessage = ref<string | null>(null)

// Computed
const sortedMajors = computed(() => {
  return [...majors.value].sort((a, b) => a.display_order - b.display_order)
})

// Load majors on mount
onMounted(() => {
  loadMajors()
})

// Load majors from API
const loadMajors = async () => {
  try {
    loading.value = true
    error.value = null
    majors.value = await getMajors()
  } catch (err: any) {
    error.value = err.message || 'Không thể tải danh sách ngành học'
    console.error('Failed to load majors:', err)
  } finally {
    loading.value = false
  }
}

// Modal handlers
const openCreateModal = () => {
  editingMajor.value = null
  showModal.value = true
}

const openEditModal = (major: Major) => {
  editingMajor.value = major
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingMajor.value = null
}

const handleSuccess = (message: string) => {
  closeModal()
  loadMajors()
  showSuccessMessage(message)
}

// Delete handlers
const confirmDelete = (major: Major) => {
  deletingMajor.value = major
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  deletingMajor.value = null
}

const handleDelete = async () => {
  if (!deletingMajor.value) return

  try {
    deleting.value = true
    await deleteMajor(deletingMajor.value.id)
    showSuccessMessage('Xóa ngành học thành công')
    loadMajors()
    closeDeleteModal()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Không thể xóa ngành học. Vui lòng thử lại.')
  } finally {
    deleting.value = false
  }
}

// Success message
const showSuccessMessage = (message: string) => {
  successMessage.value = message
  setTimeout(() => {
    successMessage.value = null
  }, 3000)
}
</script>
