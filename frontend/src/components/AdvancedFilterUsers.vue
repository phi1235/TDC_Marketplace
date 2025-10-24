<template>
  <div v-if="visible" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-3xl">
      <!-- Account Status Block -->
      <h2 class="text-lg font-semibold mb-3">Account Status</h2>
      <div class="grid grid-cols-3 gap-4 mb-6">
        <div>
          <label class="block text-sm mb-1">Role</label>
          <select v-model="filters.role" class="border rounded px-3 py-2 w-full">
            <option value="all">Tất cả</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Active</label>
          <select v-model="filters.is_active" class="border rounded px-3 py-2 w-full">
            <option value="all">All</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Email Verified</label>
          <select v-model="filters.email_verified" class="border rounded px-3 py-2 w-full">
            <option value="all">All</option>
            <option value="1">Đã xác thực</option>
            <option value="0">Chưa xác thực</option>
          </select>
        </div>
      </div>

      <!-- Engagement Block -->
      <h2 class="text-lg font-semibold mb-3">Engagement</h2>
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
          <label class="block text-sm mb-1">Login Count ></label>
          <input v-model.number="filters.login_count_min" type="number" class="border rounded px-3 py-2 w-full" />
        </div>
        <div>
          <label class="block text-sm mb-1">Last Login</label>
          <select v-model="filters.last_login" class="border rounded px-3 py-2 w-full">
            <option value="all">All</option>
            <option value="7d">7 ngày qua</option>
            <option value="30d">30 ngày qua</option>
            <option value="never">Chưa login</option>
          </select>
        </div>
      </div>

      <!-- Created Date Block -->
      <h2 class="text-lg font-semibold mb-3">Created Date</h2>
      <div class="grid grid-cols-2 gap-4 mb-6">
        <input type="date" v-model="filters.created_from" class="border rounded px-3 py-2" />
        <input type="date" v-model="filters.created_to" class="border rounded px-3 py-2" />
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-3">
        <button @click="reset" class="px-4 py-2 rounded bg-gray-200">Reset</button>
        <button @click="close" class="px-4 py-2 rounded bg-gray-400 text-white">Đóng</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, reactive, watch } from 'vue'
const props = defineProps<{ visible: boolean }>()
const emit = defineEmits(['update:visible', 'filter-change'])
const filters = reactive({ role:'all', is_active:'all', email_verified:'all', login_count_min:null, last_login:'all', created_from:null, created_to:null })

watch(filters, () => emit('filter-change', filters), { deep: true })
function close(){ emit('update:visible', false) }
function reset(){ Object.assign(filters,{ role:'all',is_active:'all',email_verified:'all',login_count_min:null,last_login:'all',created_from:null,created_to:null }) }
</script>
