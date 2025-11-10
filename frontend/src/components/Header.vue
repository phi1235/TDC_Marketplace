<template>
  <header class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 z-10 relative">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-lg">T</span>
            </div>
            <span class="text-xl font-bold text-gray-900 dark:text-gray-100">TDC Marketplace</span>
          </router-link>
        </div>

        <!-- 🔍 Search Bar with Suggest & History -->
        <div class="flex-1 max-w-lg mx-8 relative">
          <input v-model="searchQuery" type="text" placeholder="Tìm kiếm sản phẩm..."
            class="w-full pl-10 pr-40 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            @input="handleInput" @keydown.down.prevent="moveDown" @keydown.up.prevent="moveUp"
            @keydown.enter.prevent="handleEnter" @focus="handleFocus" @blur="hideDropdown" />
          <!-- 🔽 Dropdown chọn Engine + Nút tìm kiếm (liền khối) -->
          <div class="absolute right-0 top-0 bottom-0 flex items-center">
            <!-- Select Engine -->
            <select v-model="engine"
              class="h-full border-l border-gray-300 bg-gray-50 text-sm text-gray-700 px-5 pr-6 rounded-r-none focus:outline-none focus:ring-2 focus:ring-blue-500 hover:bg-white transition appearance-none"
              style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'10\' height=\'6\'><path fill=\'%23666\' d=\'M0 0l5 6 5-6z\'/></svg>'); background-repeat: no-repeat; background-position: right 0.6rem center; background-size: 10px;">
              <option value="es">Elasticsearch</option>
              <option value="solr">Solr</option>
              <option value="compare">So sánh</option>
            </select>

            <!-- Button Search -->
            <button @click="searchFullKeyword"
              class="h-full bg-blue-600 text-white px-4 rounded-r-lg text-sm font-medium hover:bg-blue-700 focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
              title="Tìm kiếm">
              🔍
            </button>
          </div>

          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <!-- Dropdown -->
          <ul v-if="showDropdown && (loadingSuggest || suggestions.length || (showHistory && history.length))"
            class="absolute left-0 top-full z-50 w-full bg-white border border-gray-200 rounded-lg shadow-md mt-1 max-h-72 overflow-auto">
            <!-- 🔍 Search full keyword -->
            <li v-if="searchQuery.trim()"
              class="px-4 py-2 cursor-pointer text-gray-700 hover:bg-blue-50 font-medium border-b border-gray-100"
              @mousedown.prevent="searchFullKeyword">
              🔍 Tìm kiếm "<span class="text-blue-600">{{ searchQuery }}</span>" trong toàn bộ sản phẩm
            </li>

            <!-- History (chỉ hiển thị khi input trống) -->
            <template v-else-if="showHistory && history.length">
              <li class="px-4 py-2 text-gray-500 text-sm border-b bg-gray-50 font-semibold">
                📜 Lịch sử tìm kiếm gần đây
              </li>
              <li v-for="(item, i) in history" :key="'h' + i"
                class="px-4 py-2 cursor-pointer text-gray-700 hover:bg-blue-50"
                @mousedown.prevent="selectHistory(item.keyword)" :title="new Date(item.timestamp).toLocaleString()">
                <span class="text-gray-800">{{ item.keyword }}</span>
                <span class="text-xs text-gray-400 ml-2">({{ item.results_count }} kết quả)</span>
              </li>
              <li class="px-4 py-2 text-sm text-red-600 hover:underline cursor-pointer border-t border-gray-100"
                @mousedown.prevent="clearHistory">
                🗑 Xóa lịch sử
              </li>
            </template>

            <!-- Loading -->
            <li v-if="loadingSuggest" class="px-4 py-2 text-gray-400 italic">Đang gợi ý...</li>

            <!-- Suggest results -->
            <li v-for="(item, i) in suggestions" :key="'s' + i" class="px-4 py-2 cursor-pointer transition"
              :class="i === selectedIndex ? 'bg-blue-100 font-semibold' : 'hover:bg-blue-50'"
              @mousedown.prevent="selectSuggestion(item)">
              <span v-html="highlight(item)"></span>
            </li>
          </ul>
        </div>

        <!-- Navigation -->
        <nav class="flex items-center space-x-4">
          <router-link to="/"
            class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">
            Trang chủ
          </router-link>

          <router-link to="/listings"
            class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">
            Danh sách
          </router-link>

          <!-- Test pages -->
          <div class="relative test-menu-container">
            <!-- <button @click="showTestMenu = !showTestMenu"
              class="flex items-center space-x-1 text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
              <span>Test Pages</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button> -->

            <!-- Test pages dropdown -->
            <!-- <div v-if="showTestMenu"
              class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
              <router-link to="/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showTestMenu = false">
                Dashboard Page
              </router-link>
              <router-link to="/panel" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showTestMenu = false">
                Panel Page
              </router-link>
              <router-link to="/userpanel" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showTestMenu = false">
                User Page
              </router-link>
              <router-link to="/listwish" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showTestMenu = false">
                List wish page
              </router-link>
              <router-link to="/listingcard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showTestMenu = false">
                Listing Card page
              </router-link>
              <router-link to="/notifications" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showTestMenu = false">
                News
              </router-link>
            </div> -->
          </div>

          <!-- Auth -->
          <div v-if="!isAuthenticated" class="flex items-center space-x-2">
            <router-link to="/login"
              class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">
              Đăng nhập
            </router-link>
            <router-link to="/register"
              class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium">
              Đăng ký
            </router-link>
          </div>

          <div v-else class="flex items-center space-x-2">
            <template v-if="auth.isAdmin">
              <router-link to="/dashboard"
                class="flex items-center space-x-1 text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                Quản trị
              </router-link>
            </template>

            <template v-else>
              <router-link to="/create-listing"
                class="bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-md text-sm font-medium">
                Đăng tin
              </router-link>
             
            </template>

            <!-- User menu -->
            <div class="relative user-menu-container">
              <button @click="toggleUserMenu"
                class="flex items-center text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                {{ user?.name }}
                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>

              <div v-if="showUserMenu"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="showUserMenu = false">
                  Hồ sơ
                </router-link>
                <div v-if="isAuthenticated && !auth.isAdmin">
                  <router-link to="/listwish" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false">
                    Danh sách 💟 {{ wishlistStore.count }}
                  </router-link>
                  <router-link to="/orders/my" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showUserMenu = false">
                    Đơn hàng của tôi 📦
                  </router-link>
                </div>
                <router-link to="/my-listings"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                @click="showUserMenu = false">
                Tin của tôi
              </router-link>
                <router-link to="/my-reports" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="showUserMenu = false">
                  Báo cáo của tôi
                </router-link>
                <router-link to="/my-activity" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="showUserMenu = false">
                  Hoạt động của tôi
                </router-link>
                <button @click="handleLogout"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  Đăng xuất
                </button>
              </div>
            </div>
          </div>

          <!-- Anounce for user -->
          <div v-if="!auth.isAdmin" class="flex items-center space-x-2 relative bell">
            <div v-if="isAuthenticated">
              <button @click="isOpen = !isOpen">
                🔔
                <span v-if="unreadCount > 0"
                  class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1 rounded-full">
                  {{ unreadCount }}
                </span>
              </button>
            </div>
            <transition name="fade-slide">
              <div v-if="isOpen" class="absolute right-0 top-9 mt-2 w-72 bg-white shadow-lg rounded-lg z-50">
                <div v-for="value in notifications" key="index" class="p-3 hover:bg-gray-100 cursor-pointer border">
                  <p> {{ value.title }} </p>
                </div>
                <router-link to="/notifications" class="block text-center py-2 hover:bg-gray-100">
                  Xem tất cả thông báo
                </router-link>
              </div>
            </transition>
          </div>

          <!-- Dark Mode -->
          <button @click="toggleDark"
            class="ml-3 p-2 rounded-md bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
            :title="isDark ? 'Chuyển sang chế độ sáng' : 'Chuyển sang chế độ tối'">
            <span v-if="!isDark">🌙</span>
            <span v-else>☀️</span>
          </button>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { showToast } from '@/utils/toast'
