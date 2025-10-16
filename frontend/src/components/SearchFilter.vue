<template>
  <v-container class="py-8">
    <v-row>
      <!-- 🔍 Bộ tìm kiếm nâng cao -->
      <v-col cols="12" md="3">
        <v-card elevation="2" class="pa-4">
          <h2 class="text-h6 font-weight-bold mb-4">Tìm kiếm & Bộ lọc</h2>

          <!-- Tìm theo từ khóa -->
          <v-text-field
            v-model="filters.keyword"
            label="Tìm kiếm sản phẩm"
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            dense
            clearable
          />

          <!-- Danh mục -->
          <v-select
            v-model="filters.category"
            :items="categories"
            label="Danh mục"
            prepend-inner-icon="mdi-shape"
            variant="outlined"
            dense
            clearable
          />

          <!-- Giá -->
          <div class="mt-4">
            <label class="font-weight-medium mb-2 d-block">Khoảng giá (VNĐ)</label>
            <v-range-slider
              v-model="filters.price"
              :max="1000000"
              :min="0"
              step="50000"
              thumb-label
              color="primary"
            ></v-range-slider>
            <div class="text-caption">
              {{ filters.price[0].toLocaleString() }}₫ - {{ filters.price[1].toLocaleString() }}₫
            </div>
          </div>

          <!-- Sắp xếp -->
          <v-select
            v-model="filters.sort"
            :items="sortOptions"
            label="Sắp xếp theo"
            prepend-inner-icon="mdi-sort"
            variant="outlined"
            dense
            class="mt-4"
          />

          <!-- Nút áp dụng -->
          <v-btn
            color="primary"
            class="mt-6"
            block
            @click="applyFilters"
          >
            Áp dụng bộ lọc
          </v-btn>
        </v-card>
      </v-col>

      <!-- 🧾 Kết quả tìm kiếm -->
      <v-col cols="12" md="9">
        <v-card class="pa-4 mb-4" elevation="2">
          <h2 class="text-h6 font-weight-bold mb-4">
            Kết quả: {{ filteredItems.length }} sản phẩm
          </h2>

          <v-row>
            <v-col
              v-for="item in filteredItems"
              :key="item.id"
              cols="12"
              sm="6"
              md="4"
            >
              <v-card class="hover:shadow-lg transition" elevation="1">
                <v-img
                  :src="item.image"
                  height="160"
                  cover
                ></v-img>
                <v-card-title class="text-subtitle-1 font-weight-bold">
                  {{ item.name }}
                </v-card-title>
                <v-card-subtitle class="text-grey-darken-1">
                  {{ item.category }}
                </v-card-subtitle>
                <v-card-text class="font-weight-bold text-primary">
                  {{ item.price.toLocaleString() }}₫
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue'

const filters = ref({
  keyword: '',
  category: null,
  price: [0, 500000],
  sort: 'newest',
})

const categories = ['Sách giáo khoa', 'Điện tử', 'Đồ dùng học tập', 'Quần áo']
const sortOptions = [
  { title: 'Mới nhất', value: 'newest' },
  { title: 'Giá tăng dần', value: 'asc' },
  { title: 'Giá giảm dần', value: 'desc' },
]

// 🧩 Dữ liệu mẫu
const items = ref([
  { id: 1, name: 'Sách Toán 12', category: 'Sách giáo khoa', price: 40000, image: 'https://picsum.photos/300/200?1' },
  { id: 2, name: 'Laptop Dell cũ', category: 'Điện tử', price: 3500000, image: 'https://picsum.photos/300/200?2' },
  { id: 3, name: 'Bút bi Thiên Long', category: 'Đồ dùng học tập', price: 5000, image: 'https://picsum.photos/300/200?3' },
  { id: 4, name: 'Áo khoác Khoa CNTT', category: 'Quần áo', price: 120000, image: 'https://picsum.photos/300/200?4' },
])

// ⚙️ Lọc dữ liệu theo bộ lọc
const filteredItems = computed(() => {
  let results = items.value.filter(i =>
    i.name.toLowerCase().includes(filters.value.keyword.toLowerCase())
  )

  if (filters.value.category) {
    results = results.filter(i => i.category === filters.value.category)
  }

  results = results.filter(
    i => i.price >= filters.value.price[0] && i.price <= filters.value.price[1]
  )

  if (filters.value.sort === 'asc') {
    results.sort((a, b) => a.price - b.price)
  } else if (filters.value.sort === 'desc') {
    results.sort((a, b) => b.price - a.price)
  }

  return results
})

const applyFilters = () => {
  console.log('Applied filters:', filters.value)
}
</script>
