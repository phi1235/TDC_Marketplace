import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

interface Wish {
  id: number
  created_at: string
  updated_at: string
}

interface Pagination {
  data: Wish[]
  links: any[]
}

export const useWishStore = defineStore('wish', () => {
  const wishes = ref<Pagination>({ data: [], links: [] })

  const total = computed(() => wishes.value.data.length)  // tổng wishlist

  const fetchWishes = async (url = 'http://localhost:8001/api/wishes') => {
    try {
      const res = await axios.get(url.startsWith('http') ? url : `http://localhost:8001${url}`)
      wishes.value = res.data
    } catch (error) {
      console.error('Error fetching wishes:', error)
    }
  }

  return { wishes, total, fetchWishes }
})


//button wishlist view_count
export const useWishlistStore = defineStore('wishlist', () => {
  const count = ref(0)

  const setCount = (value: number) => {
    count.value = value
  }

  return { count, setCount }
})