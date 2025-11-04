<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { adminListingsService } from '@/services/adminListings'
import { showToast } from '@/utils/toast'
import { imageUrl } from '@/utils/image'
import AdvancedFilterPending from '@/components/AdvancedFilterPending.vue'

const allRows = ref<any[]>([]) // dữ liệu gốc
const rows = ref<any[]>([])     // dữ liệu hiển thị
const total = ref(0)
const page = ref(1)
const perPage = ref(10)
const search = ref('')
const loading = ref(false)
const showReject = ref(false)
const rejectReason = ref('')
const selectedId = ref<number | null>(null)

// Advanced filter modal
const showAdvanced = ref(false)
const appliedFilter = ref<any>(null)

// =====================
// FETCH DATA
// =====================
async function fetchData() {
  loading.value = true
  try {
    const data = await adminListingsService.list({
      page: 1,
      per_page: 1000, // lấy toàn bộ pending
      status: 'pending',
    })
    allRows.value = data.data || []
    applyFilter() // gọi lại hàm lọc
    console.log('haha ', allRows.value);
  } catch (e: any) {
    showToast(e?.response?.data?.message || 'Tải danh sách thất bại', 'error')
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)
watch(page, applyFilter)

// =====================
// LỌC NÂNG CAO
// =====================
function applyAdvancedFilter(filters: any) {
  appliedFilter.value = filters
  applyFilter()
}

function applyFilter() {
  let list = [...allRows.value]
  const f = appliedFilter.value

  // --- Search ---
  if (search.value) {
    list = list.filter(r =>
      r.title?.toLowerCase().includes(search.value.toLowerCase())
    )
  }

  if (f) {
    // --- Views count ---
    if (f.views_count_value != null) {
      const val = f.views_count_value
      const op = f.views_count_op || '>'
      list = list.filter(r => {
        if (op === '>') return r.views_count > val
        if (op === '<') return r.views_count < val
        return r.views_count === val
      })
    }

    // --- Price ---
    if (f.price_value != null) {
      const val = f.price_value
      const op = f.price_op || '='
      list = list.filter(r => {
        if (op === '>') return r.price > val
        if (op === '<') return r.price < val
        return r.price === val
      })
    }

    // --- Created date ---
    if (f.created_from) {
      const from = new Date(f.created_from)
      list = list.filter(r => new Date(r.created_at) >= from)
    }
    if (f.created_to) {
      const to = new Date(f.created_to)
      list = list.filter(r => new Date(r.created_at) <= to)
    }
  }

  // --- Pagination ---
  const start = (page.value - 1) * perPage.value
  const end = start + perPage.value
  rows.value = list.slice(start, end)
  total.value = list.length
}

// =====================
// CÁC HÀM DƯỚI KHÔNG ĐỤNG TỚI
// =====================
async function onSearch() {
  page.value = 1
  await fetchData()
}

async function approveOne(id: number) {
  if (loading.value) return
  loading.value = true
  try {
    await adminListingsService.approve(id)
    showToast('success', 'Duyệt thành công')
  } catch (e: any) {
    const msg = e?.response?.data?.message || ''
    await fetchData()
    const stillPending = rows.value.some((r) => r.id === id)
    if (!stillPending) {
      showToast('success', 'Tin đã được duyệt')
    } else {
      showToast('error', msg || 'Duyệt thất bại')
    }
    return
  } finally {
    await fetchData()
    loading.value = false
  }
}

function rejectOne(id: number) {
  selectedId.value = id
  rejectReason.value = ''
  showReject.value = true
}

async function confirmReject() {
  if (!selectedId.value || loading.value) return
  const id = selectedId.value
  loading.value = true
  try {
    await adminListingsService.reject(id, 'other', rejectReason.value || 'Không phù hợp')
    showToast('success', 'Từ chối thành công')
    showReject.value = false
    selectedId.value = null
  } catch (e: any) {
    const msg = e?.response?.data?.message || ''
    await fetchData()
    const stillPending = rows.value.some((r) => r.id === id)
    if (!stillPending) {
      showToast('success', 'Tin đã từ chối thành công')
      showReject.value = false
      selectedId.value = null
    } else {
      showToast('error', msg || 'Từ chối thất bại')
    }
    return
  } finally {
    await fetchData()
    loading.value = false
  }
}
</script>


<template>
  <!--  style="padding:24px; max-width:1200px; margin:0 auto;" -->
  <section>
    <header style="display:flex; align-items:center; gap:16px; margin-bottom:16px;">
      <h1 style="font-size:22px; font-weight:700;">Tin chờ duyệt</h1>
      <span style="color:#6b7280;">Tổng số: <b>{{ total }}</b></span>
      <div style="margin-left:auto; display:flex; gap:8px;">
        <input v-model="search" @keyup.enter="onSearch" type="search" placeholder="Tìm kiếm..."
          style="padding:8px 10px; border:1px solid #e5e7eb; border-radius:8px; width:260px;" />
        <!-- <button :disabled="loading" @click="onSearch"
          style="padding:8px 12px; border-radius:8px; background:#2563eb; color:#fff;">Lọc</button> -->
      </div>
      <!-- Filter advance -->
      <label class="font-medium">Bộ lọc:</label>
      <button @click="showAdvanced = true" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
        Nâng cao
      </button>
      <AdvancedFilterPending :visible="showAdvanced" @update:visible="showAdvanced = $event"
        @filter-change="applyAdvancedFilter" />


    </header>

    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
      <table style="width:100%; border-collapse:collapse;">
        <thead style="background:#f8fafc;">
          <tr>
            <th style="padding:12px; text-align:left;">STT</th>
            <th style="padding:12px; text-align:left;">ID</th>
            <th style="padding:12px; text-align:left;">Ảnh</th>
            <th style="padding:12px; text-align:left;">Tiêu đề</th>
            <th style="padding:12px; text-align:left;">Người bán</th>
            <th style="padding:12px; text-align:right;">Giá</th>
            <th style="padding:12px; text-align:center;">Ngày tạo</th>
            <th style="padding:12px; text-align:center;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(r, i) in rows" :key="r.id" style="border-top:1px solid #f1f5f9;">
            <td style="padding:12px;">{{ (page - 1) * perPage + i + 1 }}</td>
            <td style="padding:12px;">#{{ String(r.id).padStart(3, '0') }}</td>
            <td style="padding:12px;">
              <img :src="imageUrl(r.images?.[0]?.image_path || r.thumbnail) || 'https://via.placeholder.com/60'"
                alt="thumb"
                style="width:60px; height:60px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb;" />
            </td>
            <td style="padding:12px; max-width:360px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{
              r.title }}</td>
            <td style="padding:12px;">{{ r.seller?.name || r.user?.name || '-' }}</td>
            <td style="padding:12px; text-align:right; font-variant-numeric: tabular-nums;">{{
              r.price?.toLocaleString?.('vi-VN') || r.price }}</td>
            <td style="padding:12px; text-align:center;">{{ (r.created_at || '').slice(0, 10) }}</td>
            <td style="padding:12px; text-align:center; display:flex; gap:8px; justify-content:center;">
              <button :disabled="loading" @click="approveOne(r.id)"
                style="padding:6px 10px; border-radius:8px; background:#10b981; color:#fff;">Duyệt</button>
              <button :disabled="loading" @click="rejectOne(r.id)"
                style="padding:6px 10px; border-radius:8px; background:#ef4444; color:#fff;">Từ chối</button>
            </td>
          </tr>
          <tr v-if="!rows.length">
            <td colspan="8" style="padding:16px; color:#64748b; text-align:center;">Không có dữ liệu</td>
          </tr>
        </tbody>
      </table>

      <footer style="display:flex; justify-content:center; gap:8px; padding:12px;">
        <button :disabled="page <= 1 || loading" @click="page = Math.max(1, page - 1)" class="page-btn">« Trước</button>
        <span class="page-btn active">{{ page }}</span>
        <button :disabled="loading || rows.length < perPage" @click="page = page + 1" class="page-btn">Sau »</button>
      </footer>
    </div>

    <!-- Modal từ chối -->
    <div v-if="showReject"
      style="position:fixed;inset:0;background:rgba(0,0,0,.4);display:flex;align-items:center;justify-content:center;z-index:2000;">
      <div style="background:#fff;border-radius:12px;padding:20px;width:420px;box-shadow:0 10px 30px rgba(0,0,0,.15)">
        <h3 style="margin-bottom:10px;font-weight:700;font-size:18px;">Lý do từ chối</h3>
        <textarea v-model="rejectReason" rows="4" placeholder="Nhập lý do..."
          style="width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:8px;"></textarea>
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
          <button @click="showReject = false"
            style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#fff;">Hủy</button>
          <button @click="confirmReject" style="padding:8px 12px;border-radius:8px;background:#ef4444;color:#fff;">Xác
            nhận từ chối</button>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.page-btn {
  background: #f3f4f6;
  border: 1px solid #d1d5db;
  color: #374151;
  padding: 8px 12px;
  border-radius: 6px;
}

.page-btn.active {
  background: #2563eb;
  color: #fff;
  border-color: #2563eb;
}
</style>
