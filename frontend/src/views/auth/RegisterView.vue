<template>
  <div :class="darkMode ? 'dark bg-gray-900 text-gray-100' : 'bg-gray-50 text-gray-900'" class="min-h-screen transition-colors duration-300">
    <main class="container mx-auto px-4 py-8">
      <div class="max-w-md mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 transition-colors duration-300">
          <h1 class="text-2xl font-bold mb-6 text-center">Đăng ký</h1>

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
                v-model.trim="email"
                type="email"
                required
                placeholder="ví dụ: 22211tt3094@tdc.edu.vn"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                @blur="validateEmail()"
              />
              <!-- Gợi ý khi là email edu -->
              <div v-if="isEduEmail" class="text-xs mt-1"
                   :class="eduEmailValid ? 'text-green-600' : 'text-amber-600'">
                <template v-if="eduEmailValid">
                  ✔ Đúng mẫu sinh viên: năm {{ parsedEdu?.yearFull }} • ngành CNTT (TT)
                </template>
                <template v-else>
                  ⚠ Email sinh viên cần dạng: <b>YY + 3 số + TT + số</b> @tdc.edu.vn (ví dụ: <i>22211tt3094@tdc.edu.vn</i>)
                </template>
              </div>
              <p v-if="fieldErrors.email" class="text-xs text-red-500 mt-1">{{ fieldErrors.email }}</p>
            </div>

            <!-- OTP cho email edu -->
            <div v-if="isEduEmail" class="space-y-2">
              <div class="flex items-center gap-2">
                <button type="button"
                        class="px-3 py-2 rounded-lg border disabled:opacity-60 disabled:cursor-not-allowed"
                        :disabled="!eduEmailValid || sendingOtp || countdown>0"
                        @click="sendOtp">
                  <span v-if="!countdown">Gửi mã xác thực</span>
                  <span v-else>Gửi lại sau {{ countdown }}s</span>
                </button>
                <span v-if="otpSent" class="text-xs text-gray-600 dark:text-gray-400">
                  Đã gửi mã đến email sinh viên của bạn.
                </span>
              </div>

              <div>
                <label class="block text-sm font-medium mb-1">Mã xác thực</label>
                <input
                  v-model.trim="otp"
                  type="text"
                  inputmode="numeric"
                  placeholder="Nhập mã gồm 6 ký tự"
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :disabled="!otpSent"
                  maxlength="6"
                />
                <p v-if="fieldErrors.otp_code" class="text-xs text-red-500 mt-1">{{ fieldErrors.otp_code }}</p>
              </div>
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
import { ref, computed, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { showToast } from "@/utils/toast";

const router = useRouter();
const auth = useAuthStore();

const name = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const otp = ref("");
const errorMessage = ref("");
const loading = ref(false);
const darkMode = ref(false);
const fieldErrors = ref<{ [k: string]: string }>({});

// OTP state
const otpSent = ref(false);
const sendingOtp = ref(false);
const countdown = ref(0);
let timer: number | undefined;

// --- EDU email helpers ---
const isEduEmail = computed(() => /@tdc\.edu\.vn$/i.test(email.value));

/**
 * Validate và parse email edu theo mẫu:
 *  - 2 số năm + 3 số + 2 chữ cái (TT) + 3-5 số + @tdc.edu.vn
 *  major phải là "tt" (không phân biệt hoa/thường)
 */
function parseEduEmail(raw: string) {
  const re = /^(?<year>\d{2})(?<seq>\d{3})(?<major>[A-Za-z]{2})(?<id>\d{3,5})@tdc\.edu\.vn$/i;
  const m = raw.match(re);
  if (!m || !m.groups) return null;
  const major = m.groups.major.toLowerCase();
  if (major !== "tt") return null; // chỉ chấp nhận ngành CNTT
  const year = Number(m.groups.year);
  const yearFull = 2000 + year; // ví dụ: 22 -> 2022
  return {
    year: m.groups.year,
    yearFull,
    seq: m.groups.seq,
    major,
    id: m.groups.id,
  };
}

const parsedEdu = computed(() => (isEduEmail.value ? parseEduEmail(email.value) : null));
const eduEmailValid = computed(() => !!parsedEdu.value);

const toggleDarkMode = () => {
  darkMode.value = !darkMode.value;
  document.documentElement.classList.toggle("dark", darkMode.value);
};

function validateEmail() {
  fieldErrors.value.email = "";
  // Cho phép email thường; nếu là edu thì phải hợp lệ theo mẫu
  const genericEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!genericEmail.test(email.value)) {
    fieldErrors.value.email = "Email không hợp lệ";
    return false;
  }
  if (isEduEmail.value && !eduEmailValid.value) {
    fieldErrors.value.email = "Email sinh viên @tdc.edu.vn không đúng mẫu (YY + 3 số + TT + số)";
    return false;
  }
  return true;
}

