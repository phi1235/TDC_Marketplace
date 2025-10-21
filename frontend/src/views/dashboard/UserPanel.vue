<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getCurrentUser } from '@/services/auth' // giả sử có API get current user

interface User {
  id: number
  name: string
  role: string
}

const user = ref<User | null>(null)

onMounted(async () => {
  user.value = await getCurrentUser()
})
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center p-6">
    <div v-if="user" class="bg-white shadow rounded-lg p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold mb-4 text-gray-800">User Panel</h1>

      <div class="mb-4">
        <p class="text-gray-600"><span class="font-semibold">Name:</span> {{ user.name }}</p>
        <p class="text-gray-600"><span class="font-semibold">Role:</span> <span class="text-blue-600">{{ user.role }}</span></p>
      </div>

      <div class="grid grid-cols-2 gap-4 mt-4">
        <button class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded shadow">
          My Listings
        </button>
        <button class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded shadow">
          My Wishlist
        </button>
        <button class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded shadow">
          My Offers
        </button>
        <button class="bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded shadow">
          Account Settings
        </button>
      </div>
    </div>

    <div v-else class="text-gray-500 text-lg">Loading...</div>
  </div>
</template>
