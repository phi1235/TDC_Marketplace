<script setup lang="ts">
import {ref, onMounted} from 'vue'
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
//
onMounted(() => {
  fetchUsers();
})
// export default {
//   name: 'DashboardView',
// };


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
        <li class="item active"><a href="#">USERS</a></li>
        <li class="item"><a href="#">LISTINGS</a></li>
        <li class="item"><a href="#">PENDING</a></li>
        <li class="item"><a href="#">REPORTS</a></li>
      </ul>
    </nav>

    <!-- CONTENT -->
    <main class="content">
      <div class="func">
        <div class="total">Tổng số: <b>{{ users.length }}</b></div>

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
          <select id="role" name="role">
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
            <!-- <tr>
              <td>1</td>
              <td>#001</td>
              <td>Trương Tuấn Dũng</td>
              <td>dung@example.com</td>
              <td>Admin</td>
              <td>0909123456</td>
              <td><img src="https://via.placeholder.com/40" /></td>
              <td>✅</td>
              <td>2025-10-15</td>
              <td>24</td>
            </tr> -->
            <tr v-for="(user, index) in users" :key="index">
              <td>{{ index + 1}}</td>
              <td>{{ user.id }}</td>
              <td>{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.role }}</td>
              <td>{{ user.phone }}</td>
              <td>{{ user.avatar }}</td>
              <td>{{ user.is_active }}</td>
              <td>{{ user.last_login_at }}</td>
              <td>{{ user.login_count }}</td>
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
    </main>
  </div>
</template>

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
</style>
