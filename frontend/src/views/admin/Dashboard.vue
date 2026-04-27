<template>
  <div class="p-8 md:p-14 space-y-16 max-w-[1800px] mx-auto pb-32 animate-fade-in">
    <!-- Top Command Header -->
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-10">
       <div dir="rtl" class="text-right">
          <div class="flex items-center gap-3 mb-4">
             <span class="w-12 h-1 bg-emerald-500 rounded-full"></span>
             <h2 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.4em]">Operational Overview</h2>
          </div>
          <h1 class="text-6xl font-black text-white tracking-tighter leading-tight">سڵاو، {{ user?.name?.split(' ')[0] || 'بەڕێز' }}</h1>
          <p class="text-slate-500 font-medium mt-4 text-lg">بەخێربێیتەوە بۆ سەنتەری فەرماندەیی سەروەر موکریان.</p>
       </div>
       
       <div class="flex gap-6">
          <div class="bg-slate-900/40 p-8 rounded-[3rem] border border-white/5 backdrop-blur-3xl flex items-center gap-8 shadow-2xl relative overflow-hidden group">
             <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
             <div class="text-left border-l border-white/5 pl-8 relative z-10">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">System Time</p>
                <p class="text-3xl font-black text-white font-mono">{{ currentTime }}</p>
             </div>
             <div class="text-left relative z-10">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Trading Date</p>
                <p class="text-sm font-black text-slate-400 uppercase">{{ currentDate }}</p>
             </div>
          </div>
       </div>
    </header>

    <!-- Professional Vault Grid (Visual Safe Boxes) -->
    <section class="space-y-8" dir="rtl">
      <div class="flex items-center justify-between px-4">
         <h3 class="text-2xl font-black text-white flex items-center gap-4">
           <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
           باڵانسی سندوقە چالاکەکان
         </h3>
         <router-link to="/admin/accounts" class="text-xs font-black text-slate-500 hover:text-emerald-500 transition-colors uppercase tracking-widest">بەڕێوەبردنی سندوقەکان</router-link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8">
        <div v-for="vault in groupedVaults" :key="vault.name" 
             class="relative group bg-slate-900/40 border border-white/5 p-10 rounded-[3.5rem] overflow-hidden transition-all hover:scale-[1.02] hover:border-emerald-500/30 shadow-3xl">
          
          <!-- Safe Box Decoration -->
          <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all"></div>
          
          <div class="flex justify-between items-center mb-8 relative z-10">
            <div class="w-14 h-14 bg-slate-950 border border-white/5 rounded-2xl flex items-center justify-center text-emerald-400 shadow-inner group-hover:border-emerald-500/50 transition-all">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div class="text-right">
               <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Account Vault</p>
               <span class="text-sm font-black text-white tracking-tight">{{ vault.name }}</span>
            </div>
          </div>

          <div class="space-y-4 relative z-10">
            <div v-for="bal in vault.balances" :key="bal.currency" 
                 class="flex justify-between items-end p-4 bg-slate-950/50 rounded-2xl border border-white/[0.02] group-hover:border-white/5 transition-all">
              <div class="flex flex-col">
                 <span class="text-[9px] font-black text-emerald-500 uppercase">{{ bal.currency }}</span>
                 <span class="text-2xl font-black text-white tracking-tighter">{{ formatNum(bal.balance) }}</span>
              </div>
              <div class="text-[10px] font-bold text-slate-600 mb-1">Available</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Key Performance Indicators (KPIs) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
       <div v-for="stat in stats" :key="stat.name" 
            class="group bg-slate-900/20 border border-white/5 p-10 rounded-[3rem] hover:bg-slate-900/40 transition-all relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-white/[0.01] rounded-full -mr-16 -mt-16"></div>
          <div class="relative z-10 text-right">
             <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-6">{{ stat.name }}</p>
             <h3 class="text-5xl font-black text-white mb-4 tracking-tighter group-hover:text-emerald-400 transition-colors">
                {{ stat.prefix }}{{ formatNum(stat.value) }}
             </h3>
             <div class="flex items-center gap-3 justify-end">
                <div class="flex -space-x-1">
                   <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                   <div class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></div>
                </div>
                <span class="text-[9px] font-black text-emerald-500/80 uppercase tracking-widest">Live Ledger Feed</span>
             </div>
          </div>
       </div>
    </div>

    <!-- Live Market & Rapid Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 h-full">
       <!-- Visual Profit Analytics -->
       <div class="lg:col-span-2 bg-slate-900/40 border border-white/5 rounded-[4rem] p-12 relative overflow-hidden group shadow-3xl">
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-50"></div>
          <div class="flex justify-between items-center mb-12 relative z-10">
             <div dir="rtl" class="text-right">
                <h3 class="text-2xl font-black text-white tracking-tight">ڕەوتی گەشەی قازانج</h3>
                <p class="text-sm text-slate-500 font-medium mt-2">بەراوردی چالاکییەکان لە ٧ ڕۆژی ڕابردوودا</p>
             </div>
             <div class="bg-slate-950 px-6 py-2 rounded-full border border-white/5 text-[10px] font-black text-emerald-500 uppercase tracking-widest">Performance Matrix</div>
          </div>
          
          <div class="h-80 w-full relative z-10">
             <svg class="w-full h-full" viewBox="0 0 700 200" preserveAspectRatio="none">
                <defs>
                   <linearGradient id="chartGlow" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="#10b981" stop-opacity="0.3" />
                      <stop offset="100%" stop-color="#10b981" stop-opacity="0" />
                   </linearGradient>
                </defs>
                <path :d="chartPath" fill="none" stroke="#10b981" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="drop-shadow-[0_0_15px_rgba(16,185,129,0.5)]" />
                <path :d="chartAreaPath" fill="url(#chartGlow)" />
                <circle v-for="(p, i) in chartPoints" :key="i" :cx="p.x" :cy="p.y" r="6" fill="#10b981" stroke="#050505" stroke-width="3" />
             </svg>
             <div class="flex justify-between mt-8 px-4" dir="rtl">
                <span v-for="d in apiStats.chart_data" :key="d.day" class="text-[10px] font-black text-slate-600 uppercase tracking-widest">{{ d.day }}</span>
             </div>
          </div>
       </div>

       <!-- Unified Rapid Actions -->
       <div class="bg-slate-900/40 border border-white/5 rounded-[4rem] p-12 flex flex-col shadow-3xl overflow-hidden relative group">
          <div class="absolute top-0 right-0 w-full h-full bg-emerald-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
          <div class="relative z-10 h-full flex flex-col" dir="rtl">
             <div class="text-right mb-10">
                <h3 class="text-2xl font-black text-white mb-3">تێرمیناڵی خێرا</h3>
                <p class="text-sm text-slate-500 font-medium leading-relaxed">دەستپێکردنی کارەکان بە یەک کلیک لە هەر شوێنێک بیت.</p>
             </div>
             
             <div class="space-y-6 flex-1">
                <router-link to="/admin/exchange" class="flex items-center justify-between p-8 bg-emerald-500 text-slate-950 rounded-[2.5rem] transition-all hover:scale-[1.03] active:scale-95 shadow-2xl shadow-emerald-500/20 group/btn">
                   <div class="flex items-center gap-5">
                      <div class="w-12 h-12 bg-slate-950/10 rounded-2xl flex items-center justify-center">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                      </div>
                      <span class="text-xl font-black">ئاڵوگۆڕی دراو</span>
                   </div>
                   <svg class="w-6 h-6 group-hover/btn:-translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                </router-link>

                <router-link to="/admin/registry" class="flex items-center justify-between p-8 bg-slate-950 border border-white/5 text-white rounded-[2.5rem] transition-all hover:border-emerald-500/40 hover:scale-[1.03] active:scale-95 shadow-2xl group/btn">
                   <div class="flex items-center gap-5">
                      <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                      </div>
                      <span class="text-xl font-black text-right">تۆمارکردن (ووردبینی)</span>
                   </div>
                   <svg class="w-6 h-6 group-hover/btn:-translate-x-2 transition-transform text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                </router-link>
             </div>

             <div class="mt-10 p-6 bg-slate-950/40 rounded-3xl border border-white/[0.02] text-center">
                <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Enterprise Support Active</p>
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
  vault_balances: [],
  chart_data: []
})

// Group balances by account for easier display
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

const stats = computed(() => [
  { name: 'کۆی قازانج (USD)', value: apiStats.value.total_profit_usd, prefix: '$ ' },
  { name: 'مەعامەلات (گشتی)', value: apiStats.value.total_transactions, prefix: '' },
  { name: 'مەعامەلات (ئەمڕۆ)', value: apiStats.value.today_transactions, prefix: '' },
  { name: 'کۆی حسابەکان', value: apiStats.value.total_accounts, prefix: '' },
])

const chartPoints = computed(() => {
  if (!apiStats.value.chart_data?.length) return []
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