// import axios from 'axios'
import { getWishes } from '@/services/wishlist'
import { useWishlistStore } from '@/stores/wishlist'
import { fire } from '@/services/activities'

const router = useRouter()
const auth = useAuthStore()

const searchQuery = ref('')
const engine = ref('es') // 
const showDropdown = ref(false)
const showHistory = ref(false)
const suggestions = ref<string[]>([])
const history = ref<any[]>([])
const loadingSuggest = ref(false)
const selectedIndex = ref(-1)
const cache = new Map()
let debounceTimer: any = null
let abortController: AbortController | null = null

const showUserMenu = ref(false)
const showTestMenu = ref(false)
const isDark = ref(false)

const isAuthenticated = computed(() => auth.isAuthenticated)
const user = computed(() => auth.user)
const isAdmin = computed(() => auth.isAdmin)

// === 🧠 Suggest & History Logic ===
const handleInput = () => {
  showHistory.value = false
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchSuggestions, 150)
}

const handleFocus = () => {
  showDropdown.value = true
  if (!searchQuery.value.trim()) fetchHistory()
}

const fetchSuggestions = async () => {
  const q = searchQuery.value.trim()
  if (!q) {
    suggestions.value = []
    showHistory.value = true
    return
  }
  if (cache.has('suggest_' + q)) {
    suggestions.value = cache.get('suggest_' + q)
    return
  }

  if (abortController) abortController.abort()
  abortController = new AbortController()
  loadingSuggest.value = true
  try {
    const res = await fetch(`http://localhost:8001/api/search-es/suggest?q=${encodeURIComponent(q)}`, {
      signal: abortController.signal,
    })
    const data = await res.json()
    suggestions.value = data.suggestions || []
    cache.set('suggest_' + q, suggestions.value)
  } catch (e: any) {
    if (e.name !== 'AbortError') console.error('Suggest error:', e)
  } finally {
    loadingSuggest.value = false
  }
}

