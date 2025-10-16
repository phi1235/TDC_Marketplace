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

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mô tả</label>
                <textarea
                  v-model="form.bio"
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Giới thiệu về bản thân..."
                ></textarea>
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
import axios from 'axios'

const user = ref({
  id: 1, // ID người dùng (có thể lấy từ store/auth)
  name: '',
  email: '',
  phone: '',
  bio: '',
  avatar: ''
})

const form = reactive({
  name: '',
  email: '',
  phone: '',
  bio: ''
})

const previewImage = ref<string | null>(null)
const fileImage = ref<File | null>(null)
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

// Lấy thông tin user từ API Laravel
const fetchUser = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/profile', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    user.value = response.data
    form.name = user.value.name
    form.email = user.value.email
    form.phone = user.value.phone
    form.bio = user.value.bio
  } catch (error) {
    console.error(error)
    errorMessage.value = 'Không thể tải thông tin người dùng.'
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
    formData.append('bio', form.bio)
    if (fileImage.value) {
      formData.append('avatar', fileImage.value)
    }

    const response = await axios.post(
      'http://localhost:8000/api/profile/update',
      formData,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
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
  form.bio = user.value.bio
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
