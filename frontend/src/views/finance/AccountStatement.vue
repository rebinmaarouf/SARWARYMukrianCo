<template>
  <!-- Main Container -->
  <div class="p-2 md:p-6 lg:p-10 space-y-4 max-w-[1700px] mx-auto pb-40 text-white font-sans" dir="rtl">
    
    <!-- PROFESSIONAL STATEMENT HEADER (Visible in Print/PDF) -->
    <div class="bg-slate-900/60 backdrop-blur-3xl p-8 rounded-[3rem] border border-white/5 shadow-3xl relative overflow-hidden print-header-container">
      <div class="absolute inset-0 bg-gradient-to-l from-emerald-500/10 via-transparent to-transparent pointer-events-none no-print"></div>
      
      <div class="relative z-10 flex flex-col md:flex-row justify-between items-start gap-8">
        <!-- Brand & Logo -->
        <div class="flex items-center gap-6">
          <div class="w-24 h-24 md:w-32 md:h-32 bg-white rounded-[2.5rem] border-4 border-slate-950 flex items-center justify-center p-4 shadow-2xl print-logo-box">
             <img src="/logo.png" class="max-w-full max-h-full object-contain" alt="SARWARY MUKRIAN" />
          </div>
          <div class="text-right">
             <h1 class="text-3xl md:text-5xl font-black text-white tracking-tighter leading-tight print-text-black">کۆمپانیای سەروەری موکریان</h1>
             <p class="text-slate-400 font-black text-xs md:text-sm uppercase tracking-[0.3em] mt-1 print-text-slate">SARWARY MUKRIAN / ENTERPRISE FINANCE</p>
             <div class="mt-4 flex items-center gap-3 no-print">
                <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full text-[10px] font-black text-emerald-500 uppercase tracking-widest">Official Statement</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
             </div>
          </div>
        </div>

        <!-- Statement Metadata -->
        <div class="bg-slate-950/80 p-6 rounded-[2rem] border border-white/10 min-w-[320px] print-meta-box">
           <h2 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-4 border-b border-white/5 pb-2">Statement Details</h2>
           <div class="space-y-3">
              <div class="flex justify-between items-center">
                 <span class="text-[10px] font-bold text-slate-500 uppercase">Customer</span>
                 <span class="text-sm font-black text-white print-text-black">{{ selectedAccount?.name || '---' }}</span>
              </div>
              <div class="flex justify-between items-center">
                 <span class="text-[10px] font-bold text-slate-500 uppercase">Account Code</span>
                 <span class="text-sm font-black text-emerald-400 font-mono print-text-black">{{ selectedAccount?.code || '---' }}</span>
              </div>
              <div class="flex justify-between items-center">
                 <span class="text-[10px] font-bold text-slate-500 uppercase">Period</span>
                 <span class="text-sm font-black text-white print-text-black">{{ filters.start_date || 'Inception' }} - Today</span>
              </div>
           </div>
        </div>
      </div>

      <!-- Action Bar (Hidden in Print) -->
      <div class="mt-8 flex justify-end gap-4 no-print">
         <div class="bg-slate-950/60 p-1.5 rounded-2xl border border-white/5 flex gap-2">
            <button @click="viewMode = 'ledger'" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all" :class="viewMode === 'ledger' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20' : 'text-slate-500 hover:text-white'">Movements</button>
            <button @click="viewMode = 'summary'" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all" :class="viewMode === 'summary' ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/20' : 'text-slate-500 hover:text-white'">Summary</button>
         </div>
         <button @click="printReport" class="px-8 py-2.5 bg-emerald-500 text-slate-950 rounded-xl font-black text-[10px] uppercase hover:bg-emerald-400 active:scale-95 transition-all shadow-xl shadow-emerald-500/20">
            Export PDF Statement
         </button>
      </div>
    </div>

    <!-- Selection Bar (Hidden in Print) -->
    <div class="bg-slate-950/40 p-4 rounded-[2rem] border border-white/5 flex flex-wrap items-center gap-6 no-print shadow-xl backdrop-blur-3xl">
       <div class="flex flex-col px-6 border-l border-white/10 flex-1 min-w-[250px]">
          <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-0.5">Select Client Account</span>
          <select v-model="filters.account_id" @change="fetchStatement" class="bg-transparent border-none p-0 text-sm font-black text-emerald-400 focus:ring-0 cursor-pointer">
            <option :value="null" disabled>Choose from list...</option>
            <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.code }} | {{ a.name }}</option>
          </select>
       </div>
       <div class="flex flex-col px-6 border-l border-white/10">
          <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-0.5">Filter From Date</span>
          <input v-model="filters.start_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-white focus:ring-0 cursor-pointer" />
       </div>
       <button @click="fetchStatement" :disabled="loading || !filters.account_id" class="bg-white/5 hover:bg-white/10 text-white px-8 py-3 rounded-xl font-black text-[10px] border border-white/10 transition-all uppercase tracking-widest">
          Update Report
       </button>
    </div>

    <!-- MOVEMENT SUMMARY SECTION (Task 3 Requirement) -->
    <div v-if="filters.account_id" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 print-summary-grid">
       <div v-for="sum in summaries" :key="sum.id" class="bg-slate-900/40 border border-white/5 p-6 rounded-[2rem] relative overflow-hidden group hover:border-emerald-500/20 transition-all">
          <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
          <div class="flex items-center gap-4 mb-4">
             <div class="w-10 h-10 rounded-xl bg-slate-950 flex items-center justify-center font-black text-xs text-emerald-500 border border-white/5">{{ sum.currency?.code }}</div>
             <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ sum.currency?.name }} Summary</h4>
          </div>
          <div class="space-y-3">
             <div class="flex justify-between items-center text-[10px]">
                <span class="font-bold text-slate-600 uppercase">Debits (+)</span>
                <span class="font-black text-emerald-400 font-mono">{{ formatNum(sum.total_debit) }}</span>
             </div>
             <div class="flex justify-between items-center text-[10px]">
                <span class="font-bold text-slate-600 uppercase">Credits (-)</span>
                <span class="font-black text-rose-400 font-mono">{{ formatNum(sum.total_credit) }}</span>
             </div>
             <div class="pt-2 border-t border-white/5 flex justify-between items-center">
                <span class="text-[11px] font-black text-white uppercase">Balance</span>
                <span class="text-xl font-black font-mono tracking-tight" :class="getBalanceValue(sum) >= 0 ? 'text-emerald-500' : 'text-rose-500'">
                   {{ formatNum(getBalanceValue(sum)) }}
                </span>
             </div>
          </div>
       </div>
    </div>

    <!-- Detailed Ledger Movements -->
    <div v-if="filters.account_id" class="bg-slate-900/60 border border-white/5 rounded-[2.5rem] overflow-hidden shadow-3xl animate-fade-in print-ledger-container">
      <div class="p-6 bg-slate-950/40 border-b border-white/5 flex justify-between items-center">
         <h3 class="text-sm font-black text-white uppercase tracking-widest">Transaction Movement Log</h3>
         <span class="text-[10px] font-black text-slate-500">{{ entries.length }} Operations Recorded</span>
      </div>
      <div class="overflow-x-auto print-overflow-visible">
        <table class="w-full text-right border-collapse">
          <thead>
            <tr class="text-[9px] font-black text-slate-600 uppercase tracking-widest bg-slate-950/80 border-b border-white/5 print-bg-slate">
              <th class="px-6 py-5">Date / بەروار</th>
              <th class="px-6 py-5">Description / وردەکاری</th>
              <th class="px-6 py-5 text-center text-emerald-400">Debit / مەدین (+)</th>
              <th class="px-6 py-5 text-center text-rose-400">Credit / داین (-)</th>
              <th class="px-6 py-5 text-center">Currency / دراو</th>
              <th class="px-6 py-5 text-left">Internal Ref.</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/[0.03] print-divide-slate">
            <tr v-for="entry in entries" :key="entry.id" class="hover:bg-white/[0.04] transition-colors group">
              <td class="px-6 py-5">
                <p class="text-xs font-black text-white group-hover:text-emerald-400 transition-colors print-text-black">{{ formatDate(entry.date) }}</p>
                <p class="text-[8px] text-slate-600 font-bold mt-1">LOG-#{{ entry.id }}</p>
              </td>
              <td class="px-6 py-5 max-w-md">
                <p class="text-xs font-bold text-slate-300 leading-relaxed print-text-black">{{ entry.description }}</p>
              </td>
              <td class="px-6 py-5 text-center">
                 <span v-if="entry.debit > 0" class="text-lg font-black text-emerald-400 font-mono print-text-black">{{ formatNum(entry.debit) }}</span>
                 <span v-else class="text-slate-900 opacity-10 print-opacity-0">—</span>
              </td>
              <td class="px-6 py-5 text-center">
                 <span v-if="entry.credit > 0" class="text-lg font-black text-rose-400 font-mono print-text-black">{{ formatNum(entry.credit) }}</span>
                 <span v-else class="text-slate-900 opacity-10 print-opacity-0">—</span>
              </td>
              <td class="px-6 py-5 text-center">
                <span class="px-3 py-1 bg-slate-950 border border-white/5 rounded-lg text-[9px] font-black text-slate-400 uppercase print-border-black">
                   {{ entry.currency?.code }}
                </span>
              </td>
              <td class="px-6 py-5 text-left">
                <span class="text-[10px] font-black font-mono text-slate-600 uppercase tracking-tighter">
                   {{ entry.transaction_id ? `TX-${entry.transaction_id}` : `REG-${entry.registry_id}` }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Professional Audit Footer -->
      <div class="p-10 bg-slate-950/80 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-10">
         <div class="flex items-center gap-6">
            <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 border border-emerald-500/20">
               <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
               <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Total Valuation (Consolidated IQD)</span>
               <p class="text-3xl font-black text-white font-mono tracking-tighter print-text-black">{{ formatNum(totalIqdNet) }} <span class="text-sm text-slate-500">IQD</span></p>
            </div>
         </div>
         <div class="bg-slate-900 border border-white/10 px-8 py-3 rounded-full no-print">
            <span class="text-xs font-black text-slate-400 tracking-widest uppercase">Verified by SM-ERP Audit Engine v2.0</span>
         </div>
      </div>
    </div>

    <!-- OFFICIAL SEAL & SIGNATURE (Task 3 Requirement) -->
    <div class="hidden print-only-block mt-12 pt-8 border-t-2 border-slate-900">
       <div class="flex justify-between items-start">
          <div class="space-y-6">
             <div class="flex flex-col gap-2">
                <span class="text-[10px] font-black text-slate-400 uppercase">Legal Disclaimer:</span>
                <p class="text-[10px] font-bold text-slate-800 leading-tight max-w-sm">
                   ئەم ڕاپۆرتە بە شێوەیەکی فەرمی لەلایەن سیستەمی کۆمپانیای سەروەری موکریانەوە دەرچووە. تکایە لە کاتی هەر هەڵەیەکدا پەیوەندی بە بەشی وردبینی بکەن.
                </p>
             </div>
             <div class="flex items-center gap-4">
                <span class="text-[10px] font-black text-slate-500 uppercase">Official Signature:</span>
                <div class="w-48 h-10 border-b border-slate-300"></div>
             </div>
          </div>
          <div class="text-center space-y-2">
             <div class="w-24 h-24 rounded-full border-4 border-double border-slate-200 mx-auto flex items-center justify-center">
                <span class="text-[8px] font-black text-slate-300 uppercase rotate-12">Official Seal</span>
             </div>
             <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ new Date().toLocaleString('ku-IQ') }}</p>
          </div>
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from '../../plugins/axios'

