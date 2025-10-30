<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
      <!-- Header -->
      <div class="text-center mb-6">
        <div
          class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mx-auto mb-4"
        >
          <span class="text-white font-bold text-2xl">T</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">Đăng nhập</h2>
        <p class="text-gray-600 mt-2">Nhập email và mật khẩu để đăng nhập</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-gray-700 text-sm font-medium mb-1"
            >Email</label
          >
          <input
            v-model="email"
            type="email"
            required
            placeholder="Nhập email"
            :disabled="loading"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60 disabled:cursor-not-allowed"
          />
          <p v-if="fieldErrors.email" class="text-xs text-red-500 mt-1">{{ fieldErrors.email }}</p>
        </div>

        <div>
          <label class="block text-gray-700 text-sm font-medium mb-1"
            >Mật khẩu</label
          >
          <input
            v-model="password"
            type="password"
            required
            placeholder="Nhập mật khẩu"
            :disabled="loading"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60 disabled:cursor-not-allowed"
          />
          <p v-if="fieldErrors.password" class="text-xs text-red-500 mt-1">{{ fieldErrors.password }}</p>
        </div>

        <div v-if="errorMessage" class="text-red-500 text-sm text-center">
          {{ errorMessage }}
        </div>

        <button
          :disabled="loading"
          type="submit"
          class="w-full bg-blue-600 hover:bg-blue-500 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold py-2 rounded-lg transition-colors flex items-center justify-center gap-2"
        >
          <svg v-if="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
          </svg>
          <span>Đăng nhập</span>
        </button>
      </form>

      <!-- Register link -->
      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
          Chưa có tài khoản?
          <router-link
            to="/register"
            class="text-blue-600 hover:text-blue-500 font-medium"
          >
            Đăng ký ngay
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { showToast } from "@/utils/toast";

const email = ref("");
const password = ref("");
const errorMessage = ref("");
const fieldErrors = ref<{ [k: string]: string }>({});
const loading = ref(false);
const router = useRouter();
const auth = useAuthStore();

const handleLogin = () => {
  fieldErrors.value = {};
  // Kiểm tra định dạng email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    fieldErrors.value.email = "Email không hợp lệ";
    return;
  }

  // Kiểm tra mật khẩu
  if (password.value.length < 6) {
    fieldErrors.value.password = "Mật khẩu phải có ít nhất 6 ký tự";
    return;
  }

  // Nếu hợp lệ, gọi API đăng nhập thật
  errorMessage.value = "";
  loading.value = true;
  auth
    .login({ email: email.value, password: password.value })
    .then((res) => {
      if (res.success) {
        showToast("success", "Đăng nhập thành công");
        // Redirect dựa trên role
        if (auth.isAdmin) {
          router.push("/dashboard");
        } else {
          router.push("/");
        }
      } else {
        errorMessage.value = res.error || "Đăng nhập thất bại";
        showToast(errorMessage.value, "error");
      }
    })
    .catch(() => {
      errorMessage.value = "Đăng nhập thất bại";
      showToast(errorMessage.value, "error");
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>
