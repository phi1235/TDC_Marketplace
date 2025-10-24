<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <!-- Main Image Display -->
    <div class="relative aspect-w-16 aspect-h-12 bg-gray-100">
      <img
        v-if="currentImage"
        :src="getImageUrl(currentImage.image_path)"
        :alt="altText"
        class="w-full h-96 object-contain cursor-zoom-in"
        @click="openLightbox(currentIndex)"
      />
      <div v-else class="w-full h-96 flex items-center justify-center text-gray-400">
        <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>

      <!-- Navigation Arrows -->
      <button
        v-if="images.length > 1"
        @click="previousImage"
        class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full p-2 transition-colors"
        aria-label="Previous image"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      
      <button
        v-if="images.length > 1"
        @click="nextImage"
        class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full p-2 transition-colors"
        aria-label="Next image"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>

      <!-- Image Counter -->
      <div
        v-if="images.length > 1"
        class="absolute bottom-4 right-4 bg-black/60 text-white px-3 py-1 rounded-full text-sm"
      >
        {{ currentIndex + 1 }} / {{ images.length }}
      </div>
    </div>

    <!-- Thumbnail Strip -->
    <div v-if="images.length > 1" class="p-4">
      <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
        <button
          v-for="(image, index) in images"
          :key="index"
          @click="currentIndex = index"
          :class="[
            'relative aspect-square rounded-lg overflow-hidden border-2 transition-all',
            currentIndex === index
              ? 'border-blue-500 ring-2 ring-blue-200'
              : 'border-gray-200 hover:border-gray-300'
          ]"
        >
          <img
            :src="getImageUrl(image.image_path)"
            :alt="`${altText} - ${index + 1}`"
            class="w-full h-full object-cover"
          />
        </button>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <Teleport to="body">
      <Transition name="lightbox">
        <div
          v-if="showLightbox"
          class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center"
          @click="closeLightbox"
        >
          <!-- Close Button -->
          <button
            @click="closeLightbox"
            class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10"
            aria-label="Close lightbox"
          >
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Main Image -->
          <div class="relative max-w-7xl max-h-screen p-8" @click.stop>
            <img
              v-if="currentImage"
              :src="getImageUrl(currentImage.image_path)"
              :alt="altText"
              class="max-w-full max-h-[90vh] object-contain mx-auto"
            />

            <!-- Navigation in Lightbox -->
            <button
              v-if="images.length > 1"
              @click.stop="previousImage"
              class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 text-white rounded-full p-3 transition-colors"
              aria-label="Previous image"
            >
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            
            <button
              v-if="images.length > 1"
              @click.stop="nextImage"
              class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 text-white rounded-full p-3 transition-colors"
              aria-label="Next image"
            >
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>

            <!-- Counter in Lightbox -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/60 text-white px-4 py-2 rounded-full">
              {{ currentIndex + 1 }} / {{ images.length }}
            </div>
          </div>

          <!-- Thumbnail Strip in Lightbox -->
          <div
            v-if="images.length > 1"
            class="absolute bottom-8 left-1/2 -translate-x-1/2 max-w-4xl"
            @click.stop
          >
            <div class="flex gap-2 overflow-x-auto px-4">
              <button
                v-for="(image, index) in images"
                :key="index"
                @click="currentIndex = index"
                :class="[
                  'flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all',
                  currentIndex === index
                    ? 'border-white ring-2 ring-white/50'
                    : 'border-white/30 hover:border-white/60'
                ]"
              >
                <img
                  :src="getImageUrl(image.image_path)"
                  :alt="`Thumbnail ${index + 1}`"
                  class="w-full h-full object-cover"
                />
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface ListingImage {
  id?: number
  image_path: string
}

interface Props {
  images: ListingImage[]
  altText?: string
}

const props = withDefaults(defineProps<Props>(), {
  altText: 'Product image'
})

const currentIndex = ref(0)
const showLightbox = ref(false)

const currentImage = computed(() => {
  console.log('🖼️ [ImageGallery] Images:', props.images)
  console.log('🖼️ [ImageGallery] Current index:', currentIndex.value)
  const image = props.images[currentIndex.value] || null
  console.log('🖼️ [ImageGallery] Current image:', image)
  return image
})

const getImageUrl = (path: string) => {
  if (!path) return ''
  
  // If already a full URL, return as is
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path
  }
  
  // Use direct backend URL for now
  const url = `http://localhost:8001/storage/${path}`
  console.log('🖼️ [ImageGallery] Generated URL:', url)
  return url
}

const previousImage = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
  } else {
    currentIndex.value = props.images.length - 1
  }
}

const nextImage = () => {
  if (currentIndex.value < props.images.length - 1) {
    currentIndex.value++
  } else {
    currentIndex.value = 0
  }
}

const openLightbox = (index: number) => {
  currentIndex.value = index
  showLightbox.value = true
  document.body.style.overflow = 'hidden'
}

const closeLightbox = () => {
  showLightbox.value = false
  document.body.style.overflow = ''
}

// Keyboard navigation
const handleKeydown = (e: KeyboardEvent) => {
  if (!showLightbox.value) return
  
  switch (e.key) {
    case 'Escape':
      closeLightbox()
      break
    case 'ArrowLeft':
      previousImage()
      break
    case 'ArrowRight':
      nextImage()
      break
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
  document.body.style.overflow = ''
})
</script>

<style scoped>
.lightbox-enter-active,
.lightbox-leave-active {
  transition: opacity 0.3s ease;
}

.lightbox-enter-from,
.lightbox-leave-to {
  opacity: 0;
}

/* Custom scrollbar for thumbnail strip */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}
</style>

