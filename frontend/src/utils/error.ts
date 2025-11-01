/**
 * Error handling utilities
 */

export interface AppError {
  message: string
  code?: string
  status?: number
  details?: any
}

/**
 * Extract error message from various error types
 */
export function getErrorMessage(error: unknown): string {
  if (error instanceof Error) {
    return error.message
  }
  
  if (typeof error === 'string') {
    return error
  }
  
  // Axios error
  if (error && typeof error === 'object' && 'response' in error) {
    const axiosError = error as any
    return axiosError.response?.data?.message 
      || axiosError.response?.data?.error
      || axiosError.message
      || 'Đã xảy ra lỗi không xác định'
  }
  
  return 'Đã xảy ra lỗi không xác định'
}

/**
 * Log error with context
 */
export function logError(error: unknown, context?: string): void {
  const message = getErrorMessage(error)
  const errorObj: AppError = {
    message,
    ...(context && { details: { context } })
  }
  
  // Console error for development
  console.error('[Error]', context ? `${context}:` : '', errorObj, error)
  
  // In production, you would send to error tracking service
  // e.g., Sentry.captureException(error, { extra: errorObj })
}

/**
 * Handle and display error to user
 */
export function handleError(error: unknown, context?: string): string {
  logError(error, context)
  return getErrorMessage(error)
}

