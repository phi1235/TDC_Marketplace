/**
 * Application-wide constants
 */

// API
export const API_CONSTANTS = {
  TIMEOUT: 30000, // 30 seconds
  BASE_URL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8001',
} as const

// File upload
export const FILE_CONSTANTS = {
  MAX_SIZE: {
    IMAGE: 5 * 1024 * 1024, // 5MB
    DOCUMENT: 10 * 1024 * 1024, // 10MB
  },
  ALLOWED_TYPES: {
    IMAGE: ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'],
    DOCUMENT: ['application/pdf', 'application/msword'],
  },
} as const

// Validation
export const VALIDATION_CONSTANTS = {
  MIN_PASSWORD_LENGTH: 6,
  MAX_PASSWORD_LENGTH: 100,
  MAX_EMAIL_LENGTH: 255,
  MAX_NAME_LENGTH: 100,
} as const

// UI
export const UI_CONSTANTS = {
  DEBOUNCE_DELAY: 300, // milliseconds
  TOAST_DURATION: 3000, // milliseconds
  SNACKBAR_DURATION: 4000,
} as const

