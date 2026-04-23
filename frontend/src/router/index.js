import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import AdminLayout from '../layouts/AdminLayout.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/auth/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/verify-2fa',
    name: 'TwoFactorVerify',
    component: () => import('../views/auth/TwoFactorVerify.vue'),
    meta: { guest: true }
  },
  {
    path: '/',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: () => import('../views/admin/Dashboard.vue')
      },
      {
        path: 'pos',
        name: 'QuickTrade',
        component: () => import('../views/pos/QuickTrade.vue')
      },
      {
        path: 'accounts',
        name: 'AccountsManager',
        component: () => import('../views/admin/AccountsManager.vue')
      },
      {
        path: 'users',
        name: 'UserManager',
        component: () => import('../views/admin/UserManager.vue')
      },
      {
        path: 'registry/:currencyId',
        name: 'DynamicRegistry',
        component: () => import('../views/admin/DynamicRegistry.vue')
      },
      {
        path: 'hawala',
        name: 'Hawala',
        component: () => import('../views/admin/ComingSoon.vue')
      },
      {
        path: 'vaults',
        name: 'Vaults',
        component: () => import('../views/admin/ComingSoon.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation Guard
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && auth.isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router
