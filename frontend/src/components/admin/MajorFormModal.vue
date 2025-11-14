<template>
  <div class="fixed z-50 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <!-- Header -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ isEdit ? 'Chỉnh sửa ngành học' : 'Thêm ngành học mới' }}
            </h3>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">
                Tên ngành học <span class="text-red-500">*</span>
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-300': errors.name }"
                placeholder="Ví dụ: Công nghệ thông tin"
              />
              <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
            </div>

            <!-- Icon (Emoji) -->
            <div>
              <label for="icon" class="block text-sm font-medium text-gray-700">
                Icon (Emoji) <span class="text-red-500">*</span>
              </label>
              <div class="mt-1 flex items-center space-x-2">
                <input
                  id="icon"
                  v-model="form.icon"
                  type="text"
                  required
                  maxlength="10"
                  class="block w-24 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-2xl text-center"
                  :class="{ 'border-red-300': errors.icon }"
                  placeholder="💻"
                />
                <span class="text-sm text-gray-500">Chọn emoji phù hợp với ngành học</span>
              </div>
              <p v-if="errors.icon" class="mt-1 text-sm text-red-600">{{ errors.icon }}</p>
              <p class="mt-1 text-xs text-gray-500">
                💡 Gợi ý: 💻 (CNTT), ⚡ (Điện), ⚙️ (Cơ khí), 📊 (Kế toán), 💼 (Quản trị), 🏨 (Du lịch), 🌐 (Ngoại ngữ), 🎨 (Thiết kế)
              </p>
            </div>

            <!-- Description -->
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700">
                Mô tả
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                maxlength="1000"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-300': errors.description }"
                placeholder="Mô tả ngắn gọn về ngành học này..."
              ></textarea>
              <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
              <p class="mt-1 text-xs text-gray-500">
                {{ form.description?.length || 0 }}/1000 ký tự
              </p>
            </div>

            <!-- Display Order -->
            <div>
              <label for="display_order" class="block text-sm font-medium text-gray-700">
                Thứ tự hiển thị
              </label>
              <input
                id="display_order"
                v-model.number="form.display_order"
                type="number"
                min="0"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-300': errors.display_order }"
                placeholder="0"
              />
              <p v-if="errors.display_order" class="mt-1 text-sm text-red-600">{{ errors.display_order }}</p>
              <p class="mt-1 text-xs text-gray-500">
                Số nhỏ hơn sẽ hiển thị trước
              </p>
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
              <input
                id="is_active"
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label for="is_active" class="ml-2 block text-sm text-gray-900">
                Kích hoạt ngành học này
              </label>
            </div>

            <!-- Error message -->
            <div v-if="errorMessage" class="rounded-md bg-red-50 p-4">
              <div class="flex">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div class="ml-3">
                  <p class="text-sm text-red-700">{{ errorMessage }}</p>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="submitting"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ submitting ? 'Đang lưu...' : (isEdit ? 'Cập nhật' : 'Tạo mới') }}
              </button>
              <button
                type="button"
                @click="$emit('close')"
                :disabled="submitting"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
              >
                Hủy
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { createMajor, updateMajor } from '@/services/majors'
import type { Major, CreateMajorData, UpdateMajorData } from '@/types/major'

interface Props {
  major?: Major | null
}

interface Emits {
  (e: 'close'): void
  (e: 'success', message: string): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// Computed
const isEdit = computed(() => !!props.major)

// Form state
const form = ref<CreateMajorData | UpdateMajorData>({
  name: '',
  icon: '',
  description: '',
  is_active: true,
  display_order: 0
})

const errors = ref<Record<string, string>>({})
const errorMessage = ref<string | null>(null)
const submitting = ref(false)

// Watch major prop changes
watch(() => props.major, (newMajor) => {
  if (newMajor) {
    form.value = {
      name: newMajor.name,
      icon: newMajor.icon,
      description: newMajor.description || '',
      is_active: newMajor.is_active,
      display_order: newMajor.display_order
    }
  } else {
    resetForm()
  }
}, { immediate: true })

// Reset form
const resetForm = () => {
  form.value = {
    name: '',
    icon: '',
    description: '',
    is_active: true,
    display_order: 0
  }
  errors.value = {}
  errorMessage.value = null
}

// Validate form
const validateForm = (): boolean => {
  errors.value = {}

  if (!form.value.name?.trim()) {
    errors.value.name = 'Vui lòng nhập tên ngành học'
  }

  if (!form.value.icon?.trim()) {
    errors.value.icon = 'Vui lòng chọn icon'
  }

  if (form.value.description && form.value.description.length > 1000) {
    errors.value.description = 'Mô tả không được vượt quá 1000 ký tự'
  }

  if (form.value.display_order !== undefined && form.value.display_order < 0) {
    errors.value.display_order = 'Thứ tự hiển thị phải >= 0'
  }

  return Object.keys(errors.value).length === 0
}

// Submit handler
const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  try {
    submitting.value = true
    errorMessage.value = null

    if (isEdit.value && props.major) {
      await updateMajor(props.major.id, form.value as UpdateMajorData)
      emit('success', 'Cập nhật ngành học thành công')
    } else {
      await createMajor(form.value as CreateMajorData)
      emit('success', 'Thêm ngành học mới thành công')
    }
  } catch (err: any) {
    console.error('Failed to save major:', err)
    
    if (err.response?.data?.errors) {
      // Laravel validation errors
      const validationErrors = err.response.data.errors
      Object.keys(validationErrors).forEach(key => {
        errors.value[key] = validationErrors[key][0]
      })
    } else {
      errorMessage.value = err.response?.data?.message || 'Có lỗi xảy ra. Vui lòng thử lại.'
    }
  } finally {
    submitting.value = false
  }
}
</script>
