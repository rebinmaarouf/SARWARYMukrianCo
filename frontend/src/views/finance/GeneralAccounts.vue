<template>
  <div class="p-6 md:p-10 space-y-8 max-w-[1600px] mx-auto" dir="rtl">
    <!-- Executive Top Header -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-10">
       <div>
          <h1 class="text-4xl font-black text-white tracking-tighter">ڕاپۆرتی ووردبینی گشتی</h1>
          <p class="text-slate-400 font-medium mt-2 flex items-center gap-2">
             <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
             سیستەمی حساباتی موەحەدی کۆمپانیای سەروەر موکریان
          </p>
       </div>
       
       <div class="flex flex-wrap items-center gap-3 bg-slate-900/60 backdrop-blur-xl p-3 rounded-[2rem] border border-slate-800 shadow-2xl">
          <div class="flex flex-col px-4 border-l border-slate-800/50">
             <span class="text-[9px] font-black text-slate-500 uppercase">لە بەرواری</span>
             <input v-model="filters.start_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-white focus:ring-0 cursor-pointer" />
          </div>
          <div class="flex flex-col px-4 border-l border-slate-800/50">
             <span class="text-[9px] font-black text-slate-500 uppercase">بۆ بەرواری</span>
             <input v-model="filters.end_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-white focus:ring-0 cursor-pointer" />
          </div>
          <div class="flex flex-col px-4">
             <span class="text-[9px] font-black text-slate-500 uppercase">هەڵبژاردنی حساب</span>
             <select v-model="filters.account_id" class="bg-transparent border-none p-0 text-sm font-black text-emerald-400 focus:ring-0 cursor-pointer min-w-[150px]">
                <option :value="null">هەموو حسابەکان</option>
                <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.name }}</option>
             </select>
          </div>
          <button @click="fetchReport" :disabled="loading" class="bg-emerald-600 hover:bg-emerald-500 text-white w-12 h-12 rounded-2xl flex items-center justify-center transition-all shadow-lg shadow-emerald-900/20">
             <svg v-if="!loading" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
             <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
          </button>
       </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8 items-start">
       <!-- Left: Financial Status Cards -->
       <div class="xl:col-span-3 space-y-8">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
             <div v-for="(data, currency) in reportData.currencies" :key="currency" 
                  class="bg-slate-900/40 border border-slate-800 p-8 rounded-[2.5rem] relative overflow-hidden group hover:border-emerald-500/30 transition-all">
                <div class="flex justify-between items-center mb-6">
                   <div class="px-4 py-1.5 bg-emerald-500/10 text-emerald-400 text-xs font-black rounded-full">{{ currency }} ASSET</div>
                   <span class="text-[10px] font-bold text-slate-500">ڕەسیدی پێشوو: {{ formatNum(data.opening_balance) }}</span>
                </div>
                
                <h3 class="text-4xl font-black text-white mb-2">{{ formatNum(data.final_balance) }}</h3>
                <div class="flex items-center gap-2 mb-6">
                   <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                      <div class="h-full bg-emerald-500" style="width: 100%"></div>
                   </div>
                   <span class="text-[10px] font-black text-emerald-500">+{{ formatNum(data.period_balance) }}</span>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-6 border-t border-slate-800/50">
                   <div>
                      <p class="text-[9px] font-black text-slate-500 uppercase">کۆی کڕین</p>
                      <p class="text-sm font-bold text-white">{{ formatNum(data.total_buy) }}</p>
                   </div>
                   <div>
                      <p class="text-[9px] font-black text-slate-500 uppercase">کۆی فرۆشتن</p>
                      <p class="text-sm font-bold text-white">{{ formatNum(data.total_sell) }}</p>
                   </div>
                   <div class="col-span-2 pt-2">
                      <p class="text-[9px] font-black text-emerald-500 uppercase">قازانجی سەلمێندراو ({{ data.secondary_symbol }})</p>
                      <p class="text-xl font-black text-emerald-400">+{{ formatNum(data.profit, data.secondary_symbol === 'USD' ? 3 : 0) }}</p>
                   </div>
                </div>
             </div>
          </div>

          <!-- Detailed Audit Table (Scrollable for scalability) -->
          <div class="bg-slate-900/40 border border-slate-800 rounded-[3rem] overflow-hidden shadow-2xl flex flex-col">
             <div class="p-8 border-b border-slate-800 flex justify-between items-center bg-slate-900/20">
                <h3 class="text-xl font-black text-white">ووردەکاری مەعامەلات (Audit Log)</h3>
                <span class="px-4 py-1 bg-slate-800 rounded-full text-[10px] font-black text-slate-400">پیشاندانی دوایین مەعامەلات</span>
             </div>
             <div class="overflow-y-auto max-h-[600px] custom-scrollbar">
                <table class="w-full text-right border-collapse">
                   <thead class="sticky top-0 z-20 bg-slate-950 shadow-xl">
                      <tr class="text-[10px] font-black text-slate-500 uppercase">
                         <th class="px-8 py-5">کات</th>
                         <th class="px-8 py-5">حساب</th>
                         <th class="px-8 py-5">جۆری مەعامەلە</th>
                         <th class="px-8 py-5">بڕی دراو</th>
                         <th class="px-8 py-5">قازانج</th>
                      </tr>
                   </thead>
                   <tbody class="divide-y divide-slate-800/30">
                      <tr v-for="t in reportData.transactions" :key="t.id" class="hover:bg-slate-800/40 transition-all group">
                         <td class="px-8 py-4 text-xs font-bold text-slate-500">{{ new Date(t.created_at).toLocaleTimeString() }}</td>
                         <td class="px-8 py-4 text-sm font-black text-white">{{ t.account?.name }}</td>
                         <td class="px-8 py-4">
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase" :class="t.type === 'buy' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'">
                               {{ t.type === 'buy' ? 'کڕین' : 'فرۆشتن' }} {{ t.primary_currency }}
                            </span>
                         </td>
                         <td class="px-8 py-4 text-sm font-black text-slate-300">{{ formatNum(t.primary_amount) }} {{ t.primary_currency }}</td>
                         <td class="px-8 py-4 text-sm font-black text-emerald-400">+{{ formatNum(t.profit, t.secondary_currency === 'USD' ? 3 : 0) }} <span class="text-[10px] opacity-50">{{ t.secondary_currency }}</span></td>
                      </tr>
                   </tbody>
                </table>
             </div>
          </div>
       </div>

       <!-- Right: Executive Summary Sidebar -->
       <div class="space-y-6">
          <div class="bg-gradient-to-br from-emerald-600 to-teal-700 p-8 rounded-[3rem] shadow-2xl shadow-emerald-900/20 relative overflow-hidden">
             <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-2xl"></div>
             <p class="text-[10px] font-black text-white/70 uppercase tracking-widest mb-2">کۆی قازانج (NET WORTH GROWTH)</p>
             <h2 class="text-4xl font-black text-white mb-1">{{ formatNum(reportData.total_profit_iqd, 0) }}</h2>
             <p class="text-lg font-bold text-white/90">دیناری عێراقی</p>
             <div class="mt-8 pt-6 border-t border-white/10">
                <div class="flex justify-between items-center text-white/80">
                   <span class="text-xs font-bold">بە دۆلار:</span>
                   <span class="text-xl font-black">${{ formatNum(reportData.total_profit_usd, 2) }}</span>
                </div>
                <p class="text-[10px] text-white/50 mt-1">Valuation Rate: 1515 IQD</p>
             </div>
          </div>

          <div class="bg-slate-900/40 border border-slate-800 p-8 rounded-[2.5rem]">
             <h4 class="text-xs font-black text-slate-400 uppercase mb-6">ئاماری گشتی</h4>
             <div class="space-y-4">
                <div class="flex justify-between items-center">
                   <span class="text-xs font-bold text-slate-500">مەعامەلات:</span>
                   <span class="text-sm font-black text-white">{{ reportData.transaction_count }}</span>
                </div>
                <div class="flex justify-between items-center">
                   <span class="text-xs font-bold text-slate-500">بەروار:</span>
                   <span class="text-[10px] font-black text-emerald-500">{{ filters.start_date }}</span>
                </div>
             </div>
          </div>
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'

const accounts = ref([])
const loading = ref(false)
const filters = ref({
  account_id: null,
  start_date: new Date().toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0]
})

const reportData = ref({
  total_profit_iqd: 0,
  total_profit_usd: 0,
  transaction_count: 0,
  currencies: {},
  transactions: []
})

async function fetchAccounts() {
  const { data } = await axios.get('/accounts?per_page=1000')
  accounts.value = data.data
}

async function fetchReport() {
  loading.value = true
  try {
    const { data } = await axios.get('/reports/profit', { params: filters.value })
    reportData.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

function formatNum(val, decimals = 0) {
  if (!val && val !== 0) return '0'
  const num = parseFloat(val)
  // If it's a very small decimal (like 0.007), use 3 decimals automatically
  const d = (num > 0 && num < 1) ? 3 : decimals
  return new Intl.NumberFormat().format(num.toFixed(d))
}

onMounted(() => {
  fetchAccounts()
  fetchReport()
})
</script>
