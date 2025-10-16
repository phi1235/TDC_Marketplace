<template>
  <div class="p-4">
    <h2 class="text-xl font-bold mb-4">User Management</h2>
    <table class="w-full border-collapse border">
      <thead>
        <tr class="bg-gray-200">
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">Name</th>
          <th class="border px-2 py-1">Email</th>
          <th class="border px-2 py-1">Role</th>
          <th class="border px-2 py-1">Active</th>
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td class="border px-2 py-1">{{ user.id }}</td>
          <td class="border px-2 py-1">{{ user.name }}</td>
          <td class="border px-2 py-1">{{ user.email }}</td>
          <td class="border px-2 py-1">{{ user.role }}</td>
          <td class="border px-2 py-1">
            <span :class="user.is_active ? 'text-green-600' : 'text-red-600'">
              {{ user.is_active ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td class="border px-2 py-1">
            <button
              class="px-2 py-1 bg-blue-500 text-white rounded mr-2"
              @click="toggleStatus(user.id)"
            >
              Toggle
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getAllUsers, toggleUserStatus, User } from '@/services/user'

const users = ref<User[]>([])

const fetchUsers = async () => {
  try {
    users.value = await getAllUsers()
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

const toggleStatus = async (id: number) => {
  try {
    await toggleUserStatus(id)
    await fetchUsers() // refresh
  } catch (error) {
    console.error('Error toggling status:', error)
  }
}

onMounted(() => {
  fetchUsers()
})
</script>

<style scoped>
table th, table td {
  text-align: left;
}
</style>