const fetchHistory = async () => {
  try {
    const res = await fetch('http://localhost:8001/api/search-es/history', { credentials: 'include' })
    const data = await res.json()
    history.value = data.history || []
    showHistory.value = true
  } catch (e) {
    console.error('History error:', e)
  }
}

const clearHistory = async () => {
  if (!confirm('Bạn có chắc muốn xóa toàn bộ lịch sử tìm kiếm?')) return
  try {
    const res = await fetch('http://localhost:8001/api/search-es/history/clear', {
      method: 'DELETE',
      credentials: 'include',
    })
    const data = await res.json()
    if (data.success) {
      history.value = []
      alert('🗑 Đã xóa lịch sử tìm kiếm!')
    }
  } catch (e) {
    console.error('Clear history error:', e)
  }
}

const selectSuggestion = (item: string) => {
  searchQuery.value = item
  showDropdown.value = false
  router.push({ name: 'search', query: { q: item, engine: engine.value } })
}

const selectHistory = (keyword: string) => {
  searchQuery.value = keyword
  showDropdown.value = false
  router.push({ name: 'search', query: { q: keyword, engine: engine.value } })
}

const searchFullKeyword = () => {
  const q = searchQuery.value.trim()
  if (!q) return
  showDropdown.value = false
  router.push({ name: 'search', query: { q, engine: engine.value } })
  // fire search event
  fire('search_performed', { q, engine: engine.value })
}

