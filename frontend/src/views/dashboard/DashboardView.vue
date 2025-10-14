<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Bảng điều khiển</h1>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Stats Cards -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Tin rao của tôi</h3>
          <div class="text-3xl font-bold text-blue-600">5</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Đã bán</h3>
          <div class="text-3xl font-bold text-green-600">3</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Đánh giá</h3>
          <div class="text-3xl font-bold text-yellow-600">4.8</div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Thao tác nhanh</h3>
        <div class="flex flex-wrap gap-4">
          <router-link
            to="/listings/new"
            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors"
          >
            Đăng tin mới
          </router-link>
          <router-link
            to="/listings"
            class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors"
          >
            Xem tất cả tin rao
          </router-link>
          <router-link
            to="/profile"
            class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors"
          >
            Chỉnh sửa hồ sơ
          </router-link>
        </div>
      </div>

      <!-- Recent Listings -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Tin rao gần đây</h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-4 border rounded-lg">
            <div>
              <h4 class="font-medium text-gray-900">Sách Toán 12</h4>
              <p class="text-sm text-gray-600">Đăng lúc: 2 giờ trước</p>
            </div>
            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Đang bán</span>
          </div>
          
          <div class="flex items-center justify-between p-4 border rounded-lg">
            <div>
              <h4 class="font-medium text-gray-900">Laptop Dell</h4>
              <p class="text-sm text-gray-600">Đăng lúc: 1 ngày trước</p>
            </div>
            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-sm">Chờ duyệt</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Simple dashboard component
</script>
