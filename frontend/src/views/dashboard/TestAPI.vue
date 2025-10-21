<script setup>
    import { ref, onMounted } from 'vue';
    
    //
    const testapi = ref([]);
    onMounted(async () => {
        const res = await fetch('http://localhost:8001/api/testapi');
        testapi.value = await res.json();
    });
    //form add
    const showAddForm = ref(true);
    const newItem = ref({ name: '', email: '', description: '', age: '' });
    //
    const toggleAddForm = () =>{
        showAddForm.value = !showAddForm.value;
    }
    const addInf = async () => {
      if(!newItem.value.name || !newItem.value.email){
        alert('Vui lòng nhập Name và Email');
        return;
      }
      //
      try {
        const res = await fetch('http://localhost:8001/api/testapi', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(newItem.value)
        });

          if (!res.ok) throw new Error('Lỗi khi gửi dữ liệu!');

          const data = await res.json();

          //them data
          testapi.value.push(data);

          // reset form
          newItem.value = { name: '', email: '', description: '', age: '' };
          showAddForm.value = false;
      } catch (err) {
        console.error(err);
        alert('Gửi dữ liệu thất bại!');
      }
    }
</script>   

<template>
  <div class="container">
    <h1 class="title">TEST API</h1>
    <!-- Nút Thêm mới -->
    <div class="top-bar">
      <button class="add-btn" @click="toggleAddForm">Thêm mới</button>
    </div>
    <div v-if="showAddForm" class="add-from">
        <input v-model="newItem.name" type="text" placeholder="Name" >
        <input v-model="newItem.email" type="email" placeholder="Email">
        <input v-model="newItem.description" type="text" placeholder="Description">
        <input v-model="newItem.age" type="number" placeholder="Age">
        <button class="btn-add" @click="addInf" >Add</button>
    </div>
    <!--  -->
    <table class="api-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Description</th>
          <th>Age</th>
          <th>Created At</th>
          <th>Updated At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="api in testapi" :key="api.id">
          <td>{{ api.id }}</td>
          <td>{{ api.name }}</td>
          <td>{{ api.email }}</td>
          <td>{{ api.description }}</td>
          <td>{{ api.age }}</td>
          <td>{{ api.created_at }}</td>
          <td>{{ api.updated_at }}</td>
          <td>
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.container {
  max-width: 1200px;
  margin: 40px auto;
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.title {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
}

/* Nút Thêm mới */
.add-btn {
  background-color: #007BFF;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
  margin-bottom: 5px;
}

.add-btn:hover {
  background-color: #0056b3;
  transform: scale(1.05);
}

.api-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.api-table th, .api-table td {
  padding: 12px;
  text-align: center;
}

.api-table thead {
  background-color: #007BFF;
  color: white;
}

.api-table tbody tr {
  border-bottom: 1px solid #eee;
  transition: background 0.2s;
}

.api-table tbody tr:hover {
  background-color: #f1f7ff;
}

/* Nút chỉnh sửa */
.edit-btn {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 6px 12px;
  margin-right: 5px;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.edit-btn:hover {
  background-color: #45a049;
  transform: scale(1.05);
}

/* Nút xóa */
.delete-btn {
  background-color: #f44336;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.delete-btn:hover {
  background-color: #da190b;
  transform: scale(1.05);
}
</style>