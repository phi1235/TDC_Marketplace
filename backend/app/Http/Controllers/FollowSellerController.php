<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Danh sách Sellers</h1>

    <div v-if="loading">Loading...</div>
    <div v-else>
      <div
        v-for="seller in sellers"
        :key="seller.user_id"
        class="border p-4 mb-2 rounded flex justify-between items-center"
      >
        <div>
          <h2 class="font-semibold">{{ seller.bio || 'Seller #' + seller.user_id }}</h2>
          <p>Rating: {{ seller.rating }} | Total Sales: {{ seller.total_sales }}</p>
        </div>
        <FollowButton :sellerId="seller.user_id" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import FollowButton from './FollowButton.vue';

const sellers = ref([]);
const loading = ref(true);

const fetchSellers = async () => {
  try {
    const res = await axios.get('/api/sellers');
    sellers.value = res.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchSellers);
</script>
