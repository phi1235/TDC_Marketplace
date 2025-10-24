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

      <!-- Search Engine Comparison -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">🔍 Search Engine Comparison</h3>
        <p class="text-gray-600 mb-4">
          So sánh hiệu năng giữa Elasticsearch và Apache Solr
        </p>
        <div class="flex space-x-4">
          <router-link 
            to="/admin/comparison" 
            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Comparison Dashboard
          </router-link>
          <router-link 
            to="/search-test" 
            class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Search Test
          </router-link>
          <router-link 
            to="/dashboard/search-analytics" 
            class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Search Analytics
          </router-link>
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
