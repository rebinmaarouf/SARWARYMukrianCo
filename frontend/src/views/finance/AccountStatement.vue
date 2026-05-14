<template>
  <!-- Main Container -->
  <div class="p-2 md:p-6 lg:p-10 space-y-4 max-w-[1700px] mx-auto pb-40 text-slate-800 font-sans" dir="rtl">
    
    <!-- PROFESSIONAL STATEMENT HEADER (Visible in Print/PDF) -->
    <div class="bg-white p-8 rounded-[3rem] border border-slate-200 shadow-sm relative overflow-hidden print-header-container">
      <div class="absolute inset-0 bg-gradient-to-l from-emerald-500/5 via-transparent to-transparent pointer-events-none no-print"></div>
      
      <div class="relative z-10 flex flex-col md:flex-row justify-between items-start gap-8">
        <!-- Brand & Logo -->
        <div class="flex items-center gap-6">
          <div class="w-24 h-24 md:w-32 md:h-32 bg-white rounded-[2.5rem] border-2 border-slate-200 flex items-center justify-center p-4 shadow-sm print-logo-box">
             <img src="/logo.png" class="max-w-full max-h-full object-contain" alt="SARWARY MUKRIAN" />
          </div>
          <div class="text-right">
             <h1 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tighter leading-tight print-text-black">کۆمپانیای سەروەری موکریان</h1>
             <p class="text-slate-500 font-black text-xs md:text-sm uppercase tracking-[0.3em] mt-1 print-text-slate">SARWARY MUKRIAN / ENTERPRISE FINANCE</p>
             <div class="mt-4 flex items-center gap-3 no-print">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-black uppercase tracking-widest shadow-xs">Official Statement</span>
                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
             </div>
          </div>
        </div>

        <!-- Statement Metadata -->
        <div class="bg-slate-50 p-6 rounded-[2rem] border border-slate-200 min-w-[320px] print-meta-box shadow-inner">
           <h2 class="text-[10px] font-black text-emerald-700 uppercase tracking-[0.2em] mb-4 border-b border-slate-200 pb-2">Statement Details</h2>
           <div class="space-y-3">
              <div class="flex justify-between items-center">
                 <span class="text-[10px] font-bold text-slate-500 uppercase">Customer</span>
                 <span class="text-sm font-black text-slate-900 print-text-black">{{ selectedAccount?.name || '---' }}</span>
              </div>
              <div class="flex justify-between items-center">
                 <span class="text-[10px] font-bold text-slate-500 uppercase">Account Code</span>
                 <span class="text-sm font-black text-emerald-700 font-mono print-text-black">{{ selectedAccount?.code || '---' }}</span>
              </div>
              <div class="flex justify-between items-center">
                 <span class="text-[10px] font-bold text-slate-500 uppercase">Period</span>
                 <span class="text-sm font-black text-slate-900 print-text-black">{{ filters.start_date || 'Inception' }} - Today</span>
              </div>
           </div>
        </div>
      </div>

      <!-- Action Bar (Hidden in Print) -->
      <div class="mt-8 flex justify-end gap-4 no-print">
         <div class="bg-slate-50 p-1.5 rounded-2xl border border-slate-200 flex gap-2 shadow-inner">
            <button @click="viewMode = 'ledger'" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all" :class="viewMode === 'ledger' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/10' : 'text-slate-600 hover:text-slate-900'">Movements</button>
            <button @click="viewMode = 'summary'" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all" :class="viewMode === 'summary' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/10' : 'text-slate-600 hover:text-slate-900'">Summary</button>
         </div>
         <button @click="printReport" class="px-8 py-2.5 bg-emerald-600 text-white rounded-xl font-black text-[10px] uppercase hover:bg-emerald-700 active:scale-95 transition-all shadow-md shadow-emerald-600/10">
            Export PDF Statement
         </button>
      </div>
    </div>

    <!-- Selection Bar (Hidden in Print) -->
    <div class="bg-white p-4 rounded-[2rem] border border-slate-200 flex flex-wrap items-center gap-6 no-print shadow-sm">
       <div class="flex flex-col px-6 border-l border-slate-100 flex-1 min-w-[250px]">
          <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-0.5">Select Client Account</span>
          <select v-model="filters.account_id" @change="fetchStatement" class="bg-transparent border-none p-0 text-sm font-black text-emerald-700 focus:ring-0 cursor-pointer outline-none">
            <option :value="null" disabled>Choose from list...</option>
            <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.code }} | {{ a.name }}</option>
          </select>
       </div>
       <div class="flex flex-col px-6 border-l border-slate-100">
          <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-0.5">Filter From Date</span>
          <input v-model="filters.start_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-slate-900 focus:ring-0 cursor-pointer outline-none" />
       </div>
       <button @click="fetchStatement" :disabled="loading || !filters.account_id" class="bg-slate-100 hover:bg-slate-200 text-slate-800 px-8 py-3 rounded-xl font-black text-[10px] border border-slate-200 transition-all uppercase tracking-widest active:scale-95 shadow-xs">
          Update Report
       </button>
    </div>

    <!-- MOVEMENT SUMMARY SECTION (Task 3 Requirement) -->
    <div v-if="filters.account_id" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 print-summary-grid">
       <div v-for="sum in summaries" :key="sum.id" class="bg-white border border-slate-200 p-6 rounded-[2rem] relative overflow-hidden group hover:border-emerald-300 transition-all shadow-sm">
          <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-600/5 rounded-full blur-2xl group-hover:bg-emerald-600/10 transition-all"></div>
          <div class="flex items-center gap-4 mb-4">
             <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center font-black text-xs text-emerald-700 border border-slate-200 shadow-xs">{{ sum.currency?.code }}</div>
             <h4 class="text-[10px] font-black text-slate-600 uppercase tracking-widest">کورتەی حیسابی ({{ sum.currency?.name }})</h4>
          </div>
          <div class="space-y-3 font-semibold">
             <div class="flex justify-between items-center text-[10px]">
                <span class="font-bold text-slate-500 uppercase">کۆی وەرگیراو (+)</span>
                <span class="font-black text-emerald-700 font-mono">{{ formatNum(sum.total_debit) }}</span>
             </div>
             <div class="flex justify-between items-center text-[10px]">
                <span class="font-bold text-slate-500 uppercase">کۆی دراو (-)</span>
                <span class="font-black text-rose-600 font-mono">{{ formatNum(sum.total_credit) }}</span>
             </div>
             <div class="pt-2 border-t border-slate-100 flex justify-between items-center">
                <span class="text-[11px] font-black text-slate-900 uppercase">Balance</span>
                <span class="text-xl font-black font-mono tracking-tight" :class="getBalanceValue(sum) >= 0 ? 'text-emerald-700' : 'text-rose-600'">
                   {{ formatNum(getBalanceValue(sum)) }}
                </span>
             </div>
          </div>
       </div>
    </div>

    <!-- Detailed Ledger Movements -->
    <div v-if="filters.account_id" class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm animate-fade-in print-ledger-container">
      <div class="p-6 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
         <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Transaction Movement Log</h3>
         <span class="text-[10px] font-black text-slate-500">{{ entries.length }} Operations Recorded</span>
      </div>
      <div class="overflow-x-auto print-overflow-visible">
        <table class="w-full text-right border-collapse">
          <thead>
            <tr class="text-[9px] font-black text-slate-500 uppercase tracking-widest bg-slate-100/50 border-b border-slate-200 print-bg-slate">
              <th class="px-6 py-5">Date / بەروار</th>
              <th class="px-6 py-5">Description / وردەکاری</th>
              <th class="px-6 py-5 text-center text-emerald-700">Debit / مەدین (+)</th>
              <th class="px-6 py-5 text-center text-rose-600">Credit / داین (-)</th>
              <th class="px-6 py-5 text-center">Currency / دراو</th>
              <th class="px-6 py-5 text-right">Account / حیساب</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 print-divide-slate font-semibold">
            <tr v-for="entry in entries" :key="entry.id" class="hover:bg-slate-50 transition-colors group">
              <td class="px-6 py-5">
                <p class="text-xs font-black text-slate-900 group-hover:text-emerald-600 transition-colors print-text-black">{{ formatDate(entry.date) }}</p>
                <p class="text-[8px] text-slate-500 font-bold mt-1 font-mono">LOG-#{{ entry.id }}</p>
              </td>
              <td class="px-6 py-5 max-w-md">
                <p class="text-xs font-bold text-slate-700 leading-relaxed print-text-black">{{ entry.description }}</p>
              </td>
              <td class="px-6 py-5 text-center">
                 <span v-if="entry.debit > 0" class="text-lg font-black text-emerald-700 font-mono print-text-black">{{ formatNum(entry.debit) }}</span>
                 <span v-else class="text-slate-300 print-opacity-0">—</span>
              </td>
              <td class="px-6 py-5 text-center">
                 <span v-if="entry.credit > 0" class="text-lg font-black text-rose-600 font-mono print-text-black">{{ formatNum(entry.credit) }}</span>
                 <span v-else class="text-slate-300 print-opacity-0">—</span>
              </td>
              <td class="px-6 py-5 text-center">
                 <span class="px-3 py-1 bg-slate-100 border border-slate-200 rounded-lg text-[9px] font-black text-slate-700 uppercase print-border-black font-mono shadow-xs">
                    {{ entry.currency?.code }}
                 </span>
              </td>
              <td class="px-6 py-5 text-right">
                <p class="text-[10px] font-black text-slate-700">{{ entry.account?.name }}</p>
                <p class="text-[8px] text-slate-500 font-bold mt-0.5 font-mono">{{ entry.account?.code }}</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Professional Audit Footer -->
      <div class="p-10 bg-slate-50 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-10 shadow-inner">
         <div class="flex items-center gap-6">
            <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-700 border border-emerald-200 shadow-xs">
               <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
               <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">سەرمایەی گشتی بە دینار (بە نرخی ڕۆژ)</span>
               <p class="text-3xl font-black text-slate-900 font-mono tracking-tighter print-text-black">{{ formatNum(totalIqdNet) }} <span class="text-sm text-slate-500">IQD</span></p>
               <p class="text-[9px] text-slate-500 font-bold mt-1">ئەمە بەهای هەموو دراوەکانی ناو ئەم حیسابەیە ئەگەر هەمووی بکرێت بە دینار بە نرخی بازاڕی ئێستا.</p>
            </div>
         </div>
         <div class="bg-white border border-slate-200 px-8 py-3 rounded-full no-print shadow-xs">
            <span class="text-xs font-black text-slate-600 tracking-widest uppercase font-mono">Verified by SM-ERP Audit Engine v2.0</span>
         </div>
      </div>
    </div>

    <!-- OFFICIAL SEAL & SIGNATURE (Task 3 Requirement) -->
    <div class="hidden print-only-block print-seal-section mt-12 pt-8">
       <div class="flex justify-between items-start">
          <div class="space-y-6">
             <div class="flex flex-col gap-2">
                <span class="text-[10px] font-black text-slate-500 uppercase">Legal Disclaimer / مەرجی یاسایی:</span>
                <p class="text-[10px] font-bold text-slate-800 leading-tight max-w-sm">
                   ئەم ڕاپۆرتە بە شێوەیەکی فەرمی لەلایەن سیستەمی کۆمپانیای سەروەری موکریانەوە دەرچووە. تکایە لە کاتی هەر هەڵەیەکدا پەیوەندی بە بەشی وردبینی بکەن.
                </p>
             </div>
             <div class="flex items-center gap-4">
                <span class="text-[10px] font-black text-slate-500 uppercase">Official Signature / واژۆی فەرمی:</span>
                <div class="w-48 h-10 border-b border-slate-300"></div>
             </div>
          </div>
          <div class="text-center space-y-2">
             <div class="print-seal-circle w-24 h-24 rounded-full border-4 border-double border-slate-300 mx-auto flex items-center justify-center">
                <span class="text-[8px] font-black text-slate-400 uppercase rotate-12">Official Seal</span>
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

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