const route = useRoute()
const accounts = ref([])
const entries = ref([])
const summaries = ref([])
const selectedAccount = ref(null)
const loading = ref(false)
const viewMode = ref('ledger') 

const filters = reactive({ account_id: null, start_date: '', currency_id: null })

const totalIqdNet = computed(() => {
  return summaries.value.reduce((acc, s) => {
    const net = parseFloat(s.total_debit) - parseFloat(s.total_credit)
    const rate = s.currency?.current_rate || 1
    return acc + (net * rate)
  }, 0)
})

async function fetchAccounts() {
  const { data } = await axios.get('/accounts?per_page=1000'); accounts.value = data.data || data
  if (route.query.id) { filters.account_id = parseInt(route.query.id); fetchStatement(); }
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
  } catch (e) { console.error(e) } finally { loading.value = false }
}

function getBalanceValue(summary) { return parseFloat(summary.total_debit) - parseFloat(summary.total_credit) }
function formatNum(val) { return new Intl.NumberFormat().format(parseFloat(val || 0)) }
function formatDate(d) { return new Date(d).toLocaleDateString('ku-IQ', { year: 'numeric', month: 'short', day: 'numeric' }) }
function printReport() { window.print() }
onMounted(() => fetchAccounts())
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

@media print {
  @page { size: A4; margin: 0.3cm 0.8cm 0.8cm 0.8cm; }
  .no-print { display: none !important; }
  .print-only-block { display: block !important; }
  
  /* CRITICAL: Remove all shadows, blurs and backgrounds for print */
  * { 
    box-shadow: none !important; 
    filter: none !important; 
    text-shadow: none !important;
  }
  
  body { background: white !important; color: black !important; padding: 0 !important; margin: 0 !important; font-size: 8pt !important; }
  .max-w-\[1700px\] { max-width: 100% !important; margin: 0 !important; }
  
  .print-table-a4 { table-layout: fixed !important; width: 100% !important; }
  .print-col-date { width: 10% !important; }
  .print-col-desc { width: 32% !important; }
  .print-col-amount { width: 15% !important; }
  .print-col-cur { width: 8% !important; }
  .print-col-net { width: 20% !important; }

  th, td { padding: 3px !important; word-wrap: break-word !important; }
  
  /* Explicitly make headers transparent */
  .bg-slate-900\/60, .bg-slate-950\/80, .bg-slate-950\/40, .backdrop-blur-3xl { 
    background: transparent !important; 
    color: black !important; 
    border: none !important; 
    backdrop-filter: none !important;
  }
  
  .print-header-container { 
    border-bottom: 2px solid black !important; 
    padding: 0 !important; 
    padding-bottom: 0.3rem !important; 
    margin-bottom: 0.5rem !important; 
    border-radius: 0 !important; 
    box-shadow: none !important;
  }
  
  .print-logo-box { 
    border: 1px solid black !important; 
    border-radius: 0.8rem !important; 
    width: 60px !important; 
    height: 60px !important; 
    background: white !important;
    box-shadow: none !important;
  }
  
  .print-text-black { color: black !important; }
  .print-text-slate { color: #555 !important; }
  .print-bg-slate { background: #f8fafc !important; }
  .print-border-black { border: 1px solid black !important; }
  .print-border-slate { border: 1px solid #eee !important; }
  .print-divide-slate > :not([hidden]) ~ :not([hidden]) { border-color: #eee !important; }
  .print-status-badge { border: 1px solid black !important; color: black !important; background: #eee !important; }
  .print-overflow-visible { overflow: visible !important; }
  .print-ledger-container { border: 1px solid #eee !important; border-radius: 1rem !important; box-shadow: none !important; }
}
</style>
