<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Hồ sơ cá nhân</h1>
      
      <div class="bg-white rounded-lg shadow-md p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Profile Info -->
          <div class="lg:col-span-1">
            <div class="text-center">
              <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
                <span class="text-gray-500 text-2xl">👤</span>
              </div>
              <h2 class="text-2xl font-bold text-gray-900">{{ user.name }}</h2>
              <p class="text-gray-600">{{ user.email }}</p>
              <div class="mt-4">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                  Đã xác thực
                </span>
              </div>
            </div>
          </div>

          <!-- Profile Form -->
          <div class="lg:col-span-2">
            <form @submit.prevent="updateProfile" class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Họ và tên</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                <textarea
                  v-model="form.bio"
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Giới thiệu về bản thân..."
                ></textarea>
              </div>

              <div class="flex gap-4">
                <button
                  type="submit"
                  class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors"
                >
                  Cập nhật hồ sơ
                </button>
                <button
                  type="button"
                  class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors"
                >
                  Hủy
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'

const user = ref({
  name: 'Nguyễn Văn A',
  email: 'nguyenvana@tdc.edu.vn',
  phone: '0123456789',
  bio: 'Sinh viên năm 3 ngành Công nghệ thông tin'
})

const form = reactive({
  name: '',
  email: '',
  phone: '',
  bio: ''
})

const updateProfile = () => {
  console.log('Updating profile:', form)
}

onMounted(() => {
  // Initialize form with user data
  form.name = user.value.name
  form.email = user.value.email
  form.phone = user.value.phone
  form.bio = user.value.bio
})
</script>