function startCountdown(sec = 60) {
  countdown.value = sec;
  timer && clearInterval(timer);
  // @ts-ignore
  timer = setInterval(() => {
    countdown.value -= 1;
    if (countdown.value <= 0) {
      clearInterval(timer);
      timer = undefined;
    }
  }, 1000);
}

onUnmounted(() => {
  timer && clearInterval(timer);
});

// Gửi mã OTP cho email edu
const sendOtp = async () => {
  fieldErrors.value.otp_code = "";
  if (!isEduEmail.value || !eduEmailValid.value) return;

  try {
    sendingOtp.value = true;
    // gọi API BE gửi mã
    await auth.sendEduOtp({ email: email.value }); // <-- cần thêm trong store
    otpSent.value = true;
    showToast("Đã gửi mã xác thực đến email sinh viên.", "success");
    startCountdown(60);
  } catch (e: any) {
    otpSent.value = false;
    showToast(e?.response?.data?.message || "Gửi mã thất bại", "error");
  } finally {
    sendingOtp.value = false;
  }
};

const handleSubmit = async () => {
  errorMessage.value = "";
  fieldErrors.value = {};

  if (!validateEmail()) return;

  if (password.value.length < 6) {
    fieldErrors.value.password = "Mật khẩu phải có ít nhất 6 ký tự";
    return;
  }
  if (password.value !== confirmPassword.value) {
    fieldErrors.value.password_confirmation = "Mật khẩu xác nhận không khớp";
    return;
  }

  // Nếu là email edu: bắt buộc đã gửi mã và có otp
  if (isEduEmail.value) {
    if (!otpSent.value) {
      fieldErrors.value.otp_code = "Vui lòng bấm 'Gửi mã xác thực' trước";
      return;
    }
    if (!otp.value || otp.value.length < 6) {
      fieldErrors.value.otp_code = "Mã xác thực không hợp lệ";
      return;
    }
  }

  try {
    loading.value = true;
    await new Promise((resolve) => setTimeout(resolve, 600)); // hiệu ứng loading nhẹ

    const res = await auth.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
      // BE có thể bỏ qua nếu không phải edu
      otp_code: isEduEmail.value ? otp.value : undefined,
    });

    if (res.success) {
      showToast("Đăng ký thành công! Vui lòng đăng nhập.", "success");
      router.push("/login");
      return;
    }

    if (res.errors) {
      fieldErrors.value = {
        name: res.errors.name?.[0],
        email: res.errors.email?.[0],
        password: res.errors.password?.[0],
        password_confirmation: res.errors.password_confirmation?.[0],
        otp_code: res.errors.otp_code?.[0],
      };
      errorMessage.value = "Vui lòng kiểm tra lại các trường nhập";
      showToast(errorMessage.value, "error");
      return;
    }
  } catch (error: any) {
    if (error.response) {
      if (error.response.status === 422 && error.response.data?.errors) {
        const errs = error.response.data.errors;
        fieldErrors.value = {
          name: errs.name?.[0],
          email: errs.email?.[0],
          password: errs.password?.[0],
          password_confirmation: errs.password_confirmation?.[0],
          otp_code: errs.otp_code?.[0],
        };
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
:root {
  color-scheme: light dark;
}
</style>
