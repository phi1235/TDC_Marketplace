<template>
  <div v-if="visible" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-3xl">
      <!-- Listing Status -->
      <h2 class="text-lg font-semibold mb-3">Listing Status</h2>
      <div class="grid grid-cols-1 gap-4 mb-6">
        <label class="block text-sm mb-1">Status</label>
        <select v-model="filters.status" class="border rounded px-3 py-2 w-full">
          <option value="all">Tất cả</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
          <option value="pending">Pending</option>
        </select>
      </div>

      <!-- Views Count -->
      <h2 class="text-lg font-semibold mb-3">Views Count</h2>
      <div class="grid grid-cols-2 gap-4 mb-6">
        <select v-model="filters.views_count_op" class="border rounded px-3 py-2 w-full">
          <option value=">">></option>
          <option value="<"><</option>
          <option value="=">=</option>
        </select>
        <input
          v-model.number="filters.views_count_value"
          type="number"
          class="border rounded px-3 py-2 w-full"
        />
      </div>

      <!-- Created Date -->
      <h2 class="text-lg font-semibold mb-3">Created Date</h2>
      <div class="grid grid-cols-2 gap-4 mb-6">
        <input type="date" v-model="filters.created_from" class="border rounded px-3 py-2" />
        <input type="date" v-model="filters.created_to" class="border rounded px-3 py-2" />
      </div>

      <!-- Updated Date -->
      <h2 class="text-lg font-semibold mb-3">Updated Date</h2>
      <div class="grid grid-cols-2 gap-4 mb-6">
        <input type="date" v-model="filters.updated_from" class="border rounded px-3 py-2" />
        <input type="date" v-model="filters.updated_to" class="border rounded px-3 py-2" />
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-3">
        <button @click="reset" class="px-4 py-2 rounded bg-gray-200">Reset</button>
        <button @click="apply" class="px-4 py-2 rounded bg-blue-500 text-white">Apply</button>
        <button @click="close" class="px-4 py-2 rounded bg-gray-400 text-white">Đóng</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, reactive, watch } from 'vue'

const props = defineProps<{ visible: boolean }>()
const emit = defineEmits(['update:visible', 'filter-change'])

const filters = reactive({
  status: 'all',
  views_count_op: '>' as string,
  views_count_value: null as number | null,
  created_from: '',
  created_to: '',
  created_preset: 'none' as string,
  updated_from: '',
  updated_to: '',
  updated_preset: 'none' as string
})

// Watch realtime changes if needed
watch(filters, () => emit('filter-change', { ...filters }), { deep: true })

function close() {
  emit('update:visible', false)
}

function reset() {
  Object.assign(filters, {
    status: 'all',
    views_count_op: '>',
    views_count_value: null,
    created_from: '',
    created_to: '',
    created_preset: 'none',
    updated_from: '',
    updated_to: '',
    updated_preset: 'none'
  })
  // emit reset filters immediately
  emit('filter-change', { ...filters })
}

function apply() {
  emit('filter-change', { ...filters })
  emit('update:visible', false)
}
</script>
