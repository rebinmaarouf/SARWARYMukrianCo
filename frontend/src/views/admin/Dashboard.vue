<template>
  <div class="p-6 md:p-10 space-y-8 max-w-[1920px] mx-auto pb-32 animate-fade-in text-white font-sans">
    
    <!-- Top Navbar & Greeting -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-slate-900/50 backdrop-blur-3xl border border-white/5 p-6 rounded-3xl shadow-2xl relative overflow-hidden">
       <div class="absolute right-0 top-0 w-64 h-full bg-gradient-to-l from-emerald-500/10 to-transparent pointer-events-none"></div>
       <div dir="rtl" class="flex items-center gap-6 relative z-10">
          <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-2xl shadow-emerald-500/20 relative group cursor-pointer overflow-hidden">
             <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent"></div>
             <!-- Embedded Logo -->
             <img :src="logoUrl" alt="Logo" class="w-12 h-12 object-contain relative z-10 group-hover:scale-110 transition-transform duration-500" @error="logoError = true" v-if="!logoError" />
             <div v-else class="text-emerald-500 font-black text-xl relative z-10">{{ user?.name?.charAt(0) }}</div>
          </div>
          <div>
             <div class="flex items-center gap-3 mb-1">
                <div class="flex h-2 w-2 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </div>
                <h2 class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Global Terminal Active</h2>
             </div>
             <h1 class="text-3xl font-black tracking-tight leading-none">بەخێربێیتەوە، {{ user?.name?.split(' ')[0] || 'بەڕێز' }}</h1>
          </div>
       </div>
       
       <div class="flex gap-4 relative z-10">
          <div class="bg-slate-950 border border-white/5 px-6 py-3 rounded-2xl flex items-center gap-6">
             <div class="text-left">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">System Time</p>
                <p class="text-xl font-black text-white font-mono">{{ currentTime }}</p>
             </div>
             <div class="w-px h-8 bg-white/10"></div>
             <div class="text-left">
                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Date</p>
                <p class="text-xs font-black text-slate-400 uppercase mt-1">{{ currentDate }}</p>
             </div>
          </div>
       </div>
    </header>

    <!-- Compact Vault Balances (Ticker/Horizontal Strip Style) -->
    <section dir="rtl" class="space-y-4">
      <div class="flex items-center justify-between px-2">
         <h3 class="text-lg font-black text-white flex items-center gap-3">
           <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
           باڵانسی سندوقەکان
         </h3>
         <router-link to="/admin/accounts" class="text-[10px] font-black text-slate-500 hover:text-emerald-500 transition-colors uppercase tracking-widest bg-slate-900 px-3 py-1.5 rounded-lg border border-white/5">بەڕێوەبردن</router-link>
      </div>

      <!-- Compact Horizontal Scrollable Area -->
      <div class="flex gap-4 overflow-x-auto pb-4 custom-scrollbar snap-x">
        <div v-for="vault in groupedVaults" :key="vault.name" 
             class="snap-start flex-none w-[320px] bg-slate-900/40 border border-white/5 rounded-3xl p-5 hover:bg-slate-900/60 hover:border-white/10 transition-all flex flex-col gap-4">
           
           <div class="flex justify-between items-center border-b border-white/5 pb-3">
              <span class="text-sm font-black text-white truncate max-w-[200px]">{{ vault.name }}</span>
              <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-500">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
              </div>
           </div>

           <div class="space-y-2">
             <div v-for="bal in vault.balances" :key="bal.currency" class="flex justify-between items-center bg-slate-950 px-3 py-2 rounded-xl">
                <span class="text-[10px] font-black text-slate-500 uppercase">{{ bal.currency }}</span>
                <span class="text-base font-black tracking-tight" :class="bal.balance >= 0 ? 'text-white' : 'text-rose-400'">{{ formatNum(bal.balance) }}</span>
             </div>
             <div v-if="vault.balances.length === 0" class="text-xs text-slate-600 text-center py-2 font-bold">هیچ باڵانسێک نییە</div>
           </div>
        </div>
      </div>
    </section>

    <!-- Main Dynamic Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full">
       
       <!-- Market Analytics & Charts (Takes 8 columns) -->
       <div class="lg:col-span-8 bg-slate-900/40 border border-white/5 rounded-[2.5rem] p-8 flex flex-col relative overflow-hidden">
          <div class="absolute -top-32 -right-32 w-96 h-96 bg-emerald-500/5 rounded-full blur-[100px] pointer-events-none"></div>
          
          <div class="flex justify-between items-center mb-8 relative z-10">
             <div dir="rtl">
                <h3 class="text-xl font-black text-white">شیکاری دارایی (Financial Analytics)</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">پوختەی مەعامەلات و قازانجەکان لە ٧ ڕۆژی ڕابردوودا</p>
             </div>
             <div class="flex items-center gap-2">
                <button class="px-3 py-1.5 bg-emerald-500 text-slate-950 rounded-lg text-[10px] font-black uppercase">1W</button>
                <button class="px-3 py-1.5 bg-slate-950 text-slate-500 rounded-lg text-[10px] font-black uppercase border border-white/5 hover:text-white">1M</button>
             </div>
          </div>

          <!-- The Graph Area -->
          <div class="flex-1 relative min-h-[300px] z-10 w-full bg-slate-950/50 rounded-3xl border border-white/5 p-6">
             <!-- Background Grid Lines -->
             <div class="absolute inset-0 flex flex-col justify-between px-6 py-6 opacity-10 pointer-events-none">
                <div class="w-full h-px bg-white"></div>
                <div class="w-full h-px bg-white"></div>
                <div class="w-full h-px bg-white"></div>
                <div class="w-full h-px bg-white"></div>
                <div class="w-full h-px bg-white"></div>
             </div>

             <!-- SVG Chart -->
             <svg class="w-full h-full overflow-visible" viewBox="0 0 800 200" preserveAspectRatio="none">
                <defs>
                   <linearGradient id="profitGrad" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="#10b981" stop-opacity="0.4" />
                      <stop offset="100%" stop-color="#10b981" stop-opacity="0" />
                   </linearGradient>
                </defs>
                <!-- Line -->
                <path :d="chartPath" fill="none" stroke="#10b981" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" class="drop-shadow-[0_4px_12px_rgba(16,185,129,0.4)]" />
                <!-- Area -->
                <path :d="chartAreaPath" fill="url(#profitGrad)" />
                <!-- Dots -->
                <g v-for="(p, i) in chartPoints" :key="i">
                   <circle :cx="p.x" :cy="p.y" r="5" fill="#10b981" stroke="#020617" stroke-width="3" class="hover:r-7 transition-all cursor-pointer" />
                   <text v-if="apiStats.chart_data[i]" :x="p.x" :y="p.y - 15" fill="#94a3b8" font-size="10" font-weight="bold" text-anchor="middle" class="opacity-0 hover:opacity-100 transition-opacity">
                      ${{ formatNum(apiStats.chart_data[i].profit) }}
                   </text>
                </g>
             </svg>
             
             <!-- X Axis Labels -->
             <div class="absolute bottom-2 left-6 right-6 flex justify-between px-2">
                <span v-for="d in apiStats.chart_data" :key="d.day" class="text-[9px] font-black text-slate-600 uppercase">{{ d.day }}</span>
             </div>
          </div>
       </div>

       <!-- Right Side Widgets (Takes 4 columns) -->
       <div class="lg:col-span-4 flex flex-col gap-6">
          
          <!-- Key Metrics -->
          <div class="grid grid-cols-2 gap-4">
             <!-- Metric 1 -->
             <div class="bg-slate-900/40 p-5 rounded-3xl border border-white/5 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-3">
                   <div class="w-8 h-8 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-500">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                   </div>
                </div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-10">Total Profit</p>
                <p class="text-2xl font-black text-white mt-1 group-hover:text-emerald-400 transition-colors">${{ formatNum(apiStats.total_profit_usd) }}</p>
             </div>
             
             <!-- Metric 2 -->
             <div class="bg-slate-900/40 p-5 rounded-3xl border border-white/5 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-3">
                   <div class="w-8 h-8 bg-blue-500/10 rounded-full flex items-center justify-center text-blue-500">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                   </div>
                </div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-10">Transactions</p>
                <p class="text-2xl font-black text-white mt-1 group-hover:text-blue-400 transition-colors">{{ formatNum(apiStats.total_transactions) }}</p>
             </div>
          </div>

          <!-- Quick Actions Panel -->
          <div class="bg-slate-900/40 border border-white/5 rounded-[2.5rem] p-6 flex-1 flex flex-col relative" dir="rtl">
             <h3 class="text-lg font-black text-white mb-6">کردارە خێراکان</h3>
             <div class="space-y-4 flex-1">
                <router-link to="/admin/exchange" class="flex items-center justify-between p-5 bg-gradient-to-l from-emerald-500 to-teal-500 text-slate-950 rounded-2xl transition-all hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-emerald-500/20 group/action">
                   <div class="flex items-center gap-4">
                      <div class="w-10 h-10 bg-slate-950/20 rounded-xl flex items-center justify-center">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                      </div>
                      <span class="text-base font-black">ئاڵوگۆڕی دراو</span>
                   </div>
                   <svg class="w-5 h-5 group-hover/action:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                </router-link>

                <router-link to="/admin/transfers" class="flex items-center justify-between p-5 bg-slate-950 border border-white/5 rounded-2xl transition-all hover:border-emerald-500/40 hover:scale-[1.02] active:scale-[0.98] group/action">
                   <div class="flex items-center gap-4">
                      <div class="w-10 h-10 bg-emerald-500/10 text-emerald-500 rounded-xl flex items-center justify-center">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                      </div>
                      <span class="text-base font-black text-white">گواستنەوەی پارە</span>
                   </div>
                   <svg class="w-5 h-5 text-slate-500 group-hover/action:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                </router-link>
                
                <router-link to="/admin/registry" class="flex items-center justify-between p-5 bg-slate-950 border border-white/5 rounded-2xl transition-all hover:border-blue-500/40 hover:scale-[1.02] active:scale-[0.98] group/action">
                   <div class="flex items-center gap-4">
                      <div class="w-10 h-10 bg-blue-500/10 text-blue-500 rounded-xl flex items-center justify-center">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                      </div>
                      <span class="text-base font-black text-white">تۆمارکردن (مەعامەلات)</span>
                   </div>
                   <svg class="w-5 h-5 text-slate-500 group-hover/action:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                </router-link>
             </div>
          </div>
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../../plugins/axios'

