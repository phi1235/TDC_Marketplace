<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Analytics</h1>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6 grid grid-cols-1 md:grid-cols-5 gap-3 items-center">
      <input v-model="from" type="date" class="border rounded px-3 py-2" />
      <input v-model="to" type="date" class="border rounded px-3 py-2" />
      <select v-model="group" class="border rounded px-3 py-2">
        <option value="day">Theo ngày</option>
        <option value="week">Theo tuần</option>
        <option value="month">Theo tháng</option>
      </select>
      <div class="md:col-span-2 flex justify-end">
        <button @click="load" class="bg-blue-600 text-white rounded px-5 py-2">Tải dữ liệu</button>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow-sm border p-4">
        <div class="text-sm text-gray-500">Tổng sự kiện</div>
        <div class="text-2xl font-bold">{{ totalEvents }}</div>
      </div>
      <div class="bg-white rounded-lg shadow-sm border p-4">
        <div class="text-sm text-gray-500">Search</div>
        <div class="text-2xl font-bold">{{ analytics.search?.total || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow-sm border p-4">
        <div class="text-sm text-gray-500">No-result rate</div>
        <div class="text-2xl font-bold">{{ ((analytics.search?.no_result_rate || 0) * 100).toFixed(1) }}%</div>
      </div>
      <div class="bg-white rounded-lg shadow-sm border p-4">
        <div class="text-sm text-gray-500">Khoảng thời gian</div>
        <div class="text-sm">{{ analytics.range?.from }} → {{ analytics.range?.to }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Series chart + table -->
      <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-4 font-semibold">Sự kiện theo ngày</div>
        <!-- Chart.js canvas -->
        <div class="px-4 pb-4">
          <canvas ref="chartRef" height="160"></canvas>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 border-b">
                <th class="py-2 px-4">Ngày</th>
                <th class="py-2 px-4">Tổng</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in series" :key="'t-' + row.d" class="border-b">
                <td class="py-2 px-4">{{ row.d }}</td>
                <td class="py-2 px-4">{{ row.total }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top keywords -->
      <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-4 font-semibold">Top từ khóa</div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 border-b">
                <th class="py-2 px-4">Từ khóa</th>
                <th class="py-2 px-4">Lượt</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="k in analytics.search?.top_keywords || []" :key="k.keyword" class="border-b">
                <td class="py-2 px-4">{{ k.keyword }}</td>
                <td class="py-2 px-4">{{ k.total }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top listings -->
      <div class="bg-white rounded-lg shadow-sm border lg:col-span-2">
        <div class="p-4 font-semibold">Top tin rao được xem</div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 border-b">
                <th class="py-2 px-4">Listing ID</th>
                <th class="py-2 px-4">Tiêu đề</th>
                <th class="py-2 px-4">Lượt xem</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="l in analytics.top_listings || []" :key="l.listing_id" class="border-b">
                <td class="py-2 px-4">{{ l.listing_id }}</td>
                <td class="py-2 px-4 truncate max-w-[360px]" :title="l.title || ''">{{ l.title || '-' }}</td>
                <td class="py-2 px-4">{{ l.total }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { adminAnalyticsService } from '@/services/adminAnalytics'
// Chart.js via CDN
let Chart: any

const from = ref<string>('')
const to = ref<string>('')
const analytics = ref<any>({})
const group = ref<'day'|'week'|'month'>('day')
const chartRef = ref<HTMLCanvasElement | null>(null)
let chartInstance: any = null

const totalEvents = computed(() => {
  const totals = analytics.value.totals || {}
  return Object.values(totals).reduce((a: any, b: any) => Number(a) + Number(b), 0)
})

const series = computed(() => analytics.value.series || [])
const maxSeries = computed(() => Math.max(1, ...series.value.map((s: any) => Number(s.total))))
function barHeight(v: number | string) {
  const h = (Number(v) / maxSeries.value) * 140 // 140px max height
  return `${Math.max(2, Math.round(h))}px`
}
function shortDate(d: string) {
  try { return new Date(d).toLocaleDateString('vi-VN', { month: '2-digit', day: '2-digit' }) } catch { return d }
}

async function load() {
  const params: Record<string, any> = {}
  if (from.value) params.from = from.value
  if (to.value) params.to = to.value
  params.group = group.value
  analytics.value = await adminAnalyticsService.overview(params)
  await renderChart()
}

onMounted(load)

async function ensureChartJs() {
  if (window && (window as any).Chart) { Chart = (window as any).Chart; return }
  await new Promise<void>((resolve, reject) => {
    const s = document.createElement('script')
    s.src = 'https://cdn.jsdelivr.net/npm/chart.js'
    s.onload = () => { Chart = (window as any).Chart; resolve() }
    s.onerror = () => reject(new Error('Chart.js load failed'))
    document.head.appendChild(s)
  })
}

async function renderChart() {
  await ensureChartJs()
  const ctx = chartRef.value?.getContext('2d')
  if (!ctx) return
  if (chartInstance) { chartInstance.destroy() }
  const labels = (analytics.value.series || []).map((r: any) => r.g || r.d)
  // datasets: total + per-event if available
  const totalData = (analytics.value.series || []).map((r: any) => r.total)
  const datasets: any[] = [{ label: 'Tổng', data: totalData, type: 'line', borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.2)', tension: 0.3, yAxisID: 'y' }]
  const map = analytics.value.series_by_event || {}
  const palette = ['#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4']
  let i = 0
  for (const [name, arr] of Object.entries(map)) {
    const dict: Record<string, number> = {}
    ;(arr as any[]).forEach(row => { dict[String((row as any).g)] = Number((row as any).total) })
    const data = labels.map((g: any) => dict[String(g)] || 0)
    datasets.push({ label: String(name), data, backgroundColor: palette[i % palette.length], stack: 'events', type: 'bar', yAxisID: 'y' })
    i++
  }
  chartInstance = new Chart(ctx, {
    data: { labels, datasets },
    options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { position: 'bottom' } } }
  })
}
</script>


