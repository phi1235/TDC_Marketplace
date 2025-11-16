<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="loading" class="container mx-auto px-4 py-8">
      <div class="max-w-6xl mx-auto">
        <div class="animate-pulse space-y-6">
          <div class="h-8 bg-gray-200 rounded w-1/3"></div>
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
              <div class="h-96 bg-gray-200 rounded-lg"></div>
              <div class="grid grid-cols-4 gap-2">
                <div class="h-20 bg-gray-200 rounded"></div>
                <div class="h-20 bg-gray-200 rounded"></div>
                <div class="h-20 bg-gray-200 rounded"></div>
                <div class="h-20 bg-gray-200 rounded"></div>
              </div>
            </div>
            <div class="space-y-4">
              <div class="h-64 bg-gray-200 rounded-lg"></div>
              <div class="h-48 bg-gray-200 rounded-lg"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="container mx-auto px-4 py-8">
      <div class="max-w-2xl mx-auto text-center">
        <svg class="w-24 h-24 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Không tìm thấy tin rao</h2>
        <p class="text-gray-600 mb-6">{{ error }}</p>
        <router-link to="/listings"
          class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Quay lại danh sách
        </router-link>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else-if="listing" class="container mx-auto px-4 py-8">
      <div class="max-w-6xl mx-auto">
        <!-- Breadcrumb -->
        <Breadcrumb :items="breadcrumbItems" />

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 ">
          <!-- Left Column: Images & Details -->
          <div class="lg:col-span-2 space-y-6 relative">
            
            <!-- Image Gallery -->
            <ImageGallery :images="listing.images || []" :alt-text="listing.title" />
            <!-- Lishwish icon -->
             <button @click.stop="toggleFavorite(listing)"
              class="absolute -top-4 right-2 z-20 bg-white rounded-full p-1 shadow-md flex flex-col items-center">
              <span class="text-xl" :class="listing.is_favorite ? 'text-red-500' : 'text-gray-400'">
                {{ listing.is_favorite ? '♥️' : '🤍' }}
              </span>
            </button>

            <!-- Listing Details Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                    {{ listing.title }}
                  </h1>

                  <!-- Meta Info -->
                  <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      {{ listing.views_count || listing.view_count || 0 }} lượt xem
                    </div>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      {{ formatDate(listing.created_at) }}
                    </div>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                      </svg>
                      {{ listing.category?.name || 'Chưa phân loại' }}
                    </div>
                  </div>
                </div>

                <!-- Status Badge -->
                <span :class="[
                  'px-3 py-1 rounded-full text-sm font-medium whitespace-nowrap ml-4',
                  getStatusClass(listing.status)
                ]">
                  {{ getStatusText(listing.status) }}
                </span>
              </div>

              <!-- Price -->
              <div class="mb-6 pb-6 border-b border-gray-200">
                <div class="text-4xl font-bold text-blue-600">
                  {{ formatPrice(listing.price) }}
                </div>
              </div>

              <!-- Specs Grid -->
              <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                  <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div>
                    <div class="text-xs text-gray-500">Tình trạng</div>
                    <div class="font-medium text-gray-900">{{ getConditionText(listing.condition) }}</div>
                  </div>
                </div>

                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                  <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <div>
                    <div class="text-xs text-gray-500">Địa điểm</div>
                    <div class="font-medium text-gray-900">{{ listing.location || 'Chưa cung cấp' }}</div>
                  </div>
                </div>
              </div>

              <!-- Pickup Point (nếu có) -->
              <div v-if="listing.pickup_point" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                  <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 mb-1">📍 Điểm giao dịch</h4>
                    <p class="text-sm font-medium text-blue-700 mb-1">{{ listing.pickup_point.name }}</p>
                    <p v-if="listing.pickup_point.address" class="text-sm text-gray-600">
                      {{ listing.pickup_point.address }}
                    </p>
                    <p v-if="listing.pickup_point.campus_code" class="text-xs text-gray-500 mt-1">
                      Mã: {{ listing.pickup_point.campus_code }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Mô tả chi tiết</h3>
                <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-line">
                  {{ listing.description }}
                </div>
              </div>
            </div>

            <!-- ✅ Related inline (bên trong v-else-if="listing") -->
            <section class="mt-10">
              <h3 class="text-xl font-bold mb-4 text-gray-900">Tin rao tương tự</h3>

              <div v-if="loadingRelated" class="text-gray-500 italic">Đang tải...</div>
              <div v-else-if="errorRelated" class="text-red-600">{{ errorRelated }}</div>

              <div v-else-if="relatedListings.length === 0" class="text-gray-500 italic">
                Không có tin rao tương tự nào.
              </div>

              <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div v-for="item in relatedListings" :key="item.id"
                  class="bg-white border rounded-lg shadow-sm hover:shadow-md transition p-3 cursor-pointer"
                  @click="$router.push(`/listings/${item.id}`)">
                  <div class="aspect-square rounded-md overflow-hidden bg-gray-100 mb-2">
                    <img v-if="item.images && item.images.length" :src="buildImageUrl(item.images[0]?.image_path)"
                      :alt="item.title" class="w-full h-full object-cover hover:scale-105 transition-transform" />
                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                      Không có ảnh
                    </div>
                  </div>
                  <h4 class="text-sm font-semibold text-gray-800 truncate">{{ item.title }}</h4>
                  <div class="text-blue-600 font-bold text-base mt-1">{{ formatPrice(item.price) }}</div>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ item.category?.name || 'Chưa phân loại' }}
                  </p>
                </div>
              </div>
            </section>
          </div>

          <!-- Right Column: Seller Info & Actions -->
          <div class="space-y-6">
            <!-- Price Sticky Card (Mobile) -->
            <div class="lg:hidden bg-white rounded-lg shadow-sm border border-gray-200 p-4 sticky top-4 z-10">
              <div class="text-3xl font-bold text-blue-600 mb-3">
                {{ formatPrice(listing.price) }}
              </div>
              <button @click="openContactModal"
                class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Liên hệ người bán
              </button>
            </div>

            <!-- Seller Info Card -->
            <SellerInfoCard v-if="listing.seller" :seller="listing.seller" @contact="openContactModal" />
            <button @click="handleBuyNow" :disabled="buyNowLoading || !listing || disableBuyNow"
              class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white
         bg-green-600 hover:bg-green-700 active:scale-95 transition-transform disabled:opacity-60 disabled:cursor-not-allowed">
              <svg v-if="!buyNowLoading" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6H19a2 2 0 100-4H8.1M7 13L5.4 5M16 21a1 1 0 11-2 0 1 1 0z" />
              </svg>
              <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
              </svg>
              {{ buyNowLoading ? 'Đang tạo đơn...' : 'Mua ngay' }}
            </button>

            <!-- Banner kết quả -->
            <div v-if="lastOrder" class="bg-green-50 border border-green-200 rounded-lg p-4 mt-3">
              <p class="text-sm text-green-800">
                ✅ Đã tạo đơn hàng <span class="font-semibold">{{ lastOrder.order_number }}</span>!<br>
                Trạng thái: <span class="font-semibold">{{ lastOrder.status }}</span>.<br>
                Vui lòng chờ người bán xác nhận.
              </p>
              <div class="mt-3 flex gap-2">
                <button @click="goToMyOrders"
                  class="px-3 py-2 rounded-md text-sm bg-green-600 text-white hover:bg-green-700">
                  Đi tới đơn hàng của tôi
                </button>
                <button @click="clearLastOrder"
                  class="px-3 py-2 rounded-md text-sm border border-green-300 text-green-700 hover:bg-green-100">
                  Đóng
                </button>
              </div>
            </div>

            <!-- Báo lỗi -->
            <div v-if="buyNowError" class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700 mt-2">
              {{ buyNowError }}
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
              <h3 class="text-sm font-semibold text-gray-900 mb-3">Hành động nhanh</h3>
              <div class="space-y-2">
                <button @click="copyLink"
                  class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                  </svg>
                  Sao chép liên kết
                </button>

                <button @click="reportListing"
                  class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                  </svg>
                  Báo cáo tin rao
                </button>
              </div>
            </div>

            <!-- Safety Tips Card -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
              <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                  <h4 class="text-sm font-semibold text-yellow-800 mb-1">Lưu ý an toàn</h4>
                  <ul class="text-xs text-yellow-700 space-y-1">
                    <li>• Gặp gỡ tại nơi công cộng</li>
                    <li>• Kiểm tra kỹ sản phẩm trước khi mua</li>
                    <li>• Không chuyển tiền trước</li>
                    <li>• Báo cáo nếu phát hiện gian lận</li>
                  </ul>
                </div>
              </div>
            </div>
          </div> <!-- /Right column -->
        </div> <!-- /Grid -->
      </div> <!-- /inner container -->
    </div> <!-- /main content -->

    <!-- Contact Seller Modal -->
    <ContactSellerModal v-if="listing && listing.seller" :is-open="showContactModal" :listing="listing"
      :seller="listing.seller" @close="showContactModal = false" @send="handleSendMessage" />

    <!-- Report Modal -->
    <ReportModal v-if="listing" :is-open="showReportModal" reportable-type="App\\Models\\Listing"
      :reportable-id="listing.id" :reportable-title="listing.title" @close="showReportModal = false"
      @submitted="handleReportSubmitted" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'
