<template>
  <div class="p-6 md:p-10 space-y-8 max-w-[1700px] mx-auto pb-32 text-white font-sans" dir="rtl">
    
    <!-- Premium Header & Print Action -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-10 bg-slate-900/40 backdrop-blur-3xl p-10 rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-l from-emerald-500/5 to-transparent pointer-events-none"></div>
      <div class="relative z-10">
        <div class="flex items-center gap-3 mb-3">
           <span class="w-10 h-1 bg-emerald-500 rounded-full"></span>
           <h2 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.4em]">Audit-Ready Financial Statement</h2>
        </div>
        <h1 class="text-4xl font-black text-white tracking-tighter">پوختەی حیسابی کڕیار</h1>
        <p class="text-slate-400 font-medium mt-2 flex items-center gap-2">
          ووردەکاری تەواوی جوڵەی حیساب و قەرز و کەسرەکان
        </p>
      </div>

      <!-- Filters & Selection Terminal -->
      <div class="flex flex-wrap items-center gap-4 bg-slate-950/80 p-3 rounded-[2.5rem] border border-white/5 shadow-2xl relative z-10 no-print">
        <div class="flex flex-col px-6 border-l border-white/10">
          <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1">Select Account</span>
          <select v-model="filters.account_id" @change="fetchStatement" class="bg-transparent border-none p-0 text-sm font-black text-emerald-400 focus:ring-0 cursor-pointer min-w-[250px]">
            <option :value="null" disabled>حیسابێک هەڵبژێرە...</option>
            <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.code }} - {{ a.name }}</option>
          </select>
        </div>
        <div class="flex flex-col px-6 border-l border-white/10">
          <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1">Starting Date</span>
          <input v-model="filters.start_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-white focus:ring-0 cursor-pointer" />
        </div>
        <button @click="fetchStatement" :disabled="loading || !filters.account_id" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 w-14 h-14 rounded-2xl flex items-center justify-center transition-all shadow-xl shadow-emerald-500/20 active:scale-95">
          <svg v-if="!loading" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <div v-else class="w-6 h-6 border-3 border-slate-900/30 border-t-slate-900 rounded-full animate-spin"></div>
        </button>
      </div>
    </div>

    <!-- Multi-Currency Wallet Overview -->
    <div v-if="selectedAccount && summaries.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 no-print">
      <div v-for="s in summaries" :key="s.currency_id" 
           class="group relative bg-slate-900/40 border border-white/5 p-8 rounded-[2.5rem] flex flex-col justify-between overflow-hidden transition-all hover:border-emerald-500/30 shadow-xl">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
        <div class="relative z-10 flex flex-col">
           <span class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] mb-4">Total Balance ({{ s.currency?.code }})</span>
           <div class="flex justify-between items-end">
             <h3 class="text-4xl font-black font-mono tracking-tighter" :class="getBalanceValue(s) >= 0 ? 'text-emerald-400' : 'text-rose-400'">
               {{ formatNum(getBalanceValue(s)) }}
             </h3>
             <div class="flex flex-col items-end">
                <span class="text-xs font-black text-slate-500 uppercase">{{ s.currency?.code }}</span>
                <span class="text-[9px] font-bold" :class="getBalanceValue(s) >= 0 ? 'text-emerald-500/50' : 'text-rose-500/50'">{{ getBalanceValue(s) >= 0 ? 'Surplus' : 'Debt' }}</span>
             </div>
           </div>
        </div>
      </div>
    </div>

    <!-- Advanced Statement Table -->
    <div v-if="filters.account_id" class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] overflow-hidden shadow-2xl relative">
      <div class="p-8 border-b border-white/5 flex justify-between items-center no-print">
         <h3 class="text-xl font-black text-white">جوڵەی حیساب</h3>
         <button @click="window.print()" class="px-6 py-2 bg-slate-950 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-white hover:border-white/30 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2H7a2 2 0 00-2 2v4m14 0h2"/></svg>
            Print Statement
         </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse min-w-[1000px]">
          <thead>
            <tr class="text-[10px] font-black text-slate-600 uppercase tracking-widest bg-slate-950/60 border-b border-white/5">
              <th class="px-10 py-6 w-40">بەروار و کات</th>
              <th class="px-10 py-6">وەسفی مامەڵە</th>
              <th class="px-10 py-6 text-center text-emerald-400">مەدین (قەرز)</th>
              <th class="px-10 py-6 text-center text-rose-400">داین (کەسر)</th>
              <th class="px-10 py-6 text-center">دراو</th>
              <th class="px-10 py-6 text-left">ئەنجامی خاوێن (Net)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/[0.03]">
            <tr v-for="entry in entries" :key="entry.id" class="hover:bg-white/[0.02] transition-all group">
              <td class="px-10 py-6">
                <p class="text-xs font-black text-slate-400 font-mono">{{ formatDate(entry.date) }}</p>
                <p class="text-[9px] text-slate-600 font-black uppercase tracking-tighter mt-1">{{ entry.entryable_type?.split('\\').pop() }} ID:{{ entry.id }}</p>
              </td>
              <td class="px-10 py-6">
                <div class="flex flex-col">
                   <p class="text-sm font-black text-white group-hover:text-emerald-400 transition-colors">{{ entry.description }}</p>
                   <span class="text-[9px] text-slate-700 font-bold mt-0.5">Verified Transaction</span>
                </div>
              </td>
              <td class="px-10 py-6 text-center">
                <div v-if="entry.debit > 0" class="flex flex-col items-center">
                   <span class="text-xl font-black text-emerald-400 font-mono">{{ formatNum(entry.debit) }}</span>
                </div>
                <span v-else class="text-slate-900">—</span>
              </td>
              <td class="px-10 py-6 text-center">
                <div v-if="entry.credit > 0" class="flex flex-col items-center">
                   <span class="text-xl font-black text-rose-400 font-mono">{{ formatNum(entry.credit) }}</span>
                </div>
                <span v-else class="text-slate-900">—</span>
              </td>
              <td class="px-10 py-6 text-center">
                <span class="px-3 py-1 bg-slate-900 border border-white/5 rounded-lg text-[10px] font-black text-slate-400 uppercase tracking-widest">
                  {{ entry.currency?.code }}
                </span>
              </td>
              <td class="px-10 py-6 text-left">
                <div class="flex flex-col items-end">
                   <span class="text-xs font-black font-mono" :class="entry.base_amount >= 0 ? 'text-emerald-500/40' : 'text-rose-500/40'">
                     {{ formatNum(entry.base_amount) }}
                   </span>
                   <span class="text-[8px] font-black text-slate-800 uppercase tracking-widest">IQD Equivalent</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Empty State -->
      <div v-if="!entries.length && !loading" class="p-32 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-slate-950/20 backdrop-blur-sm"></div>
        <div class="relative z-10 flex flex-col items-center gap-4">
           <svg class="w-16 h-16 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
           <span class="text-[10px] font-black text-slate-700 uppercase tracking-[0.5em]">No Movement Recorded</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRoute } from 'vue-router'
