<template>
  <div class="space-y-3">
    <div class="flex gap-2">
      <input v-model="search" placeholder="Tìm điểm giao dịch..." class="border rounded px-3 py-2 w-full" />
      <button @click="load" class="px-3 py-2 rounded bg-blue-600 text-white">Tìm</button>
    </div>

    <div v-if="items.length === 0" class="text-gray-500">Không có điểm phù hợp.</div>

    <ul class="divide-y">
      <li v-for="p in items" :key="p.id" class="py-2 flex items-start gap-3">
        <input v-if="mode==='single'" type="radio" name="pickup" :value="p.id" v-model="selectedOne" class="mt-1"/>
        <input v-else type="checkbox" :value="p.id" v-model="selectedMany" class="mt-1"/>
        <div>
          <div class="font-medium">{{ p.name }}</div>
          <div class="text-sm text-gray-500">{{ p.address }}</div>
          <div class="text-xs text-gray-400" v-if="p.campus_code">Khu: {{ p.campus_code }}</div>
        </div>
      </li>
    </ul>

    <div class="pt-2">
      <button @click="emitDone" class="px-4 py-2 rounded bg-green-600 text-white">Xác nhận</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { pickupApi } from '@/services/pickup'

const props = defineProps<{ mode?: 'single'|'multi'; defaultSelected?: number[]|number }>()
const emit = defineEmits<{ (e:'done', value:number[]|number|null):void }>()

const search = ref('')
const items = ref<any[]>([])
const selectedMany = ref<number[]>(Array.isArray(props.defaultSelected) ? props.defaultSelected as number[] : [])
const selectedOne = ref<number | null>(typeof props.defaultSelected === 'number' ? props.defaultSelected : null)

async function load() {
  const { data } = await pickupApi.list({ search: search.value })
  items.value = data.data ?? data
}
function emitDone() {
  emit('done', props.mode === 'single' ? selectedOne.value : selectedMany.value)
}
onMounted(load)
watch(() => props.defaultSelected, (v) => {
  if (Array.isArray(v)) selectedMany.value = v
  else if (typeof v === 'number') selectedOne.value = v
})
</script>
