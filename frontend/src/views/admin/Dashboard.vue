<template>
  <div class="p-4 md:p-8 lg:p-10 space-y-8 max-w-[1900px] mx-auto pb-40 text-slate-900 font-sans" dir="rtl">
    
    <!-- PREMIUM SYSTEM HEADER (The Global Terminal) -->
    <header class="bg-white backdrop-blur-3xl p-8 rounded-[3rem] border border-slate-200/80 shadow-sm relative overflow-hidden flex flex-col md:flex-row justify-between items-center gap-8">
      <div class="absolute inset-0 bg-gradient-to-l from-emerald-500/5 to-transparent pointer-events-none"></div>
      
      <div class="flex items-center gap-6 relative z-10">
        <!-- Branded Logo Box -->
        <div class="w-20 h-20 bg-white rounded-[1.8rem] border border-slate-200 flex items-center justify-center p-3 shadow-md">
           <img src="/logo.png" class="max-w-full max-h-full object-contain" alt="Logo" />
        </div>
        <div>
           <div class="flex items-center gap-3 mb-2">
              <span class="px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[9px] font-black uppercase tracking-widest rounded-full animate-pulse">Global Terminal Active</span>
           </div>
           <h1 class="text-4xl font-black tracking-tight text-slate-900 leading-none">بەخێربێیتەوە، {{ user?.name?.split(' ')[0] || 'بەڕێز' }}</h1>
        </div>
      </div>

      <!-- Real-time Clock & System Status -->
      <div class="flex gap-4 relative z-10">
         <div class="bg-slate-50 p-5 rounded-[2rem] border border-slate-200 flex items-center gap-8 shadow-xs">
            <div class="text-left px-4 border-l border-slate-200">
               <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-1">System Time</span>
               <p class="text-2xl font-black text-slate-900 font-mono">{{ currentTime }}</p>
            </div>
            <div class="text-left px-4">
               <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-1">Market Date</span>
               <p class="text-xs font-black text-slate-600 uppercase">{{ currentDate }}</p>
            </div>
         </div>
      </div>
    </header>

    <!-- KEY PERFORMANCE INDICATORS (IUAS P&L) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in">
       <!-- Revenue -->
       <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200/80 shadow-sm relative overflow-hidden group">
          <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-4">کۆی داهات (Revenue - 4)</span>
          <h3 class="text-3xl font-black text-emerald-600 font-mono tracking-tighter">{{ formatNum(stats.summary?.revenue_iqd) }} <span class="text-xs text-slate-500">IQD</span></h3>
          <div class="mt-4 flex items-center gap-2">
             <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></div>
             <span class="text-[8px] text-slate-500 font-black uppercase">Live Updates</span>
          </div>
       </div>

       <!-- Expenses -->
       <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200/80 shadow-sm relative overflow-hidden">
          <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-4">کۆی خەرجی (Expenses - 3)</span>
          <h3 class="text-3xl font-black text-rose-600 font-mono tracking-tighter">{{ formatNum(stats.summary?.expense_iqd) }} <span class="text-xs text-slate-500">IQD</span></h3>
          <p class="text-[8px] text-slate-500 font-black mt-4 uppercase">Operational Overhead</p>
       </div>

       <!-- Net Profit (USD) -->
       <div class="bg-emerald-600 p-8 rounded-[2.5rem] shadow-emerald-600/20 shadow-xl text-white flex flex-col justify-between">
          <div>
            <span class="text-[9px] font-black opacity-80 uppercase tracking-widest block mb-2">Estimated Net Profit</span>
            <div class="flex items-baseline gap-1">
               <span class="text-xl font-black">$</span>
               <h3 class="text-5xl font-black font-mono tracking-tighter">{{ formatNum(stats.summary?.net_profit_usd) }}</h3>
            </div>
          </div>
          <p class="text-[9px] font-black mt-4 uppercase opacity-80 tracking-widest">Global Valuation Mode</p>
       </div>

       <!-- Period Toggles (Quick Select) -->
       <div class="bg-slate-100 p-4 rounded-[2.5rem] border border-slate-200 flex flex-col gap-2">
          <h4 class="text-[8px] font-black text-slate-600 uppercase tracking-[0.2em] mb-2 text-center">Analytics Period</h4>
          <button v-for="p in ['7d', '30d', '1y']" :key="p" @click="changePeriod(p)"
            class="w-full py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all"
            :class="filters.period === p ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:text-slate-900'">
            {{ p }} View
          </button>
       </div>
    </div>

    <!-- MAIN ANALYTICS CHART & VAULTS MONITOR -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
       <!-- SVG Advanced Multi-Stream Analytics Chart -->
       <div class="lg:col-span-2 bg-white rounded-[3.5rem] border border-slate-200/80 p-10 relative overflow-hidden shadow-sm">
          <div class="flex items-center justify-between mb-12 relative z-10">
             <div>
                <h3 class="text-xl font-black text-slate-900">شیکاری دارایی (Financial Analytics)</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">پوختەی مەعامەلات و قازانجەکان بەپێی نیزامی موەحەد</p>
             </div>
             <div class="flex gap-6">
                <div class="flex items-center gap-2">
                   <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                   <span class="text-[9px] font-black text-slate-600 uppercase">Revenue</span>
                </div>
                <div class="flex items-center gap-2">
                   <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                   <span class="text-[9px] font-black text-slate-600 uppercase">Expense</span>
                </div>
             </div>
          </div>

          <!-- Chart Area -->
          <div class="h-80 w-full relative z-10">
             <!-- Premium Glassmorphic Empty State Overlay -->
             <div v-if="isChartEmpty" class="absolute inset-0 bg-white/90 backdrop-blur-md flex flex-col items-center justify-center rounded-[2rem] border border-slate-200 z-20 animate-fade-in text-center p-6">
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center border border-emerald-200 shadow-[0_0_20px_rgba(16,185,129,0.1)] mb-4">
                   <svg class="w-8 h-8 text-emerald-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                   </svg>
                </div>
                <h4 class="text-base font-black text-slate-900 tracking-tight">ئامارەکان لە چاوەڕوانی جووڵەی دارایی دان</h4>
                <p class="text-slate-600 text-[10px] font-semibold max-w-sm mt-1 leading-relaxed">
                   هێشتا هیچ کڕین و فرۆشتن، مەسروفات یان گواستنەوەیەک بۆ ئەم لقی کارایە تۆمار نەکراوە.
                </p>
             </div>

             <svg class="w-full h-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 1000 400">
                <defs>
                   <linearGradient id="revGrad" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="#10b981" stop-opacity="0.2" />
                      <stop offset="100%" stop-color="transparent" />
                   </linearGradient>
                   <linearGradient id="expGrad" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="#f43f5e" stop-opacity="0.1" />
                      <stop offset="100%" stop-color="transparent" />
                   </linearGradient>
                </defs>
                
                <!-- Grid Lines -->
                <line v-for="i in 5" :key="i" x1="0" :y1="i*80" x2="1000" :y2="i*80" stroke="rgba(0,0,0,0.05)" stroke-width="1" />
                
                <!-- Paths -->
                <path :d="revenuePath" fill="url(#revGrad)" />
                <path :d="revenuePathLine" fill="none" stroke="#10b981" stroke-width="4" stroke-linecap="round" />
                
                <path :d="expensePath" fill="url(#expGrad)" />
                <path :d="expensePathLine" fill="none" stroke="#f43f5e" stroke-width="4" stroke-dasharray="10 6" />
             </svg>
             
             <!-- X-Axis Labels -->
             <div class="absolute bottom-[-30px] inset-x-0 flex justify-between px-4">
                <span v-for="d in stats.chart_data" :key="d.label" class="text-[9px] font-black text-slate-500 uppercase">{{ d.label }}</span>
             </div>
          </div>
       </div>

       <!-- Real-time Vault Balances Strip -->
       <div class="bg-white rounded-[3.5rem] border border-slate-200/80 p-8 flex flex-col shadow-sm">
          <div class="flex items-center justify-between mb-8">
             <h3 class="text-lg font-black tracking-tight text-slate-900">باڵانسی سندوقەکان</h3>
             <router-link to="/admin/accounts" class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 hover:text-slate-900 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
             </router-link>
          </div>
          
          <div class="flex-1 space-y-4 overflow-y-auto custom-scrollbar pr-2">
             <div v-for="v in stats.vault_balances" :key="v.account_name + v.currency_code" 
                  class="bg-slate-50 p-6 rounded-[2rem] border border-slate-200 hover:border-emerald-500/30 transition-all group">
                <div class="flex justify-between items-center mb-2">
                   <span class="text-[9px] font-black text-slate-500 uppercase">{{ v.account_name }}</span>
                   <span class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-[9px] font-black text-emerald-600">{{ v.currency_code }}</span>
                </div>
                <h4 class="text-2xl font-black font-mono tracking-tighter" :class="v.balance >= 0 ? 'text-slate-900' : 'text-rose-600'">
                   {{ formatNum(v.balance) }}
                </h4>
             </div>
          </div>
       </div>
    </div>

    <!-- LIVE JOURNAL FEED & QUICK ACTIONS -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
       <!-- Fast Command Center -->
       <div class="bg-white rounded-[3rem] p-8 border border-slate-200/80 shadow-sm space-y-4">
          <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-6 px-2">کردارە خێراکان</h4>
          <router-link to="/admin/registry" class="flex items-center justify-between p-6 bg-emerald-600 rounded-3xl text-white font-black shadow-lg shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
             <span>تۆمارکردنی مامەڵە</span>
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
          </router-link>
          <router-link to="/admin/transfers" class="flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-200 font-black hover:bg-slate-100 transition-all text-slate-900">
             <span>گواستنەوەی پارە</span>
             <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          </router-link>
          <router-link to="/admin/account-statement" class="flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-200 font-black hover:bg-slate-100 transition-all text-slate-900">
             <span>وردبینی حسابەکان</span>
             <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 2v-6m0 10v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012-2H7a2 2 0 012-2"/></svg>
          </router-link>
       </div>

       <!-- Real-time Event Feed -->
       <div class="lg:col-span-3 bg-white rounded-[3rem] border border-slate-200/80 overflow-hidden shadow-sm">
          <div class="p-8 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
             <h3 class="text-lg font-black tracking-tight text-slate-900">جوڵەی ڕۆژنامەی گشتی (Live Journal)</h3>
             <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Real-time Feed</span>
             </div>
          </div>
          <div class="overflow-x-auto">
             <table class="w-full text-right border-collapse">
                <thead>
                   <tr class="text-[9px] font-black text-slate-600 uppercase tracking-widest border-b border-slate-200 bg-slate-100/50">
                      <th class="px-8 py-6">وەسفی مامەڵە و حیساب</th>
                      <th class="px-8 py-6 text-center">بۆ (Debit)</th>
                      <th class="px-8 py-6 text-center">لە (Credit)</th>
                      <th class="px-8 py-6 text-center">دراو</th>
                      <th class="px-8 py-6">کاتی تۆمار</th>
                   </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                   <tr v-for="j in journals" :key="j.id" class="hover:bg-slate-50 transition-colors group">
                      <td class="px-8 py-5">
                         <div class="flex flex-col gap-1">
                            <span class="text-sm font-black text-slate-900 group-hover:text-emerald-600 transition-colors">{{ j.description }}</span>
                            <div class="flex items-center gap-2">
                               <span class="px-2 py-0.5 bg-slate-100 border border-slate-200 rounded text-[8px] font-black text-slate-600 uppercase">{{ j.account?.type }}</span>
                               <span class="text-[9px] text-slate-600 font-bold">{{ j.account?.name }}</span>
                            </div>
                         </div>
                      </td>
                      <!-- Debit Column -->
                      <td class="px-8 py-5 text-center font-mono text-base font-black">
                         <span v-if="parseFloat(j.debit) > 0" class="text-emerald-600">
                            {{ formatNum(j.debit) }}
                         </span>
                         <span v-else class="text-slate-400">-</span>
                      </td>
                      <!-- Credit Column -->
                      <td class="px-8 py-5 text-center font-mono text-base font-black">
                         <span v-if="parseFloat(j.credit) > 0" class="text-rose-600">
                            {{ formatNum(j.credit) }}
                         </span>
                         <span v-else class="text-slate-400">-</span>
                      </td>
                      <td class="px-8 py-5 text-center">
                         <span class="px-3 py-1 bg-white rounded-lg text-[9px] font-black text-slate-700 border border-slate-200">{{ j.currency?.code }}</span>
                      </td>
                      <td class="px-8 py-5">
                         <span class="text-[10px] font-black text-slate-500 font-mono">{{ formatDate(j.date) }}</span>
                      </td>
                   </tr>
                </tbody>
             </table>
          </div>
       </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue'
