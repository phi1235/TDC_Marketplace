<template>
  <div class="max-w-6xl mx-auto p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Tin rao của tôi</h1>
        <p class="text-gray-600 mt-1">Quản lý các tin rao bạn đã đăng</p>
      </div>
      <router-link
        to="/create-listing"
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Đăng tin mới
      </router-link>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
          <input
            v-model="filters.search"
            @input="debouncedSearch"
            type="text"
            placeholder="Tìm theo tiêu đề..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <!-- Status Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
          <select
            v-model="filters.status"
            @change="loadListings()"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Tất cả</option>
            <option value="pending">Chờ duyệt</option>
            <option value="approved">Đã duyệt</option>
            <option value="rejected">Từ chối</option>
            <option value="sold">Đã bán</option>
            <option value="archived">Lưu trữ</option>
          </select>
        </div>

        <!-- Sort -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp</label>
          <select
            v-model="filters.sort"
            @change="loadListings()"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="created_at">Mới nhất</option>
            <option value="price">Giá thấp nhất</option>
            <option value="views_count">Nhiều lượt xem</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="listings.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có tin rao nào</h3>
      <p class="mt-1 text-sm text-gray-500">Bắt đầu đăng tin rao đầu tiên của bạn.</p>
      <div class="mt-6">
        <router-link
          to="/create-listing"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Đăng tin mới
        </router-link>
      </div>
    </div>

    <!-- Listings Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="listing in listings"
        :key="listing.id"
        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
      >
        <!-- Image -->
        <div class="aspect-w-16 aspect-h-12 bg-gray-200">
          <img
            v-if="listing.images && listing.images.length > 0"
            :src="imageUrl(listing.images[0].image_path)"
            :alt="listing.title"
            class="w-full h-48 object-cover"
          />
          <div v-else class="w-full h-48 bg-gray-200 flex items-center justify-center">
            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
        </div>

        <!-- Content -->
        <div class="p-4">
          <!-- Title -->
          <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
            {{ listing.title }}
          </h3>

          <!-- Price -->
          <div class="flex items-center justify-between mb-2">
            <span class="text-xl font-bold text-blue-600">
              {{ formatPrice(listing.price) }} VNĐ
            </span>
            <span
              :class="getStatusClass(listing.status)"
              class="px-2 py-1 rounded-full text-xs font-medium"
            >
              {{ getStatusText(listing.status) }}
            </span>
          </div>

          <!-- Condition -->
          <div class="flex items-center mb-2">
            <span class="text-sm text-gray-600">Tình trạng:</span>
            <span
              :class="getConditionClass(listing.condition)"
              class="ml-2 px-2 py-1 rounded-full text-xs font-medium"
            >
              {{ getConditionText(listing.condition) }}
            </span>
          </div>

          <!-- Views -->
          <div class="flex items-center text-sm text-gray-600 mb-3">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <span>{{ listing.views_count }} lượt xem</span>
          </div>

          <!-- Actions -->
          <div class="space-y-2">
            <div class="flex items-center gap-2">
              <router-link
                :to="`/listings/${listing.id}`"
                class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-sm"
              >
                Xem chi tiết
              </router-link>
              
              <button
                v-if="listing.status !== 'approved'"
                @click="editListing(listing.id)"
                class="p-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 transition-colors"
                title="Chỉnh sửa"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
            </div>
            
            <div class="flex items-center gap-2">
              <button
                @click="openDuplicateModal(listing)"
                :disabled="!canDuplicate(listing)"
                :class="[
                  'flex-1 text-center py-2 px-4 rounded-md transition-colors text-sm border',
                  canDuplicate(listing)
                    ? 'bg-green-50 text-green-700 hover:bg-green-100 border-green-200 cursor-pointer'
                    : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                ]"
                :title="getDuplicateTooltip(listing)"
              >
                Nhân bản
                <span v-if="listing.status === 'rejected' && listing.duplicate_count > 0" class="text-xs">
                  ({{ listing.duplicate_count }}/2)
                </span>
              </button>
              
              <button
                @click="openDeleteModal(listing)"
                class="flex-1 bg-red-50 text-red-700 text-center py-2 px-4 rounded-md hover:bg-red-100 transition-colors text-sm border border-red-200"
              >
                Xóa
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.last_page > 1" class="mt-8 flex justify-center">
      <nav class="flex items-center space-x-2">
        <button
          v-for="page in pagination.last_page"
          :key="page"
          @click="loadListings(page)"
          :class="[
            'px-3 py-2 text-sm font-medium rounded-md',
            page === pagination.current_page
              ? 'bg-blue-600 text-white'
              : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
          ]"
        >
          {{ page }}
        </button>
      </nav>
    </div>

    <!-- Duplicate Confirmation Modal -->
    <div
      v-if="showDuplicateModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeDuplicateModal"
    >
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0 w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-semibold text-gray-900">Xác nhận nhân bản</h3>
          </div>
        </div>
        
        <p class="text-gray-600 mb-6">
          Bạn có chắc muốn nhân bản tin rao "<strong>{{ selectedListing?.title }}</strong>"?
          <br><br>
          Tin rao mới sẽ được tạo với trạng thái "Chờ duyệt" và cần được phê duyệt lại.
          <br><br>
          <span v-if="selectedListing?.duplicate_count >= 0" class="text-sm text-orange-600">
            ⚠️ Lưu ý: Mỗi tin chỉ được nhân bản tối đa 2 lần. 
            (Đã nhân bản: {{ selectedListing?.duplicate_count || 0 }}/2)
          </span>
        </p>
        
        <div class="flex gap-3">
          <button
            @click="closeDuplicateModal"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Hủy
          </button>
          <button
            @click="confirmDuplicate"
            :disabled="duplicating"
            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="duplicating">Đang xử lý...</span>
            <span v-else>Nhân bản</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeDeleteModal"
    >
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-semibold text-gray-900">Xác nhận xóa</h3>
          </div>
        </div>
        
        <p class="text-gray-600 mb-6">
          Bạn có chắc muốn xóa tin rao "<strong>{{ selectedListing?.title }}</strong>"?
          <br><br>
          <span class="text-red-600 font-medium">Hành động này không thể hoàn tác!</span>
        </p>
        
        <div class="flex gap-3">
          <button
            @click="closeDeleteModal"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Hủy
          </button>
          <button
            @click="confirmDelete"
            :disabled="deleting"
            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="deleting">Đang xóa...</span>
            <span v-else>Xóa</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { listingsService } from '@/services/listings'
