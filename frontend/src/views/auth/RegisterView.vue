<template>
  <div :class="darkMode ? 'dark bg-gray-900 text-gray-100' : 'bg-gray-50 text-gray-900'" class="min-h-screen transition-colors duration-300">

    

    <!-- Main -->
    <main class="container mx-auto px-4 py-8">
      <div class="max-w-md mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 transition-colors duration-300">
          <h1 class="text-2xl font-bold mb-6 text-center">Đăng ký</h1>

          <!-- Form luôn hiển thị; chỉ disable điều khiển khi loading để tránh giật layout -->
          <form @submit.prevent="handleSubmit" class="space-y-4">
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
              <p v-if="fieldErrors.name" class="text-xs text-red-500 mt-1">{{ fieldErrors.name }}</p>
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
              <p v-if="fieldErrors.email" class="text-xs text-red-500 mt-1">{{ fieldErrors.email }}</p>
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
              <p v-if="fieldErrors.password" class="text-xs text-red-500 mt-1">{{ fieldErrors.password }}</p>
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
              <p v-if="fieldErrors.password_confirmation" class="text-xs text-red-500 mt-1">{{ fieldErrors.password_confirmation }}</p>
            </div>

            <!-- Thông báo lỗi -->
            <div v-if="errorMessage" class="text-red-500 text-sm text-center">
              {{ errorMessage }}
            </div>

            <!-- Nút đăng ký -->
            <button
              :disabled="loading"
              type="submit"
              class="w-full bg-blue-600 hover:bg-blue-500 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold py-2 rounded-lg transition-colors flex items-center justify-center gap-2"
            >
              <svg v-if="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>Đăng ký</span>
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
import { useAuthStore } from "@/stores/auth";
import { showToast } from "@/utils/toast";

const router = useRouter();
const auth = useAuthStore();

const name = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const errorMessage = ref("");
const loading = ref(false);
const darkMode = ref(false);
const fieldErrors = ref<{ [k: string]: string }>({});

const toggleDarkMode = () => {
  darkMode.value = !darkMode.value;
  document.documentElement.classList.toggle("dark", darkMode.value);
};

const handleSubmit = async () => {
  errorMessage.value = "";
  fieldErrors.value = {};

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  // FE validation tối thiểu để UX tốt, chuẩn hóa thông báo theo-field
  if (!emailRegex.test(email.value)) {
    fieldErrors.value.email = "Email không hợp lệ";
    return;
  }
  if (password.value.length < 6) {
    fieldErrors.value.password = "Mật khẩu phải có ít nhất 6 ký tự";
    return;
  }
  if (password.value !== confirmPassword.value) {
    fieldErrors.value.password_confirmation = "Mật khẩu xác nhận không khớp";
    return;
  }

  try {
    loading.value = true;
    await new Promise((resolve) => setTimeout(resolve, 1200)); // hiệu ứng loading

    const res = await auth.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
    })
    if (res.success) {
      showToast("Đăng ký thành công! Vui lòng đăng nhập.", "success");
    router.push("/login");
      return
    }
    // Chuẩn hóa lỗi từ store (BE 422)
    if (res.errors) {
      fieldErrors.value = {
        name: res.errors.name?.[0],
        email: res.errors.email?.[0],
        password: res.errors.password?.[0],
        password_confirmation: res.errors.password_confirmation?.[0],
      }
      errorMessage.value = "Vui lòng kiểm tra lại các trường nhập";
      // Không ném lỗi để tránh nhảy vào nhánh "không thể kết nối máy chủ"
      showToast(errorMessage.value, "error");
      return
    }
  } catch (error: any) {
    if (error.response) {
      if (error.response.status === 422 && error.response.data?.errors) {
        const errs = error.response.data.errors
        // Map từng trường của BE về UI
        fieldErrors.value = {
          name: errs.name?.[0],
          email: errs.email?.[0],
          password: errs.password?.[0],
          password_confirmation: errs.password_confirmation?.[0],
        }
        errorMessage.value = "Vui lòng kiểm tra lại các trường nhập";
      } else {
        errorMessage.value = error.response.data?.message || "Đăng ký thất bại";
      }
    } else {
      errorMessage.value = "Không thể kết nối máy chủ.";
    }
    showToast(errorMessage.value, "error");
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
