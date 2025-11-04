<template>
  <div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">Đăng tin rao vặt</h1>
        <p class="text-gray-600 mt-1">Chia sẻ đồ dùng học tập với cộng đồng TDC</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="p-6 space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Title -->
          <div class="md:col-span-2">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
              Tiêu đề tin rao *
            </label>
            <input id="title" v-model="form.title" type="text" required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Ví dụ: Laptop Dell Inspiron cũ, còn bảo hành" :class="{ 'border-red-500': errors.title }" />
            <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title[0] }}</p>
          </div>

          <!-- Category -->
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
              Danh mục *
            </label>
            <select id="category" v-model="form.category_id" required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': errors.category_id }">
              <option value="">Chọn danh mục</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <p v-if="errors.category_id" class="mt-1 text-sm text-red-600">{{ errors.category_id[0] }}</p>
          </div>

          <!-- Condition -->
          <div>
            <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">
              Tình trạng *
            </label>
            <select id="condition" v-model="form.condition" required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': errors.condition }">
              <option value="">Chọn tình trạng</option>
              <option value="new">Mới (A)</option>
              <option value="like_new">Như mới (B)</option>
              <option value="good">Tốt (C)</option>
              <option value="fair">Khá (D)</option>
            </select>
            <p v-if="errors.condition" class="mt-1 text-sm text-red-600">{{ errors.condition[0] }}</p>
          </div>

          <!-- Price Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Hình thức giá *
            </label>
            <div class="flex items-center space-x-6">
              <label class="inline-flex items-center">
                <input type="radio" class="mr-2" value="paid" v-model="pricingType" />
                Tính phí
              </label>
              <label class="inline-flex items-center">
                <input type="radio" class="mr-2" value="free" v-model="pricingType" />
                Miễn phí
              </label>
            </div>
          </div>

          <!-- Price -->
          <div>
            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
              Giá bán (VNĐ) <span v-if="pricingType === 'paid'">*</span>
            </label>
            <input id="price" v-model.number="form.price" type="number" :required="pricingType === 'paid'"
              :disabled="pricingType === 'free'" min="0"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100"
              placeholder="1000000" :class="{ 'border-red-500': errors.price }" />
            <p v-if="pricingType === 'free'" class="mt-1 text-sm text-gray-500">Tin miễn phí sẽ có giá = 0 VNĐ.</p>
            <p v-if="errors.price" class="mt-1 text-sm text-red-600">{{ errors.price[0] }}</p>
          </div>

          <!-- Location -->
          <div>
            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
              Địa điểm giao dịch *
            </label>
            <input id="location" v-model="form.location" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Ví dụ: TDC Campus, Khu A" :class="{ 'border-red-500': errors.location }" />
            <p v-if="errors.location" class="mt-1 text-sm text-red-600">{{ errors.location[0] }}</p>
          </div>
          <!-- Pickup Points (đa chọn) -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Điểm giao dịch (có thể chọn nhiều)
            </label>

            <PickupPointSelector mode="multi" :defaultSelected="pickupIds" @done="onPickupSelected" />

            <p class="mt-2 text-xs text-gray-500">
              Mẹo: Gợi ý chọn các điểm công cộng trong khuôn viên TDC (cổng chính, thư viện, căn tin…)
            </p>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Mô tả chi tiết *
          </label>
          <textarea id="description" v-model="form.description" required rows="4"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Mô tả chi tiết về sản phẩm, tình trạng, lý do bán..."
            :class="{ 'border-red-500': errors.description }"></textarea>
          <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description[0] }}</p>
        </div>

        <!-- Image Upload -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Hình ảnh sản phẩm
          </label>
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
            <input ref="fileInput" type="file" multiple accept="image/*" @change="handleFileSelect" class="hidden" />

            <div v-if="selectedFiles.length === 0" class="text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path
                  d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div class="mt-4">
                <button type="button" @click="$refs.fileInput.click()"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                  Chọn ảnh
                </button>
                <p class="mt-2 text-sm text-gray-500">PNG, JPG, JPEG tối đa 5MB mỗi ảnh</p>
              </div>
            </div>

            <!-- Image Preview -->
            <div v-if="selectedFiles.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div v-for="(file, index) in selectedFiles" :key="index" class="relative group">
                <img :src="file.preview" :alt="`Preview ${index + 1}`" class="w-full h-32 object-cover rounded-lg" />
                <button type="button" @click="removeFile(index)"
                  class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                  ×
                </button>
              </div>
            </div>
          </div>
          <p v-if="errors.images" class="mt-1 text-sm text-red-600">{{ errors.images[0] }}</p>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
          <button type="button" @click="$router.go(-1)"
            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Hủy
          </button>
          <button type="submit" :disabled="isSubmitting"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
              xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
            {{ isSubmitting ? 'Đang đăng...' : 'Đăng tin' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { listingsService } from '@/services/listings'
import { categoriesService } from '@/services/categories'
import { showToast } from '@/utils/toast'
import PickupPointSelector from '@/components/pickup/PickupPointSelector.vue'
import { pickupApi } from '@/services/pickup'
const router = useRouter()
const authStore = useAuthStore()

// Form data
const form = reactive({
  title: '',
  category_id: '',
  condition: '',
  price: null,
  location: '',
  description: '',
})

// pricing type state
const pricingType = ref<'free' | 'paid'>('paid')

// Keep price in sync with pricing type
watch(pricingType, (type) => {
  if (type === 'free') {
    form.price = 0
  } else if (form.price === null || form.price === 0) {
    form.price = null
  }
})

// State
const categories = ref([])
const selectedFiles = ref([])
const errors = ref({})
const isSubmitting = ref(false)

// Load categories
const loadCategories = async () => {
  try {
    categories.value = await categoriesService.getCategories()
  } catch (error) {
    console.error('Error loading categories:', error)
    // Fallback to hardcoded categories
    categories.value = [
      { id: 1, name: 'Sách giáo khoa' },
      { id: 2, name: 'Điện tử' },
      { id: 3, name: 'Văn phòng phẩm' },
      { id: 4, name: 'Quần áo' },
    ]
  }
}

// Handle file selection
const handleFileSelect = (event) => {
  const files = Array.from(event.target.files)

  // Validate files
  const validFiles = files.filter(file => {
    if (file.size > 5 * 1024 * 1024) {
      showToast('error', `File ${file.name} quá lớn (tối đa 5MB)`)
      return false
    }
    if (!file.type.startsWith('image/')) {
      showToast('error', `File ${file.name} không phải là ảnh`)
      return false
    }
    return true
  })

  // Create preview URLs
  validFiles.forEach(file => {
    const reader = new FileReader()
    reader.onload = (e) => {
      selectedFiles.value.push({
        file,
        preview: e.target.result
      })
    }
    reader.readAsDataURL(file)
  })
}

// Remove file
const removeFile = (index) => {
  selectedFiles.value.splice(index, 1)
}

// Submit form
const submitForm = async () => {
  console.log('🎯 [submitForm] Form submitted!')
  console.log('📋 Form data:', form)
  console.log('🖼️ Selected files:', selectedFiles.value)

  if (!authStore.isAuthenticated) {
    showToast('error', 'Vui lòng đăng nhập để đăng tin')
    router.push('/login')
    return
  }

  isSubmitting.value = true
  errors.value = {}

  try {
    const payload = { ...form } as any

    // Enforce price based on pricing type
    if (pricingType.value === 'free') {
      payload.price = 0
    }

    const formData = {
      ...payload,
      images: selectedFiles.value.map(item => item.file)
    }

    console.log('📦 Payload prepared:', formData)
    console.log('📤 Calling listingsService.createListing...')

    const response = await listingsService.createListing(formData)
    // Lấy id listing mới. Tuỳ backend của bạn trả về:
    // - response.id
    // - response.data.id
    // - response.listing.id
    const newListingId =
      (response?.id) ??
      (response?.data?.id) ??
      (response?.listing?.id)

    // Nếu lấy được id thì sync pickup points
    if (newListingId && pickupIds.value.length) {
      await pickupApi.listingSync(newListingId, pickupIds.value)
    }
    //console.log('✅ Listing created successfully:', response)
    showToast('success', 'Đăng tin thành công! Tin rao đang chờ duyệt.')
    router.push('/my-listings')

  } catch (error: any) {
    console.error('❌ Error creating listing:', error)

    // 🧩 Nếu backend trả về lỗi moderation (400 hoặc 403)
    if (error.response?.status === 400 || error.response?.status === 403) {
      const message = error.response?.data?.message || 'Nội dung không hợp lệ.'
      showToast('error', message)
      return
    }

    // 🧾 Nếu validation lỗi (422)
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors
      showToast('error', 'Vui lòng kiểm tra lại thông tin')
      return
    }

    // ⚙️ Các lỗi khác (mạng, server,...)
    showToast('error', 'Có lỗi xảy ra khi đăng tin. Vui lòng thử lại.')
  } finally {
    isSubmitting.value = false
  }
}
onMounted(() => {
  loadCategories()
})
// state lưu id các điểm seller chọn cho listing
const pickupIds = ref<number[]>([])
function onPickupSelected(val: number[] | number | null) {
  pickupIds.value = Array.isArray(val) ? val : (val ? [val] : [])
}
</script>
