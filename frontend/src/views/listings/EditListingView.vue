<template>
  <div class="max-w-4xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Chỉnh sửa tin rao</h1>
      <p class="text-gray-600 mt-1">Cập nhật thông tin tin rao của bạn</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Form -->
    <form v-else @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Category -->
      <div>
        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
          Danh mục <span class="text-red-500">*</span>
        </label>
        <select
          id="category"
          v-model="form.category_id"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
          <option value="">Chọn danh mục</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <p v-if="errors.category_id" class="mt-1 text-sm text-red-600">{{ errors.category_id }}</p>
      </div>

      <!-- Title -->
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
          Tiêu đề <span class="text-red-500">*</span>
        </label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          required
          maxlength="200"
          placeholder="VD: iPhone 13 Pro Max 256GB còn mới 99%"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
        <p class="mt-1 text-sm text-gray-500">{{ form.title.length }}/200 ký tự</p>
        <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
      </div>

      <!-- Description -->
      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
          Mô tả chi tiết <span class="text-red-500">*</span>
        </label>
        <textarea
          id="description"
          v-model="form.description"
          required
          rows="6"
          maxlength="2000"
          placeholder="Mô tả chi tiết về sản phẩm: tình trạng, lý do bán, phụ kiện kèm theo..."
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        ></textarea>
        <p class="mt-1 text-sm text-gray-500">{{ form.description.length }}/2000 ký tự</p>
        <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
      </div>

      <!-- Condition -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Tình trạng <span class="text-red-500">*</span>
        </label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <label
            v-for="condition in conditions"
            :key="condition.value"
            :class="[
              'flex items-center justify-center px-4 py-3 border-2 rounded-lg cursor-pointer transition-all',
              form.condition === condition.value
                ? 'border-blue-500 bg-blue-50 text-blue-700'
                : 'border-gray-300 hover:border-gray-400'
            ]"
          >
            <input
              type="radio"
              v-model="form.condition"
              :value="condition.value"
              class="sr-only"
            />
            <span class="text-sm font-medium">{{ condition.label }}</span>
          </label>
        </div>
        <p v-if="errors.condition" class="mt-1 text-sm text-red-600">{{ errors.condition }}</p>
      </div>

      <!-- Price -->
      <div>
        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
          Giá bán <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <input
            id="price"
            v-model.number="form.price"
            type="number"
            required
            min="0"
            step="1000"
            placeholder="0"
            class="w-full px-4 py-2 pr-16 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
          <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">VNĐ</span>
        </div>
        <p class="mt-1 text-sm text-gray-500">
          {{ formatPrice(form.price) }} VNĐ
        </p>
        <p v-if="errors.price" class="mt-1 text-sm text-red-600">{{ errors.price }}</p>
      </div>

      <!-- Location -->
      <div>
        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
          Địa điểm giao dịch
        </label>
        <input
          id="location"
          v-model="form.location"
          type="text"
          maxlength="255"
          placeholder="VD: Ký túc xá khu A, ĐHQG TP.HCM"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
        <p v-if="errors.location" class="mt-1 text-sm text-red-600">{{ errors.location }}</p>
      </div>

      <!-- Images -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Hình ảnh
        </label>
        
        <!-- Current Images -->
        <div v-if="currentImages.length > 0" class="mb-4">
          <p class="text-sm text-gray-600 mb-2">Ảnh hiện tại:</p>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div
              v-for="(image, index) in currentImages"
              :key="image.id"
              class="relative aspect-square bg-gray-100 rounded-lg overflow-hidden group"
            >
              <img
                :src="imageUrl(image.image_path)"
                :alt="`Image ${index + 1}`"
                class="w-full h-full object-cover"
              />
              <button
                type="button"
                @click="removeCurrentImage(index)"
                class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- New Images Upload -->
        <div
          @click="triggerFileInput"
          @dragover.prevent="isDragging = true"
          @dragleave.prevent="isDragging = false"
          @drop.prevent="handleDrop"
          :class="[
            'border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors',
            isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'
          ]"
        >
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          <p class="mt-2 text-sm text-gray-600">
            Nhấp để chọn hoặc kéo thả ảnh vào đây
          </p>
          <p class="mt-1 text-xs text-gray-500">
            PNG, JPG, WEBP tối đa 5MB (tối đa 5 ảnh)
          </p>
        </div>
        <input
          ref="fileInput"
          type="file"
          accept="image/*"
          multiple
          @change="handleFileSelect"
          class="hidden"
        />

        <!-- New Image Previews -->
        <div v-if="newImagePreviews.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
          <div
            v-for="(preview, index) in newImagePreviews"
            :key="index"
            class="relative aspect-square bg-gray-100 rounded-lg overflow-hidden group"
          >
            <img
              :src="preview"
              :alt="`Preview ${index + 1}`"
              class="w-full h-full object-cover"
            />
            <button
              type="button"
              @click="removeNewImage(index)"
              class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        <p v-if="errors.images" class="mt-2 text-sm text-red-600">{{ errors.images }}</p>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-between pt-6 border-t">
        <button
          type="button"
          @click="router.back()"
          class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
        >
          Hủy
        </button>
        <button
          type="submit"
          :disabled="submitting"
          class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="submitting">Đang cập nhật...</span>
          <span v-else>Cập nhật tin rao</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { listingsService } from '@/services/listings'
