import { defineStore } from 'pinia'
import axios from '../plugins/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null,
    twoFactorEmail: null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isSuperAdmin: (state) => {
      if (!state.user?.roles) return false
      return state.user.roles.some(r => r === 'Super Admin' || r.name === 'Super Admin')
    },
    permissions: (state) => {
      if (!state.user?.permissions) return []
      return state.user.permissions.map(p => typeof p === 'string' ? p : p.name)
    }
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post('/login', credentials)
        if (response.data.two_factor_required) {
          this.twoFactorEmail = response.data.email
          return { twoFactor: true }
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'کێشەیەک ڕوویدا لە کاتی لۆگین'
        throw err
      } finally {
        this.loading = false
      }
    },

    async verify2FA(code) {
      this.loading = true
      try {
        const response = await axios.post('/verify-2fa', {
          email: this.twoFactorEmail,
          code
        })
        
        this.token = response.data.access_token
        this.user = response.data.user
        
        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))
        
        return response.data
      } catch (err) {
        this.error = err.response?.data?.message || 'کۆدەکە هەڵەیە'
        throw err
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await axios.post('/logout')
      } catch (err) {
        console.error('Logout error', err)
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
      }
    },
    async fetchProfile() {
      try {
        const response = await axios.get('/user')
        this.user = response.data
        localStorage.setItem('user', JSON.stringify(this.user))
        return this.user
      } catch (err) {
        console.error('Fetch profile error', err)
      }
    }
  }
})
