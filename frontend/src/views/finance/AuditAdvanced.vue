<template>
  <div class="min-h-screen bg-[#050505] text-white p-4 md:p-10 font-sans pb-32">
    
    <!-- Header Section (No Print) -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-10 no-print">
      <div>
        <h1 class="text-3xl md:text-5xl font-black tracking-tighter mb-2">ڕاپۆرتی وردبینی پێشکەوتوو</h1>
        <p class="text-slate-400 text-sm font-medium">چاودێری گشتگیر بۆ ماڵ و سامان، قەرز، داهات و خەرجییەکان.</p>
      </div>

      <div class="flex flex-wrap gap-3">
        <button @click="exportToExcel" class="px-6 py-3 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 rounded-2xl font-black hover:bg-emerald-500 hover:text-slate-950 transition-all flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          Excel Export
        </button>
        <button @click="printReport" class="px-6 py-3 bg-white text-slate-950 rounded-2xl font-black hover:bg-slate-200 transition-all flex items-center gap-2 shadow-xl shadow-white/5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
          Print PDF
        </button>
      </div>
    </div>

    <!-- Filters (No Print) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-slate-900/40 p-6 rounded-[2.5rem] border border-white/5 backdrop-blur-3xl mb-10 no-print">
      <div class="space-y-2">
        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">لە بەرواری</label>
        <input v-model="filters.from_date" type="date" class="w-full bg-slate-950 border border-white/10 rounded-2xl p-4 text-white font-bold outline-none focus:border-emerald-500/50 transition-all" />
      </div>
      <div class="space-y-2">
        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بۆ بەرواری</label>
        <input v-model="filters.to_date" type="date" class="w-full bg-slate-950 border border-white/10 rounded-2xl p-4 text-white font-bold outline-none focus:border-emerald-500/50 transition-all" />
      </div>
      <div class="flex items-end">
        <button @click="fetchData" :disabled="loading" class="w-full py-4 bg-emerald-500 text-slate-950 font-black rounded-2xl hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-emerald-500/20 disabled:opacity-50">
          {{ loading ? 'چاوەڕێ بکە...' : 'نوێکردنەوەی ڕاپۆرت' }}
        </button>
      </div>
    </div>

    <!-- Main Report Content (Printable) -->
    <div id="printable-report" class="bg-white text-slate-950 md:rounded-[3rem] overflow-hidden shadow-2xl relative min-h-[1200px]" dir="rtl">
      
      <!-- Official Header (Visible on print & screen) -->
      <div class="p-8 md:p-12 border-b-4 border-emerald-600 bg-emerald-50 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-6">
          <img src="/logo.png" class="w-20 h-20 object-contain grayscale" />
          <div>
            <h1 class="text-3xl font-black text-emerald-900 leading-none mb-2">کۆمپانیای سەروەری موکریان</h1>
            <p class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">SARWARY MUKRIAN / FINANCIAL AUDIT BUREAU</p>
          </div>
        </div>
        <div class="text-left md:text-right">
          <h2 class="text-xl font-black text-slate-900 mb-1">ڕاپۆرتی دارایی گشتی</h2>
          <p class="text-xs font-bold text-slate-500 italic">ماوەی: {{ filters.from_date }} بۆ {{ filters.to_date }}</p>
          <div class="mt-4 inline-block bg-emerald-600 text-white px-4 py-1.5 rounded-full text-[10px] font-black">
             رەسیدی بازاڕ: $1 = {{ formatNum(data.exchange_rate) }} IQD
          </div>
        </div>
      </div>

      <div class="p-8 md:p-12 space-y-12">
        
        <!-- Summary Dashboard (4 Pillars) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="p-6 bg-slate-50 border-r-4 border-emerald-500 rounded-2xl">
            <span class="text-[9px] font-black text-slate-400 uppercase mb-2 block">کۆی ماڵ و سامان (Assets)</span>
            <p class="text-2xl font-black text-emerald-700">{{ formatNum(data.financials?.assets?.total_iqd) }} <span class="text-xs font-bold opacity-60">IQD</span></p>
          </div>
          <div class="p-6 bg-slate-50 border-r-4 border-rose-500 rounded-2xl">
            <span class="text-[9px] font-black text-slate-400 uppercase mb-2 block">سەرمایە و پابەندییەکان (Liabilities)</span>
            <p class="text-2xl font-black text-rose-700">{{ formatNum(data.financials?.liabilities?.total_iqd) }} <span class="text-xs font-bold opacity-60">IQD</span></p>
          </div>
          <div class="p-6 bg-slate-50 border-r-4 border-amber-500 rounded-2xl">
            <span class="text-[9px] font-black text-slate-400 uppercase mb-2 block">داهاتی گشتی (Revenue)</span>
            <p class="text-2xl font-black text-amber-700">{{ formatNum(data.financials?.revenues?.total_iqd) }} <span class="text-xs font-bold opacity-60">IQD</span></p>
          </div>
          <div class="p-6 bg-slate-50 border-r-4 border-blue-500 rounded-2xl">
            <span class="text-[9px] font-black text-slate-400 uppercase mb-2 block">پوختەی قازانج و زیان (Net)</span>
            <p class="text-2xl font-black" :class="data.financials?.net_profit >= 0 ? 'text-emerald-600' : 'text-rose-600'">
               {{ formatNum(data.financials?.net_profit) }} <span class="text-xs font-bold opacity-60">IQD</span>
            </p>
          </div>
        </div>

        <!-- 1. Assets Detail Table -->
        <section>
          <div class="flex items-center gap-3 mb-6">
            <div class="w-3 h-8 bg-emerald-600 rounded-full"></div>
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">١. ماڵ و سامان (Assets)</h3>
          </div>
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="bg-slate-100 text-[10px] font-black text-slate-600 uppercase border-y border-slate-200">
                <th class="px-6 py-4">کۆدی حیساب</th>
                <th class="px-6 py-4">ناوی حیساب</th>
                <th class="px-6 py-4 text-left">باڵانس (IQD Equivalent)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="acc in data.financials?.assets?.accounts" :key="acc.code">
                <td class="px-6 py-4 text-xs font-black text-slate-500">{{ acc.code }}</td>
                <td class="px-6 py-4 text-sm font-black text-slate-800">{{ acc.name }}</td>
                <td class="px-6 py-4 text-sm font-black text-emerald-700 text-left">{{ formatNum(acc.balance_iqd) }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-emerald-50 font-black">
                <td colspan="2" class="px-6 py-4 text-sm uppercase">کۆی گشتی ماڵ و سامان</td>
                <td class="px-6 py-4 text-lg text-emerald-800 text-left underline decoration-double">{{ formatNum(data.financials?.assets?.total_iqd) }} IQD</td>
              </tr>
            </tfoot>
          </table>
        </section>

        <!-- 2. Liabilities Detail Table -->
        <section>
          <div class="flex items-center gap-3 mb-6">
            <div class="w-3 h-8 bg-rose-600 rounded-full"></div>
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">٢. سەرمایە و پابەندییەکان (Liabilities & Equity)</h3>
          </div>
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="bg-slate-100 text-[10px] font-black text-slate-600 uppercase border-y border-slate-200">
                <th class="px-6 py-4">کۆدی حیساب</th>
                <th class="px-6 py-4">ناوی حیساب</th>
                <th class="px-6 py-4 text-left">باڵانس (IQD Equivalent)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="acc in data.financials?.liabilities?.accounts" :key="acc.code">
                <td class="px-6 py-4 text-xs font-black text-slate-500">{{ acc.code }}</td>
                <td class="px-6 py-4 text-sm font-black text-slate-800">{{ acc.name }}</td>
                <td class="px-6 py-4 text-sm font-black text-rose-700 text-left">{{ formatNum(acc.balance_iqd) }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-rose-50 font-black">
                <td colspan="2" class="px-6 py-4 text-sm uppercase">کۆی گشتی سەرمایە و پابەندییەکان</td>
                <td class="px-6 py-4 text-lg text-rose-800 text-left underline decoration-double">{{ formatNum(data.financials?.liabilities?.total_iqd) }} IQD</td>
              </tr>
            </tfoot>
          </table>
        </section>

        <!-- 3. Performance (P&L Breakdown) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-6 border-t border-slate-200">
           <!-- Revenue Column -->
           <div class="space-y-6">
              <h4 class="text-base font-black text-amber-700 flex items-center gap-2">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                 سەرچاوەی داهات و قازانج (Revenues)
              </h4>
              <ul class="space-y-3">
                 <li v-for="acc in data.financials?.revenues?.accounts" :key="acc.code" class="flex justify-between items-center text-sm">
                    <span class="font-bold text-slate-600">{{ acc.name }}</span>
                    <span class="font-black text-slate-950">{{ formatNum(acc.balance_iqd) }}</span>
                 </li>
                 <li class="pt-2 border-t border-amber-100 flex justify-between items-center font-black text-amber-700">
                    <span>کۆی داهات</span>
                    <span>{{ formatNum(data.financials?.revenues?.total_iqd) }}</span>
                 </li>
              </ul>
           </div>

           <!-- Expense Column -->
           <div class="space-y-6">
              <h4 class="text-base font-black text-blue-700 flex items-center gap-2">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                 خەرجییە کارگێڕییەکان (Expenses)
              </h4>
              <ul class="space-y-3">
                 <li v-for="acc in data.financials?.expenses?.accounts" :key="acc.code" class="flex justify-between items-center text-sm">
                    <span class="font-bold text-slate-600">{{ acc.name }}</span>
                    <span class="font-black text-slate-950">{{ formatNum(acc.balance_iqd) }}</span>
                 </li>
                 <li class="pt-2 border-t border-blue-100 flex justify-between items-center font-black text-blue-700">
                    <span>کۆی مەسروفات</span>
                    <span>{{ formatNum(data.financials?.expenses?.total_iqd) }}</span>
                 </li>
              </ul>
           </div>
        </div>

        <!-- 4. Real-time Vault Status Summary -->
        <section class="bg-slate-900 text-white p-8 rounded-[2rem] no-print">
           <h4 class="text-xl font-black mb-6 text-emerald-400">چاودێری سندوقەکان (Real-time Vaults)</h4>
           <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div v-for="v in data.vaults" :key="v.name + v.currency_code" class="bg-white/5 border border-white/10 p-4 rounded-2xl">
                 <p class="text-[10px] font-black text-slate-500 uppercase">{{ v.name }}</p>
                 <p class="text-xl font-black mt-1">{{ formatNum(v.balance) }} <span class="text-xs text-emerald-500">{{ v.currency_code }}</span></p>
              </div>
           </div>
        </section>

        <!-- Final Validation Section -->
        <div class="pt-12 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-10">
           <div class="flex items-center gap-4">
              <div class="w-16 h-16 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                 <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
              </div>
              <div>
                 <p class="text-lg font-black text-slate-900 leading-none">بەردەست بوونی نەقدی گۆڕاوە بۆ دۆلار</p>
                 <p class="text-xs font-bold text-slate-500 mt-1">Certified Financial Audit - SM-ERP Audit Engine v2.4</p>
              </div>
           </div>
           <div class="text-right">
              <p class="text-3xl font-black text-emerald-600">{{ formatNum(data.financials?.net_profit / data.exchange_rate) }} <span class="text-sm font-bold opacity-50">$ USD</span></p>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">TOTAL CONSOLIDATED NET VALUATION</p>
           </div>
        </div>

        <!-- Signature Space (Visible on Print) -->
        <div class="mt-24 hidden print:flex justify-between px-12">
           <div class="text-center w-48 border-t-2 border-slate-900 pt-2">
              <p class="text-xs font-black uppercase">Accountant Signature</p>
           </div>
           <div class="text-center w-48 border-t-2 border-slate-900 pt-2">
              <p class="text-xs font-black uppercase">Manager Approval</p>
           </div>
           <div class="text-center w-48 border-t-2 border-slate-900 pt-2">
              <p class="text-xs font-black uppercase">Official Stamp</p>
           </div>
        </div>

      </div> <!-- End Content Padding -->

    </div> <!-- End Printable Area -->

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'

const data = ref({})
const loading = ref(false)
const filters = ref({
  from_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
  to_date: new Date().toISOString().split('T')[0]
})

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

function exportToExcel() {
  // Simple CSV Generator
  let csv = "Account Code,Account Name,Balance (IQD Equivalent)\n"
  
  // Assets
  csv += "\nASSETS\n"
  data.value.financials.assets.accounts.forEach(acc => {
    csv += `${acc.code},${acc.name},${acc.balance_iqd}\n`
  })
  csv += `Total Assets,,${data.value.financials.assets.total_iqd}\n`

  // Liabilities
  csv += "\nLIABILITIES & EQUITY\n"
  data.value.financials.liabilities.accounts.forEach(acc => {
    csv += `${acc.code},${acc.name},${acc.balance_iqd}\n`
  })
  csv += `Total Liabilities,,${data.value.financials.liabilities.total_iqd}\n`

  // Final P&L
  csv += `\nNet Profit,,${data.value.financials.net_profit}\n`

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement("a")
  const url = URL.createObjectURL(blob)
  link.setAttribute("href", url)
  link.setAttribute("download", `Audit_Report_${filters.value.from_date}_to_${filters.value.to_date}.csv`)
  link.style.visibility = 'hidden'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

function formatNum(n) { return new Intl.NumberFormat().format(n || 0) }

onMounted(fetchData)
</script>

<style scoped>
@media print {
  @page { size: A4; margin: 0; }
  body { background: white !important; color: black !important; padding: 0 !important; margin: 0 !important; }
  .no-print { display: none !important; }
  #printable-report { 
    border-radius: 0 !important; 
    box-shadow: none !important; 
    width: 100% !important; 
    position: absolute;
    left: 0; top: 0;
  }
  .bg-slate-900 { background-color: #f1f5f9 !important; color: black !important; }
}

input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>
