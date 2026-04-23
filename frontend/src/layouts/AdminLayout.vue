<template>
  <div class="min-h-screen bg-[#0a0f1e] text-slate-200 font-sans selection:bg-emerald-500/30">
    <!-- Main Decorative Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-emerald-500/5 blur-[120px] rounded-full"></div>
      <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-blue-500/5 blur-[120px] rounded-full"></div>
    </div>

    <!-- Enhanced Glass Navbar -->
    <header class="sticky top-0 z-50 px-6 py-4">
      <div class="max-w-[1600px] mx-auto bg-[#0f172a]/60 backdrop-blur-xl border border-white/10 rounded-3xl px-8 py-3 flex items-center justify-between shadow-2xl shadow-black/20">
        <!-- Brand -->
        <div class="flex items-center gap-4 cursor-pointer" @click="$router.push('/')">
          <div class="w-12 h-12 bg-gradient-to-tr from-emerald-500 to-teal-400 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 rotate-3 group-hover:rotate-0 transition-transform">
            <span class="text-white text-xl font-black">SM</span>
          </div>
          <div class="hidden sm:block">
            <h1 class="text-xl font-black text-white tracking-tight leading-none">سەروەر موکریان</h1>
            <span class="text-[10px] text-emerald-500 font-bold tracking-widest uppercase opacity-80">ENTERPRISE ERP</span>
          </div>
        </div>

        <!-- Navigation -->
        <div class="flex items-center gap-8">
          <nav class="hidden lg:flex items-center bg-[#0f172a]/80 rounded-full p-1 border border-slate-700/50 shadow-inner">
            <router-link to="/" class="px-6 py-2 rounded-full text-sm font-bold transition-all duration-300" :class="$route.path === '/' ? 'bg-gradient-to-r from-[#10B981] to-emerald-500 text-white shadow-lg shadow-[#10B981]/20' : 'text-slate-400 hover:text-white hover:bg-slate-800'">داشبۆرد</router-link>
            <router-link to="/pos" class="px-6 py-2 rounded-full text-sm font-bold transition-all duration-300" :class="$route.path === '/pos' ? 'bg-gradient-to-r from-[#10B981] to-emerald-500 text-white shadow-lg shadow-[#10B981]/20' : 'text-slate-400 hover:text-white hover:bg-slate-800'">کڕین و فرۆشتن</router-link>
            <router-link to="/accounts" class="px-6 py-2 rounded-full text-sm font-bold transition-all duration-300" :class="$route.path === '/accounts' ? 'bg-gradient-to-r from-[#10B981] to-emerald-500 text-white shadow-lg shadow-[#10B981]/20' : 'text-slate-400 hover:text-white hover:bg-slate-800'">حسابەکان</router-link>
            <router-link v-if="auth.isSuperAdmin" to="/users" class="px-6 py-2 rounded-full text-sm font-bold transition-all duration-300" :class="$route.path === '/users' ? 'bg-gradient-to-r from-[#10B981] to-emerald-500 text-white shadow-lg shadow-[#10B981]/20' : 'text-slate-400 hover:text-white hover:bg-slate-800'">کارمەندان</router-link>
            <router-link to="/hawala" class="px-6 py-2 rounded-full text-sm font-bold transition-all duration-300 text-slate-400 hover:text-white hover:bg-slate-800">حەواڵە</router-link>
          </nav>

          <!-- Live Clock -->
          <div class="hidden xl:flex items-center gap-4 px-6 border-x border-white/10">
            <div class="text-right">
              <p class="text-lg font-mono font-black text-white leading-none">{{ currentClock }}</p>
              <p class="text-[10px] text-emerald-500 font-bold tracking-tighter">{{ currentDate }}</p>
            </div>
          </div>

          <!-- User Profile -->
          <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
              <p class="text-sm font-black text-white leading-none mb-1">{{ auth.user?.name || 'User' }}</p>
              <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter">{{ auth.user?.roles?.[0] || 'Member' }}</p>
            </div>
            <div class="relative group">
              <button class="w-12 h-12 rounded-2xl bg-slate-800 border border-white/10 flex items-center justify-center overflow-hidden hover:border-emerald-500/50 transition-all duration-300 shadow-xl group-hover:shadow-emerald-500/10">
                <span class="text-emerald-500 font-black text-xl">{{ auth.user?.name?.charAt(0) || 'U' }}</span>
              </button>
              
              <!-- Profile Dropdown -->
              <div class="absolute top-full right-0 mt-4 w-56 bg-slate-900 border border-white/10 rounded-2xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 overflow-hidden z-50">
                <div class="p-4 border-b border-white/5 text-right">
                  <p class="text-xs text-slate-500 font-bold">پڕۆفایل و ڕێکخستن</p>
                </div>
                <div class="p-2">
                  <button @click="handleLogout" class="w-full px-4 py-3 flex items-center justify-end gap-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-colors font-bold text-sm">
                    <span>چوونە دەرەوە</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content Area -->
    <main class="max-w-[1600px] mx-auto px-6 py-8 relative">
      <router-view v-slot="{ Component }">
        <transition name="page" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const currentClock = ref('')
const currentDate = ref('')
let timer

const updateDateTime = () => {
  const now = new Date()
  currentClock.value = now.toLocaleTimeString('en-US', { hour12: false })
  currentDate.value = now.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/\//g, ' / ')
}

onMounted(() => {
  updateDateTime()
  timer = setInterval(updateDateTime, 1000)
})

onUnmounted(() => {
  clearInterval(timer)
})

const handleLogout = async () => {
  await auth.logout()
  router.push('/login')
}
</script>

<style>
.page-enter-active, .page-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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
