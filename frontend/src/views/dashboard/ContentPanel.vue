<script setup>
import { onMounted } from 'vue'
import { useUserStore } from '@/store/user'

const userStore = useUserStore()

onMounted(async () => {
  await userStore.fetchCurrentUser()
})
</script>

<template>
  <div>
    <p v-if="userStore.user">Hello, {{ userStore.user.name }}</p>
    <p v-else>Loading...</p>
  </div>
</template>
