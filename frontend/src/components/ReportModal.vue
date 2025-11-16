<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="close"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 transition-opacity"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full"
            @click.stop
          >
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Báo cáo vi phạm</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Vui lòng mô tả chi tiết về vi phạm</p>
              </div>
              <button
                @click="close"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                aria-label="Close"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Content -->
            <form @submit.prevent="submitReport" class="px-6 py-4">
              <!-- What are you reporting -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Đang báo cáo về:
                </label>
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                  <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                      <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ reportableTitle }}</p>
                      <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ reportableTypeLabel }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Reason -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Lý do báo cáo <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.reason"
                  required
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.reason }"
                >
                  <option value="">Chọn lý do...</option>
                  <option v-for="reason in reasons" :key="reason.key" :value="reason.key">
                    {{ reason.label }}
                  </option>
                </select>
                <p v-if="errors.reason" class="mt-1 text-sm text-red-600">{{ errors.reason }}</p>
              </div>

              <!-- Description -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Mô tả chi tiết <span class="text-red-500">*</span>
                </label>
                <textarea
                  v-model="form.description"
                  required
                  rows="4"
                  minlength="10"
                  maxlength="1000"
                  placeholder="Vui lòng mô tả chi tiết về vấn đề bạn gặp phải (tối thiểu 10 ký tự)..."
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                  :class="{ 'border-red-500': errors.description }"
                ></textarea>
                <div class="mt-1 flex justify-between items-center">
                  <p v-if="errors.description" class="text-sm text-red-600">{{ errors.description }}</p>
                  <p class="text-sm text-gray-500 ml-auto">{{ form.description.length }}/1000</p>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button
                  type="button"
                  @click="close"
                  :disabled="isSubmitting"
                  class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Hủy
                </button>
                <button
                  type="submit"
                  :disabled="isSubmitting || !isFormValid"
                  class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                >
                  <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ isSubmitting ? 'Đang gửi...' : 'Gửi báo cáo' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { reportService } from '@/services/report'
import { showToast } from '@/utils/toast'

interface Props {
  isOpen: boolean
  reportableType: string
  reportableId: number
  reportableTitle?: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
  submitted: [report: any]
}>()

const form = ref({
  reason: '',
  description: ''
})

const errors = ref({
  reason: '',
  description: ''
})

// Default reasons in case API fails to load
const defaultReasons = [
  { key: 'fraud', label: 'Lừa đảo' },
  { key: 'fake_product', label: 'Hàng giả' },
  { key: 'spam', label: 'Spam' },
  { key: 'inappropriate_content', label: 'Nội dung không phù hợp' },
  { key: 'price_manipulation', label: 'Thao túng giá' },
  { key: 'fake_reviews', label: 'Đánh giá giả' },
  { key: 'harassment', label: 'Quấy rối' },
  { key: 'copyright_violation', label: 'Vi phạm bản quyền' },
  { key: 'other', label: 'Khác' }
]

const reasons = ref<Array<{ key: string; label: string }>>(defaultReasons)
const isSubmitting = ref(false)
const reasonsLoaded = ref(false)

const reportableTypeLabel = computed(() => {
  const typeMap: Record<string, string> = {
    'App\\Models\\Listing': 'Tin rao',
    'App\\Models\\User': 'Người dùng',
    'App\\Models\\Review': 'Đánh giá'
  }
  return typeMap[props.reportableType] || 'Đối tượng'
})

const isFormValid = computed(() => {
  return form.value.reason && form.value.description.length >= 10
})

// Load reasons from API
const loadReasons = async () => {
  if (reasonsLoaded.value) return
  try {
    const fetchedReasons = await reportService.getReportReasons()
    if (fetchedReasons && fetchedReasons.length > 0) {
      reasons.value = fetchedReasons
      reasonsLoaded.value = true
    }
  } catch (error) {
    console.error('Failed to load report reasons:', error)
    // Keep default reasons
  }
}

// Load reasons when modal opens
watch(() => props.isOpen, async (newVal) => {
  if (newVal && !reasonsLoaded.value) {
    await loadReasons()
  }
  
  if (!newVal) {
    form.value = { reason: '', description: '' }
    errors.value = { reason: '', description: '' }
  }
})

const close = () => {
  emit('close')
  form.value = { reason: '', description: '' }
  errors.value = { reason: '', description: '' }
}

const validateForm = () => {
  errors.value = { reason: '', description: '' }
  let isValid = true

  if (!form.value.reason) {
    errors.value.reason = 'Vui lòng chọn lý do báo cáo'
    isValid = false
  }

  if (form.value.description.length < 10) {
    errors.value.description = 'Mô tả phải có ít nhất 10 ký tự'
    isValid = false
  } else if (form.value.description.length > 1000) {
    errors.value.description = 'Mô tả không được vượt quá 1000 ký tự'
    isValid = false
  }

  return isValid
}

const submitReport = async () => {
  if (!validateForm()) {
    return
  }

  isSubmitting.value = true

  try {
    const data = {
      reportable_type: props.reportableType,
      reportable_id: props.reportableId,
      reason: form.value.reason,
      description: form.value.description
    }

    const response = await reportService.createReport(data)
    
    showToast('success', 'Báo cáo đã được gửi thành công')
    emit('submitted', response.report)
    close()
  } catch (error: any) {
    console.error('Failed to submit report:', error)
    const errorMessage = error.response?.data?.message || 'Có lỗi xảy ra khi gửi báo cáo'
    showToast('error', errorMessage)
    
    // Handle validation errors
    if (error.response?.data?.errors) {
      const serverErrors = error.response.data.errors
      if (serverErrors.reason) errors.value.reason = serverErrors.reason[0]
      if (serverErrors.description) errors.value.description = serverErrors.description[0]
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .bg-white,
.modal-leave-active .bg-white,
.modal-enter-active .bg-gray-800,
.modal-leave-active .bg-gray-800 {
  transition: transform 0.3s ease;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white,
.modal-enter-from .bg-gray-800,
.modal-leave-to .bg-gray-800 {
  transform: scale(0.95);
}
</style>

