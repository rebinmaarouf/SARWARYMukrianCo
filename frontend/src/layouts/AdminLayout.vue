<template>
  <div class="flex h-screen bg-[#050505] text-slate-200 font-['Inter',sans-serif] overflow-hidden rtl" dir="rtl">
    
    <!-- Mobile Backdrop (Visible when sidebar open on mobile) -->
    <div v-if="isMobileMenuOpen" @click="isMobileMenuOpen = false" 
         class="fixed inset-0 bg-black/60 backdrop-blur-md z-[60] lg:hidden animate-fade-in"></div>

    <!-- Main Sidebar -->
    <aside :class="[
      'bg-slate-900/20 backdrop-blur-3xl border-l border-white/5 flex flex-col transition-all duration-500 relative z-[70]',
      // Desktop widths
      isCollapsed ? 'md:w-24' : 'md:w-80',
      // Mobile handling
      isMobileMenuOpen ? 'fixed inset-y-0 right-0 w-80 translate-x-0' : 'fixed inset-y-0 right-0 w-80 translate-x-full lg:translate-x-0 lg:relative lg:translate-x-0'
    ]">
      <!-- Sidebar Header / Logo Area -->
      <div class="p-6 mb-4 flex items-center" :class="isCollapsed ? 'justify-center' : 'justify-between'">
        <div class="w-12 h-12 relative group cursor-pointer shrink-0">
            <div class="absolute inset-0 bg-white rounded-xl shadow-2xl shadow-emerald-500/20 group-hover:scale-110 transition-transform duration-500"></div>
            <img :src="logoUrl" alt="Logo" class="w-full h-full object-contain relative z-10 p-1.5 rounded-xl" @error="logoError = true" v-if="!logoError" />
            <div v-else class="w-full h-full bg-slate-800 rounded-xl flex items-center justify-center relative z-10 border border-white/10">
               <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <button v-if="!isCollapsed" @click="isCollapsed = true" class="hidden lg:flex w-8 h-8 items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white transition-all">
           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
        </button>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto custom-scrollbar">
        <!-- Dashboard -->
        <router-link to="/admin" class="nav-link" :class="{ 'active': $route.path === '/admin', 'collapsed': isCollapsed }" data-tip="Dashboard">
          <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          <span v-if="!isCollapsed" class="font-bold whitespace-nowrap">داشبۆرد</span>
        </router-link>

        <div v-if="!isCollapsed" class="pt-4 pb-1.5 px-6 text-[10px] font-black text-slate-600 uppercase tracking-widest">Financial Ledger</div>
        <div v-else class="h-px bg-white/5 my-4 mx-4"></div>

        <!-- Links with tooltips when collapsed -->
        <router-link to="/admin/registry" class="nav-link" :class="{ 'active': $route.path === '/admin/registry', 'collapsed': isCollapsed }" data-tip="Registry">
          <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          <span v-if="!isCollapsed" class="font-bold whitespace-nowrap">تۆماری گشتی</span>
        </router-link>

        <router-link to="/admin/audit" class="nav-link" :class="{ 'active': $route.path === '/admin/audit', 'collapsed': isCollapsed }" data-tip="Audit Center">
          <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          <span v-if="!isCollapsed" class="font-bold whitespace-nowrap">ناوەندی وردبینی</span>
        </router-link>

        <router-link v-if="auth.permissions.includes('view_forensics') || auth.isSuperAdmin" to="/admin/forensics" class="nav-link" :class="{ 'active': $route.path === '/admin/forensics', 'collapsed': isCollapsed }" data-tip="Forensics">
          <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <span v-if="!isCollapsed" class="font-bold whitespace-nowrap">بەدواداچوونی ورد</span>
        </router-link>

        <div v-if="!isCollapsed" class="pt-6 pb-1.5 px-6 text-[10px] font-black text-slate-600 uppercase tracking-widest">Core Modules</div>
        <div v-else class="h-px bg-white/5 my-4 mx-4"></div>

        <router-link to="/admin/exchange" class="nav-link" :class="{ 'active': $route.path === '/admin/exchange', 'collapsed': isCollapsed }" data-tip="Exchange">
          <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          <span v-if="!isCollapsed" class="font-bold whitespace-nowrap">تێرمیناڵی ئاڵوگۆڕ</span>
        </router-link>

        <router-link to="/admin/transfers" class="nav-link" :class="{ 'active': $route.path === '/admin/transfers', 'collapsed': isCollapsed }" data-tip="Transfers">
          <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span v-if="!isCollapsed" class="font-bold whitespace-nowrap">گواستنەوەی پارە</span>
        </router-link>

        <router-link to="/admin/accounts" class="nav-link" :class="{ 'active': $route.path === '/admin/accounts', 'collapsed': isCollapsed }" data-tip="Accounts">
          <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          <span v-if="!isCollapsed" class="font-bold whitespace-nowrap">حسابەکان</span>
        </router-link>

        <div v-if="!isCollapsed" class="pt-6 pb-1.5 px-6 text-[10px] font-black text-slate-600 uppercase tracking-widest">Administration</div>
        <div v-else class="h-px bg-white/5 my-4 mx-4"></div>

        <router-link v-if="auth.permissions.includes('view_users') || auth.isSuperAdmin" to="/admin/users" class="nav-link" :class="{ 'active': $route.path === '/admin/users', 'collapsed': isCollapsed }" data-tip="Users">
          <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          <span v-if="!isCollapsed" class="font-bold whitespace-nowrap">بەرێوەبەری یوسەرەکان</span>
        </router-link>

        <router-link v-if="auth.permissions.includes('view_roles') || auth.isSuperAdmin" to="/admin/roles" class="nav-link" :class="{ 'active': $route.path === '/admin/roles', 'collapsed': isCollapsed }" data-tip="Roles">
          <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <span v-if="!isCollapsed" class="font-bold whitespace-nowrap">رۆڵ و پێرمیشنەکان</span>
        </router-link>

        <div v-if="!isCollapsed" class="pt-6 pb-1.5 px-6 text-[10px] font-black text-slate-600 uppercase tracking-widest">Configuration</div>
        <div v-else class="h-px bg-white/5 my-4 mx-4"></div>

        <router-link to="/admin/currencies" class="nav-link" :class="{ 'active': $route.path === '/admin/currencies', 'collapsed': isCollapsed }" data-tip="Currencies">
          <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span v-if="!isCollapsed" class="font-bold whitespace-nowrap">ڕێکخستنی دراوەکان</span>
        </router-link>
      </nav>

      <!-- Logout / User Section -->
      <div class="p-6 border-t border-white/5 bg-slate-950/20">
        <button @click="logout" class="nav-link text-rose-500 hover:bg-rose-500/10 w-full" :class="{ 'collapsed': isCollapsed }" data-tip="Logout">
          <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          <span v-if="!isCollapsed" class="font-bold text-right flex-1 whitespace-nowrap">چوونە دەرەوە</span>
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col relative bg-[#050505] overflow-hidden">
       <!-- Header -->
       <header class="h-20 flex items-center justify-between px-6 md:px-10 border-b border-white/5 bg-black/40 backdrop-blur-xl relative z-40">
          <div class="flex items-center gap-4">
             <!-- Mobile Toggle -->
             <button @click="isMobileMenuOpen = true" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 text-slate-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
             </button>
             <!-- Desktop Expand -->
             <button v-if="isCollapsed" @click="isCollapsed = false" class="hidden lg:flex w-10 h-10 items-center justify-center rounded-xl bg-white/5 text-slate-400 hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
             </button>
             <div class="hidden sm:flex items-center gap-4">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black rounded-full uppercase tracking-widest border border-emerald-500/20">Enterprise ERP v2.4</span>
             </div>
          </div>

          <div class="flex items-center gap-4">
             <div class="text-right hidden sm:block">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">Authenticated Account</p>
                <p class="text-sm font-bold text-white">{{ auth.user?.name }}</p>
             </div>
             <div class="w-10 h-10 bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl flex items-center justify-center font-black text-emerald-500 border border-white/10 shadow-lg">
                {{ auth.user?.name?.charAt(0) }}
             </div>
          </div>
       </header>

       <!-- Dynamic View Content -->
       <div class="flex-1 overflow-y-auto p-4 md:p-10 custom-scrollbar relative z-30">
          <router-view v-slot="{ Component }">
            <component :is="Component" :key="$route.fullPath" />
          </router-view>
       </div>

       <!-- Subtle Background Bloom -->
       <div class="fixed bottom-0 right-0 w-[50vw] h-[50vh] bg-emerald-500/5 blur-[120px] rounded-full pointer-events-none -z-10"></div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter, useRoute } from 'vue-router'
