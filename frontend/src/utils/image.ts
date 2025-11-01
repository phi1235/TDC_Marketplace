/**
 * Image utility functions
 */

/**
 * Normalize image URL to absolute path
 * Handles relative paths, storage paths, and full URLs
 */
export function getImageUrl(url: string | null | undefined): string {
  if (!url) return ''
  
  // Already absolute URL
  if (url.startsWith('http://') || url.startsWith('https://')) {
    return url
  }
  
  // Storage path starting with /storage/
  if (url.startsWith('/storage/')) {
    return url
  }
  
  // Contains storage/ but might be relative
  if (url.includes('storage/')) {
    const path = url.includes('/storage/') ? url.split('/storage/')[1] : url
    return `/storage/${path}`
  }
  
  // Relative path starting with /
  if (url.startsWith('/')) {
    return url
  }
  
  // Plain relative path
  return `/${url}`
}

/**
 * Validate image file
 */
export function validateImageFile(file: File, maxSize: number = 5 * 1024 * 1024): {
  valid: boolean
  error?: string
} {
  if (!file.type.startsWith('image/')) {
    return {
      valid: false,
      error: 'File phải là hình ảnh'
    }
  }
  
  if (file.size > maxSize) {
    const maxSizeMB = maxSize / (1024 * 1024)
    return {
      valid: false,
      error: `Ảnh không được vượt quá ${maxSizeMB}MB`
    }
  }
  
  return { valid: true }
}

/**
 * Create image preview from file
 */
export function createImagePreview(file: File): Promise<string> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    
    reader.onload = (e) => {
      const result = e.target?.result
      if (typeof result === 'string') {
        resolve(result)
      } else {
        reject(new Error('Failed to create image preview'))
      }
    }
    
    reader.onerror = () => {
      reject(new Error('Failed to read image file'))
    }
    
    reader.readAsDataURL(file)
  })
}
