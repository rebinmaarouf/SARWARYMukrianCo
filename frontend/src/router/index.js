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
      // Finance Domain
      { path: 'exchange', name: 'admin-exchange', component: () => import('../views/finance/ExchangeManager.vue') },
      { path: 'general-accounts', name: 'admin-general-accounts', component: () => import('../views/finance/GeneralAccounts.vue') },
      { path: 'accounts', name: 'admin-accounts', component: () => import('../views/finance/AccountsManager.vue') },
      { 
        path: 'registry/:currencyId', 
        name: 'DynamicRegistry', 
        component: () => import('../views/finance/DynamicRegistry.vue') 
      },
      { path: 'print-invoice', name: 'print-invoice', component: () => import('../views/finance/InvoicePrint.vue'), meta: { layout: 'empty' } },
      
      // Admin/Management Domain
      { path: 'users', name: 'UserManager', component: () => import('../views/admin/UserManager.vue') },
      
      // Future Modules
      {
        path: 'hawala',
        name: 'Hawala',
        component: () => import('../views/hawala/ComingSoon.vue')
      },
      {
        path: 'vaults',
        name: 'Vaults',
        component: () => import('../views/vaults/ComingSoon.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

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
