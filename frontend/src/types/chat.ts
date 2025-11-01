/**
 * Chat-related TypeScript interfaces and types
 */

export interface User {
  id: number
  name: string
  email?: string
  avatar?: string
}

export interface ConversationParticipant {
  id?: number
  user_id: number
  user?: User
  conversation_id?: number
  last_read_message_id?: number | null
  role_in_convo?: string
}

export interface Conversation {
  id: number
  is_support?: boolean
  last_message_at?: string | null
  unread_count?: number
  _preview?: string
  last_message?: Message
  participants?: ConversationParticipant[]
  created_at?: string
  updated_at?: string
}

export interface Message {
  id: number
  conversation_id: number
  sender_id: number
  type: 'text' | 'image'
  content?: string | null
  meta?: {
    image_url?: string
    image_name?: string
    image_size?: number
    [key: string]: any
  } | null
  read_at?: string | null
  created_at: string
  updated_at?: string
  sender?: User
}

export interface ChatEvent {
  id: number
  conversation_id: number
  sender_id: number
  type: 'text' | 'image'
  content?: string | null
  meta?: Record<string, any> | null
  created_at: string
}

export interface TypingEvent {
  user_id: number
  conversation_id?: number
}

export interface SendMessagePayload {
  type?: 'text' | 'image'
  content?: string
  meta?: Record<string, any>
  image?: File
}

export interface ConversationFilters {
  is_support?: boolean
  user_id?: number
}

export interface MessagePaginationParams {
  per_page?: number
  page?: number
  before_id?: number
  after_id?: number
}

