<script setup lang="ts">
import { ref, watch, reactive, onMounted, computed } from 'vue'
import { getAllUsers, searchUsers } from '@/services/user';
import AdvancedFilter from '@/components/AdvancedFilterUsers.vue'

//AdvancedFilter
function applyAdvancedFilter(newFilters: any) {
  // newFilters chính là dữ liệu realtime từ popup
  appliedFilter.value = newFilters;
}

// ---------- types ----------
type User = {
  id: number;
  name: string;
  email: string;
  email_verified_at: string | null;
  phone?: string | null;
  avatar?: string | null;
  role: string;
  is_active: boolean | number;
  last_login_at: string | null;
  login_count: number;
  created_at: string | null;
};

// ---------- data ----------
const users = ref<User[]>([]);
const keyword = ref('');
const selectedRole = ref('all'); // basic dropdown (all, user, admin, active)
const showAdvanced = ref(false);
const advancedFilterApplied = ref(false);

// fetch all users
const fetchUsers = async () => {
  try {
    users.value = await getAllUsers();
  } catch (error) {
    console.error('Error fetching users:', error);
  }
};

// search API
const search = async () => {
  try {
    users.value = await searchUsers(keyword.value);
  } catch (error) {
    console.error('Error searching users:', error);
  }
};

onMounted(() => {
  fetchUsers();
});

// ---------- ADVANCED FILTER STATE ----------
const advancedFilter = reactive({
  // Block 1: Account Status
  role: 'all' as string,             // all | user | admin
  is_active: 'all' as string,        // all | active | inactive
  email_verified: 'all' as string,   // all | verified | unverified

  // Block 2: Engagement & Behavior
  login_count_op: '>' as string,     // > | < | =
  login_count_value: null as number | null,
  last_login_preset: 'all' as string, // all | 7 | 30 | never

  // Block 3: Created Date
  created_from: '' as string,
  created_to: '' as string,
  created_preset: 'none' as string, // none | today | 7 | 30 | older_year
});

// copy of applied filter (so user can cancel / clear)
const appliedFilter = ref<Record<string, any> | null>(null);

// Watchers: if user wants "auto apply" (you chose 2), we'll apply every change.
// Because you selected 2 (realtime), we update appliedFilter on change.
watch(advancedFilter, () => {
  // apply in realtime
  appliedFilter.value = JSON.parse(JSON.stringify(advancedFilter));
  advancedFilterApplied.value = isFilterActive(appliedFilter.value);
}, { deep: true });

// helper to check if filter isn't default
function isFilterActive(f: Record<string, any> | null) {
  if (!f) return false;
  // any non-default value => active
  if (f.role !== 'all') return true;
  if (f.is_active !== 'all') return true;
  if (f.email_verified !== 'all') return true;
  if (f.login_count_min !== null && f.login_count_min !== '') return true;
  if (f.last_login_preset !== 'all') return true;
  if (f.created_from || f.created_to || f.created_preset !== 'none') return true;
  return false;
}

// clear advanced filter
function clearAdvancedFilter() {
  advancedFilter.role = 'all';
  advancedFilter.is_active = 'all';
  advancedFilter.email_verified = 'all';
  advancedFilter.login_count_op = '>';
  advancedFilter.login_count_value = null;
  advancedFilter.last_login_preset = 'all';
  advancedFilter.created_from = '';
  advancedFilter.created_to = '';
  advancedFilter.created_preset = 'none';

  appliedFilter.value = null;
  advancedFilterApplied.value = false;
}

// If you prefer manual apply button instead of realtime: comment out the watch() above and use this function
function applyAdvancedFilterManual() {
  appliedFilter.value = JSON.parse(JSON.stringify(advancedFilter));
  advancedFilterApplied.value = isFilterActive(appliedFilter.value);
  showAdvanced.value = false;
}