const moveDown = () => {
  if (!suggestions.value.length) return
  selectedIndex.value = (selectedIndex.value + 1) % suggestions.value.length
  searchQuery.value = suggestions.value[selectedIndex.value]
}

const moveUp = () => {
  if (!suggestions.value.length) return
  selectedIndex.value = (selectedIndex.value - 1 + suggestions.value.length) % suggestions.value.length
  searchQuery.value = suggestions.value[selectedIndex.value]
}

const handleEnter = () => {
  if (selectedIndex.value >= 0 && suggestions.value[selectedIndex.value]) {
    selectSuggestion(suggestions.value[selectedIndex.value])
  } else if (searchQuery.value.trim()) {
    searchFullKeyword()
  }
}

const hideDropdown = () => setTimeout(() => (showDropdown.value = false), 200)

const highlight = (text: string) => {
  const q = searchQuery.value.trim()
  if (!text) return ''
  if (!q) return text

  // Nếu backend (Solr hoặc ES) đã trả <mark> thì giữ nguyên, không xử lý lại
  if (text.includes('<mark>')) return text

  // Escape ký tự đặc biệt (để tránh lỗi khi người dùng nhập regex)
  const safeQuery = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
  const regex = new RegExp(`(${safeQuery})`, 'gi')

  return text.replace(regex, '<mark class="bg-yellow-200 font-semibold">$1</mark>')
}

// === 🔐 Auth Logic ===
const toggleUserMenu = () => (showUserMenu.value = !showUserMenu.value)

const handleLogout = async () => {
  try {
    await auth.logout()
    showToast('success', 'Đăng xuất thành công')
    router.push('/')
    showUserMenu.value = false
  } catch (error) {
    showToast('error', 'Đăng xuất thất bại')
  }
}

// Close dropdowns when clicking outside
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement

  // Close user menu if clicking outside
  if (!target.closest('.user-menu-container')) {
    showUserMenu.value = false
  }

  // Close test menu if clicking outside
  if (!target.closest('.test-menu-container')) {
    showTestMenu.value = false
  }
}

//wishlist
const wishlistStore = useWishlistStore()

// ✅ Log real-time
watch(
  () => wishlistStore.count,
  (newVal) => {
    console.log("🎯 Wishlist Count (real-time):", newVal)
  },
  { immediate: true }
)

onMounted(async () => {
  await auth.checkAuthStatus?.()
  if (!auth.isAuthenticated) return

  try {
    const res = await getWishes()
    wishlistStore.setCount(Array.isArray(res) ? res.length : 0)
  } catch (err) {
    console.error('Lỗi lấy wishlist:', err)
    wishlistStore.setCount(0)
  }
})

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  const saved = localStorage.getItem('theme')
  if (saved === 'dark') {
    isDark.value = true
    document.documentElement.classList.add('dark')
  }
})

watch(isDark, (val) => {
  if (val) {
    document.documentElement.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
})

const toggleDark = () => (isDark.value = !isDark.value)

//Anounce for user
const isOpen = ref(false); //trạng thái để mở thông báo
const unreadCount = ref(4); //tin chưa đọc
const notifications = ref([
  { title: 'Bạn có đơn hàng mới' },
  { title: 'Tin nhắn mới từ admin' },
  { title: 'Khuyến mãi siêu hot' },
  { title: 'Notification thứ 4' },
]) //hiện tạm thời, khi nào có api thì truyền vô
//đóng khi click ra ngoài
const closeNotificationIfOutside = (e) => {
  const bell = document.querySelector('.bell')
  if (bell && !bell.contains(e.target)) {
    isOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', closeNotificationIfOutside))
onBeforeUnmount(() => document.removeEventListener('click', closeNotificationIfOutside))

onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
mark {
  background-color: #fef08a;
  color: inherit;
}

/* style announce */
.fade-slide-enter-active {
  transition: all 0.2s ease;
}

.fade-slide-leave-active {
  transition: all 0.2s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>
