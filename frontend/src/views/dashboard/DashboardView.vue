<script setup lang="ts">
import {ref, onMounted, computed } from 'vue'
import { getAllUsers, searchUsers} from '@/services/user';
//
const users = ref<User[]>([]);

//get api all user
const fetchUsers = async() => {
  try{
    users.value = await getAllUsers();
    for(let i = 0; i < users.value.length; i++){
      console.log(users.value[i]);
    }
  }catch(error){
    console.error('Error fetching users:', error)
  }
}

//getapi search
const keyword = ref('');

const search = async () => {
  try {
    users.value = await searchUsers(keyword.value)
  } catch (error) {
    console.error('Error searching users:', error)
  }
}
//onMounted: gọi hàm lên, hay dùng gọi api
onMounted(() => {
  fetchUsers();
})

// Computed filtered theo dropdown
const selectedRole = ref('all');
const filteredUsers = computed(() => {
  // all user
  if (selectedRole.value === 'all') return users.value
  //user is_active
  if (selectedRole.value === 'active') {
    return users.value.filter(user => user.is_active)
  }
  //filter() lọc dữ liệu mảng, trả về dữ liệu mới
  //role 1 là admin 2 là user, user.role trả về 2 cái
  return users.value.filter(user => user.role === selectedRole.value)
})


</script>
<template>
  <div class="dashboard">
    <!-- HEADER -->
    <!-- <header class="header">
      <h1>Header</h1>
    </header> -->
    <!-- NAVBAR -->
    <nav class="navbar">
      <h2 class="title">DASHBOARD</h2>
      <ul class="list-items">
        <li class="item"><router-link to="/dashboard">USERS</router-link></li>
        <li class="item"><router-link to="/dashboard/listings">LISTINGS</router-link></li>
        <li class="item"><router-link to="/dashboard/pending">PENDING</router-link></li>
        <li class="item"><a href="#">REPORTS</a></li>
      </ul>
    </nav>

    <!-- CONTENT -->
    <main class="content">
      <!-- Chỉ hiển thị danh sách người dùng khi ở route /dashboard -->
      <div v-if="$route.path === '/dashboard'">
        <div class="func">
          <div class="total">Tổng số: <b>{{ users.length }}</b></div>s không hiển

          <div class="search">
            <input v-model="keyword" @keyup.enter="search" type="search" placeholder="Tìm kiếm..." />
                <!-- Button -->
            <button id="btn_search"
              @click="search"
              class="bg-blue-500 text-white rounded-r-lg hover:bg-blue-600">
              Tìm
            </button>
          </div>

          <div class="filter">
            <label for="role">Bộ lọc:</label>
            <select id="role" v-model="selectedRole" name="role">
              <option value="all">Tất cả</option>
              <option value="user">User</option>
              <option value="admin">Admin</option>
              <option value="active">Active</option>
            </select>
          </div>
        </div>

        <div class="inf">
          <h2>Danh sách người dùng</h2>
          <table>
            <thead>
              <tr>
                <th>STT</th>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Role</th>
                <th>Phone</th>
                <th>Avatar</th>
                <th>Active</th>
                <th>Last Login</th>
                <th>Login Count</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, index) in filteredUsers" :key="user.id">
            <td class="px-2 py-1 border">{{ index + 1 }}</td>
            <td class="px-2 py-1 border">{{ user.id }}</td>
            <td class="px-2 py-1 border">{{ user.name }}</td>
            <td class="px-2 py-1 border">{{ user.email }}</td>
            <td class="px-2 py-1 border">{{ user.role }}</td>
            <td class="px-2 py-1 border">{{ user.phone }}</td>
            <td class="px-2 py-1 border">
              <img :src="user.avatar" alt="avatar" class="w-10 h-10 rounded-full" />
            </td>
            <td class="px-2 py-1 border">{{ user.is_active ? 'Active' : 'Inactive' }}</td>
            <td class="px-2 py-1 border">{{ user.last_login_at }}</td>
            <td class="px-2 py-1 border">{{ user.login_count }}</td>
          </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div class="pagination">
            <button class="page-btn prev" disabled>« Trước</button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn next">Sau »</button>
          </div>
        </div>
      </div>

      <!-- Hiển thị component con khi ở route con -->
      <router-view v-else />
    </main>
  </div>
</template>

<style scoped>
.badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  color: #fff;
}
.badge.pending { background-color: #f59e0b; }
.badge.approved { background-color: #10b981; }
.badge.rejected { background-color: #ef4444; }
</style>

<style scoped>
/* RESET */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
ul, li, a {
  list-style: none;
  text-decoration: none;
  color: inherit;
}

/* --- LAYOUT CHUNG --- */
.dashboard {
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
}

/* HEADER */
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 70px;
  background-color: #fff;
  border-bottom: 1px solid #ddd;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
}

/* NAVBAR */
.navbar {
  position: fixed;
  top: 70px;
  left: 0;
  bottom: 0;
  width: 220px;
  background-color: #1f2937;
  color: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 40px;
  z-index: 99;
}

.navbar .title {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 30px;
}

.navbar .list-items {
  width: 100%;
}

.navbar .item {
  padding: 15px 0;
  width: 100%;
  text-align: center;
  transition: 0.3s;
}

.navbar .item:hover {
  background-color: #374151;
}

.navbar .item.active {
  background-color: #3b82f6;
}

/* --- CONTENT --- */
.content {
  margin-top: 70px;
  margin-left: 220px;
  padding: 30px;
  background-color: #f5f6fa;
  width: calc(100vw - 220px);
  height: calc(100vh - 70px);
  overflow-y: auto;
}

.func {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

/* --- BẢNG --- */
.inf {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.inf h2 {
  font-size: 22px;
  margin-bottom: 20px;
}

table {
  width: 100%;
  border-collapse: collapse;
  text-align: center;
}

thead {
  background-color: #3b82f6;
  color: white;
}

th, td {
  padding: 12px 10px;
  border-bottom: 1px solid #e5e7eb;
}

/* --- PAGINATION --- */
.pagination {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin: 25px 0;
}

.page-btn {
  background-color: #f3f4f6;
  border: 1px solid #d1d5db;
  color: #374151;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.page-btn:hover {
  background-color: #e5e7eb;
}

.page-btn.active {
  background-color: #3b82f6;
  color: white;
  border-color: #2563eb;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}


#btn_search{
  padding: 1px 10px;
}
=======
.btn-primary { background:#2563eb; color:#fff; padding:10px 14px; border-radius:8px; }

</style>
