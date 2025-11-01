<template>
  <div class="p-3 border-t">
    <!-- Image preview -->
    <div v-if="selectedImage" class="mb-2 relative inline-block">
      <img :src="selectedImagePreview" alt="Preview" class="max-h-32 rounded border" />
      <button
        @click="$emit('clear-image')"
        class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs"
      >
        ×
      </button>
    </div>
    <div class="flex gap-2 items-end">
      <input
        type="file"
        ref="fileInputRef"
        accept="image/*"
        @change="handleImageSelect"
        class="hidden"
      />
      <button
        @click="triggerFileInput"
        class="bg-gray-200 hover:bg-gray-300 rounded px-3 py-2 text-gray-700"
      >
        📷
      </button>
      <textarea
        :value="draft"
        @input="handleInput"
        @focus="$emit('typing')"
        @keydown.enter.exact.prevent="$emit('send')"
        ref="textareaRef"
        placeholder="Nhập tin nhắn..."
        class="flex-1 border rounded px-3 py-2 min-h-[40px] max-h-[120px] resize-none overflow-y-auto"
        rows="1"
      />
      <button @click="$emit('send')" class="bg-blue-600 text-white rounded px-4 py-2 h-fit">
        Gửi
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  draft: string
  selectedImage: File | null
  selectedImagePreview: string
}>()

const emit = defineEmits<{
  'update:draft': [value: string]
  'image-select': [file: File]
  'clear-image': []
  'send': []
  'typing': []
}>()

const fileInputRef = ref<HTMLInputElement | null>(null)
const textareaRef = ref<HTMLTextAreaElement | null>(null)

function triggerFileInput() {
  fileInputRef.value?.click()
}

function handleImageSelect(e: Event) {
  const input = e.target as HTMLInputElement
  const file = input.files?.[0]
  if (!file) return
  if (file.size > 5 * 1024 * 1024) {
    alert('Ảnh không được vượt quá 5MB')
    return
  }
  emit('image-select', file)
  // Reset input
  if (input) input.value = ''
}

function handleInput(e: Event) {
  const target = e.target as HTMLTextAreaElement
  emit('update:draft', target.value)
  autoResize()
}

function autoResize() {
  const el = textareaRef.value
  if (!el) return
  el.style.height = '0'
  el.style.height = Math.min(el.scrollHeight, 120) + 'px'
}

// Reset textarea height when draft is cleared
watch(() => props.draft, (newVal) => {
  if (!newVal && textareaRef.value) {
    textareaRef.value.style.height = 'auto'
  }
})

defineExpose({
  resetTextarea: () => {
    if (textareaRef.value) {
      textareaRef.value.style.height = 'auto'
    }
  }
})
</script>