@media print {
  /* 
     CRITICAL SPA LAYOUT UNCLOGGER:
     These selectors target the exact layout parents of AdminLayout and router-view.
     We strip away the fixed viewport limits (h-screen, overflow-hidden) and flex constraints
     so that the browser can flow and paginate the content naturally over multiple A4 sheets.
  */
  html, body, #app {
    height: auto !important;
    min-height: 100% !important;
    overflow: visible !important;
    position: static !important;
    background-color: #ffffff !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  /* Target root AdminLayout grid/flex wrappers */
  div.flex.h-screen.overflow-hidden,
  .admin-layout,
  #app > div,
  body > div {
    height: auto !important;
    min-height: auto !important;
    overflow: visible !important;
    position: static !important;
    display: block !important;
    background-color: #ffffff !important;
  }

  /* Target main container wrapper */
  main.flex-1.flex.flex-col,
  main.overflow-hidden {
    height: auto !important;
    min-height: auto !important;
    overflow: visible !important;
    position: static !important;
    display: block !important;
    background-color: #ffffff !important;
  }

  /* Target router-view scrolling viewport wrapper */
  div.flex-1.overflow-y-auto,
  div.custom-scrollbar {
    height: auto !important;
    min-height: auto !important;
    overflow: visible !important;
    position: static !important;
    display: block !important;
    padding: 0 !important;
    background-color: #ffffff !important;
  }

  /* Hide navigation sidebar and header completely */
  aside, header, .no-print {
    display: none !important;
  }

  @page { 
    size: A4 portrait; 
    margin: 1.5cm 1.2cm 1.5cm 1.2cm; 
  }
  
  .print-only-block { 
    display: block !important; 
  }
  
  * { 
    box-shadow: none !important; 
    filter: none !important; 
    text-shadow: none !important;
  }
  
  body { 
    color: #0f172a !important; 
    font-family: 'Geeza Pro', 'Segoe UI', Arial, sans-serif !important;
    padding: 0 !important; 
    margin: 0 !important; 
  }
  
  .max-w-\[1700px\] { 
    max-width: 100% !important; 
    margin: 0 !important; 
    padding: 0 !important;
  }
  
  .print-header-container { 
    background: transparent !important; 
    border: none !important;
    border-bottom: 2.5px solid #0f172a !important;
    border-radius: 0 !important; 
    padding: 0 0 1.25rem 0 !important;
    margin-bottom: 2rem !important;
    display: flex !important;
    flex-direction: row !important;
    justify-content: space-between !important;
    align-items: center !important;
  }

  .print-logo-box {
    border: 1.5px solid #0f172a !important;
    border-radius: 1rem !important;
    padding: 0.5rem !important;
    background-color: #ffffff !important;
    width: 75px !important;
    height: 75px !important;
  }

  .print-text-black { 
    color: #0f172a !important; 
  }

  .print-text-slate {
    color: #475569 !important;
  }
  
  .print-meta-box {
    background-color: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 1rem !important;
    padding: 1rem 1.25rem !important;
    min-width: 280px !important;
  }

  /* 
     2-COLUMN SUMMARY GRID:
     This is highly spacious (each card gets ~9cm horizontal space), completely preventing 
     Kurdish and numeric overlaps, providing a clean, table-like layout on A4.
  */
  .print-summary-grid {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 1rem !important;
    margin-bottom: 2rem !important;
    page-break-inside: avoid !important;
  }

  .print-summary-grid > div {
    background-color: #f8fafc !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 1.25rem !important;
    box-shadow: none !important;
    position: relative !important;
    overflow: hidden !important;
    page-break-inside: avoid !important;
  }

  .print-summary-grid .w-10 {
    width: 2rem !important;
    height: 2rem !important;
    border-radius: 0.5rem !important;
    background-color: #0f172a !important;
    color: #ffffff !important;
    font-size: 10px !important;
  }

  .print-summary-grid h4 {
    color: #0f172a !important;
    font-weight: 800 !important;
    font-size: 11px !important;
  }

  .print-summary-grid span {
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 11px !important;
  }

  .print-summary-grid .font-mono {
    font-size: 13px !important;
    font-weight: 800 !important;
  }

  .print-summary-grid .text-emerald-400,
  .print-summary-grid .text-emerald-500 {
    color: #15803d !important; /* Rich print green */
  }

  .print-summary-grid .text-rose-400,
  .print-summary-grid .text-rose-500 {
    color: #b91c1c !important; /* Rich print red */
  }

  .print-summary-grid .text-xl {
    font-size: 15px !important;
  }

  /* Table / Ledger Container */
  .print-ledger-container {
    background: transparent !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 12px !important;
    box-shadow: none !important;
    margin-top: 2rem !important;
    overflow: visible !important;
    height: auto !important;
    min-height: auto !important;
  }

  .print-ledger-container h3 {
    color: #0f172a !important;
    font-size: 13px !important;
    font-weight: 900 !important;
  }

  .print-ledger-container span {
    color: #475569 !important;
  }

  .print-overflow-visible {
    overflow: visible !important;
    height: auto !important;
    min-height: auto !important;
  }

  /* 
     REPEATING TABLE HEADER:
     Forces the browser's printing engine to duplicate the table headers (thead) 
     on top of page 2, 3, etc. for extreme professional clarity.
  */
  table {
    width: 100% !important;
    border-collapse: collapse !important;
    page-break-inside: auto !important;
  }

  thead {
    display: table-header-group !important;
  }

  tbody {
    display: table-row-group !important;
  }

  thead tr {
    background-color: #f1f5f9 !important;
    border-bottom: 2.5px solid #94a3b8 !important;
  }

  th {
    color: #0f172a !important;
    font-size: 10px !important;
    font-weight: 900 !important;
    padding: 0.85rem 1rem !important;
  }

  tbody tr {
    border-bottom: 1px solid #e2e8f0 !important;
    page-break-inside: avoid !important;
    page-break-after: auto !important;
  }

  tbody td {
    padding: 0.85rem 1rem !important;
    color: #0f172a !important;
    font-size: 11px !important;
  }

  /* STRICT COLUMN WIDTH ALIGNMENT */
  th:nth-child(1), td:nth-child(1) { width: 14% !important; text-align: right !important; } /* Date */
  th:nth-child(2), td:nth-child(2) { width: 36% !important; text-align: right !important; } /* Description */
  th:nth-child(3), td:nth-child(3) { width: 14% !important; text-align: center !important; } /* Debit */
  th:nth-child(4), td:nth-child(4) { width: 14% !important; text-align: center !important; } /* Credit */
  th:nth-child(5), td:nth-child(5) { width: 8% !important; text-align: center !important; } /* Currency */
  th:nth-child(6), td:nth-child(6) { width: 14% !important; text-align: right !important; } /* Account */

  tbody td p {
    color: #0f172a !important;
  }

  tbody td span {
    color: #0f172a !important;
  }

  tbody td .text-emerald-400 {
    color: #15803d !important;
    font-weight: 900 !important;
  }

  tbody td .text-rose-400 {
    color: #b91c1c !important;
    font-weight: 900 !important;
  }

  tbody td .bg-slate-950 {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    border: 1px solid #94a3b8 !important;
    border-radius: 4px !important;
    padding: 0.15rem 0.4rem !important;
    font-weight: 800 !important;
  }

  /* Professional Audit Footer */
  .print-ledger-container > div:last-child {
    background-color: #f8fafc !important;
    border-top: 2.5px solid #cbd5e1 !important;
    padding: 1.5rem !important;
    page-break-inside: avoid !important;
  }

  .print-ledger-container .text-3xl {
    color: #0f172a !important;
    font-size: 22px !important;
    font-weight: 900 !important;
  }

  .print-ledger-container .text-emerald-500 {
    color: #15803d !important;
  }

  /* Official Seal & Signature Section */
  .print-seal-section {
    margin-top: 4rem !important;
    padding-top: 1.5rem !important;
    border-top: 2.5px solid #94a3b8 !important;
    page-break-inside: avoid !important;
  }

  .print-seal-section span {
    color: #475569 !important;
  }

  .print-seal-section p {
    color: #0f172a !important;
  }

  .print-seal-circle {
    border: 3px double #94a3b8 !important;
    background: transparent !important;
    width: 95px !important;
    height: 95px !important;
  }

  .print-seal-circle span {
    color: #64748b !important;
    font-weight: 900 !important;
    font-size: 9px !important;
  }
}
</style>
