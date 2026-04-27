<template>
  <div class="flex h-screen bg-[#050505] text-slate-200 font-['Inter',sans-serif] overflow-hidden rtl" dir="rtl">
    <!-- Main Sidebar -->
    <aside class="w-24 md:w-80 bg-slate-900/20 backdrop-blur-3xl border-l border-white/5 flex flex-col transition-all duration-500 relative z-50">
      <!-- Logo -->
      <div class="p-8 mb-6 flex justify-center md:justify-start">
        <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-emerald-500/40 rotate-3">
           <span class="text-2xl font-black text-slate-950">SM</span>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 px-4 space-y-2 overflow-y-auto custom-scrollbar">
        <!-- Dashboard -->
        <router-link to="/admin" class="nav-link" :class="{ 'active': $route.path === '/admin' }">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          <span class="hidden md:block font-bold">داشبۆرد</span>
        </router-link>

        <div class="pt-4 pb-2 px-6 text-[10px] font-black text-slate-600 uppercase tracking-widest hidden md:block">Financial Ledger</div>

        <!-- Universal Journal -->
        <router-link to="/admin/registry" class="nav-link" :class="{ 'active': $route.path === '/admin/registry' }">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          <span class="hidden md:block font-bold">ڕۆژنامەی گشتی</span>
        </router-link>

        <div class="pt-6 pb-2 px-6 text-[10px] font-black text-slate-600 uppercase tracking-widest hidden md:block">Core Modules</div>

        <!-- Exchange -->
        <router-link to="/admin/exchange" class="nav-link" :class="{ 'active': $route.path === '/admin/exchange' }">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          <span class="hidden md:block font-bold">تێرمیناڵی ئاڵوگۆڕ</span>
        </router-link>

        <!-- Transfer -->
        <router-link to="/admin/transfers" class="nav-link" :class="{ 'active': $route.path === '/admin/transfers' }">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          <span class="hidden md:block font-bold">گواستنەوەی پارە</span>
        </router-link>

        <!-- Accounts -->
        <router-link to="/admin/accounts" class="nav-link" :class="{ 'active': $route.path === '/admin/accounts' }">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          <span class="hidden md:block font-bold">حسابەکان</span>
        </router-link>

        <div class="pt-6 pb-2 px-6 text-[10px] font-black text-slate-600 uppercase tracking-widest hidden md:block">Configuration</div>

        <!-- Currency Settings -->
        <router-link to="/admin/currencies" class="nav-link" :class="{ 'active': $route.path === '/admin/currencies' }">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span class="hidden md:block font-bold">ڕێکخستنی دراوەکان</span>
        </router-link>

        <div class="pt-6 pb-2 px-6 text-[10px] font-black text-slate-600 uppercase tracking-widest hidden md:block">Administration</div>

        <!-- Users -->
        <router-link to="/admin/users" class="nav-link" :class="{ 'active': $route.path === '/admin/users' }">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          <span class="hidden md:block font-bold">بەکارهێنەران</span>
        </router-link>

        <!-- Roles -->
        <router-link to="/admin/roles" class="nav-link" :class="{ 'active': $route.path === '/admin/roles' }">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <span class="hidden md:block font-bold">دەسەڵاتەکان (Roles)</span>
        </router-link>
      </nav>

      <!-- Logout -->
      <div class="p-6 border-t border-white/5">
        <button @click="logout" class="nav-link text-rose-500 hover:bg-rose-500/10 w-full">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          <span class="hidden md:block font-bold text-right flex-1">چوونە دەرەوە</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative bg-[#050505]">
       <header class="h-20 flex items-center justify-between px-10 border-b border-white/5 bg-black/40 backdrop-blur-xl relative z-40">
          <div class="flex items-center gap-4">
             <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black rounded-full uppercase tracking-widest border border-emerald-500/20">Enterprise ERP</span>
          </div>
          <div class="flex items-center gap-4">
             <div class="text-right">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">Authenticated</p>
                <p class="text-sm font-bold text-white">{{ auth.user?.name }}</p>
             </div>
             <div class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center font-black text-emerald-500 border border-white/10">
                {{ auth.user?.name?.charAt(0) }}
             </div>
          </div>
       </header>

       <div class="flex-1 overflow-y-auto p-10 custom-scrollbar relative z-30">
          <router-view v-slot="{ Component }">
            <transition name="page" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
       </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import axios from '../plugins/axios'

const auth = useAuthStore()
const router = useRouter()
const currencies = ref([])
const loadingCurrencies = ref(true)

const fetchCurrencies = async () => {
  try {
    const { data } = await axios.get('/currencies')
    currencies.value = data
  } catch (err) {
    console.error('Failed to fetch currencies for sidebar', err)
  } finally {
    loadingCurrencies.value = false
  }
}

const logout = async () => {
  await auth.logout()
  router.push('/login')
}

onMounted(fetchCurrencies)
</script>

<style scoped>
.nav-link { @apply flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 text-slate-400 hover:bg-white/5 hover:text-white; }
.nav-link.active { @apply bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-lg shadow-emerald-500/10; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
.page-enter-active, .page-leave-active { transition: all 0.3s ease; }
.page-enter-from { opacity: 0; transform: translateY(10px); }
.page-leave-to { opacity: 0; transform: translateY(-10px); }
</style>