const router = useRouter()
const user = ref(null)

const logoUrl = '/logo.png'
const logoError = ref(false)

const apiStats = ref({
  total_transactions: 0,
  today_transactions: 0,
  total_accounts: 0,
  total_profit_usd: 0,
  vault_balances: [],
  chart_data: []
})

// Group balances by account for compact display
const groupedVaults = computed(() => {
  const groups = {}
  apiStats.value.vault_balances.forEach(vb => {
    if (!groups[vb.account_name]) {
      groups[vb.account_name] = { name: vb.account_name, balances: [] }
    }
    groups[vb.account_name].balances.push({
      currency: vb.currency_code,
      balance: vb.balance
    })
  })
  return Object.values(groups)
})

const currentTime = ref('')
const currentDate = ref('')

// Chart Math
const chartPoints = computed(() => {
  if (!apiStats.value.chart_data?.length) return []
  const data = apiStats.value.chart_data
  const maxProfit = Math.max(...data.map(d => d.profit), 10)
  const width = 800
  const height = 180
  const step = width / Math.max(data.length - 1, 1)
  
  return data.map((d, i) => ({
    x: i * step,
    y: height - (d.profit / maxProfit * (height - 20))
  }))
})

const chartPath = computed(() => {
  if (chartPoints.value.length < 2) return ''
  return 'M ' + chartPoints.value.map(p => `${p.x},${p.y}`).join(' L ')
})

