<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Quản trị hệ thống</h1>
      
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Tổng người dùng</h3>
          <div class="text-3xl font-bold text-blue-600">1,234</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Tin rao</h3>
          <div class="text-3xl font-bold text-green-600">567</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Chờ duyệt</h3>
          <div class="text-3xl font-bold text-yellow-600">23</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Báo cáo</h3>
          <div class="text-3xl font-bold text-red-600">12</div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Thao tác nhanh</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <button class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
            Quản lý người dùng
          </button>
          <button class="bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors">
            Duyệt tin rao
          </button>
          <button class="bg-yellow-600 text-white px-4 py-3 rounded-lg hover:bg-yellow-700 transition-colors">
            Xử lý báo cáo
          </button>
          <button class="bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition-colors">
            Thống kê
          </button>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Hoạt động gần đây</h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-4 border rounded-lg">
            <div>
              <h4 class="font-medium text-gray-900">Tin rao mới cần duyệt</h4>
              <p class="text-sm text-gray-600">5 tin rao mới được đăng</p>
            </div>
            <span class="text-sm text-gray-500">2 phút trước</span>
          </div>
          
          <div class="flex items-center justify-between p-4 border rounded-lg">
            <div>
              <h4 class="font-medium text-gray-900">Báo cáo vi phạm</h4>
              <p class="text-sm text-gray-600">3 báo cáo mới cần xử lý</p>
            </div>
            <span class="text-sm text-gray-500">15 phút trước</span>
          </div>
          
          <div class="flex items-center justify-between p-4 border rounded-lg">
            <div>
              <h4 class="font-medium text-gray-900">Người dùng mới</h4>
              <p class="text-sm text-gray-600">12 người dùng đăng ký mới</p>
            </div>
            <span class="text-sm text-gray-500">1 giờ trước</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Simple admin dashboard component
</script>
