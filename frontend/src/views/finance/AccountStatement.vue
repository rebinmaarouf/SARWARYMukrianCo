<template>
  <!-- Main Container -->
  <div class="p-2 md:p-6 lg:p-10 space-y-4 max-w-[1700px] mx-auto pb-40 text-white font-sans" dir="rtl">
    
    <!-- BRANDED TERMINAL HEADER -->
    <div class="bg-slate-900/60 backdrop-blur-3xl p-6 md:p-8 rounded-[2rem] md:rounded-[3rem] border border-white/5 shadow-3xl relative overflow-hidden print-header-container">
      <!-- Gradient (Hidden in Print) -->
      <div class="absolute inset-0 bg-gradient-to-l from-emerald-500/10 to-transparent pointer-events-none no-print"></div>
      
      <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-6 print-logo-row">
          <!-- Logo - Simplified for Print -->
          <div class="w-20 h-20 md:w-28 md:h-28 bg-white rounded-[2rem] border-4 border-slate-950 flex items-center justify-center p-3 shadow-2xl print-logo-box">
             <img src="/logo.png" class="max-w-full max-h-full object-contain" alt="SARWARY MUKRIAN" />
          </div>
          <div class="text-right print-title-box">
             <h1 class="text-3xl md:text-5xl font-black text-white tracking-tighter leading-tight print-text-black">کۆمپانیای سەروەری موکریان</h1>
             <p class="text-slate-400 font-black text-xs md:text-sm uppercase tracking-[0.3em] mt-1 print-text-slate">SARWARY MUKRIAN FOR EXCHANGE & FINANCE</p>
          </div>
        </div>

        <!-- Terminal Controls -->
        <div class="flex flex-col items-end gap-3 no-print w-full md:w-auto">
          <div class="bg-slate-950/80 p-1.5 rounded-[1.5rem] border border-white/5 flex gap-2 w-full md:w-auto">
             <button @click="viewMode = 'ledger'" class="flex-1 px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all" :class="viewMode === 'ledger' ? 'bg-emerald-500 text-slate-950' : 'text-slate-500'">Ledger</button>
             <button @click="viewMode = 'audit'" class="flex-1 px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all" :class="viewMode === 'audit' ? 'bg-blue-500 text-white' : 'text-slate-500'">Audit</button>
          </div>
          <button @click="printReport" class="px-8 py-3 bg-emerald-500/10 text-emerald-400 rounded-xl border border-emerald-500/20 text-[10px] font-black uppercase">Print Report</button>
        </div>
      </div>

      <!-- Metadata (Print Only) -->
      <div class="hidden print-only-block mt-2 grid grid-cols-3 gap-10 pt-2 border-t border-slate-900">
         <div class="flex gap-2 items-center">
            <span class="text-[8px] font-black text-slate-400 uppercase">Account:</span>
            <span class="text-[10px] font-black text-slate-950">{{ selectedAccount?.name || '---' }}</span>
         </div>
         <div class="flex gap-2 items-center">
            <span class="text-[8px] font-black text-slate-400 uppercase">Period:</span>
            <span class="text-[10px] font-black text-slate-950">{{ filters.start_date || 'Full' }}</span>
         </div>
         <div class="text-left flex gap-2 items-center justify-end">
            <span class="text-[8px] font-black text-slate-400 uppercase">Ref:</span>
            <span class="text-[10px] font-mono font-bold text-slate-800">#{{ new Date().getTime().toString().slice(-6) }}</span>
         </div>
      </div>
    </div>

    <!-- Selection Bar -->
    <div class="bg-slate-950/40 p-4 rounded-[2rem] border border-white/5 flex flex-wrap items-center gap-6 no-print shadow-xl">
       <div class="flex flex-col px-6 border-l border-white/10 flex-1 min-w-[250px]">
          <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-0.5">Account Entity</span>
          <select v-model="filters.account_id" @change="fetchStatement" class="bg-transparent border-none p-0 text-sm font-black text-emerald-400 focus:ring-0">
            <option :value="null" disabled>Select Account...</option>
            <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.code }} - {{ a.name }}</option>
          </select>
       </div>
       <div class="flex flex-col px-6 border-l border-white/10">
          <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-0.5">Starting From</span>
          <input v-model="filters.start_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-white focus:ring-0 cursor-pointer" />
       </div>
       <button @click="fetchStatement" :disabled="loading || !filters.account_id" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 px-8 py-3 rounded-xl font-black text-[10px]">Load</button>
    </div>

    <!-- Ledger View -->
    <div v-if="viewMode === 'ledger' && filters.account_id" class="bg-slate-900/60 border border-white/5 rounded-[2.5rem] overflow-visible shadow-3xl animate-fade-in print-ledger-container">
      <div class="overflow-x-auto print-overflow-visible">
        <table class="w-full text-right border-collapse print-table-a4">
          <thead>
            <tr class="text-[9px] font-black text-slate-600 uppercase tracking-widest bg-slate-950/80 border-b border-white/5 print-bg-slate">
              <th class="px-3 py-4 print-col-date">بەروار</th>
              <th class="px-3 py-4 print-col-desc">وەسفی مامەڵە</th>
              <th class="px-3 py-4 text-center text-emerald-400 print-col-amount">مەدین (Debit)</th>
              <th class="px-3 py-4 text-center text-rose-400 print-col-amount">داین (Credit)</th>
              <th class="px-3 py-4 text-center print-col-cur">دراو</th>
              <th class="px-3 py-4 text-left print-col-net">NET EQ.</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/[0.03] print-divide-slate">
            <tr v-for="entry in entries" :key="entry.id" class="hover:bg-white/[0.04]">
              <td class="px-3 py-3 print-col-date">
                <p class="text-[9px] font-black text-slate-400 font-mono print-text-black">{{ formatDate(entry.date) }}</p>
                <p class="text-[7px] text-slate-600 font-black mt-1">#{{ entry.id }}</p>
              </td>
              <td class="px-3 py-3 print-col-desc">
                <p class="text-xs font-black text-white leading-tight print-text-black">{{ entry.description }}</p>
              </td>
              <td class="px-3 py-3 text-center print-col-amount">
                 <span v-if="entry.debit > 0" class="text-lg font-black text-emerald-400 font-mono print-text-black">{{ formatNum(entry.debit) }}</span>
                 <span v-else class="text-slate-900 opacity-10 print-opacity-0">—</span>
              </td>
              <td class="px-3 py-3 text-center print-col-amount">
                 <span v-if="entry.credit > 0" class="text-lg font-black text-rose-400 font-mono print-text-black">{{ formatNum(entry.credit) }}</span>
                 <span v-else class="text-slate-900 opacity-10 print-opacity-0">—</span>
              </td>
              <td class="px-3 py-3 text-center print-col-cur">
                <span class="px-2 py-0.5 bg-slate-950 border border-white/5 rounded text-[8px] font-black text-slate-400 print-border-black">
                  {{ entry.currency?.code }}
                </span>
              </td>
              <td class="px-3 py-3 text-left print-col-net">
                <span class="text-[10px] font-black font-mono print-text-slate" :class="entry.base_amount >= 0 ? 'text-emerald-500/40' : 'text-rose-500/40'">
                   {{ formatNum(entry.base_amount) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Footer Summaries -->
      <div v-if="entries.length" class="p-6 bg-slate-950/80 border-t border-white/5 space-y-4 print-footer-summary">
         <div class="grid grid-cols-4 gap-4 print-grid-summary">
            <div v-for="sum in summaries" :key="sum.currency_id" class="flex items-center gap-3 bg-slate-900/60 p-2 rounded-lg border border-white/5 print-border-slate">
               <div class="w-6 h-6 rounded bg-white/5 flex items-center justify-center font-black text-[8px] text-slate-400 print-text-black">{{ sum.currency?.code }}</div>
               <div>
                  <span class="text-[7px] font-black text-slate-600 uppercase block print-text-slate">{{ sum.currency?.code }} Bal.</span>
                  <p class="text-xs font-black font-mono tracking-tighter" :class="getBalanceValue(sum) >= 0 ? 'text-emerald-400' : 'text-rose-400'">
                     {{ formatNum(getBalanceValue(sum)) }}
                  </p>
               </div>
            </div>
         </div>
         
         <div class="pt-4 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
               <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
               </div>
               <div>
                  <span class="text-[8px] font-black text-slate-600 uppercase tracking-widest print-text-slate">Valuation (IQD)</span>
                  <p class="text-2xl font-black text-white font-mono tracking-tighter print-text-black">{{ formatNum(totalIqdNet) }} <span class="text-xs text-slate-500">IQD</span></p>
               </div>
            </div>
         </div>
      </div>
    </div>

    <!-- Official Footer -->
    <div class="hidden print-only-block mt-8 pt-4 border-t border-slate-300">
       <div class="flex justify-between items-center px-10">
          <div class="flex items-center gap-4">
             <span class="text-[9px] font-black text-slate-500 uppercase">Sign:</span>
             <div class="w-40 h-8 border-b border-dashed border-slate-300"></div>
          </div>
          <div class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">SARWARY MUKRIAN ERP — {{ new Date().toLocaleString() }}</div>
          <div class="flex items-center gap-4">
             <span class="text-[9px] font-black text-slate-500 uppercase">Seal:</span>
             <div class="w-16 h-16 rounded-full border border-slate-200"></div>
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