const chartAreaPath = computed(() => {
  if (chartPoints.value.length < 2) return ''
  const first = chartPoints.value[0]
  const last = chartPoints.value[chartPoints.value.length - 1]
  return `M ${first.x},200 L ` + chartPoints.value.map(p => `${p.x},${p.y}`).join(' L ') + ` L ${last.x},200 Z`
})

async function fetchData() {
  try {
    const [userRes, statsRes] = await Promise.all([
      axios.get('/user'),
      axios.get('/dashboard/stats')
    ])
    user.value = userRes.data
    apiStats.value = statsRes.data
    
    // Inject fake chart data if empty (for visual demo)
    if (!apiStats.value.chart_data || apiStats.value.chart_data.length === 0) {
      apiStats.value.chart_data = [
         { day: 'Mon', profit: 120 }, { day: 'Tue', profit: 150 }, { day: 'Wed', profit: 140 },
         { day: 'Thu', profit: 210 }, { day: 'Fri', profit: 180 }, { day: 'Sat', profit: 320 }, { day: 'Sun', profit: 280 }
      ]
    }
  } catch (e) {
    console.error(e)
    router.push('/login')
  }
}

function updateTime() {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' })
  currentDate.value = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })
}

function formatNum(val) {
  return new Intl.NumberFormat().format(val || 0)
}

onMounted(() => {
  fetchData()
  updateTime()
  setInterval(updateTime, 1000)
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.custom-scrollbar::-webkit-scrollbar { height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.4); }
</style>
