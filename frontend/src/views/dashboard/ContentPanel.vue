<template>
  <div>
    <h1>Danh sách người dùng</h1>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Tên</th>
          <th>Email</th>
          <th>Roles</th>
          <th>Trạng thái</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.roles.map(r => r.name).join(', ') }}</td>
          <td>{{ user.is_active ? 'Active' : 'Inactive' }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import { userService } from '@/services/user'

export default defineComponent({
  name: 'Panel',
  setup() {
    const users = ref([])

    const fetchUsers = async () => {
      try {
        const response = await userService.getAllUsers()
        users.value = response.data
      } catch (error) {
        console.error('Error fetching users:', error)
      }
    }

    onMounted(() => {
      fetchUsers()
    })

    return {
      users
    }
  }
})
</script>
