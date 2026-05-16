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
        path: 'vouchers',
        name: 'Vouchers',
        component: () => import('../views/finance/VoucherManager.vue')
      },
      {
        path: 'general-ledger',
        name: 'GeneralLedger',
        component: () => import('../views/finance/DynamicRegistry.vue')
      },
      {
        path: 'audit',
        name: 'AuditCenter',
        component: () => import('../views/finance/AuditCenter.vue')
      },
      {
        path: 'audit-advanced',
        name: 'AuditAdvanced',
        component: () => import('../views/finance/AuditAdvanced.vue'),
        meta: { permission: 'view_advanced_reports' }
      },
      {
        path: 'forensics',
        name: 'ForensicLogs',
        component: () => import('../views/finance/ForensicLogs.vue'),
        meta: { permission: 'view_forensics' }
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
  },
  {
    path: '/error/:code',
    name: 'ErrorPage',
    component: () => import('../views/errors/ErrorPage.vue')
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/error/404'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()
  
  if (auth.isAuthenticated && (!auth.user?.roles || auth.user.roles.length === 0)) {
    await auth.fetchProfile()
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && auth.isAuthenticated) {
    next('/admin')
  } else if (to.meta.permission && !auth.isSuperAdmin && !auth.permissions.includes(to.meta.permission)) {
    next('/admin')
  } else {
    next()
  }
})

export default router
