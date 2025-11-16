<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
        📂 Quản lý Danh mục
      </h1>
      <button
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
      >
        <span>➕</span>
        <span>Thêm danh mục</span>
      </button>
    </div>

    <!-- Categories Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Icon</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tên</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Slug</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Mô tả</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="category in categories" :key="category.id">
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ category.id }}</td>
            <td class="px-6 py-4 text-2xl">{{ category.icon || '📦' }}</td>
            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ category.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ category.slug }}</td>
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ category.description || '-' }}</td>
            <td class="px-6 py-4">
              <span :class="[
                'px-2 py-1 text-xs rounded-full',
                category.is_active 
                  ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                  : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
              ]">
                {{ category.is_active ? 'Hoạt động' : 'Tắt' }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm space-x-2">
              <button
                @click="openEditModal(category)"
                class="text-blue-600 hover:text-blue-800 dark:text-blue-400"
              >
                ✏️
              </button>
              <button
                @click="toggleStatus(category)"
                class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400"
              >
                {{ category.is_active ? '🔒' : '🔓' }}
              </button>
              <button
                @click="deleteCategory(category.id)"
                class="text-red-600 hover:text-red-800 dark:text-red-400"
              >
                🗑️
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
          {{ isEditing ? 'Chỉnh sửa danh mục' : 'Thêm danh mục mới' }}
        </h2>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Tên danh mục <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Icon -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Icon (emoji)
            </label>
            <input
              v-model="form.icon"
              type="text"
              placeholder="📦"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Mô tả
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>
          </div>

          <!-- Active Status -->
          <div class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              id="is_active"
              class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            />
            <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
              Hoạt động
            </label>
          </div>

          <!-- Buttons -->
          <div class="flex gap-2 justify-end pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg"
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
import { ref, onMounted } from 'vue'
import { categoriesService } from '@/services/categories'
import { showToast } from '@/utils/toast'

interface Category {
  id: number
  name: string
  slug: string
  description?: string
  icon?: string
  is_active: boolean
}

const categories = ref<Category[]>([])
const showModal = ref(false)
const isEditing = ref(false)
const loading = ref(false)
const form = ref({
  id: null as number | null,
  name: '',
  icon: '',
  description: '',
  is_active: true
})

const loadCategories = async () => {
  try {
    categories.value = await categoriesService.getCategories()
  } catch (error) {
    console.error('Error loading categories:', error)
    showToast('error', 'Không thể tải danh mục')
  }
}

const openCreateModal = () => {
  isEditing.value = false
  form.value = {
    id: null,
    name: '',
    icon: '',
    description: '',
    is_active: true
  }
  showModal.value = true
}

const openEditModal = (category: Category) => {
  isEditing.value = true
  form.value = {
    id: category.id,
    name: category.name,
    icon: category.icon || '',
    description: category.description || '',
    is_active: category.is_active
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const handleSubmit = async () => {
  loading.value = true
  try {
    if (isEditing.value && form.value.id) {
      // Update category
      await categoriesService.updateCategory(form.value.id, {
        name: form.value.name,
        icon: form.value.icon,
        description: form.value.description,
        is_active: form.value.is_active
      })
      showToast('success', 'Cập nhật danh mục thành công')
    } else {
      // Create category
      await categoriesService.createCategory({
        name: form.value.name,
        icon: form.value.icon,
        description: form.value.description,
        is_active: form.value.is_active
      })
      showToast('success', 'Tạo danh mục thành công')
    }
    closeModal()
    await loadCategories()
  } catch (error: any) {
    console.error('Error saving category:', error)
    showToast('error', error.response?.data?.message || 'Có lỗi xảy ra')
  } finally {
    loading.value = false
  }
}

const toggleStatus = async (category: Category) => {
  try {
    await categoriesService.updateCategory(category.id, {
      is_active: !category.is_active
    })
    showToast('success', 'Cập nhật trạng thái thành công')
    await loadCategories()
  } catch (error) {
    console.error('Error toggling status:', error)
    showToast('error', 'Không thể cập nhật trạng thái')
  }
}

const deleteCategory = async (id: number) => {
  if (!confirm('Bạn có chắc chắn muốn xóa danh mục này?')) return
  
  try {
    await categoriesService.deleteCategory(id)
    showToast('success', 'Xóa danh mục thành công')
    await loadCategories()
  } catch (error) {
    console.error('Error deleting category:', error)
    showToast('error', 'Không thể xóa danh mục')
  }
}

onMounted(() => {
  loadCategories()
})
</script>