import { listingsService, type Listing } from '@/services/listings'
import { showToast } from '@/utils/toast'
import ImageGallery from '@/components/listings/ImageGallery.vue'
import SellerInfoCard from '@/components/listings/SellerInfoCard.vue'
import ContactSellerModal from '@/components/listings/ContactSellerModal.vue'
import ReportModal from '@/components/ReportModal.vue'
import Breadcrumb from '@/components/Breadcrumb.vue'
import { watch } from 'vue'
import { fire } from '@/services/activities'
//listwish
import { wishlistService } from '@/services/wishlist'
import { useWishlistStore } from '@/stores/wishlist'

const route = useRoute()
const router = useRouter()

const buyNowLoading = ref(false)
const disableBuyNow = ref(false)
const buyNowError = ref('')
const lastOrder = ref<any | null>(null)

const listing = ref<Listing | null>(null)
const loading = ref(true)
const error = ref('')
const showContactModal = ref(false)
const showReportModal = ref(false)

//listing wishlist
// const listings = ref<any[]>([])

// helper lấy token
function getBuyerToken() {
  return (
    localStorage.getItem('token_buyer') ||
    localStorage.getItem('auth_token') ||
    ''
  )
}
// hàm xử lý mua hàng
async function handleBuyNow() {
  if (!listing.value?.id) return
  buyNowLoading.value = true
  buyNowError.value = ''
  lastOrder.value = null

  try {
    const token = getBuyerToken()
    if (!token) {
      buyNowError.value = 'Bạn cần đăng nhập để mua hàng.'
      showToast('error', buyNowError.value)
      return
    }

    const res = await axios.post(
      '/api/orders',
      { listing_id: listing.value.id },
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json'
        }
      }
    )

    // 🚫 Người bán tự mua sản phẩm của mình
    if (res.data?.success === false) {
      buyNowError.value = res.data.message || 'Bạn không thể mua sản phẩm của chính mình.'
      showToast('error', buyNowError.value)
      disableBuyNow.value = true
      return
    }

    // ✅ Cập nhật thông tin đơn hàng
    lastOrder.value = res.data?.order || null
    showToast('success', res.data?.message || 'Đã tạo đơn hàng thành công!')

    // 🔒 Nếu là đơn cũ được cập nhật (updated=true) → khóa nút “Mua ngay”
    if (res.data?.updated) {
      disableBuyNow.value = true
    }

  } catch (err: any) {
    const msg =
      err?.response?.data?.message ||
      err?.message ||
      'Không thể tạo đơn hàng. Vui lòng thử lại.'
    buyNowError.value = msg
    showToast('error', msg)
  } finally {
    buyNowLoading.value = false
  }
}

