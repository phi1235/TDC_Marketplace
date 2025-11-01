<template>
  <div class=""> 
    <!-- dashboard min-h-screen p-6 bg-gray-100 flex -->
      <!-- <main class="content flex-1 bg-white p-6 rounded-xl shadow-md overflow-y-auto">
    </main> -->
    <!-- <router-view /> -->
        <Navbar v-if="auth.isAdmin" class="fixed w-64 h-full w-full" />
  </div>
</template>


<script setup>
import { computed } from 'vue'
import Navbar from '@/components/Navbar.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore();
const isAdmin = computed(() => auth.isAdmin)
