<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
        📍 Quản lý điểm nhận hàng
      </h1>
      <button
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
      >
        <span>➕</span>
        <span>Thêm điểm nhận</span>
      </button>
    </div>

    <!-- Search -->
    <div class="mb-6">
      <input
        v-model="searchQuery"
        @input="handleSearch"
        type="text"
        placeholder="🔍 Tìm kiếm theo tên hoặc địa chỉ..."
        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
      />
    </div>

    <!-- Pickup Points Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
              Tên điểm nhận
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
              Mã cơ sở
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
              Địa chỉ
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
              Tọa độ
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
              Trạng thái
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
              Hành động
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="point in pickupPoints" :key="point.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
              {{ point.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
              {{ point.campus_code || 'N/A' }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
              {{ point.address || 'N/A' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
              {{ point.lat && point.lng ? `${point.lat}, ${point.lng}` : 'N/A' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'px-2 py-1 text-xs font-semibold rounded-full',
                  point.is_active
                    ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                    : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                ]"
              >
                {{ point.is_active ? 'Hoạt động' : 'Vô hiệu' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
              <button
                @click="openEditModal(point)"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400"
              >
                ✏️ Sửa
              </button>
              <button
                @click="confirmDelete(point)"
                class="text-red-600 hover:text-red-900 dark:text-red-400"
              >
                🗑️ Xóa
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="pickupPoints.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
        Không có điểm nhận hàng nào.
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">
          {{ isEditing ? '✏️ Chỉnh sửa điểm nhận' : '➕ Thêm điểm nhận mới' }}
        </h2>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Tên điểm nhận <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
              placeholder="Ví dụ: Cổng chính TDC"
            />
          </div>

          <!-- Campus Code -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Mã cơ sở
            </label>
            <input
              v-model="form.campus_code"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
              placeholder="Ví dụ: TDC-CS1"
            />
          </div>

          <!-- Address -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Địa chỉ
            </label>
            <textarea
              v-model="form.address"
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
              placeholder="Nhập địa chỉ chi tiết"
            ></textarea>
          </div>

          <!-- Coordinates -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Vĩ độ (Latitude)
              </label>
              <input
                v-model.number="form.lat"
                type="number"
                step="0.000001"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
                placeholder="10.850769"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Kinh độ (Longitude)
              </label>
              <input
                v-model.number="form.lng"
                type="number"
                step="0.000001"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
                placeholder="106.771999"
              />
            </div>
          </div>

          <!-- Opening Hours (optional advanced feature) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Giờ mở cửa (JSON - tùy chọn)
            </label>
            <textarea
              v-model="openingHoursText"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg font-mono text-xs"
              placeholder='{"mon": "8:00-17:00", "tue": "8:00-17:00"}'
            ></textarea>
          </div>

          <!-- Is Active -->
          <div class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              id="is_active"
              class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            />
            <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
              Kích hoạt điểm nhận này
            </label>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              Hủy
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50"
            >
              {{ loading ? 'Đang lưu...' : (isEditing ? 'Cập nhật' : 'Tạo mới') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'
import { showToast } from '@/utils/toast'

interface PickupPoint {
  id: number
  name: string
  campus_code?: string
  address?: string
  lat?: number
  lng?: number
  opening_hours?: any
  is_active: boolean
}

const pickupPoints = ref<PickupPoint[]>([])
const showModal = ref(false)
const isEditing = ref(false)
const loading = ref(false)
const searchQuery = ref('')

const form = ref({
  id: null as number | null,
  name: '',
  campus_code: '',
  address: '',
  lat: null as number | null,
  lng: null as number | null,
  opening_hours: null as any,
  is_active: true
})

const openingHoursText = ref('')

const fetchPickupPoints = async () => {
  try {
    const params: any = {}
    if (searchQuery.value) {
      params.search = searchQuery.value
    }
    const response = await api.get('/pickup-points', { params })
    pickupPoints.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching pickup points:', error)
    showToast('error', 'Không thể tải danh sách điểm nhận')
  }
}

let searchTimeout: any = null
const handleSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchPickupPoints()
  }, 500)
}

const openCreateModal = () => {
  isEditing.value = false
  form.value = {
    id: null,
    name: '',
    campus_code: '',
    address: '',
    lat: null,
    lng: null,
    opening_hours: null,
    is_active: true
  }
  openingHoursText.value = ''
  showModal.value = true
}

const openEditModal = (point: PickupPoint) => {
  isEditing.value = true
  form.value = {
    id: point.id,
    name: point.name,
    campus_code: point.campus_code || '',
    address: point.address || '',
    lat: point.lat || null,
    lng: point.lng || null,
    opening_hours: point.opening_hours,
    is_active: point.is_active
  }
  openingHoursText.value = point.opening_hours ? JSON.stringify(point.opening_hours, null, 2) : ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const handleSubmit = async () => {
  loading.value = true

  try {
    // Parse opening hours if provided
    let openingHours = null
    if (openingHoursText.value.trim()) {
      try {
        openingHours = JSON.parse(openingHoursText.value)
      } catch (e) {
        showToast('error', 'Định dạng JSON giờ mở cửa không hợp lệ')
        loading.value = false
        return
      }
    }

    const data = {
      name: form.value.name,
      campus_code: form.value.campus_code || null,
      address: form.value.address || null,
      lat: form.value.lat,
      lng: form.value.lng,
      opening_hours: openingHours,
      is_active: form.value.is_active
    }

    if (isEditing.value && form.value.id) {
      await api.put(`/pickup-points/${form.value.id}`, data)
      showToast('success', 'Cập nhật điểm nhận thành công')
    } else {
      await api.post('/pickup-points', data)
      showToast('success', 'Tạo điểm nhận thành công')
    }

    closeModal()
    fetchPickupPoints()
  } catch (error: any) {
    console.error('Error saving pickup point:', error)
    showToast('error', error.response?.data?.message || 'Có lỗi xảy ra')
  } finally {
    loading.value = false
  }
}

const confirmDelete = async (point: PickupPoint) => {
  if (!confirm(`Bạn có chắc chắn muốn xóa điểm nhận "${point.name}"?`)) {
    return
  }

  try {
    await api.delete(`/pickup-points/${point.id}`)
    showToast('success', 'Xóa điểm nhận thành công')
    fetchPickupPoints()
  } catch (error) {
    console.error('Error deleting pickup point:', error)
    showToast('error', 'Không thể xóa điểm nhận')
  }
}

onMounted(() => {
  fetchPickupPoints()
})
</script>
