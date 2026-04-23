<template>
  <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 pb-10">
    
    <!-- Left Area (Main Grid) -->
    <div class="xl:col-span-9 space-y-8">
      
      <!-- Sections wrapper -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- کڕین و فرۆشتن (Buying & Selling) Section -->
        <div class="bg-[#1e293b]/50 backdrop-blur-md rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-slate-700/50 p-6 sm:p-8 flex flex-col relative overflow-hidden group/section">
          <!-- Ambient glow -->
          <div class="absolute -top-20 -right-20 w-40 h-40 bg-[#10B981]/20 rounded-full blur-[80px] pointer-events-none transition duration-700 group-hover/section:bg-[#10B981]/30"></div>
          
          <div class="flex items-center justify-center gap-4 mb-8 relative z-10">
            <h2 class="text-3xl font-black text-white drop-shadow-md tracking-tight">کڕین و فرۆشتن</h2>
          </div>
          
          <div class="grid grid-cols-2 gap-4 flex-grow relative z-10">
            <button v-for="btn in exchangeButtons" :key="btn.name" 
              class="relative overflow-hidden group rounded-2xl p-4 flex flex-col items-center justify-center min-h-[110px] transition-all duration-300 transform hover:-translate-y-2 border border-white/5 active:scale-95 shadow-lg"
              :class="btn.colorClass" @click="$router.push(btn.route || '/pos')">
              
              <!-- Glass reflection effect -->
              <div class="absolute top-0 left-0 right-0 h-1/2 bg-gradient-to-b from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-t-2xl"></div>
              
              <span class="relative z-10 font-black text-xl md:text-2xl drop-shadow-sm transition-transform group-hover:scale-105" :class="btn.textClass || 'text-slate-900'">{{ btn.name }}</span>
            </button>

            <!-- Error Correction Button -->
            <button class="col-span-2 mt-4 relative overflow-hidden group bg-gradient-to-r from-slate-900 to-black text-white rounded-2xl py-5 text-xl font-bold shadow-[0_10px_40px_rgba(0,0,0,0.5)] hover:shadow-[0_10px_50px_rgba(0,0,0,0.7)] transition-all transform hover:-translate-y-1 active:scale-95 border border-slate-700/50 flex items-center justify-center gap-3">
              <div class="absolute inset-0 bg-gradient-to-r from-red-600/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <svg class="w-6 h-6 text-red-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
              <span class="relative z-10 tracking-wide text-2xl font-black">دۆزینەوەی هەڵە</span>
            </button>
          </div>
        </div>

        <!-- تۆمار (Registry) Section -->
        <div class="bg-[#1e293b]/50 backdrop-blur-md rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-slate-700/50 p-6 sm:p-8 flex flex-col relative overflow-hidden group/section">
          <!-- Ambient glow -->
          <div class="absolute -top-20 -left-20 w-40 h-40 bg-blue-500/20 rounded-full blur-[80px] pointer-events-none transition duration-700 group-hover/section:bg-blue-500/30"></div>
          
          <div class="flex items-center justify-center gap-4 mb-8 relative z-10">
            <h2 class="text-3xl font-black text-white drop-shadow-md tracking-tight">تۆمارەکان</h2>
          </div>
          
          <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 flex-grow relative z-10">
            <button v-for="btn in registryButtons" :key="btn.name" 
              class="relative overflow-hidden group rounded-xl p-3 flex flex-col items-center justify-center min-h-[90px] shadow-lg transition-all duration-300 transform hover:-translate-y-1.5 border border-white/10 active:scale-95"
              :class="btn.colorClass" @click="navigate(btn.route)">
              <!-- Glass reflection effect -->
              <div class="absolute top-0 left-0 right-0 h-1/2 bg-gradient-to-b from-white/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-t-xl"></div>
              
              <span class="relative z-10 font-bold text-lg text-center leading-tight drop-shadow-sm transition-transform group-hover:scale-105" :class="btn.textClass || 'text-slate-900'">{{ btn.name }}</span>
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Right Sidebar (Qasa / Vault) -->
    <div class="xl:col-span-3">
      <div class="bg-[#1e293b]/60 backdrop-blur-xl rounded-3xl shadow-2xl border border-slate-700/50 p-8 flex flex-col items-center sticky top-32 relative overflow-hidden">
        
        <!-- Ambient glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-[#10B981]/10 rounded-full blur-[100px] pointer-events-none"></div>
        
        <!-- Quick Access Sidebar Buttons -->
        <div class="w-full grid grid-cols-2 gap-3 mb-10 relative z-10">
          <button v-for="item in sidebarItems" :key="item.name" 
            class="relative overflow-hidden group rounded-xl py-4 font-bold text-base shadow-lg transition-all duration-300 transform hover:-translate-y-1 active:scale-95 border border-white/10"
            :class="[item.colorClass, item.colSpan || 'col-span-1']" @click="navigate(item.route)">
            <!-- Glass reflection effect -->
            <div class="absolute top-0 left-0 right-0 h-1/2 bg-gradient-to-b from-white/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-t-xl"></div>
            <span class="relative z-10 drop-shadow-sm" :class="item.textClass || 'text-slate-900'">{{ item.name }}</span>
          </button>
        </div>

        <div class="flex justify-center gap-6 items-end w-full relative z-10">
          <!-- Refresh Button -->
          <button class="w-16 h-16 rounded-full bg-slate-800 text-white flex flex-col items-center justify-center shadow-[0_0_20px_rgba(0,0,0,0.5)] hover:shadow-[0_0_30px_rgba(16,185,129,0.4)] hover:bg-slate-700 hover:-translate-y-2 transition-all duration-300 border border-slate-600 hover:border-[#10B981]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          </button>

          <!-- Main Vault (Qasa) Button -->
          <button class="w-32 h-32 rounded-full bg-gradient-to-br from-slate-900 to-black text-[#10B981] flex flex-col items-center justify-center shadow-[0_0_30px_rgba(0,0,0,0.8)] hover:shadow-[0_0_50px_rgba(16,185,129,0.3)] hover:-translate-y-2 transition-all duration-300 border-4 border-[#10B981]/30 group" @click="openVaultModal">
            <span class="text-4xl font-black group-hover:scale-110 transition-transform duration-300 drop-shadow-[0_0_10px_rgba(16,185,129,0.8)]">قاصە</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const registryButtons = ref([
  { name: 'حەواڵەی دۆلار', colorClass: 'bg-gradient-to-br from-[#fdfbfb] to-[#ebedee] shadow-[#ebedee]/20', route: '/registry/2?name=حەواڵەی دۆلار' },
  { name: 'حەواڵەی دینار', colorClass: 'bg-gradient-to-br from-[#ffc3a0] to-[#ffafbd] shadow-[#ffafbd]/20', route: '/registry/1?name=حەواڵەی دینار' },
  { name: 'تۆماری تمەن', colorClass: 'bg-gradient-to-br from-[#ffecd2] to-[#fcb69f] shadow-[#fcb69f]/20', route: '/registry/1?name=تۆماری تمەن' },
  { name: 'متفرقة دۆلار', colorClass: 'bg-gradient-to-br from-[#e0c3fc] to-[#8ec5fc] shadow-[#8ec5fc]/20', route: '/registry/2?name=متفرقة دۆلار' },
  { name: 'متفرقة دینار', colorClass: 'bg-gradient-to-br from-[#e6e9f0] to-[#eef1f5] shadow-[#eef1f5]/20', route: '/registry/1?name=متفرقة دینار' },
  { name: 'تۆماری درهەم', colorClass: 'bg-gradient-to-br from-[#f6d365] to-[#fda085] shadow-[#fda085]/20', route: '/registry/1?name=تۆماری درهەم' },
  { name: 'وەرگرتنی دۆلار', colorClass: 'bg-gradient-to-br from-[#fbc2eb] to-[#a6c1ee] shadow-[#a6c1ee]/20', route: '/registry/2?name=وەرگرتنی دۆلار' },
  { name: 'وەرگرتنی دینار', colorClass: 'bg-gradient-to-br from-[#84fab0] to-[#8fd3f4] shadow-[#8fd3f4]/20', route: '/registry/1?name=وەرگرتنی دینار' },
  { name: 'تۆماری یۆرۆ', colorClass: 'bg-gradient-to-br from-[#a18cd1] to-[#fbc2eb] shadow-[#a18cd1]/20', route: '/registry/2?name=تۆماری یۆرۆ' },
  { name: 'پارەدانی دۆلار', colorClass: 'bg-gradient-to-br from-[#d4fc79] to-[#96e6a1] shadow-[#96e6a1]/20', route: '/registry/2?name=پارەدانی دۆلار' },
  { name: 'پارەدانی دینار', colorClass: 'bg-gradient-to-br from-[#ff0844] to-[#ffb199] shadow-[#ff0844]/30', textClass: 'text-white', route: '/registry/1?name=پارەدانی دینار' },
  { name: 'تۆماری پاوەن', colorClass: 'bg-gradient-to-br from-[#fccb90] to-[#d57eeb] shadow-[#d57eeb]/20', route: '/registry/1?name=تۆماری پاوەن' },
])

const exchangeButtons = ref([
  { name: 'تمەن + دۆلار', colorClass: 'bg-gradient-to-br from-[#43e97b] to-[#38f9d7] shadow-[#43e97b]/30', route: '/pos' },
  { name: 'دینار - دۆلار', colorClass: 'bg-gradient-to-br from-[#e0c3fc] to-[#8ec5fc] shadow-[#8ec5fc]/30', route: '/pos' },
  { name: 'دۆلار + پاوەن', colorClass: 'bg-gradient-to-br from-[#fbc2eb] to-[#a6c1ee] shadow-[#a6c1ee]/30', route: '/pos' },
  { name: 'دۆلار - یۆرۆ', colorClass: 'bg-gradient-to-br from-[#ffecd2] to-[#fcb69f] shadow-[#fcb69f]/30', route: '/pos' },
  { name: 'دۆلار - درهەم', colorClass: 'bg-gradient-to-br from-[#fdfbfb] to-[#ebedee] shadow-white/10', route: '/pos' },
  { name: 'تمەن + درهەم', colorClass: 'bg-gradient-to-br from-[#30cfd0] to-[#330867] shadow-[#30cfd0]/30', textClass: 'text-white', route: '/pos' },
])

const sidebarItems = ref([
  { name: 'حساباتی عام', colorClass: 'bg-gradient-to-r from-blue-500 to-blue-600 shadow-blue-500/30', textClass: 'text-white', route: '/accounts' },
  { name: 'دینار', colorClass: 'bg-gradient-to-r from-emerald-400 to-emerald-500 shadow-emerald-500/30', route: '/registry/1?name=تۆماری دینار' },
  { name: 'دۆلار', colorClass: 'bg-gradient-to-r from-cyan-400 to-blue-400 shadow-blue-400/30', route: '/registry/2?name=تۆماری دۆلار' },
  { name: 'دینار+ دۆلار', colorClass: 'bg-gradient-to-r from-purple-500 to-indigo-500 shadow-indigo-500/30', textClass: 'text-white', route: '/pos' },
  { name: 'ڕەسیدی ئەخیر', colorClass: 'bg-gradient-to-r from-slate-200 to-slate-300 shadow-slate-300/30' },
  { name: 'درهەم', colorClass: 'bg-gradient-to-r from-yellow-300 to-amber-400 shadow-amber-400/30', route: '/registry/1?name=تۆماری درهەم' },
  { name: 'تمەن', colorClass: 'bg-gradient-to-r from-teal-300 to-cyan-400 shadow-cyan-400/30', route: '/registry/1?name=تۆماری تمەن' },
  { name: 'یۆرۆ', colorClass: 'bg-gradient-to-r from-pink-300 to-rose-400 shadow-rose-400/30', route: '/registry/2?name=تۆماری یۆرۆ' },
  { name: 'پاوەن', colorClass: 'bg-gradient-to-r from-indigo-200 to-blue-200 shadow-blue-200/30', colSpan: 'col-span-2', route: '/registry/1?name=تۆماری پاوەن' },
])

const navigate = (route) => {
  if (route) router.push(route)
}

const openVaultModal = () => {
  alert('کردنەوەی قاصە...')
}
</script>

