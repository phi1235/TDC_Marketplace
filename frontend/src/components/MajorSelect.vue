<template>
  <div class="major-select">
    <label v-if="label" :for="inputId" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <div class="relative">
      <select
        :id="inputId"
        v-model.number="selectedMajor"
        @change="handleChange"
        :disabled="disabled || loading"
        :required="required"
        class="block w-full px-4 py-2 pr-10 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
        :class="{ 'border-red-300': error }"
      >
        <!-- Default option -->
        <option :value="null" v-if="allowEmpty">
          {{ placeholder || 'Chọn ngành học' }}
        </option>

        <!-- Loading state -->
        <option :value="null" disabled v-if="loading">
          Đang tải...
        </option>

        <!-- Major options -->
        <option
          v-for="major in majors"
          :key="major.id"
          :value="major.id"
        >
          {{ major.icon }} {{ major.name }}
        </option>
      </select>

      <!-- Icon -->
      <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </div>
    </div>

    <!-- Error message -->
    <p v-if="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </p>

    <!-- Help text -->
    <p v-if="helpText && !error" class="mt-1 text-sm text-gray-500">
      {{ helpText }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { getMajors } from '@/services/majors'
import type { Major } from '@/types/major'

interface Props {
  modelValue?: number | null
  label?: string
  placeholder?: string
  helpText?: string
  required?: boolean
  disabled?: boolean
  allowEmpty?: boolean // Allow "no major" selection
  error?: string
}

interface Emits {
  (e: 'update:modelValue', value: number | null): void
  (e: 'change', major: Major | null): void
}

const props = withDefaults(defineProps<Props>(), {
  allowEmpty: true,
  required: false,
  disabled: false
})

const emit = defineEmits<Emits>()

// Generate unique ID for accessibility
const inputId = computed(() => `major-select-${Math.random().toString(36).substr(2, 9)}`)

// State
const majors = ref<Major[]>([])
const loading = ref(false)
const selectedMajor = ref<number | null>(null)

// Load majors on mount
onMounted(async () => {
  await loadMajors()
})

// Watch modelValue changes from parent
watch(() => props.modelValue, (newValue) => {
  selectedMajor.value = newValue ?? null
}, { immediate: true })

// Load majors from API
const loadMajors = async () => {
  try {
    loading.value = true
    majors.value = await getMajors()
  } catch (error) {
    console.error('Failed to load majors:', error)
    // Fallback to empty array if API fails
    majors.value = []
  } finally {
    loading.value = false
  }
}

// Handle selection change
const handleChange = () => {
  const majorId = selectedMajor.value
  const selectedMajorData = majorId ? majors.value.find(m => m.id === majorId) : null
  
  emit('update:modelValue', majorId)
  emit('change', selectedMajorData ?? null)
}

// Expose loadMajors for manual refresh
defineExpose({
  loadMajors
})
</script>

<style scoped>
.major-select select {
  appearance: none;
  background-image: none;
}

/* Hover effect */
.major-select select:not(:disabled):hover {
  border-color: #9ca3af;
}

/* Focus effect */
.major-select select:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Disabled state */
.major-select select:disabled {
  opacity: 0.6;
}
</style>
