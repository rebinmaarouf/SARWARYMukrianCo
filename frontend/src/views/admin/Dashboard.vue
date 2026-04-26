<template>
  <div class="space-y-8">
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-4xl font-black text-white tracking-tight">سڵاو، {{ authStore.user?.name }} 👋</h1>
        <p class="text-slate-400 font-medium mt-1">بەخێربێیتەوە بۆ سیستەمی بەڕێوەبردنی سەروەر موکریان</p>
      </div>
      <div class="flex items-center gap-3 bg-slate-900/50 backdrop-blur-xl p-2 rounded-2xl border border-slate-800">
        <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center text-white font-black shadow-lg shadow-emerald-500/20">SM</div>
        <div class="pl-4 pr-2">
          <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">ڕێکەوتی ئەمڕۆ</p>
          <p class="text-sm font-bold text-white">{{ today }}</p>
        </div>
      </div>
    </div>

    <!-- MAIN ACTION CARDS (BIG BUTTONS) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- EXCHANGE PORTAL (MAIN) -->
      <router-link to="/exchange" class="group relative bg-gradient-to-br from-emerald-500 to-teal-600 p-8 rounded-[2.5rem] shadow-2xl shadow-emerald-500/20 hover:-translate-y-2 transition-all duration-500 overflow-hidden col-span-1 md:col-span-2">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all"></div>
        <div class="relative z-10">
          <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
             <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          </div>
          <h2 class="text-3xl font-black text-white mb-2">کڕین و فرۆشتنی دراو</h2>
          <p class="text-emerald-100 font-bold opacity-80">پۆرتاڵی پێشکەوتووی گۆڕینەوەی هەموو جۆرە دراوەکان</p>
        </div>
        <div class="absolute top-8 right-8 text-white/10 group-hover:rotate-12 transition-transform duration-700">
           <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        </div>
      </router-link>

      <!-- ACCOUNTS MANAGER -->
      <router-link to="/accounts" class="group bg-[#1e293b]/60 backdrop-blur-xl p-8 rounded-[2.5rem] border-2 border-slate-800 hover:border-emerald-500/50 transition-all duration-500">
        <div class="w-14 h-14 bg-slate-800 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-500/10 group-hover:text-emerald-400 transition-colors">
           <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <h3 class="text-xl font-black text-white mb-2 tracking-tight">حسابەکان</h3>
        <p class="text-slate-400 text-sm font-bold">بەڕێوەبردنی وەکیل و حسابە گشتییەکان</p>
      </router-link>

      <!-- SETTINGS/USERS -->
      <router-link to="/users" class="group bg-[#1e293b]/60 backdrop-blur-xl p-8 rounded-[2.5rem] border-2 border-slate-800 hover:border-blue-500/50 transition-all duration-500">
        <div class="w-14 h-14 bg-slate-800 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-500/10 group-hover:text-blue-400 transition-colors">
           <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
        </div>
        <h3 class="text-xl font-black text-white mb-2 tracking-tight">ڕێکخستنەکان</h3>
        <p class="text-slate-400 text-sm font-bold">بەڕێوەبردنی ستاف و پێرمیشنەکان</p>
      </router-link>
    </div>

    <!-- STATS ROW (Glassmorphic) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
       <div v-for="stat in quickStats" :key="stat.label" class="bg-slate-900/40 backdrop-blur-md border border-slate-800/50 p-6 rounded-3xl flex items-center justify-between">
          <div class="space-y-1">
             <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ stat.label }}</p>
             <p class="text-2xl font-black text-white">{{ stat.value }}</p>
          </div>
          <div :class="`w-12 h-12 rounded-2xl flex items-center justify-center bg-${stat.color}-500/10 text-${stat.color}-400`">
             <component :is="stat.icon" class="w-6 h-6" />
          </div>
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const today = new Intl.DateTimeFormat('ku-IQ', { dateStyle: 'full' }).format(new Date())

const quickStats = ref([
  { label: 'کۆی حسابات', value: '1,240', color: 'emerald', icon: 'UsersIcon' },
  { label: 'مەعامەلات (ئەمڕۆ)', value: '85', color: 'blue', icon: 'ChartBarIcon' },
  { label: 'سندوق (دۆلار)', value: '$45,200', color: 'amber', icon: 'CurrencyDollarIcon' },
])
</script>