// ---------- FILTERING LOGIC (computed) ----------
const filteredUsers = computed(() => {
  // start from original users array
  let list = users.value.slice();

  // first apply basic selectedRole dropdown (original logic)
  if (selectedRole.value === 'active') {
    list = list.filter(u => Boolean(u.is_active));
  } else if (selectedRole.value !== 'all') {
    list = list.filter(u => u.role === selectedRole.value);
  }

  // next apply advanced filter if present
  const f = appliedFilter.value;
  if (!f) return list;
  // trước khi return list.filter(...)
  console.log('Applied filter:', appliedFilter.value);
  console.log('Sample user login_counts:', users.value.slice(0, 5).map(u => ({ id: u.id, login_count: u.login_count })));

  return list.filter(user => {
    // Block 1: Account Status
    if (f.role && f.role !== 'all') {
      if (user.role !== f.role) return false;
    }

    if (f.is_active && f.is_active !== 'all') {
      const wantActive = f.is_active === 'active';
      if (Boolean(user.is_active) !== wantActive) return false;
    }

    if (f.email_verified && f.email_verified !== 'all') {
      const verified = user.email_verified_at !== null && user.email_verified_at !== undefined;
      if (f.email_verified === 'verified' && !verified) return false;
      if (f.email_verified === 'unverified' && verified) return false;
    }

    //login count
    // Block 2: Engagement
    if (f.login_count_min !== null && f.login_count_min !== '' && !isNaN(Number(f.login_count_min))) {
      const val = Number(f.login_count_min);
      const count = Number(user.login_count || 0);
      const op = f.login_count_op || '>'; // default >
      if (op === '>') {
        if (!(count > val)) return false;
      } else if (op === '<') {
        if (!(count < val)) return false;
      } else { // '='
        if (!(count === val)) return false;
      }
    }

    console.log('User last login:', user.last_login_at)
    //last login
    if (f.last_login && f.last_login !== 'all') {
      const now = new Date();
      const last = user.last_login_at ? new Date(user.last_login_at) : null;

      if (f.last_login === 'never') {
        if (last !== null) return false; // chỉ lấy user chưa login lần nào
      } else if (f.last_login === '7d') {
        if (!last) return false;
        const diffDays = (now.getTime() - last.getTime()) / (1000 * 60 * 60 * 24);
        if (diffDays > 7) return false; // last login cách đây hơn 7 ngày => bỏ
      } else if (f.last_login === '30d') {
        if (!last) return false;
        const diffDays = (now.getTime() - last.getTime()) / (1000 * 60 * 60 * 24);
        if (diffDays > 30) return false;
      }
    }

    //created date
    if ((f.created_from && f.created_from !== '') || (f.created_to && f.created_to !== '')) {
      const created = user.created_at ? new Date(user.created_at) : null;
      if (!created) return false;

      if (f.created_from && f.created_from !== '') {
        const from = new Date(f.created_from);
        // set giờ từ đầu ngày
        from.setHours(0, 0, 0, 0);
        if (created < from) return false;
      }

      if (f.created_to && f.created_to !== '') {
        const to = new Date(f.created_to);
        // set giờ đến cuối ngày
        to.setHours(23, 59, 59, 999);
        if (created > to) return false;
      }
    }

    // Quick preset
    if (f.created_preset && f.created_preset !== 'none') {
      const created = user.created_at ? new Date(user.created_at) : null;
      if (!created) return false;

      const now = new Date();
      if (f.created_preset === 'today') {
        const start = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const end = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59, 999);
        if (created < start || created > end) return false;
      } else if (f.created_preset === '7') {
        const cutoff = new Date();
        cutoff.setDate(now.getDate() - 7);
        if (created < cutoff) return false;
      } else if (f.created_preset === '30') {
        const cutoff = new Date();
        cutoff.setDate(now.getDate() - 30);
        if (created < cutoff) return false;
      } else if (f.created_preset === 'older_year') {
        const cutoff = new Date();
        cutoff.setFullYear(now.getFullYear() - 1);
        if (created > cutoff) return false;
      }
    }

// passed all checks
return true;
  });
});

// small helper to show preview count (fast)
const previewCount = computed(() => filteredUsers.value.length);

// optional: if you want to send the advanced filter to backend as JSON when applied, prepare payload
function getAdvancedFilterPayload() {
  return appliedFilter.value ? JSON.parse(JSON.stringify(appliedFilter.value)) : null;
}
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
        <li class="item"><router-link to="/dashboard/reports">REPORTS</router-link></li>
        <li class="item"><router-link to="/dashboard/analytics">ANALYTICS</router-link></li>
        <li class="item"><router-link to="/dashboard/monitoring">MONITORING</router-link></li>
        <li class="item"><router-link to="/dashboard/audit-logs">AUDIT LOGS</router-link></li>
      </ul>
    </nav>

    <!-- CONTENT -->
    <main class="content">
      <!-- Chỉ hiển thị danh sách người dùng khi ở route /dashboard -->
      <div v-if="$route.path === '/dashboard'">
        <div class="func">
          <div class="total">Tổng số: <b>{{ users.length }}</b></div>
          <div class="search">
            <input v-model="keyword" @keyup.enter="search" type="search" placeholder="Tìm kiếm..." />
            <!-- Button -->
            <button id="btn_search" @click="search" class="bg-blue-500 text-white rounded-r-lg hover:bg-blue-600">
              Tìm
            </button>
          </div>

          <!-- <div class="filter">
            <label for="role">Bộ lọc:</label>
            <select id="role" v-model="selectedRole" name="role">
              <option value="all">Tất cả</option>
              <option value="user">User</option>
              <option value="admin">Admin</option>
              <option value="active">Active</option>
            </select>
          </div> -->
          <div class="filter flex items-center gap-2">
            <label class="font-medium">Bộ lọc:</label>
            <select id="role" v-model="selectedRole" name="role" class="border rounded px-3 py-2">
              <option value="all">Tất cả</option>
              <option value="user">User</option>
              <option value="admin">Admin</option>
              <option value="active">Active</option>
            </select>
            <!-- nút mở advanced filter -->
            <button @click="showAdvanced = true" class="btn-primary">Nâng cao</button>
          </div>
          <AdvancedFilter :visible="showAdvanced" @update:visible="val => showAdvanced = val"
            @filter-change="applyAdvancedFilter" />


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
                <th>Created At</th>
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
                <td class="px-2 py-1 border">{{ user.created_at }}</td>
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
.func {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

.btn-primary {
  background: #2563eb;
  color: #fff;
  padding: 10px 14px;
  border-radius: 8px;
}

.badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  color: #fff;
}

.badge.pending {
  background-color: #f59e0b;
}

.badge.approved {
  background-color: #10b981;
}

.badge.rejected {
  background-color: #ef4444;
}
</style>

<style scoped>
/* RESET */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

ul,
li,
a {
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
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
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

th,
td {
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


#btn_search {
  padding: 1px 10px;
}

=======.btn-primary {
  background: #2563eb;
  color: #fff;
  padding: 10px 14px;
  border-radius: 8px;
}
</style>