function goToMyOrders() {
  router.push('/orders/my')
}
function clearLastOrder() {
  lastOrder.value = null
}


const breadcrumbItems = computed(() => {
  if (!listing.value) return []
  return [
    { label: 'Tin rao', to: '/listings' },
    ...(listing.value.category ? [{ label: listing.value.category.name, to: `/listings?category=${listing.value.category_id}` }] : []),
    { label: listing.value.title }
  ]
})

const formatPrice = (price: number) => {
  if (!price) return '0'
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price)
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now.getTime() - date.getTime())
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 0) return 'Hôm nay'
  if (diffDays === 1) return 'Hôm qua'
  if (diffDays < 7) return `${diffDays} ngày trước`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} tuần trước`
  if (diffDays < 365) return `${Math.floor(diffDays / 30)} tháng trước`
  return date.toLocaleDateString('vi-VN')
}

const getConditionText = (condition: string) => {
  const conditions: Record<string, string> = {
    new: 'Mới (A)',
    like_new: 'Như mới (B)',
    good: 'Tốt (C)',
    fair: 'Khá (D)'
  }
  return conditions[condition] || condition
}

const getStatusText = (status: string) => {
  const statuses: Record<string, string> = {
    pending: 'Chờ duyệt',
    approved: 'Đang bán',
    rejected: 'Bị từ chối',
    sold: 'Đã bán',
    archived: 'Đã ẩn'
  }
  return statuses[status] || status
}

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    sold: 'bg-gray-100 text-gray-800',
    archived: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const buildImageUrl = (path?: string) => {
  if (!path) return ''
  // nếu bạn đã có helper imageUrl, có thể dùng thay cho dòng dưới
  return `/storage/${path}`
}

const loadListing = async () => {
  loading.value = true
  error.value = ''
  try {
    const id = Number(route.params.id)
    if (isNaN(id)) throw new Error('ID tin rao không hợp lệ')

    listing.value = await listingsService.getListing(id)

    if (listing.value) {
      document.title = `${listing.value.title} - TDC Marketplace`
    }
  } catch (err: any) {
    console.error('Error loading listing:', err)
    error.value = err.response?.data?.message || 'Không thể tải thông tin tin rao'
    if (err.response?.status === 404) {
      error.value = 'Tin rao không tồn tại hoặc đã bị xóa'
    }
  } finally {
    loading.value = false
  }
}

const relatedListings = ref<any[]>([])
const loadingRelated = ref(false)
const errorRelated = ref('')

const loadRelatedListings = async (id: number) => {
  loadingRelated.value = true
  errorRelated.value = ''
  try {
    const res = await axios.get(`/api/listings/${id}/related`)
    relatedListings.value = res.data || []
  } catch (err: any) {
    console.error('Lỗi tải tin rao tương tự:', err)
    errorRelated.value = err.response?.data?.message || 'Không thể tải tin rao tương tự'
  } finally {
    loadingRelated.value = false
  }
}

onMounted(async () => {
  await loadListing()
  if (listing.value?.id) {
    loadRelatedListings(listing.value.id)
    fire('listing_view', { listing_id: listing.value.id })
  }
  await loadWishlistStatus()
})
// 🧭 Theo dõi ID thay đổi trên route
watch(
  () => route.params.id,
  async (newId, oldId) => {
    if (newId !== oldId) {
      await loadListing()
      if (listing.value?.id) {
        loadRelatedListings(listing.value.id)
      }
      // Cuộn lên đầu trang cho UX tốt hơn
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  }
)
/* ====== Actions ====== */
const openContactModal = () => { showContactModal.value = true }

const handleSendMessage = (message: string) => {
  console.log('Message sent:', message)
  // TODO: Implement actual message sending API
}

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)
    showToast('success', 'Đã sao chép liên kết!')
  } catch {
    showToast('error', 'Không thể sao chép liên kết')
  }
}

const reportListing = () => {
  showReportModal.value = true
}

const handleReportSubmitted = (report: any) => {
  console.log('Report submitted:', report)
  showToast('success', 'Báo cáo đã được gửi thành công')
}


//Favorite

const wishlistStore = useWishlistStore()

const toggleFavorite = async (item: any) => {
  try {
    const res = await wishlistService.toggleWishlist(item.id)

    // cập nhật số lượng tổng wishlist
    wishlistStore.setCount(res.total ?? wishlistStore.count + (res.is_favorited ? 1 : -1))

    // cập nhật trạng thái tim trên UI
    item.is_favorite = res.is_favorited
  } catch (err) {
    console.error('Lỗi toggle wishlist:', err)
  }
}

const loadWishlistStatus = async () => {
  const res = await wishlistService.getWishlist()
  const wishlistData = Array.isArray(res) ? res : res.data

  wishlistStore.setCount(wishlistData.length)

  if (listing.value) {
    listing.value.is_favorite = wishlistData.some(
      (w: any) => w.listing_id === listing.value.id
    )
  }
}
console.log(toggleFavorite);

</script>
