<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Chào mừng đến với TDC Marketplace</h1>
      
      <!-- Hero Section -->
      <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">
          Nền tảng trao đổi đồ học tập cũ
        </h2>
        <p class="text-gray-600 mb-6">
          Dành cho sinh viên 
        </p>
        <div class="flex gap-4">
          <router-link 
            to="/listings" 
            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors"
          >
            Xem tin rao
          </router-link>
          <router-link 
            to="/register" 
            class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors"
          >
            Đăng ký ngay
          </router-link>
        </div>
      </div>

      <!-- Categories -->
      <div class="bg-white rounded-lg shadow-md p-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-6">Danh mục phổ biến</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="text-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
            <div class="text-2xl mb-2">📚</div>
            <div class="font-medium">Sách giáo khoa</div>
          </div>
          <div class="text-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
            <div class="text-2xl mb-2">💻</div>
            <div class="font-medium">Điện tử</div>
          </div>
          <div class="text-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
            <div class="text-2xl mb-2">✏️</div>
            <div class="font-medium">Đồ dùng học tập</div>
          </div>
          <div class="text-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
            <div class="text-2xl mb-2">👕</div>
            <div class="font-medium">Quần áo</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Simple component without imports
</script>