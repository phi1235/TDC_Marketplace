import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue'),
      beforeEnter: (to, from, next) => {
        const auth = useAuthStore()
      if (auth.isAdmin) {
        // Nếu là admin thì không cho vào trang sản phẩm
        next('/dashboard')
      } else {
        next()
      }
      }
    },
    {
      path: '/my-disputes',
      name: 'my-disputes',
      component: () => import('@/views/disputes/MyDisputes.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/my-disputes/:id',
      name: 'my-disputes-detail',
      component: () => import('@/views/disputes/DisputeDetail.vue'),
      meta: { requiresAuth: true },
      props: true,
    },
    {
      path: '/orders/my',
      name: 'MyOrders',
      component: () => import('../views/orders/MyOrders.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/orders/:id',
      name: 'OrderDetail',
      component: () => import('@/views/orders/OrderDetail.vue'),
      meta: { requiresAuth: true },
      props: true
    },

    {
      path: '/search',
      name: 'search',
      component: () => import('@/components/SearchFilter.vue'),
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
      path: '/category/:id',
      name: 'category-listings',
      component: () => import('@/views/categories/CategoryListingsView.vue'),
      props: true,
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
      component: () => import('@/views/dashboard/DashboardView.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
      children: [
        {
          path: '', // <--- DEFAULT CHILD
          name: 'dashboard',
          component: () => import('@/views/dashboard/DashboardHome.vue'),
        },
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
        {
          path: 'users',
          name: 'dashboard-users',
          component: () => import('@/views/dashboard/UsersView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'categories',
          name: 'dashboard-categories',
          component: () => import('@/views/dashboard/CategoryManagementView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'pickup-points',
          name: 'dashboard-pickup-points',
          component: () => import('@/views/dashboard/PickupPointManagementView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'reports',
          name: 'dashboard-reports',
          component: () => import('@/views/admin/AdminReportsView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {

          path: 'analytics',
          name: 'dashboard-analytics',
          component: () => import('@/views/admin/AdminAnalyticsView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'monitoring',
          name: 'dashboard-monitoring',
          component: () => import('@/views/admin/AdminMonitoringView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'audit-logs',
          name: 'dashboard-audit-logs',
          component: () => import('@/views/admin/AdminAuditLogsView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'majors',
          name: 'dashboard-majors',
          component: () => import('@/views/admin/AdminMajorsView.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'disputes',
          name: 'dashboard-disputes',
          component: () => import('@/views/admin/AdminDisputes.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'disputes/:id',
          name: 'dashboard-dispute-detail',
          component: () => import('@/views/admin/AdminDisputeDetail.vue'),
          meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
          path: 'notifications',
          name: 'AdminNotifications',
          component: () => import('@/views/dashboard/AdminNotifications.vue'),
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
    {
      path: '/seller',
      name: 'seller',
      component: () => import('@/views/dashboard/SellerList.vue'),
    },
    {
      path: '/notifications',
      name: 'notifications',
      component: () => import('@/views/dashboard/NotificationsView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/detailNotification/:id',//truyền id qua
      name: 'detail-notification',
      component: () => import('@/views/dashboard/DetailNotification.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('@/views/profile/ProfileView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/my-reports',
      name: 'my-reports',
      component: () => import('@/views/account/MyReportsView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/my-activity',
      name: 'my-activity',
      component: () => import('@/views/account/MyActivityView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('@/views/admin/AdminDashboard.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    // legacy direct route (optional)
    {
      path: '/admin/audit-logs',
      name: 'admin-audit-logs',
      component: () => import('@/views/admin/AdminAuditLogsView.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/terms',
      name: 'terms',
      component: () => import('@/views/terms/TermsView.vue'),
    },
    {
      path: '/privacy-policy',
      name: 'privacy-policy',
      component: () => import('@/views/terms/TermsView.vue'), // Tạm dùng TermsView, có thể tạo PrivacyView riêng
    },
    {
      path: '/faq',
      name: 'faq',
      component: () => import('@/views/faq/faqView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/contact',
      name: 'contact',
      component: () => import('@/views/contact/ContactView.vue'),
    },
    {
      path: '/notifications',
      name: 'UserNotifications',
      component: () => import('@/views/dashboard/UserNotifications.vue'),
      meta: { requiresAuth: true },
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
