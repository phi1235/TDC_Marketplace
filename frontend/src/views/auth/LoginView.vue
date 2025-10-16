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
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
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
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div v-if="errorMessage" class="text-red-500 text-sm text-center">
          {{ errorMessage }}
        </div>

        <button
          type="submit"
          class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 rounded-lg transition-colors"
        >
          Đăng nhập
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
const router = useRouter();
const auth = useAuthStore();

const handleLogin = () => {
  // Kiểm tra định dạng email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    errorMessage.value = "Email không hợp lệ!";
    return;
  }

  // Kiểm tra mật khẩu
  if (password.value.length < 6) {
    errorMessage.value = "Mật khẩu phải có ít nhất 6 ký tự!";
    return;
  }

  // Nếu hợp lệ, gọi API đăng nhập thật
  errorMessage.value = "";
  auth
    .login({ email: email.value, password: password.value })
    .then((res) => {
      if (res.success) {
        showToast("Đăng nhập thành công", "success");
        router.push("/dashboard");
      } else {
        errorMessage.value = res.error || "Đăng nhập thất bại";
        showToast(errorMessage.value, "error");
      }
    })
    .catch(() => {
      errorMessage.value = "Đăng nhập thất bại";
      showToast(errorMessage.value, "error");
    });
};
</script>
