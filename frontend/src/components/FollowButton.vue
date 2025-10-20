<template>
  <button v-if="user" :class="buttonClass" @click="toggleFollow">
    {{ isFollow ? 'Unfollow' : 'Follow' }}
  </button>
  <span v-else>Login to follow</span>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
  sellerId: Number,
  user: Object
});

const isFollow = ref(false);

const fetchStatus = async () => {
  if (!props.user) return;
  try {
    const res = await axios.get(`/api/follow-status/${props.sellerId}`);
    isFollow.value = res.data.is_follow;
  } catch (err) {
    console.error(err);
  }
};

const toggleFollow = async () => {
  if (!props.user) return;
  try {
    const res = await axios.post('/api/follow-toggle', { seller_id: props.sellerId });
    isFollow.value = res.data.status === 'followed';
  } catch (err) {
    console.error(err);
  }
};

onMounted(fetchStatus);

const buttonClass = computed(() => ({
  'px-4 py-2 rounded': true,
  'bg-blue-500 text-white': !isFollow.value,
  'bg-gray-300 text-black': isFollow.value
}));
</script>
