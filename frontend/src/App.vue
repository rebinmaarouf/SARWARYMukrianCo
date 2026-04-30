<template>
  <router-view></router-view>
</template>

<script setup>
import { onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const router = useRouter()
const authStore = useAuthStore()

let idleTimer = null
const IDLE_TIMEOUT = 15 * 60 * 1000 // 15 Minutes

const resetTimer = () => {
  if (idleTimer) clearTimeout(idleTimer)
  
  if (authStore.token) {
    idleTimer = setTimeout(() => {
      handleAutoLogout()
    }, IDLE_TIMEOUT)
  }
}

const handleAutoLogout = async () => {
  console.log('Session timed out due to inactivity.')
  await authStore.logout()
  router.push('/login')
}

// Global Activity Listeners
const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart']

onMounted(() => {
  activityEvents.forEach(event => {
    window.addEventListener(event, resetTimer)
  })
  
  // Initial timer if already logged in
  if (authStore.token) resetTimer()
})

onUnmounted(() => {
  activityEvents.forEach(event => {
    window.removeEventListener(event, resetTimer)
  })
  if (idleTimer) clearTimeout(idleTimer)
})

// Watch for login/logout to start/stop timer
watch(() => authStore.token, (newToken) => {
  if (newToken) {
    resetTimer()
  } else {
    if (idleTimer) clearTimeout(idleTimer)
  }
})
</script>
