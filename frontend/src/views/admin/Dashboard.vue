<template>
  <div class="p-6 md:p-12 space-y-12 max-w-[1600px] mx-auto">
    <!-- Top Banner: Executive Feel -->
    <header class="flex flex-col md:flex-row justify-between items-center gap-8">
       <div>
          <h2 class="text-xs font-black text-emerald-500 uppercase tracking-[0.3em] mb-3">Enterprise Intelligence</h2>
          <h1 class="text-5xl font-black text-white tracking-tighter">سڵاو، {{ user?.name?.split(' ')[0] || 'بەڕێز' }}</h1>
          <p class="text-slate-500 font-medium mt-3">بەخێرهاتیەوە بۆ ناوەندی بەڕێوەبردنی سەروەر موکریان.</p>
       </div>
       <div class="bg-slate-900/40 p-6 rounded-[2.5rem] border border-slate-800 backdrop-blur-xl flex items-center gap-6">
          <div class="text-left border-l border-slate-800 pl-6">
             <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">کاتی ئێستا</p>
             <p class="text-xl font-black text-white">{{ currentTime }}</p>
          </div>
          <div class="text-left">
             <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">بەروار</p>
             <p class="text-sm font-bold text-slate-300">{{ currentDate }}</p>
          </div>
       </div>
    </header>

    <!-- Real Stats: Glassmorphism Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
       <div v-for="stat in stats" :key="stat.name" class="group bg-slate-900/30 border border-slate-800 p-8 rounded-[3rem] hover:border-emerald-500/30 transition-all relative overflow-hidden">
          <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all"></div>
          <div class="relative z-10 text-right">
             <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">{{ stat.name }}</p>
             <h3 class="text-4xl font-black text-white mb-2 tracking-tight">
                {{ stat.prefix }}{{ formatNum(stat.value) }}
             </h3>
             <div class="flex items-center gap-2 justify-end">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Real-time Data</span>
             </div>
          </div>
       </div>
    </div>

    <!-- Central Analytics: Real Profit Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
       <div class="lg:col-span-2 bg-slate-900/30 border border-slate-800 rounded-[3rem] p-10 relative overflow-hidden">
          <div class="flex justify-between items-center mb-10">
             <div>
                <h3 class="text-xl font-black text-white">ئاماری گەشەی قازانج</h3>
                <p class="text-xs text-slate-500 font-bold mt-1">بەدواداچوونی ٧ ڕۆژی ڕابردوو (بە دۆلار)</p>
             </div>
             <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                   <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                   <span class="text-[10px] font-black text-slate-400 uppercase">قازانجی سەلمێندراو</span>
                </div>
             </div>
          </div>
          
          <div class="h-64 w-full relative">
             <svg class="w-full h-full" viewBox="0 0 700 200" preserveAspectRatio="none">
                <defs>
                   <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="#10b981" stop-opacity="0.2" />
                      <stop offset="100%" stop-color="#10b981" stop-opacity="0" />
                   </linearGradient>
                </defs>
                <line v-for="i in 5" :key="i" x1="0" :y1="i*40" x2="700" :y2="i*40" stroke="#1e293b" stroke-width="1" />
                <path :d="chartPath" fill="none" stroke="#10b981" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                <path :d="chartAreaPath" fill="url(#chartGradient)" />
                <circle v-for="(p, i) in chartPoints" :key="i" :cx="p.x" :cy="p.y" r="5" fill="#10b981" />
             </svg>
             <div class="flex justify-between mt-4 px-2">
                <span v-for="d in apiStats.chart_data" :key="d.day" class="text-[9px] font-black text-slate-600 uppercase">{{ d.day }}</span>
             </div>
          </div>
       </div>

       <!-- Quick Actions Panel -->
       <div class="bg-slate-900/30 border border-slate-800 rounded-[3rem] p-10 flex flex-col justify-between text-right">
          <div>
             <h3 class="text-xl font-black text-white mb-2">کارە خێراکان</h3>
             <p class="text-xs text-slate-500 font-bold mb-8">دەستپێکردنی مەعامەلە یان ووردبینی حسابات.</p>
             
             <div class="space-y-4">
                <router-link to="/exchange" class="flex items-center justify-between p-6 bg-emerald-500 hover:bg-emerald-400 text-white rounded-3xl transition-all shadow-lg shadow-emerald-900/20 group">
                   <span class="font-black">مەعامەلەی نوێ</span>
                   <svg class="w-6 h-6 group-hover:-translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4 4H3"/></svg>
                </router-link>
                <router-link to="/general-accounts" class="flex items-center justify-between p-6 bg-slate-800 hover:bg-slate-700 text-white rounded-3xl transition-all group">
                   <span class="font-black text-sm">ڕاپۆرتی ووردبینی</span>
                   <svg class="w-5 h-5 text-slate-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </router-link>
             </div>
          </div>
          
          <div class="pt-8 border-t border-slate-800/50">
             <div class="flex items-center gap-4 p-4 bg-emerald-500/5 rounded-2xl border border-emerald-500/10 justify-end">
                <p class="text-[10px] font-bold text-emerald-500">سەرجەم سیستمەکان بە ئاسایی کاردەکەن</p>
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
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
const apiStats = ref({
  total_transactions: 0,
  today_transactions: 0,
  total_accounts: 0,
  total_profit_usd: 0,
  chart_data: []
})

const currentTime = ref('')
const currentDate = ref('')

const stats = computed(() => [
  { name: 'کۆی قازانج (USD)', value: apiStats.value.total_profit_usd, prefix: '$ ' },
  { name: 'مەعامەلات (گشتی)', value: apiStats.value.total_transactions, prefix: '' },
  { name: 'مەعامەلات (ئەمڕۆ)', value: apiStats.value.today_transactions, prefix: '' },
  { name: 'کۆی حسابەکان', value: apiStats.value.total_accounts, prefix: '' },
])

const chartPoints = computed(() => {
  if (!apiStats.value.chart_data.length) return []
  const maxProfit = Math.max(...apiStats.value.chart_data.map(d => d.profit), 10)
  return apiStats.value.chart_data.map((d, i) => ({
    x: i * 115,
    y: 180 - (d.profit / maxProfit * 150)
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
  } catch (e) {
    router.push('/login')
  }
}

function updateTime() {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' })
  currentDate.value = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })
}

function formatNum(val) {
  return new Intl.NumberFormat().format(val)
}

onMounted(() => {
  fetchData()
  updateTime()
  setInterval(updateTime, 1000)
})
</script>
