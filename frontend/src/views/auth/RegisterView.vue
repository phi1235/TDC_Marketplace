<template>
  <div :class="darkMode ? 'dark bg-gray-900 text-gray-100' : 'bg-gray-50 text-gray-900'" class="min-h-screen transition-colors duration-300">
    <!-- Header -->
    <header class="bg-blue-600 dark:bg-gray-800 text-white py-4 shadow">
      <div class="container mx-auto px-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">TDC Marketplace</h1>

        <!-- Dark mode toggle -->
        <button
          @click="toggleDarkMode"
          class="px-3 py-1 rounded-md bg-white/10 hover:bg-white/20 transition"
        >
          {{ darkMode ? "☀️" : "🌙" }}
        </button>
      </div>
    </header>

    <!-- Main -->
    <main class="container mx-auto px-4 py-8">
      <div class="max-w-md mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 transition-colors duration-300">
          <h1 class="text-2xl font-bold mb-6 text-center">Đăng ký</h1>

          <!-- Nếu đang loading thì hiển thị skeleton -->
          <div v-if="loading" class="space-y-4 animate-pulse">
            <div class="h-6 bg-gray-300 dark:bg-gray-700 rounded w-1/2 mx-auto"></div>
            <div class="space-y-3">
              <div class="h-10 bg-gray-300 dark:bg-gray-700 rounded"></div>
              <div class="h-10 bg-gray-300 dark:bg-gray-700 rounded"></div>
              <div class="h-10 bg-gray-300 dark:bg-gray-700 rounded"></div>
              <div class="h-10 bg-gray-300 dark:bg-gray-700 rounded"></div>
            </div>
            <div class="h-10 bg-blue-400 dark:bg-blue-700 rounded"></div>
          </div>

          <!-- Nếu không loading thì hiển thị form -->
          <form v-else @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Họ và tên -->
            <div>
              <label class="block text-sm font-medium mb-1">Họ và tên</label>
              <input
                v-model="name"
                type="text"
                required
                placeholder="Nhập họ và tên"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium mb-1">Email</label>
              <input
                v-model="email"
                type="email"
                required
                placeholder="Nhập email"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Mật khẩu -->
            <div>
              <label class="block text-sm font-medium mb-1">Mật khẩu</label>
              <input
                v-model="password"
                type="password"
                required
                minlength="6"
                placeholder="Nhập mật khẩu"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Xác nhận mật khẩu -->
            <div>
              <label class="block text-sm font-medium mb-1">Xác nhận mật khẩu</label>
              <input
                v-model="confirmPassword"
                type="password"
                required
                placeholder="Nhập lại mật khẩu"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Thông báo lỗi -->
            <div v-if="errorMessage" class="text-red-500 text-sm text-center">
              {{ errorMessage }}
            </div>

            <!-- Nút đăng ký -->
            <button
              type="submit"
              class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 rounded-lg transition-colors"
            >
              Đăng ký
            </button>
          </form>

          <!-- Link đăng nhập -->
          <div class="mt-6 text-center">
            <p class="text-gray-600 dark:text-gray-400">
              Đã có tài khoản?
              <router-link to="/login" class="text-blue-600 dark:text-blue-400 hover:underline">
                Đăng nhập
              </router-link>
            </p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const name = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const errorMessage = ref("");
const loading = ref(false);
const darkMode = ref(false);

const toggleDarkMode = () => {
  darkMode.value = !darkMode.value;
  document.documentElement.classList.toggle("dark", darkMode.value);
};

const handleSubmit = async () => {
  errorMessage.value = "";

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    errorMessage.value = "Email không hợp lệ!";
    return;
  }

  if (password.value.length < 6) {
    errorMessage.value = "Mật khẩu phải có ít nhất 6 ký tự!";
    return;
  }

  if (password.value !== confirmPassword.value) {
    errorMessage.value = "Mật khẩu xác nhận không khớp!";
    return;
  }

  try {
    loading.value = true;
    await new Promise((resolve) => setTimeout(resolve, 1200)); // hiệu ứng loading

    const response = await axios.post("http://localhost:8000/api/register", {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
    });

    alert("Đăng ký thành công! Vui lòng đăng nhập.");
    router.push("/login");
  } catch (error: any) {
    if (error.response) {
      if (error.response.status === 422 && error.response.data.errors?.email) {
        errorMessage.value = "Email này đã tồn tại.";
      } else {
        errorMessage.value = "Đăng ký thất bại. Vui lòng thử lại.";
      }
    } else {
      errorMessage.value = "Không thể kết nối máy chủ.";
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
/* Dark mode transition mượt */
:root {
  color-scheme: light dark;
}
</style>
