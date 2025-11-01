<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Monitoring</h1>

    <div class="bg-white border rounded p-4 mb-6 grid grid-cols-1 md:grid-cols-5 gap-3 items-center">
      <input v-model.number="hours" min="1" type="number" class="border rounded px-3 py-2" placeholder="Giờ (mặc định 24)" />
      <input v-model="endpoint" type="text" class="border rounded px-3 py-2" placeholder="Lọc endpoint" />
      <input v-model="status" type="number" class="border rounded px-3 py-2" placeholder="HTTP status" />
      <button @click="load" class="bg-blue-600 text-white rounded px-5 py-2">Lọc</button>
      <button @click="exportCsv" class="bg-gray-700 text-white rounded px-5 py-2">Export CSV</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white border rounded p-4">
        <div class="text-sm text-gray-500">Requests (24h)</div>
        <div class="text-2xl font-bold">{{ metrics.error_rate.total }}</div>
      </div>
      <div class="bg-white border rounded p-4">
        <div class="text-sm text-gray-500">Errors (24h)</div>
        <div class="text-2xl font-bold text-red-600">{{ metrics.error_rate.errors }}</div>
      </div>
      <div class="bg-white border rounded p-4">
        <div class="text-sm text-gray-500">p95 Response</div>
        <div class="text-2xl font-bold">{{ metrics.p95_response_ms }} ms</div>
      </div>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-6" v-if="(metrics.alerts || []).length">
      <div class="font-semibold text-yellow-800 mb-2">Cảnh báo</div>
      <ul class="list-disc pl-5 text-sm text-yellow-900">
        <li v-for="a in metrics.alerts" :key="a.rule + a.message">
          {{ a.message }}
          <span v-if="a.context?.rate">(error rate: {{ a.context.rate }}%)</span>
          <span v-if="a.context?.p95_ms">(p95: {{ a.context.p95_ms }} ms)</span>
        </li>
      </ul>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <div class="bg-white border rounded">
        <div class="p-4 font-semibold">Error rate theo giờ</div>
        <div class="px-4 pb-4"><canvas ref="errChartRef" height="160"></canvas></div>
      </div>
      <div class="bg-white border rounded">
        <div class="p-4 font-semibold">Latency trung bình theo giờ</div>
        <div class="px-4 pb-4"><canvas ref="latChartRef" height="160"></canvas></div>
      </div>
    </div>

    <div class="bg-white border rounded">
      <div class="p-4 font-semibold">Lỗi gần đây</div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500 border-b">
              <th class="py-2 px-4">Thời gian</th>
              <th class="py-2 px-4">Status</th>
              <th class="py-2 px-4">Route</th>
              <th class="py-2 px-4">Message</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="e in metrics.recent_errors || []" :key="e.id" class="border-b hover:bg-gray-50 cursor-pointer" @click="openDetail(e)">
              <td class="py-2 px-4">{{ new Date(e.created_at).toLocaleString('vi-VN') }}</td>
              <td class="py-2 px-4">{{ e.status || '-' }}</td>
              <td class="py-2 px-4">{{ e.method }} {{ e.route }}</td>
              <td class="py-2 px-4 truncate max-w-[480px]" :title="e.message">{{ e.message }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal chi tiết lỗi -->
    <div v-if="detail" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded shadow-lg w-[90vw] max-w-3xl max-h-[80vh] overflow-auto">
        <div class="p-4 border-b flex justify-between items-center">
          <div class="font-semibold">Chi tiết lỗi</div>
          <button class="text-gray-500" @click="detail=null">✕</button>
        </div>
        <div class="p-4 text-sm">
          <div class="mb-2"><b>Thời gian:</b> {{ new Date(detail.created_at).toLocaleString('vi-VN') }}</div>
          <div class="mb-2"><b>Status:</b> {{ detail.status || '-' }}</div>
          <div class="mb-2"><b>Route:</b> {{ detail.method }} {{ detail.route }}</div>
          <div class="mb-2"><b>Message:</b> {{ detail.message }}</div>
          <div><b>Trace:</b>
            <pre class="mt-2 p-3 bg-gray-100 rounded whitespace-pre-wrap">{{ detail.trace || 'N/A' }}</pre>
          </div>
        </div>
        <div class="p-4 border-t text-right">
          <button class="px-4 py-2 bg-gray-700 text-white rounded" @click="detail=null">Đóng</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import api from '@/services/api'

const metrics = ref<any>({ error_rate: { total: 0, errors: 0 }, recent_errors: [], p95_response_ms: 0 })
const hours = ref<number>(24)
const endpoint = ref<string>('')
const status = ref<number | undefined>(undefined)
const detail = ref<any|null>(null)
const errChartRef = ref<HTMLCanvasElement|null>(null)
const latChartRef = ref<HTMLCanvasElement|null>(null)
let Chart: any, errChart: any, latChart: any

async function load() {
  const params: any = { hours: hours.value }
  if (endpoint.value) params.endpoint = endpoint.value
  if (status.value) params.status = status.value
  const res = await api.get('/admin/monitoring/overview', { params })
  metrics.value = res.data
  await renderCharts()
}

onMounted(load)

const exportUrl = computed(() => {
  const p = new URLSearchParams()
  p.set('hours', String(hours.value))
  if (endpoint.value) p.set('endpoint', endpoint.value)
  if (status.value) p.set('status', String(status.value))
  return `/api/admin/monitoring/export?${p.toString()}`
})

function openDetail(e: any) { detail.value = e }

async function ensureChartJs() {
  if ((window as any).Chart) { Chart = (window as any).Chart; return }
  await new Promise<void>((resolve, reject) => {
    const s = document.createElement('script')
    s.src = 'https://cdn.jsdelivr.net/npm/chart.js'
    s.onload = () => { Chart = (window as any).Chart; resolve() }
    s.onerror = () => reject(new Error('Chart.js load failed'))
    document.head.appendChild(s)
  })
}

async function exportCsv() {
  const params: any = { hours: hours.value }
  if (endpoint.value) params.endpoint = endpoint.value
  if (status.value) params.status = status.value
  const res = await api.get('/admin/monitoring/export', { params, responseType: 'blob' })
  const blob = new Blob([res.data], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = 'monitoring.csv'
  document.body.appendChild(a)
  a.click()
  a.remove()
  window.URL.revokeObjectURL(url)
}

async function renderCharts() {
  await ensureChartJs()
  const rows = metrics.value.series || []
  const labels = rows.map((r: any) => r.h)
  const errorRate = rows.map((r: any) => {
    const t = Number(r.total)||0, e = Number(r.errors)||0; return t>0? +( (e/t)*100 ).toFixed(2) : 0
  })
  const avgMs = rows.map((r: any) => Math.round(Number(r.avg_ms)||0))

  if (errChart) errChart.destroy(); if (latChart) latChart.destroy()
  const errCtx = errChartRef.value?.getContext('2d'); const latCtx = latChartRef.value?.getContext('2d')
  if (!errCtx || !latCtx) return
  errChart = new Chart(errCtx, { type: 'line', data: { labels, datasets: [{ label: 'Error rate %', data: errorRate, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.2)', tension: 0.3 }] }, options: { scales: { y: { beginAtZero: true, ticks: { callback: (v: any)=> v+ '%' } } } } })
  latChart = new Chart(latCtx, { type: 'line', data: { labels, datasets: [{ label: 'Avg ms', data: avgMs, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.2)', tension: 0.3 }] }, options: { scales: { y: { beginAtZero: true } } } })
}
</script>