import { showToast } from '@/utils/toast'
import { imageUrl } from '@/utils/image'

const router = useRouter()

// State
const listings = ref([])
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
})
const filters = ref({
  search: '',
  status: '',
  sort: 'created_at'
})

// Modal states
const showDuplicateModal = ref(false)
const showDeleteModal = ref(false)
const selectedListing = ref(null)
const duplicating = ref(false)
const deleting = ref(false)

let searchTimeout = null

// Load listings
const loadListings = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      search: filters.value.search || undefined,
      status: filters.value.status || undefined,
      sort: filters.value.sort || 'created_at',
      order: 'desc'
    }
    
    const response = await listingsService.getMyListings(params)
    listings.value = response.data
    pagination.value = {
      current_page: response.current_page,
      last_page: response.last_page,
      per_page: response.per_page,
      total: response.total
    }
  } catch (error) {
    console.error('Error loading listings:', error)
    showToast('error', 'Không thể tải danh sách tin rao')
  } finally {
    loading.value = false
  }
}

// Debounced search
const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  searchTimeout = setTimeout(() => {
    loadListings()
  }, 500)
}

// Edit listing
const editListing = (id) => {
  router.push(`/edit-listing/${id}`)
}

// Check if listing can be duplicated
const canDuplicate = (listing) => {
  // Chỉ cho phép nhân bản tin bị từ chối
  if (listing.status !== 'rejected') {
    return false
  }
  
  // Giới hạn tối đa 2 lần nhân bản
  if (listing.duplicate_count >= 2) {
    return false
  }
  
  return true
}

// Get duplicate button tooltip
const getDuplicateTooltip = (listing) => {
  if (listing.status !== 'rejected') {
    return 'Chỉ có thể nhân bản tin rao đã bị từ chối'
  }
  
  if (listing.duplicate_count >= 2) {
    return 'Tin rao này đã được nhân bản tối đa 2 lần'
  }
  
  return 'Nhân bản tin rao này'
}

// Open duplicate modal
const openDuplicateModal = (listing) => {
  selectedListing.value = listing
  showDuplicateModal.value = true
}

// Close duplicate modal
const closeDuplicateModal = () => {
  showDuplicateModal.value = false
  selectedListing.value = null
  duplicating.value = false
}

// Confirm duplicate
const confirmDuplicate = async () => {
  if (!selectedListing.value) return
  
  duplicating.value = true
  try {
    const response = await listingsService.duplicateListing(selectedListing.value.id)
    showToast('success', response.message || 'Nhân bản tin rao thành công!')
    closeDuplicateModal()
    loadListings()
  } catch (error) {
    console.error('Error duplicating listing:', error)
    showToast('error', error.response?.data?.message || 'Không thể nhân bản tin rao')
    duplicating.value = false
  }
}

// Open delete modal
const openDeleteModal = (listing) => {
  selectedListing.value = listing
  showDeleteModal.value = true
}

// Close delete modal
const closeDeleteModal = () => {
  showDeleteModal.value = false
  selectedListing.value = null
  deleting.value = false
}

// Confirm delete
const confirmDelete = async () => {
  if (!selectedListing.value) return
  
  deleting.value = true
  try {
    const response = await listingsService.deleteListing(selectedListing.value.id)
    showToast('success', response.message || 'Xóa tin rao thành công!')
    closeDeleteModal()
    loadListings()
  } catch (error) {
    console.error('Error deleting listing:', error)
    showToast('error', error.response?.data?.message || 'Không thể xóa tin rao')
    deleting.value = false
  }
}

// Format price
const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN').format(price)
}

// Get status class
const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    sold: 'bg-blue-100 text-blue-800',
    archived: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Get status text
const getStatusText = (status) => {
  const texts = {
    pending: 'Chờ duyệt',
    approved: 'Đã duyệt',
    rejected: 'Từ chối',
    sold: 'Đã bán',
    archived: 'Lưu trữ'
  }
  return texts[status] || 'Không xác định'
}

// Get condition class
const getConditionClass = (condition) => {
  const classes = {
    new: 'bg-green-100 text-green-800',
    like_new: 'bg-blue-100 text-blue-800',
    good: 'bg-yellow-100 text-yellow-800',
    fair: 'bg-red-100 text-red-800'
  }
  return classes[condition] || 'bg-gray-100 text-gray-800'
}

// Get condition text
const getConditionText = (condition) => {
  const texts = {
    new: 'Mới',
    like_new: 'Như mới',
    good: 'Tốt',
    fair: 'Khá'
  }
  return texts[condition] || 'Không xác định'
}

onMounted(() => {
  loadListings()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
