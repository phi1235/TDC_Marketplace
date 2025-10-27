<template>
  <div class="admin-layout">
    <div class="admin-header-container">
      <!-- Sidebar Header -->
      <nav class="sidebar-header">
        <div class="sidebar-logo">
          <div class="logo-content">
            <div class="logo-icon">T</div>
            <span class="logo-text">TDC Admin</span>
          </div>
        </div>
      </nav>
      
      <!-- Main Header -->
      <div class="main-header">
        <AdminHeader />
      </div>
    </div>
    
    <div class="admin-body">
      <!-- Sidebar -->
      <nav class="sidebar">
        <!-- Navigation Menu -->
        <div class="sidebar-content">
        <!-- MENU Section -->
        <div class="menu-section">
          <div class="menu-title">MENU</div>
          <router-link to="/admin" class="menu-item" exact-active-class="active">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
            <span class="badge">9+</span>
          </router-link>
        </div>

        <!-- APPS Section -->
        <div class="menu-section">
          <div class="menu-title">APPS</div>
          <router-link to="/dashboard" class="menu-item" exact-active-class="active">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span>Users</span>
          </router-link>
          <router-link to="/dashboard/listings" class="menu-item" exact-active-class="active">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Listings</span>
          </router-link>
          <router-link to="/dashboard/pending" class="menu-item" exact-active-class="active">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Pending</span>
          </router-link>
          <div class="menu-item-dropdown" :class="{ 'expanded': expandedMenus.includes('dashboard') }">
            <div class="menu-item" @click="toggleMenu('dashboard', $event)">
              <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <span>Dashboard</span>
              <svg class="menu-arrow" :class="{ 'rotated': expandedMenus.includes('dashboard') }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </div>
            <div class="submenu">
              <router-link to="/dashboard/comparison" class="submenu-item" exact-active-class="submenu-active">Comparison</router-link>
              <router-link to="/dashboard/search-analytics" class="submenu-item" exact-active-class="submenu-active">Search Analytics</router-link>
            </div>
          </div>
        </div>
        </div>
      </nav>

      <!-- Main Content -->
      <main class="main-content">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AdminHeader from './AdminHeader.vue'

const expandedMenus = ref<string[]>([])

const toggleMenu = (menu: string, event: MouseEvent) => {
  event.preventDefault()
  const index = expandedMenus.value.indexOf(menu)
  if (index > -1) {
    expandedMenus.value.splice(index, 1)
  } else {
    expandedMenus.value.push(menu)
  }
}
</script>

<style scoped>
.admin-layout {
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
}

.admin-header-container {
  display: flex;
  height: 64px;
  position: relative;
  z-index: 1000;
}

.sidebar-header {
  width: 260px;
  background-color: #1f2937;
  color: #d1d5db;
  display: flex;
  align-items: center;
  border-right: 1px solid #374151;
  flex-shrink: 0;
}

.sidebar-logo {
  padding: 20px;
  display: flex;
  align-items: center;
}

.logo-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.main-header {
  flex: 1;
  background: white;
}

.admin-body {
  display: flex;
  flex: 1;
  overflow: hidden;
}

/* Sidebar Styles */
.sidebar {
  width: 260px;
  background-color: #1f2937;
  color: #d1d5db;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.logo-icon {
  width: 36px;
  height: 36px;
  background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  font-size: 20px;
}

.logo-text {
  font-size: 18px;
  font-weight: 600;
  color: #f9fafb;
}

.sidebar-content {
  padding: 20px 0;
}

.menu-section {
  margin-bottom: 8px;
}

.menu-title {
  font-size: 11px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 8px 20px;
  margin-bottom: 4px;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: #d1d5db;
  transition: all 0.2s;
  cursor: pointer;
  text-decoration: none;
  gap: 12px;
}

.menu-item:hover {
  background-color: #374151;
  color: #f9fafb;
}

.menu-item.active {
  background-color: #3b82f6;
  color: #ffffff;
}

.menu-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.menu-item > span {
  flex: 1;
  font-size: 14px;
}

.badge {
  background-color: #10b981;
  color: white;
  font-size: 10px;
  padding: 2px 8px;
  border-radius: 10px;
  font-weight: 600;
}

.badge.hot {
  background-color: #ef4444;
}

.menu-arrow {
  width: 16px;
  height: 16px;
  transition: transform 0.2s;
}

.menu-arrow.rotated {
  transform: rotate(180deg);
}

.menu-item-dropdown {
  overflow: hidden;
}

.submenu {
  max-height: 0;
  transition: max-height 0.3s ease-out;
  overflow: hidden;
}

.menu-item-dropdown.expanded .submenu {
  max-height: 200px;
}

.submenu-item {
  display: block;
  padding: 8px 20px 8px 52px;
  color: #d1d5db;
  font-size: 13px;
  transition: all 0.2s;
  text-decoration: none;
}

.submenu-item:hover {
  background-color: #374151;
  color: #f9fafb;
}

.submenu-item.submenu-active {
  background-color: #374151;
  color: #3b82f6;
  font-weight: 600;
}

/* Main Content Styles */
.main-content {
  flex: 1;
  background-color: #f3f4f6;
  overflow-y: auto;
  padding: 30px;
}
</style>

