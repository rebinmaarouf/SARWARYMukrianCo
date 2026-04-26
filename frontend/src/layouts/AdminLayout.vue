<template>
  <div class="flex h-screen bg-[#020617] text-slate-200 overflow-hidden" dir="rtl">
    <!-- Super Advanced Sidebar (Universal) -->
    <aside class="w-20 md:w-64 bg-slate-900/50 backdrop-blur-3xl border-l border-slate-800 flex flex-col items-center py-8 z-50 transition-all duration-500 group">
       <div class="mb-12 relative">
          <div class="w-12 h-12 bg-gradient-to-tr from-emerald-500 to-teal-400 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 rotate-3 group-hover:rotate-12 transition-all">
             <span class="text-white font-black text-xl">SM</span>
          </div>
       </div>

       <nav class="flex-1 w-full px-4 space-y-4">
          <router-link v-for="item in menuItems" :key="item.path" :to="item.path" 
             class="flex items-center gap-4 p-4 rounded-2xl transition-all group/item hover:bg-slate-800/50"
             :class="isRouteActive(item.path) ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-500'">
             <div class="w-6 h-6" v-html="item.icon"></div>
             <span class="hidden md:block font-bold text-sm tracking-tight">{{ item.name }}</span>
          </router-link>
       </nav>

       <!-- Executive Profile & Logout -->
       <div class="w-full px-4 pt-8 border-t border-slate-800/50">
          <div class="flex items-center gap-4 p-4 mb-4 bg-slate-800/30 rounded-2xl overflow-hidden">
             <div class="w-10 h-10 bg-gradient-to-br from-slate-700 to-slate-900 rounded-xl flex-shrink-0 flex items-center justify-center text-white font-black shadow-lg">
                {{ user?.name?.charAt(0) || 'A' }}
             </div>
             <div class="hidden md:block truncate text-right">
                <p class="text-xs font-black text-white truncate">{{ user?.name || 'Admin' }}</p>
                <p class="text-[9px] font-bold text-slate-500 uppercase">{{ user?.role || 'Super Admin' }}</p>
             </div>
          </div>
          <button @click="logout" class="w-full flex items-center gap-4 p-4 rounded-2xl text-rose-500 hover:bg-rose-500/10 transition-all group/logout">
             <svg class="w-6 h-6 group-hover/logout:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
             <span class="hidden md:block font-black text-sm uppercase tracking-widest">چوونە دەرەوە</span>
          </button>
       </div>
    </aside>

    <!-- Main Content Rendering -->
    <main class="flex-1 overflow-y-auto relative bg-[#020617]">
       <router-view v-slot="{ Component }">
          <transition name="page" mode="out-in">
             <component :is="Component" />
          </transition>
       </router-view>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from '../plugins/axios'

const router = useRouter()
const route = useRoute()
const user = ref(null)

const menuItems = [
  { name: 'داشبۆرد', path: '/', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>' },
  { name: 'ئاڵوگۆڕ', path: '/exchange', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>' },
  { name: 'حسابەکان', path: '/accounts', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>' },
  { name: 'ووردبینی', path: '/general-accounts', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m-8 13h11a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>' },
]

function isRouteActive(path) {
  if (path === '/') return route.path === '/'
  return route.path.startsWith(path)
}

async function fetchUser() {
  try {
    const { data } = await axios.get('/user')
    user.value = data
  } catch (e) {
    router.push('/login')
  }
}

async function logout() {
  await axios.post('/logout')
  localStorage.removeItem('token')
  router.push('/login')
}

onMounted(fetchUser)
</script>

<style>
.page-enter-active, .page-leave-active {
  transition: all 0.3s ease;
}
.page-enter-from {
  opacity: 0;
  transform: translateY(10px);
}
.page-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