import { categoriesService } from '@/services/categories'
import { showToast } from '@/utils/toast'
import { imageUrl } from '@/utils/image'

const router = useRouter()
const route = useRoute()

// State
const loading = ref(true)
const submitting = ref(false)
const categories = ref([])
const currentImages = ref([])
const newImagePreviews = ref([])
const newImageFiles = ref([])
const isDragging = ref(false)
const fileInput = ref(null)

const form = ref({
  category_id: '',
  title: '',
  description: '',
  condition: 'good',
  price: 0,
  location: ''
})

const errors = ref({})

const conditions = [
  { value: 'new', label: 'Mới' },
  { value: 'like_new', label: 'Như mới' },
  { value: 'good', label: 'Tốt' },
  { value: 'fair', label: 'Khá' }
]

// Load listing data
const loadListing = async () => {
  try {
    const listingId = parseInt(route.params.id as string)
    const listing = await listingsService.getListing(listingId)
    
    // Check if user owns this listing
    // This should be handled by backend, but we can add client-side check too
    
    form.value = {
      category_id: listing.category_id,
      title: listing.title,
      description: listing.description,
      condition: listing.condition,
      price: listing.price,
      location: listing.location || ''
    }
    
    currentImages.value = listing.images || []
  } catch (error) {
    console.error('Error loading listing:', error)
    showToast('error', 'Không thể tải thông tin tin rao')
    router.push('/my-listings')
  }
}

// Load categories
const loadCategories = async () => {
  try {
    categories.value = await categoriesService.getCategories()
  } catch (error) {
    console.error('Error loading categories:', error)
    showToast('error', 'Không thể tải danh mục')
  }
}

// File handling
const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    addFiles(Array.from(target.files))
  }
}

const handleDrop = (event: DragEvent) => {
  isDragging.value = false
  if (event.dataTransfer?.files) {
    addFiles(Array.from(event.dataTransfer.files))
  }
}

const addFiles = (files: File[]) => {
  const totalImages = currentImages.value.length + newImageFiles.value.length + files.length
  
  if (totalImages > 5) {
    showToast('error', 'Bạn chỉ được tải lên tối đa 5 hình ảnh')
    return
  }

  files.forEach(file => {
    if (!file.type.startsWith('image/')) {
      showToast('error', `${file.name} không phải là file ảnh`)
      return
    }

    if (file.size > 5 * 1024 * 1024) {
      showToast('error', `${file.name} vượt quá 5MB`)
      return
    }

    newImageFiles.value.push(file)
    
    const reader = new FileReader()
    reader.onload = (e) => {
      newImagePreviews.value.push(e.target?.result as string)
    }
    reader.readAsDataURL(file)
  })
}

const removeCurrentImage = (index: number) => {
  currentImages.value.splice(index, 1)
}

const removeNewImage = (index: number) => {
  newImageFiles.value.splice(index, 1)
  newImagePreviews.value.splice(index, 1)
}

// Format price
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('vi-VN').format(price)
}

// Submit form
const handleSubmit = async () => {
  errors.value = {}
  submitting.value = true

  try {
    const listingId = parseInt(route.params.id as string)
    
    // Prepare data
    const data: any = {
      category_id: form.value.category_id,
      title: form.value.title,
      description: form.value.description,
      condition: form.value.condition,
      price: form.value.price,
      location: form.value.location
    }

    // If user uploaded new images or removed all current images, include new images
    if (newImageFiles.value.length > 0 || currentImages.value.length === 0) {
      data.images = newImageFiles.value
    }

    const response = await listingsService.updateListing(listingId, data)
    
    showToast('success', response.message || 'Cập nhật tin rao thành công!')
    router.push('/my-listings')
  } catch (error: any) {
    console.error('Error updating listing:', error)
    
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
    
    showToast('error', error.response?.data?.message || 'Có lỗi xảy ra khi cập nhật tin rao')
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  loading.value = true
  await Promise.all([loadCategories(), loadListing()])
  loading.value = false
})
</script>

