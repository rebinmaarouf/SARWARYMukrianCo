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
    redirect: '/admin'
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: () => import('../views/admin/Dashboard.vue')
      },
      {
        path: 'currencies',
        name: 'Currencies',
        component: () => import('../views/finance/CurrencyManager.vue')
      },
      {
        path: 'account-statement',
        name: 'admin-account-statement',
        component: () => import('../views/finance/AccountStatement.vue')
      },
      {
        path: 'transfers',
        name: 'admin-transfers',
        component: () => import('../views/finance/TransferManager.vue')
      },
      {
        path: 'accounts',
        name: 'admin-accounts',
        component: () => import('../views/finance/AccountsManager.vue')
      },
      {
        path: 'exchange',
        name: 'Exchange',
        component: () => import('../views/finance/ExchangeManager.vue')
      },
      {
        path: 'general-ledger',
        name: 'GeneralLedger',
        component: () => import('../views/finance/DynamicRegistry.vue')
      },
      {
        path: 'registry/:currencyId?',
        name: 'DynamicRegistry',
        component: () => import('../views/finance/DynamicRegistry.vue')
      },
      {
        path: 'users',
        name: 'UserManager',
        component: () => import('../views/admin/UserManager.vue')
      },
      {
        path: 'roles',
        name: 'RoleManager',
        component: () => import('../views/admin/RoleManager.vue')
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
    next('/admin')
  } else {
    next()
  }
})

export default router
