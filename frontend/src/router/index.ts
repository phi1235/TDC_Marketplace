import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: { requiresGuest: true },
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/auth/RegisterView.vue'),
      meta: { requiresGuest: true },
    },
    {
      path: '/listings',
      name: 'listings',
      component: () => import('@/views/listings/ListingsView.vue'),
    },
    {
      path: '/listings/:id',
      name: 'listing-detail',
      component: () => import('@/views/listings/ListingDetailView.vue'),
      props: true,
    },
    {
      path: '/create-listing',
      name: 'create-listing',
      component: () => import('@/views/listings/CreateListingView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/my-listings',
      name: 'my-listings',
      component: () => import('@/views/listings/MyListingsView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/edit-listing/:id',
      name: 'edit-listing',
      component: () => import('@/views/listings/EditListingView.vue'),
      meta: { requiresAuth: true },
      props: true,
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/views/dashboard/DashboardView.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
      children: [
        {
          path: 'listings',
          name: 'dashboard-listings',
          component: () => import('@/views/listings/AdminListingsView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'pending',
          name: 'dashboard-pending',
          component: () => import('@/views/listings/PendingAdminView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
      ],
    },
    {
      path: '/panel',
      name: 'panel',
      component: () => import('@/views/dashboard/ContentPanel.vue'),
      meta: { requiresAuth: true },
    },
    ,
    {
      path: '/userpanel',
      name: 'user_panel',
      component: () => import('@/views/dashboard/UserPanel.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/listingcard',
      name: 'listingcard',
      component: () => import('@/views/dashboard/ListingCardView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/listwish',
      name: 'listwish',
      component: () => import('@/views/dashboard/ListWishView.vue'),
      meta: { requiresAuth: true },
    },
    ,
    {
      path: '/seller',
      name: 'seller',
      component: () => import('@/views/dashboard/SellerList.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('@/views/profile/ProfileView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('@/views/admin/AdminDashboard.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
  ],
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next('/')
  } else if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next('/')
  } else {
    next()
  }
})

export default router
