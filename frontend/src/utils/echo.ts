import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import api from '@/services/api'
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
      authorize: async (socketId: string, callback: Function) => {
        try {
          console.log('[Echo authorize] channel=', channel?.name, 'socket=', socketId)
          const bearer = localStorage.getItem('auth_token')
          const res = await api.post(
            '/broadcasting/auth',
            {
              socket_id: socketId,
              channel_name: channel.name,
            },
            bearer
              ? { headers: { Authorization: `Bearer ${bearer}` } }
              : undefined
          )
          callback(false, res.data)
        } catch (err) {
          console.error('Broadcast auth error:', err)
          try { console.error('Broadcast auth response:', (err as any)?.response?.data) } catch {}
          callback(true, err)
        }
      },
    }),
  })
  // Expose globally for debugging
  window.Echo = echoInstance
  
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

