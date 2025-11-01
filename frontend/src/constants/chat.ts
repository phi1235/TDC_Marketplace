/**
 * Chat-related constants
 */

export const CHAT_CONSTANTS = {
  // Polling intervals (milliseconds)
  POLLING_INTERVAL: 2500,
  
  // Typing indicator timeout (milliseconds)
  TYPING_TIMEOUT: 2000,
  
  // File upload limits
  MAX_IMAGE_SIZE: 5 * 1024 * 1024, // 5MB in bytes
  MAX_IMAGE_SIZE_MB: 5,
  
  // Pagination
  DEFAULT_MESSAGES_PER_PAGE: 20,
  INITIAL_MESSAGES_PER_PAGE: 50,
  
  // Unread count display
  MAX_UNREAD_COUNT_DISPLAY: 99,
  
  // WebSocket channels
  CHANNEL_PREFIX: 'chat.',
  USER_CHANNEL_PREFIX: 'user.',
  
  // Message types
  MESSAGE_TYPES: {
    TEXT: 'text',
    IMAGE: 'image',
  } as const,
  
  // Image preview settings
  MAX_IMAGE_PREVIEW_HEIGHT: 300, // pixels
  MODAL_MAX_WIDTH: '90vw',
  MODAL_MAX_HEIGHT: '90vh',
} as const

export type MessageType = typeof CHAT_CONSTANTS.MESSAGE_TYPES[keyof typeof CHAT_CONSTANTS.MESSAGE_TYPES]

