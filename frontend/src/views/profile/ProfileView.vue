<template> 
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Hồ sơ cá nhân</h1>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          
          <!-- Profile Info -->
          <div class="lg:col-span-1 text-center">
            <div class="relative w-32 h-32 mx-auto mb-4">
              <img
                v-if="previewImage || user.avatar"
                :src="previewImage || user.avatar"
                alt="Ảnh đại diện"
                class="w-32 h-32 rounded-full object-cover border-4 border-blue-200 shadow"
              />
              <div
                v-else
                class="w-32 h-32 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-500 text-4xl"
              >
                👤
              </div>

              <!-- Nút thay ảnh -->
              <label
                for="avatar"
                class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer shadow hover:bg-blue-500 transition"
                title="Đổi ảnh đại diện"
              >
                📷
              </label>
              <input
                type="file"
                id="avatar"
                accept="image/*"
                class="hidden"
                @change="handleImageUpload"
              />
            </div>

            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ user.name }}</h2>
            <p class="text-gray-600 dark:text-gray-300">{{ user.email }}</p>

            <!-- Major Display -->
            <div v-if="user.major" class="mt-3">
              <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm">
                <span class="mr-1">{{ user.major.icon }}</span>
                {{ user.major.name }}
              </span>
            </div>

            <div class="mt-4">
              <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                Đã xác thực
              </span>
            </div>
          </div>

          <!-- Profile Form -->
          <div class="lg:col-span-2">
            <form @submit.prevent="updateProfile" class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Họ và tên</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Số điện thoại</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Major Selection -->
              <div>
                <MajorSelect
                  v-model="form.major_id"
                  label="Ngành học"
                  placeholder="Chọn ngành học của bạn"
                  help-text="Chọn ngành học để nhận gợi ý tin rao phù hợp"
                  :allow-empty="true"
                />
              </div>

              <div class="flex gap-4">
                <button
                  type="submit"
                  class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors"
                  :disabled="loading"
                >
                  <span v-if="loading">Đang lưu...</span>
                  <span v-else>Cập nhật hồ sơ</span>
                </button>

                <button
                  type="button"
                  @click="resetForm"
                  class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors"
                >
                  Hủy
                </button>
              </div>

              <!-- Thông báo -->
              <p v-if="successMessage" class="text-green-600 mt-2">{{ successMessage }}</p>
              <p v-if="errorMessage" class="text-red-600 mt-2">{{ errorMessage }}</p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'
import MajorSelect from '@/components/MajorSelect.vue'
import type { Major } from '@/types/major'

const user = ref<{
  id: number
  name: string
  email: string
  phone: string
  avatar: string
  major_id: number | null
  major?: Major
}>({
  id: 1,
  name: '',
  email: '',
  phone: '',
  avatar: '',
  major_id: null
})

const form = reactive({
  name: '',
  email: '',
  phone: '',
  major_id: null as number | null
})

const previewImage = ref<string | null>(null)
const fileImage = ref<File | null>(null)
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

// Lấy thông tin user từ API Laravel
const fetchUser = async () => {
  try {
    const token = localStorage.getItem('auth_token') // Fix: Sử dụng đúng key
    
    // Kiểm tra nếu chưa đăng nhập
    if (!token) {
      errorMessage.value = 'Bạn cần đăng nhập để xem trang này.'
      // Redirect về trang login sau 2 giây
      setTimeout(() => {
        window.location.href = '/login'
      }, 2000)
      return
    }

    const response = await api.get('/user', {
      headers: { Authorization: `Bearer ${token}` }
    })
    user.value = response.data
    form.name = user.value.name
    form.email = user.value.email
    form.phone = user.value.phone
    form.major_id = user.value.major_id ?? null
  } catch (error: any) {
    console.error('Fetch user error:', error)
    
    // Xử lý lỗi 401 Unauthorized
    if (error.response?.status === 401) {
      errorMessage.value = 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.'
      localStorage.removeItem('auth_token') // Fix: Xóa đúng key
      localStorage.removeItem('user')
      setTimeout(() => {
        window.location.href = '/login'
      }, 2000)
    } else {
      errorMessage.value = 'Không thể tải thông tin người dùng. Vui lòng thử lại.'
    }
  }
}

// Xử lý upload ảnh
const handleImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  if (!file.type.startsWith('image/')) {
    alert('Vui lòng chọn tệp hình ảnh hợp lệ!')
    return
  }

  fileImage.value = file
  const reader = new FileReader()
  reader.onload = () => {
    previewImage.value = reader.result as string
  }
  reader.readAsDataURL(file)
}

// Gửi form cập nhật lên API
const updateProfile = async () => {
  loading.value = true
  successMessage.value = ''
  errorMessage.value = ''

  try {
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('email', form.email)
    formData.append('phone', form.phone)
    
    // Add major_id (can be null to unset)
    if (form.major_id !== null && form.major_id !== undefined) {
      formData.append('major_id', form.major_id.toString())
    } else {
      formData.append('major_id', '')
    }
    
    if (fileImage.value) {
      formData.append('avatar', fileImage.value)
    }

    const response = await api.post(
      '/user',
      formData,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`, // Fix: Sử dụng đúng key
          'Content-Type': 'multipart/form-data'
        }
      }
    )

    successMessage.value = 'Cập nhật hồ sơ thành công!'
    user.value = response.data.user
    previewImage.value = null
    fileImage.value = null
  } catch (error: any) {
    if (error.response && error.response.status === 422) {
      errorMessage.value = 'Email này đã tồn tại hoặc dữ liệu không hợp lệ.'
    } else {
      errorMessage.value = 'Cập nhật thất bại, vui lòng thử lại.'
    }
  } finally {
    loading.value = false
  }
}

// Khôi phục form
const resetForm = () => {
  form.name = user.value.name
  form.email = user.value.email
  form.phone = user.value.phone
  form.major_id = user.value.major_id ?? null
  previewImage.value = null
  errorMessage.value = ''
  successMessage.value = ''
}

onMounted(() => {
  fetchUser()
})
</script>

<style scoped>
.dark input,
.dark textarea {
  background-color: #374151;
  color: white;
}
</style>