import axios from '../../plugins/axios'

const route = useRoute()
const accounts = ref([])
const entries = ref([])
const summaries = ref([])
const selectedAccount = ref(null)
const loading = ref(false)

const filters = reactive({
  account_id: null,
  start_date: '',
  currency_id: null
})

async function fetchAccounts() {
  const { data } = await axios.get('/accounts?per_page=1000')
  accounts.value = data.data || data
  
  if (route.query.id) {
    filters.account_id = parseInt(route.query.id)
    fetchStatement()
  }
}

async function fetchStatement() {
  if (!filters.account_id) return
  loading.value = true
  try {
    const journalRes = await axios.get('/journals', { params: { ...filters, per_page: 500 } })
    entries.value = journalRes.data.data
    const accountRes = await axios.get(`/accounts/${filters.account_id}`)
    selectedAccount.value = accountRes.data
    summaries.value = accountRes.data.summaries || []
  } catch (e) {
    console.error('Error fetching statement:', e)
  } finally {
    loading.value = false
  }
}

function getBalanceValue(summary) {
  return parseFloat(summary.total_debit) - parseFloat(summary.total_credit)
}

function formatNum(val) {
  return new Intl.NumberFormat().format(parseFloat(val || 0))
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('ku-IQ', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(() => fetchAccounts())
</script>

<style scoped>
@media print {
  .no-print { display: none !important; }
  body { background: white !important; color: black !important; }
  .bg-slate-900\/40 { background: transparent !important; border: 1px solid #eee !important; }
  .text-white { color: black !important; }
  .text-slate-400, .text-slate-500, .text-slate-600 { color: #666 !important; }
  .bg-slate-950\/60 { background: #f9f9f9 !important; border-bottom: 2px solid black !important; }
  th { color: black !important; border-bottom: 2px solid black !important; }
  td { border-bottom: 1px solid #eee !important; }
  .rounded-\[3rem\], .rounded-2xl, .rounded-\[2\.5rem\] { border-radius: 0 !important; }
}

.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
