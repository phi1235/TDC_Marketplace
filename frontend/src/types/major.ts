/**
 * Major (Ngành học) Interface
 * Represents academic majors/departments at TDC
 */
export interface Major {
  id: number
  name: string
  slug: string
  description?: string
  icon: string // Emoji or CSS class for icon
  is_active: boolean
  display_order: number
  created_at: string
  updated_at: string
  // Computed fields (from API)
  users_count?: number
  listings_count?: number
}

/**
 * Create Major Request Data
 */
export interface CreateMajorData {
  name: string
  description?: string
  icon: string
  is_active?: boolean
  display_order?: number
}

/**
 * Update Major Request Data
 */
export interface UpdateMajorData {
  name?: string
  description?: string
  icon?: string
  is_active?: boolean
  display_order?: number
}

/**
 * Major with relationships
 */
export interface MajorWithRelations extends Major {
  users?: Array<{
    id: number
    name: string
    email: string
  }>
  listings?: Array<{
    id: number
    title: string
    price: number
  }>
}
