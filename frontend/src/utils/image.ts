const BACKEND_URL = (import.meta as any).env?.VITE_BACKEND_URL || 'http://localhost:8001'

export function imageUrl(path?: string): string {
  if (!path) return ''
  // If already absolute
  if (path.startsWith('http://') || path.startsWith('https://')) return path
  // Normalize path
  const clean = path.replace(/^\/+/, '')
  // Prefix with backend host to avoid Vite serving /storage
  return `${BACKEND_URL}/storage/${clean.replace(/^storage\//, '')}`
}
