<template>
  <div class="min-h-screen bg-[#050505] text-white p-4 md:p-10 font-sans pb-32">
    
    <!-- Header Section (No Print) -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-10 no-print">
      <div>
        <h1 class="text-3xl md:text-5xl font-black tracking-tighter mb-2">ووردبینی دارایی پێشکەوتوو</h1>
        <p class="text-slate-400 text-sm font-medium">چاودێری گشتگیر و یەکگرتوو بۆ هەموو لقەکان و سندوقەکان.</p>
      </div>

      <div class="flex flex-wrap gap-3">
        <button @click="printReport" class="px-8 py-4 bg-white text-slate-950 rounded-2xl font-black hover:bg-slate-200 transition-all flex items-center gap-2 shadow-2xl shadow-white/10">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
          پرێنت کردنی ڕاپۆرت (PDF)
        </button>
      </div>
    </div>

    <!-- Filters (No Print) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 bg-slate-900/40 p-8 rounded-[3rem] border border-white/5 backdrop-blur-3xl mb-10 no-print">
      <div class="space-y-2">
        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">لە بەرواری</label>
        <input v-model="filters.from_date" type="date" class="w-full bg-slate-950 border border-white/10 rounded-2xl p-4 text-white font-bold outline-none focus:border-emerald-500/50 transition-all" />
      </div>
      <div class="space-y-2">
        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بۆ بەرواری</label>
        <input v-model="filters.to_date" type="date" class="w-full bg-slate-950 border border-white/10 rounded-2xl p-4 text-white font-bold outline-none focus:border-emerald-500/50 transition-all" />
      </div>
      <div class="space-y-2">
        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">دیاریکردنی لق</label>
        <select v-model="filters.branch_id" class="w-full bg-slate-950 border border-white/10 rounded-2xl p-4 text-white font-bold outline-none focus:border-emerald-500/50 transition-all appearance-none">
          <option value="all">هەموو لقەکان (Consolidated)</option>
          <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
        </select>
      </div>
      <div class="flex items-end">
        <button @click="fetchData" :disabled="loading" class="w-full py-4 bg-emerald-500 text-slate-950 font-black rounded-2xl hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-emerald-500/20 disabled:opacity-50">
          {{ loading ? 'چاوەڕێ بکە...' : 'نوێکردنەوەی ڕاپۆرت' }}
        </button>
      </div>
    </div>

    <!-- Main Report Content (Printable) -->
    <div id="printable-report" class="bg-white text-slate-950 md:rounded-[3rem] overflow-hidden shadow-2xl relative print:m-0 print:rounded-none" dir="rtl">
      
      <!-- Official Header - Optimized for Print Height -->
      <div class="p-8 border-b-4 border-emerald-600 bg-slate-50 flex flex-col md:flex-row justify-between items-center gap-4 print:p-4 print:border-b-2">
        <div class="flex items-center gap-4 print:gap-2">
          <img src="/logo.png" class="w-16 h-16 object-contain print:w-12 print:h-12" />
          <div>
            <h1 class="text-2xl font-black text-slate-900 leading-none mb-1 print:text-lg">کۆمپانیای سەروەری موکریان</h1>
            <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest print:text-[7px]">SARWARY MUKRIAN / FINANCIAL AUDIT BUREAU</p>
          </div>
        </div>
        <div class="text-left md:text-right">
          <h2 class="text-lg font-black text-slate-900 mb-0.5 print:text-sm">وەسڵنامەی ووردبینی</h2>
          <p class="text-[10px] font-bold text-slate-500 italic print:text-[8px]">ماوەی: {{ filters.from_date }} بۆ {{ filters.to_date }}</p>
          <p class="text-[8px] font-black text-slate-400 mt-0.5 uppercase print:text-[7px]">لق: {{ activeBranchName }}</p>
        </div>
      </div>

      <div class="p-8 space-y-8 print:p-4 print:space-y-4">
        
        <!-- Summary Dashboard (4 Pillars) - Compact in Print -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 print:grid-cols-4 print:gap-1.5">
          <div class="p-4 bg-emerald-50 border-r-2 border-emerald-500 rounded-xl print:p-2">
            <span class="text-[8px] font-black text-emerald-700 uppercase mb-1 block print:text-[7px]">کۆی ماڵ و سامان</span>
            <p class="text-lg font-black text-emerald-900 leading-tight print:text-[11px]">{{ formatNum(data.financials?.assets?.total_iqd) }} <span class="text-[8px]">IQD</span></p>
          </div>
          <div class="p-4 bg-rose-50 border-r-2 border-rose-500 rounded-xl print:p-2">
            <span class="text-[8px] font-black text-rose-700 uppercase mb-1 block print:text-[7px]">سەرمایە و پابەندی</span>
            <p class="text-lg font-black text-rose-900 leading-tight print:text-[11px]">{{ formatNum(data.financials?.liabilities?.total_iqd) }} <span class="text-[8px]">IQD</span></p>
          </div>
          <div class="p-4 bg-amber-50 border-r-2 border-amber-500 rounded-xl print:p-2">
            <span class="text-[8px] font-black text-amber-700 uppercase mb-1 block print:text-[7px]">داهاتی گشتی</span>
            <p class="text-lg font-black text-amber-900 leading-tight print:text-[11px]">{{ formatNum(data.financials?.revenues?.total_iqd) }} <span class="text-[8px]">IQD</span></p>
          </div>
          <div class="p-4 bg-blue-50 border-r-2 border-blue-500 rounded-xl print:p-2">
            <span class="text-[8px] font-black text-blue-700 uppercase mb-1 block print:text-[7px]">پوختەی قازانج</span>
            <p class="text-lg font-black leading-tight print:text-[11px]" :class="data.financials?.net_profit >= 0 ? 'text-blue-900' : 'text-rose-900'">
               {{ formatNum(data.financials?.net_profit) }} <span class="text-[8px]">IQD</span>
            </p>
          </div>
        </div>

        <!-- Vault Forensics - Ultra Compact for Print -->
        <section class="page-break">
          <div class="flex items-center gap-2 mb-3 print:mb-1.5">
            <div class="w-2 h-6 bg-blue-600 rounded-full print:h-4"></div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tighter print:text-[10px] italic">ووردبینی جوڵەی سندوقەکان (Vault Analytics)</h3>
          </div>
          <table class="w-full text-right border-collapse print:text-[9px]">
            <thead>
              <tr class="bg-slate-100 text-[8px] font-black uppercase text-slate-600 print:text-[7px]">
                <th class="px-4 py-2 print:py-1">سندوق</th>
                <th class="px-4 py-2 print:py-1">دراو</th>
                <th class="px-4 py-2 text-emerald-600 print:py-1">هاتوو (Debtor)</th>
                <th class="px-4 py-2 text-rose-600 print:py-1">ڕۆیشتوو (Creditor)</th>
                <th class="px-4 py-2 text-left print:py-1">پوختە</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <template v-for="f in data.vault_forensics" :key="f.vault_code + f.currency_code">
                <!-- Main Row (Clickable) -->
                <tr @click="toggleRow(f)" class="cursor-pointer hover:bg-slate-50 transition-all group print:cursor-default">
                  <td class="px-4 py-2.5 font-black text-slate-800 print:py-1 flex items-center gap-2">
                    <span class="no-print text-slate-400 transition-transform duration-300 group-hover:text-blue-500" :class="{ 'rotate-180 text-blue-600': isExpanded(f) }">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                    {{ f.vault_name }}
                  </td>
                  <td class="px-4 py-2.5 font-black text-slate-500 print:py-1">{{ f.currency_code }}</td>
                  <td class="px-4 py-2.5 font-black text-emerald-600 print:py-1">{{ formatNum(f.total_in) }}</td>
                  <td class="px-4 py-2.5 font-black text-rose-600 print:py-1">{{ formatNum(f.total_out) }}</td>
                  <td class="px-4 py-2.5 font-black text-left print:py-1" :class="f.net_change >= 0 ? 'text-emerald-700' : 'text-rose-700'">
                    {{ formatNum(f.net_change) }}
                  </td>
                </tr>

                <!-- Collapsible Detail Sub-Table (Hidden in Print) -->
                <tr v-if="isExpanded(f)" class="bg-slate-50/60 no-print transition-all duration-300">
                  <td colspan="5" class="px-6 py-4">
                    <div class="bg-slate-950 text-slate-100 rounded-3xl p-6 shadow-2xl border border-white/5 space-y-4">
                      <div class="flex justify-between items-center border-b border-white/5 pb-3">
                        <span class="text-xs font-black text-blue-400 tracking-tight flex items-center gap-1.5">
                          <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-ping"></span>
                          وردەکاری جوڵە داراییەکان: {{ f.vault_name }} ({{ f.currency_code }})
                        </span>
                        <span class="text-[9px] bg-slate-900 text-slate-400 px-3 py-1.5 rounded-full font-black uppercase tracking-wider">
                          کۆی جوڵەکان: {{ getRowDetails(f).length }}
                        </span>
                      </div>
                      
                      <div v-if="getRowDetails(f).length === 0" class="text-center py-6 text-xs text-slate-500 font-bold">
                        هیچ جوڵەیەکی حیسابی بەردەست نییە بۆ ئەم سندوق و دراوە لەم بەروارەدا.
                      </div>
                      
                      <table v-else class="w-full text-right text-[10px] border-collapse">
                        <thead>
                          <tr class="text-slate-500 border-b border-white/5 text-[9px] font-black uppercase tracking-wider">
                            <th class="pb-3 text-right">ڕێکەوت و کات</th>
                            <th class="pb-3 text-right">ناو / جۆری مامەڵە</th>
                            <th class="pb-3 text-right text-emerald-400">هاتوو (+)</th>
                            <th class="pb-3 text-right text-rose-400">ڕۆیشتوو (-)</th>
                            <th class="pb-3 text-left">ئەنجامدەر</th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 font-semibold">
                          <tr v-for="d in getRowDetails(f)" :key="d.id" class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 text-slate-400 font-bold">{{ d.date }}</td>
                            <td class="py-3 font-bold text-slate-200">{{ d.description }}</td>
                            <td class="py-3 text-emerald-400 font-black">{{ d.total_in > 0 ? formatNum(d.total_in) : '-' }}</td>
                            <td class="py-3 text-rose-400 font-black">{{ d.total_out > 0 ? formatNum(d.total_out) : '-' }}</td>
                            <td class="py-3 text-left text-slate-400 font-bold">{{ d.user_name || 'سیستەم' }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </section>

        <!-- 1. Assets Detail - Compact -->
        <section class="page-break">
          <div class="flex items-center gap-2 mb-3 print:mb-1.5">
            <div class="w-2 h-6 bg-emerald-600 rounded-full print:h-4"></div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tighter print:text-[10px]">١. ماڵ و سامان (Assets)</h3>
          </div>
          <table class="w-full text-right border-collapse print:text-[9px]">
            <thead>
              <tr class="bg-slate-900 text-white text-[8px] font-black uppercase print:bg-slate-100 print:text-black print:text-[7px]">
                <th class="px-4 py-2 rounded-r-lg print:py-1 print:rounded-none">کۆدی حیساب</th>
                <th class="px-4 py-2 print:py-1">ناوی حیساب</th>
                <th class="px-4 py-2 text-left rounded-l-lg print:py-1 print:rounded-none">باڵانس (دینار)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="acc in data.financials?.assets?.accounts" :key="acc.code">
                <td class="px-4 py-2 text-[9px] font-black text-slate-400 print:py-1 print:text-[8px]">{{ acc.code }}</td>
                <td class="px-4 py-2 text-xs font-black text-slate-800 print:py-1 print:text-[9px]">{{ acc.name }}</td>
                <td class="px-4 py-2 text-xs font-black text-emerald-700 text-left print:py-1 print:text-[9px]">{{ formatNum(acc.balance_iqd) }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-emerald-600 text-white font-black print:bg-slate-50 print:text-black">
                <td colspan="2" class="px-4 py-2 text-xs print:py-1 print:text-[9px]">کۆی گشتی ماڵ و سامان</td>
                <td class="px-4 py-2 text-sm text-left print:py-1 print:text-[10px]">{{ formatNum(data.financials?.assets?.total_iqd) }} IQD</td>
              </tr>
            </tfoot>
          </table>
        </section>

        <!-- 2. Liabilities Detail - Compact -->
        <section class="page-break">
          <div class="flex items-center gap-2 mb-3 print:mb-1.5">
            <div class="w-2 h-6 bg-rose-600 rounded-full print:h-4"></div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tighter print:text-[10px]">٢. سەرمایە و پابەندی (Liabilities)</h3>
          </div>
          <table class="w-full text-right border-collapse print:text-[9px]">
            <thead>
              <tr class="bg-slate-900 text-white text-[8px] font-black uppercase print:bg-slate-100 print:text-black print:text-[7px]">
                <th class="px-4 py-2 rounded-r-lg print:py-1 print:rounded-none">کۆدی حیساب</th>
                <th class="px-4 py-2 print:py-1">ناوی حیساب</th>
                <th class="px-4 py-2 text-left rounded-l-lg print:py-1 print:rounded-none">باڵانس (دینار)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="acc in data.financials?.liabilities?.accounts" :key="acc.code">
                <td class="px-4 py-2 text-[9px] font-black text-slate-400 print:py-1 print:text-[8px]">{{ acc.code }}</td>
                <td class="px-4 py-2 text-xs font-black text-slate-800 print:py-1 print:text-[9px]">{{ acc.name }}</td>
                <td class="px-4 py-2 text-xs font-black text-rose-700 text-left print:py-1 print:text-[9px]">{{ formatNum(acc.balance_iqd) }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-rose-600 text-white font-black print:bg-slate-50 print:text-black">
                <td colspan="2" class="px-4 py-2 text-xs print:py-1 print:text-[9px]">کۆی گشتی سەرمایە</td>
                <td class="px-4 py-2 text-sm text-left print:py-1 print:text-[10px]">{{ formatNum(data.financials?.liabilities?.total_iqd) }} IQD</td>
              </tr>
            </tfoot>
          </table>
        </section>

        <!-- Keep Final Validation & Signatures strictly together to avoid break separation -->
        <div class="print-avoid-break border-t border-slate-100 pt-6 print:pt-4">
           <!-- Final Validation Section - Very Compact -->
           <div class="flex justify-between items-center">
              <div class="flex items-center gap-3">
                 <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white print:w-8 print:h-8">
                    <svg class="w-6 h-6 print:w-5 print:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                 </div>
                 <div>
                    <p class="text-sm font-black text-slate-900 print:text-[10px]">بەهای پوختەی هەڵسەنگاندن</p>
                    <p class="text-[8px] font-bold text-slate-400">Audit Engine v2.4</p>
                 </div>
              </div>
              <div class="text-right">
                 <p class="text-2xl font-black text-emerald-600 print:text-lg">{{ formatNum(data.financials?.net_profit / data.exchange_rate) }} <span class="text-xs font-bold opacity-50">$ USD</span></p>
                 <p class="text-[8px] font-black text-slate-400 uppercase mt-0.5 print:text-[7px]">TOTAL NET VALUATION</p>
              </div>
           </div>

           <!-- Signature Space - Moved up to fit in one page -->
           <div class="mt-12 flex justify-between px-8 print:mt-8 print:px-4">
              <div class="text-center w-32 border-t border-slate-900 pt-1.5">
                 <p class="text-[7px] font-black uppercase">ووردبین (Accountant)</p>
              </div>
              <div class="text-center w-32 border-t border-slate-900 pt-1.5">
                 <p class="text-[7px] font-black uppercase">بەڕێوەبەر (Manager)</p>
              </div>
              <div class="text-center w-32 border-t border-slate-900 pt-1.5">
                 <p class="text-[7px] font-black uppercase">مۆر (Stamp)</p>
              </div>
           </div>
        </div>

        <!-- Dynamic Running Print Footer -->
        <div class="hidden print:flex fixed bottom-0 left-0 right-0 justify-between items-center border-t border-slate-200 pt-1 text-[8px] font-bold text-slate-400" style="position: fixed; bottom: -0.2cm; left: 0; right: 0; direction: rtl;">
           <span>کۆمپانیای سەروەری موکریان - وەسڵنامەی ووردبینی دارایی</span>
           <span class="print-page-number">پەڕەی </span>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'

const data = ref({})
const branches = ref([])
const loading = ref(false)

const expandedRows = ref({})

function toggleRow(f) {
  const key = f.vault_code + '_' + f.currency_code
  expandedRows.value[key] = !expandedRows.value[key]
}

function isExpanded(f) {
  const key = f.vault_code + '_' + f.currency_code
  return !!expandedRows.value[key]
}

function getRowDetails(f) {
  if (!data.value.vault_details) return []
  return data.value.vault_details.filter(d => d.vault_code === f.vault_code && d.currency_code === f.currency_code)
}
const filters = ref({
  from_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
  to_date: new Date().toISOString().split('T')[0],
  branch_id: 'all'
})

const activeBranchName = computed(() => {
  if (filters.value.branch_id === 'all') return 'هەموو لقەکان'
  const branch = branches.value.find(b => b.id == filters.value.branch_id)
  return branch ? branch.name : 'لقى نەزانراو'
})

async function fetchBranches() {
  try {
    const { data: res } = await axios.get('/branches')
    branches.value = res
  } catch (e) { console.error(e) }
}

async function fetchData() {
  loading.value = true
  try {
    const { data: res } = await axios.get('/audit-advanced', { params: filters.value })
    data.value = res
  } catch (e) {
    console.error(e)
    Swal.fire({ icon: 'error', title: 'Error', text: 'شکستی هێنا لە وەرگرتنی داتاکان' })
  } finally {
    loading.value = false
  }
}

function printReport() {
  window.print()
}

function formatNum(n) { return new Intl.NumberFormat().format(n || 0) }

onMounted(() => {
  fetchBranches()
  fetchData()
})
</script>

<style scoped>
@media print {
  @page { 
    size: A4; 
    margin: 1.2cm 1cm 1.2cm 1cm; 
  }
  body { background: white !important; color: black !important; padding: 0 !important; margin: 0 !important; }
  .no-print { display: none !important; }
  #printable-report { 
    border-radius: 0 !important; 
    box-shadow: none !important; 
    width: 100% !important; 
    border: none !important;
    height: auto !important;
    padding-bottom: 0.5cm !important;
  }
  .page-break { page-break-inside: avoid; break-inside: avoid; }
  .print-avoid-break { page-break-inside: avoid !important; break-inside: avoid !important; }
  table { width: 100% !important; }
  tr { page-break-inside: avoid; break-inside: avoid; }
  /* Running print footer */
  .print-page-number::after {
    content: counter(page);
  }
  /* Force clean line-heights in print */
  * { line-height: 1.2 !important; }
}

input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>
