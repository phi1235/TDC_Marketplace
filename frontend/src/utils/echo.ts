import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { useAuthStore } from '@/stores/auth'

declare global {
  interface Window {
    Pusher: typeof Pusher
    Echo: Echo
  }
}

let echoInstance: Echo | null = null

export function initEcho(): Echo {
  if (echoInstance) return echoInstance
  
  window.Pusher = Pusher
  
  echoInstance = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || 'app-key',
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
    wsPort: parseInt(import.meta.env.VITE_PUSHER_PORT || '6001'),
    wssPort: parseInt(import.meta.env.VITE_PUSHER_PORT || '6001'),
    forceTLS: false,
    encrypted: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    authorizer: (channel: any, options: any) => ({
      authorize: (socketId: string, callback: Function) => {
        const authStore = useAuthStore()
        const token = authStore.token
        if (!token) {
          callback(true, {})
          return
        }
        // Call Laravel auth endpoint for private channels
        fetch(`/api/broadcasting/auth`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          body: JSON.stringify({
            socket_id: socketId,
            channel_name: channel.name,
          }),
        })
          .then(response => response.json())
          .then(data => callback(false, data))
          .catch(error => callback(true, error))
      },
    }),
  })
  
  return echoInstance
}

export function getEcho(): Echo | null {
  return echoInstance
}

export function disconnectEcho() {
  if (echoInstance) {
    echoInstance.disconnect()
    echoInstance = null
  }
}

