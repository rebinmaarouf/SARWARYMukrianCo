<template>
  <div class="space-y-8 animate-fade-in max-w-[1700px] mx-auto pb-20 text-white font-sans">
    
    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row items-start xl:items-center justify-between gap-6 bg-slate-950/40 p-6 md:p-10 rounded-[2.5rem] border border-white/5 relative overflow-hidden backdrop-blur-3xl shadow-2xl">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-transparent to-emerald-500/10 pointer-events-none"></div>
      <div class="relative z-10 w-full">
        <div class="flex items-center gap-3 mb-2">
           <span class="w-10 h-1 bg-gradient-to-r from-blue-500 to-emerald-500 rounded-full"></span>
           <h2 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.4em]">Enterprise Financial Intelligence</h2>
        </div>
        <h1 class="text-3xl md:text-5xl font-black text-white tracking-tighter">ناوەندی وردبینی (Audit Center)</h1>
        <p class="text-slate-400 text-xs md:text-base font-medium mt-3 max-w-2xl leading-relaxed">
          سیستەمی وردبینی پێشکەوتوو بەپێی "نیزامی موەحەد". ڕاپۆرتی حیسابی کۆتایی مانگ و ساڵ و کۆنتڕۆڵی پارێزراوی سندوقەکان لێرە بەردەستە.
        </p>
      </div>

      <!-- Compact Month/Year Picker -->
      <div class="flex flex-wrap items-center gap-3 bg-slate-900/60 p-3 rounded-[2rem] border border-white/10 shadow-2xl relative z-10 w-full md:w-auto backdrop-blur-2xl">
        <select v-model="selectedMonth" class="bg-slate-950/80 text-white border border-white/10 rounded-xl px-4 py-3 text-xs font-black focus:border-blue-500 outline-none cursor-pointer hover:bg-slate-900 transition-colors">
          <option :value="null">Full Year</option>
          <option v-for="m in 12" :key="m" :value="m">{{ getMonthName(m) }}</option>
        </select>
        <select v-model="selectedYear" class="bg-slate-950/80 text-white border border-white/10 rounded-xl px-4 py-3 text-xs font-black focus:border-blue-500 outline-none cursor-pointer hover:bg-slate-900 transition-colors">
          <option v-for="y in [2024, 2025, 2026]" :key="y" :value="y">{{ y }}</option>
        </select>
        <button @click="fetchAuditData" class="w-full md:w-12 h-12 bg-blue-500 text-slate-950 rounded-xl hover:bg-blue-400 hover:scale-105 active:scale-95 transition-all flex items-center justify-center shadow-lg shadow-blue-500/20">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </button>
      </div>
    </div>

    <!-- Real-time Liquidity Alerts (Task 2) -->
    <div v-if="liquidityAlerts.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
       <div v-for="alert in liquidityAlerts" :key="alert.id" 
            class="group relative bg-rose-500/10 border border-rose-500/20 rounded-[2rem] p-6 overflow-hidden transition-all duration-500 hover:border-rose-500/40 hover:shadow-[0_20px_50px_-15px_rgba(244,63,94,0.3)]">
          <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/5 rounded-full blur-3xl group-hover:bg-rose-500/10 transition-all"></div>
          <div class="flex items-center gap-4 mb-4">
             <div class="w-12 h-12 rounded-2xl bg-rose-500 flex items-center justify-center text-white shadow-lg animate-pulse">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
             </div>
             <div>
                <h3 class="text-xs font-black text-rose-400 uppercase tracking-widest leading-tight">ئاگاداری کەمیی پارە</h3>
                <p class="text-white font-black text-lg tracking-tight">{{ alert.name }}</p>
             </div>
          </div>
          <div class="space-y-2">
             <div v-for="balance in alert.liquidity.filter(b => b.is_low)" :key="balance.currency" class="flex justify-between items-center bg-rose-500/10 rounded-xl px-4 py-3 border border-rose-500/10">
                <span class="text-[10px] font-black text-rose-300 uppercase tracking-wider">{{ balance.currency }}</span>
                <span class="text-lg font-black text-white font-mono">{{ formatNum(balance.balance) }}</span>
             </div>
          </div>
          <p class="text-[9px] font-bold text-rose-300/60 mt-4 leading-tight uppercase">پێویستی بە تێکردنەوەی پارە هەیە بۆ بەردەوامی کارەکان</p>
       </div>
    </div>

    <!-- Unified Audit Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
       <!-- Revenues Card -->
       <div class="bg-slate-900/40 rounded-[2.5rem] border border-white/5 p-8 relative overflow-hidden group shadow-2xl backdrop-blur-3xl">
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
          <div class="flex justify-between items-center mb-8 relative z-10">
             <div class="w-14 h-14 rounded-[1.2rem] bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
             </div>
             <div class="text-right">
                <span class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em]">کۆی گشتی داهاتەکان</span>
                <p class="text-xs text-slate-500 font-bold mt-1">سەرچاوەی قازانج و کۆمیسیۆن</p>
             </div>
          </div>
          <div class="space-y-4 relative z-10">
             <div v-for="(val, curr) in report.summary.total_revenue" :key="curr" class="flex justify-between items-end">
                <span class="text-sm font-black text-slate-400">{{ curr }}</span>
                <span class="text-3xl font-black text-white font-mono tracking-tighter">{{ formatNum(val) }}</span>
             </div>
          </div>
       </div>

       <!-- Expenses Card -->
       <div class="bg-slate-900/40 rounded-[2.5rem] border border-white/5 p-8 relative overflow-hidden group shadow-2xl backdrop-blur-3xl">
          <div class="absolute inset-0 bg-gradient-to-br from-rose-500/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
          <div class="flex justify-between items-center mb-8 relative z-10">
             <div class="w-14 h-14 rounded-[1.2rem] bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
             </div>
             <div class="text-right">
                <span class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em]">کۆی گشتی خەرجییەکان</span>
                <p class="text-xs text-slate-500 font-bold mt-1">مەسروفاتی کۆمپانیا (گروپی ٣)</p>
             </div>
          </div>
          <div class="space-y-4 relative z-10">
             <div v-for="(val, curr) in report.summary.total_expense" :key="curr" class="flex justify-between items-end">
                <span class="text-sm font-black text-slate-400">{{ curr }}</span>
                <span class="text-3xl font-black text-white font-mono tracking-tighter">{{ formatNum(val) }}</span>
             </div>
          </div>
       </div>

       <!-- Net Profit Card (Master Analytics) -->
       <div class="bg-slate-950 rounded-[2.5rem] border-2 border-emerald-500/20 p-8 relative overflow-hidden group shadow-2xl shadow-emerald-500/10">
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent"></div>
          <div class="flex justify-between items-center mb-8 relative z-10">
             <div class="w-14 h-14 rounded-[1.2rem] bg-emerald-500 text-slate-950 flex items-center justify-center shadow-xl shadow-emerald-500/20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
             </div>
             <div class="text-right">
                <span class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em]">پوختەی قازانج و زیان</span>
                <p class="text-xs text-slate-400 font-bold mt-1">ئەو پارەیەی بۆت ماوەتەوە (نێت)</p>
             </div>
          </div>
          <div class="space-y-4 relative z-10">
             <div v-for="(val, curr) in report.summary.net_profit" :key="curr" class="flex justify-between items-end">
                <span class="text-sm font-black text-slate-300">{{ curr }}</span>
                <div class="text-right">
                   <p class="text-4xl font-black font-mono tracking-tighter" :class="val >= 0 ? 'text-emerald-400' : 'text-rose-500'">{{ formatNum(val) }}</p>
                   <span class="text-[10px] font-black uppercase" :class="val >= 0 ? 'text-emerald-500' : 'text-rose-500'">{{ val >= 0 ? 'قازانج' : 'زیان (Deficit)' }}</span>
                </div>
             </div>
          </div>
       </div>
    </div>

    <!-- Detailed Unified Report Sections -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-10">
       <!-- Assets Section (Group 1) -->
       <div class="bg-slate-900/40 rounded-[3rem] border border-white/5 overflow-hidden backdrop-blur-3xl shadow-2xl">
          <div class="p-8 border-b border-white/5 bg-slate-950/40 flex justify-between items-center">
             <div class="flex items-center gap-4">
                <span class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 font-black text-xl">1</span>
                <div>
                   <h3 class="text-xl font-black text-white tracking-tight">ماڵ و سامان (Assets)</h3>
                   <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-0.5">ئەو شتانەی کۆمپانیا خاوەنیانە وەک سندوقەکان</p>
                </div>
             </div>
          </div>
          <div class="overflow-x-auto">
             <table class="w-full text-right" dir="rtl">
                <thead>
                   <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-white/5 bg-slate-950/20">
                      <th class="px-8 py-5">ناوی حیساب</th>
                      <th class="px-8 py-5">کۆد</th>
                      <th class="px-8 py-5 text-center">دراو</th>
                      <th class="px-8 py-5 text-left">باڵانسی نێت</th>
                   </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                   <tr v-for="item in report.assets" :key="item.account_id + item.currency_id" class="hover:bg-white/[0.02] transition-colors group">
                      <td class="px-8 py-5">
                         <div class="flex flex-col">
                            <span class="text-sm font-black text-white group-hover:text-blue-400 transition-colors">{{ item.account.name }}</span>
                            <span class="text-[9px] font-bold text-slate-600 uppercase">{{ item.account.type }}</span>
                         </div>
                      </td>
                      <td class="px-8 py-5">
                         <span class="text-xs font-mono font-black text-slate-400">{{ item.account.code }}</span>
                      </td>
                      <td class="px-8 py-5 text-center">
                         <span class="px-3 py-1 rounded-lg bg-slate-950 text-blue-400 text-[10px] font-black border border-white/5">{{ item.currency.code }}</span>
                      </td>
                      <td class="px-8 py-5 text-left">
                         <span class="text-lg font-black text-white font-mono tracking-tight">{{ formatNum(item.net_balance) }}</span>
                      </td>
                   </tr>
                </tbody>
             </table>
          </div>
       </div>

       <!-- Liabilities & Equity Section (Group 2) -->
       <div class="bg-slate-900/40 rounded-[3rem] border border-white/5 overflow-hidden backdrop-blur-3xl shadow-2xl">
          <div class="p-8 border-b border-white/5 bg-slate-950/40 flex justify-between items-center">
             <div class="flex items-center gap-4">
                <span class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400 font-black text-xl">2</span>
                <div>
                   <h3 class="text-xl font-black text-white tracking-tight">سەرمایە و پابەندییەکان (Liabilities)</h3>
                   <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-0.5">ئەو پارانەی کە کۆمپانیا دەبێت بیداتەوە یان سەرمایەیە</p>
                </div>
             </div>
          </div>
          <div class="overflow-x-auto">
             <table class="w-full text-right" dir="rtl">
                <thead>
                   <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-white/5 bg-slate-950/20">
                      <th class="px-8 py-5">ناوی حیساب</th>
                      <th class="px-8 py-5">کۆد</th>
                      <th class="px-8 py-5 text-center">دراو</th>
                      <th class="px-8 py-5 text-left">باڵانسی نێت</th>
                   </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                   <tr v-for="item in report.liabilities" :key="item.account_id + item.currency_id" class="hover:bg-white/[0.02] transition-colors group">
                      <td class="px-8 py-5">
                         <div class="flex flex-col">
                            <span class="text-sm font-black text-white group-hover:text-purple-400 transition-colors">{{ item.account.name }}</span>
                         </div>
                      </td>
                      <td class="px-8 py-5">
                         <span class="text-xs font-mono font-black text-slate-400">{{ item.account.code }}</span>
                      </td>
                      <td class="px-8 py-5 text-center">
                         <span class="px-3 py-1 rounded-lg bg-slate-950 text-purple-400 text-[10px] font-black border border-white/5">{{ item.currency.code }}</span>
                      </td>
                      <td class="px-8 py-5 text-left">
                         <span class="text-lg font-black text-white font-mono tracking-tight">{{ formatNum(item.net_balance) }}</span>
                      </td>
                   </tr>
                </tbody>
             </table>
          </div>
       </div>

       <!-- Detailed Expenses (Group 3) -->
       <div class="bg-slate-900/40 rounded-[3rem] border border-white/5 overflow-hidden backdrop-blur-3xl shadow-2xl">
          <div class="p-8 border-b border-white/5 bg-slate-950/40 flex justify-between items-center">
             <div class="flex items-center gap-4">
                <span class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-400 font-black text-xl">3</span>
                <div>
                   <h3 class="text-xl font-black text-white tracking-tight">خەرجییە کارگێڕییەکان (Expenses)</h3>
                   <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-0.5">مووچە، کرێ، کارەبا و خەرجییە ڕۆژانەکان</p>
                </div>
             </div>
          </div>
          <div class="overflow-x-auto">
             <table class="w-full text-right" dir="rtl">
                <thead>
                   <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-white/5 bg-slate-950/20">
                      <th class="px-8 py-5">ناوی خەرجی</th>
                      <th class="px-8 py-5 text-center">دراو</th>
                      <th class="px-8 py-5 text-left">کۆی خەرجی</th>
                   </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                   <tr v-for="item in report.expenses" :key="item.account_id + item.currency_id" class="hover:bg-white/[0.02] transition-colors">
                      <td class="px-8 py-5 font-black text-white">{{ item.account.name }}</td>
                      <td class="px-8 py-5 text-center">
                         <span class="px-3 py-1 rounded-lg bg-slate-950 text-rose-400 text-[10px] font-black">{{ item.currency.code }}</span>
                      </td>
                      <td class="px-8 py-5 text-left font-mono font-black text-rose-400 text-lg">
                         {{ formatNum(item.total_debit - item.total_credit) }}
                      </td>
                   </tr>
                </tbody>
             </table>
          </div>
       </div>

       <!-- Detailed Revenues (Group 4) -->
       <div class="bg-slate-900/40 rounded-[3rem] border border-white/5 overflow-hidden backdrop-blur-3xl shadow-2xl">
          <div class="p-8 border-b border-white/5 bg-slate-950/40 flex justify-between items-center">
             <div class="flex items-center gap-4">
                <span class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 font-black text-xl">4</span>
                <div>
                   <h3 class="text-xl font-black text-white tracking-tight">سەرچاوەی داهات و قازانج (Revenues)</h3>
                   <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-0.5">کۆمیسیۆنی حەواڵە و قازانجی ئاڵوگۆڕی دراو</p>
                </div>
             </div>
          </div>
          <div class="overflow-x-auto">
             <table class="w-full text-right" dir="rtl">
                <thead>
                   <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-white/5 bg-slate-950/20">
                      <th class="px-8 py-5">ناوی سەرچاوە</th>
                      <th class="px-8 py-5 text-center">دراو</th>
                      <th class="px-8 py-5 text-left">کۆی قازانج</th>
                   </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                   <tr v-for="item in report.revenues" :key="item.account_id + item.currency_id" class="hover:bg-white/[0.02] transition-colors">
                      <td class="px-8 py-5 font-black text-white">{{ item.account.name }}</td>
                      <td class="px-8 py-5 text-center">
                         <span class="px-3 py-1 rounded-lg bg-slate-950 text-emerald-400 text-[10px] font-black">{{ item.currency.code }}</span>
                      </td>
                      <td class="px-8 py-5 text-left font-mono font-black text-emerald-400 text-lg">
                         {{ formatNum(item.total_credit - item.total_debit) }}
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
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'

const selectedMonth = ref(new Date().getMonth() + 1)
const selectedYear = ref(new Date().getFullYear())
const liquidityAlerts = ref([])
const report = ref({
  assets: [], liabilities: [], expenses: [], revenues: [],
  summary: { total_revenue: {}, total_expense: {}, net_profit: {} }
})

async function fetchAuditData() {
  try {
    const { data } = await axios.get('/reports/unified', {
      params: { year: selectedYear.value, month: selectedMonth.value }
    })
    report.value = data
  } catch (e) {
    console.error(e)
    Swal.fire({ icon: 'error', title: 'Error fetching report', background: '#020617', color: '#fff' })
  }
}

async function fetchLiquidity() {
  try {
    const { data } = await axios.get('/reports/liquidity')
    liquidityAlerts.value = data.filter(v => v.liquidity.some(b => b.is_low))
  } catch (e) {
    console.error(e)
  }
}

function getMonthName(m) {
  return new Date(2024, m - 1, 1).toLocaleString('ku-IQ', { month: 'long' })
}

function formatNum(n) {
  return new Intl.NumberFormat().format(Math.abs(n || 0))
}

onMounted(() => {
  fetchAuditData()
  fetchLiquidity()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
