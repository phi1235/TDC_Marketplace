export type ToastType = 'success' | 'error' | 'info' | 'warning'

export function showToast(type: ToastType, message: string) {
  const root = document.getElementById('toast-root')
  if (!root) return

  const el = document.createElement('div')
  el.className = `min-w-[280px] max-w-sm px-4 py-3 rounded shadow text-white flex items-start gap-3 animate-fade-in`

  const colors: Record<ToastType, string> = {
    success: 'bg-green-600',
    error: 'bg-red-600',
    info: 'bg-blue-600',
    warning: 'bg-yellow-600',
  }

  const colorClass = colors[type] || colors.info
  el.classList.add(...colorClass.split(' '))
  el.innerHTML = `<div class="font-medium">${message}</div>`

  root.appendChild(el)

  setTimeout(() => {
    el.classList.add('opacity-0')
    el.addEventListener('transitionend', () => el.remove())
  }, 2500)
}