import axios from '../plugins/axios'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const isCollapsed = ref(false)
const isMobileMenuOpen = ref(false)
const logoError = ref(false)
const logoUrl = '/logo.png'

// Close mobile menu on route change
watch(() => route.path, () => {
  isMobileMenuOpen.value = false
})

const logout = async () => {
  await auth.logout()
  router.push('/login')
}

onMounted(async () => {
  try { 
    await auth.fetchProfile()
    await axios.get('/currencies') 
  } catch (e) {
  }
})
</script>

<style scoped>
.nav-link { 
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.85rem 1.25rem;
  border-radius: 1rem;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  color: #64748b;
  border: 1px solid transparent;
}
.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.05);
  color: white;
  transform: translateX(-4px);
}
.nav-link.active { 
  background-color: rgba(16, 185, 129, 0.08);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.15);
  box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.1);
}
.nav-link.collapsed {
  justify-content: center;
  padding: 0.85rem;
  gap: 0;
}
.nav-link.collapsed:hover {
  transform: scale(1.1);
}

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }

.page-enter-active, .page-leave-active { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.page-enter-from { opacity: 0; transform: translateY(10px); }
.page-leave-to { opacity: 0; transform: translateY(-10px); }

.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* Tooltip Simulation via CSS if needed, or use a library */
[v-tooltip]:hover::after {
  content: attr(v-tooltip);
  position: absolute;
  right: 110%;
  top: 50%;
  transform: translateY(-50%);
  background: #0f172a;
  color: white;
  padding: 0.5rem 0.8rem;
  border-radius: 0.5rem;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  white-space: nowrap;
  border: 1px solid rgba(255,255,255,0.1);
  box-shadow: 0 10px 20px rgba(0,0,0,0.4);
  pointer-events: none;
  z-index: 100;
}
</style>