import axios from '../../plugins/axios'

const user = ref(null)
const stats = ref({ summary: {}, vault_balances: [], chart_data: [] })
const journals = ref([])
const filters = reactive({ period: '7d' })

const currentTime = ref('')
const currentDate = ref('')

const isChartEmpty = computed(() => {
  if (!stats.value.chart_data || stats.value.chart_data.length === 0) return true;
  return stats.value.chart_data.every(d => Number(d.revenue || 0) === 0 && Number(d.expense || 0) === 0);
});

const revenuePath = computed(() => generatePath(stats.value.chart_data, 'revenue', true))
const revenuePathLine = computed(() => generatePath(stats.value.chart_data, 'revenue', false))
const expensePath = computed(() => generatePath(stats.value.chart_data, 'expense', true))
const expensePathLine = computed(() => generatePath(stats.value.chart_data, 'expense', false))

function generatePath(data, key, close) {
  if (!data?.length) return ''
  const max = Math.max(...data.map(d => Math.max(d.revenue, d.expense))) || 100
  const points = data.map((d, i) => {
    const x = (i / (data.length - 1)) * 1000
    const y = 400 - (d[key] / max) * 350
    return `${x},${y}`
  })
  let path = `M ${points[0]}`
  for (let i = 1; i < points.length; i++) { path += ` L ${points[i]}` }
  if (close) { path += ` L 1000,400 L 0,400 Z` }
  return path
}

async function fetchData() {
  try {
    const [userRes, statsRes, journalsRes] = await Promise.all([
      axios.get('/user'),
      axios.get('/dashboard/stats', { params: filters }),
      axios.get('/journals', { params: { per_page: 8 } })
    ])
    user.value = userRes.data
    stats.value = statsRes.data
    journals.value = journalsRes.data.data
  } catch (e) { console.error(e) }
}

function updateTime() {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' })
  currentDate.value = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })
}

function changePeriod(p) { filters.period = p; fetchData(); }
function formatNum(val) { return new Intl.NumberFormat().format(parseFloat(val || 0)) }
function formatDate(d) { return new Date(d).toLocaleDateString('ku-IQ', { month: 'short', day: 'numeric' }) }

onMounted(() => {
  fetchData()
  updateTime()
  setInterval(updateTime, 1000)
})
</script>

<style scoped>
.shadow-3xl { box-shadow: 0 40px 100px -20px rgba(0,0,0,0.5); }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
