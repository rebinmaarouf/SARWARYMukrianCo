<template>
  <div> <!-- Single Root Element -->
    <div class="space-y-8 animate-fade-in max-w-5xl mx-auto pb-20 no-print">
      <!-- Header Section -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 bg-slate-900/40 backdrop-blur-3xl p-8 rounded-[2.5rem] border border-white/5 shadow-2xl">
        <div dir="rtl" class="text-right flex-1">
          <h1 class="text-3xl font-black text-white tracking-tight">بزوێنەری گواستنەوە و حەواڵەکان</h1>
          <p class="text-slate-400 text-sm font-medium mt-3 leading-relaxed">
            ئەم پەیجە تایبەتە بە بەڕێوەبردنی گشت جوڵە داراییەکانی کۆمپانیا. لێرەدا دەتوانیت حەواڵە بۆ مشتەری بنێریت یان پارە لە نێوان سندوقەکانتدا بگوازیتەوە.
          </p>
        </div>
        <div class="p-4 bg-blue-500/10 rounded-3xl border border-blue-500/20 shadow-inner">
          <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        </div>
      </div>

    <!-- Transfer Form Card -->
    <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-3xl relative group">
      <div class="p-8 md:p-12 relative">
        <form @submit.prevent="submitTransfer" class="space-y-12" dir="rtl">
          
          <!-- Accounts Selection Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
              <label class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] px-2 flex items-center gap-2">
                <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                لە کوێوە دەڕوات (سەرچاوە)
              </label>
              <select v-model="form.from_account_id" required 
                class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-5 text-white font-black text-lg focus:border-rose-500 outline-none transition-all">
                <option value="" disabled>هەڵبژاردنی حیساب...</option>
                <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }} ({{ acc.code }})</option>
              </select>
            </div>

            <div class="space-y-4">
              <label class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] px-2 flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                بۆ کوێ دەچێت (وەرگر)
              </label>
              <select v-model="form.to_account_id" required 
                class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-5 text-white font-black text-lg focus:border-emerald-500 outline-none transition-all">
                <option value="" disabled>هەڵبژاردنی حیساب...</option>
                <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }} ({{ acc.code }})</option>
              </select>
            </div>
          </div>

          <!-- Principal Transfer Amount -->
          <div class="p-8 rounded-[2rem] bg-blue-500/5 border border-blue-500/10 space-y-6">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
               <div class="space-y-4">
                  <label class="text-xs font-black text-blue-400 uppercase tracking-[0.2em] px-2">دراوی حەواڵە</label>
                  <div class="flex gap-2 p-1 bg-slate-950/80 rounded-2xl border border-white/5">
                    <button v-for="c in currencies" :key="c.id" type="button"
                      @click="form.currency_id = c.id"
                      class="flex-1 py-5 rounded-xl text-sm font-black uppercase transition-all"
                      :class="form.currency_id === c.id ? 'bg-blue-600 text-white' : 'text-slate-500 hover:text-white'">
                      {{ c.code }}
                    </button>
                  </div>
               </div>
               <div class="space-y-4">
                  <label class="text-xs font-black text-blue-400 uppercase tracking-[0.2em] px-2">بڕی پارە</label>
                  <input :value="formText.amount" @input="e => updateAmount('amount', e.target.value)" type="text" required placeholder="0"
                    class="w-full bg-slate-950/80 border border-white/5 rounded-2xl px-6 py-5 text-white font-black text-3xl focus:border-blue-500 outline-none" />
               </div>
             </div>
          </div>

          <!-- Commission Section -->
          <div class="p-8 rounded-[2rem] bg-emerald-500/5 border border-emerald-500/10 space-y-6">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
               <div class="space-y-4">
                  <label class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em] px-2">دراوی عومولە</label>
                  <div class="flex gap-2 p-1 bg-slate-950/80 rounded-2xl border border-white/5">
                    <button v-for="c in currencies" :key="c.id" type="button"
                      @click="form.commission_currency_id = c.id"
                      class="flex-1 py-5 rounded-xl text-sm font-black uppercase transition-all"
                      :class="form.commission_currency_id === c.id ? 'bg-emerald-500 text-slate-950' : 'text-slate-500 hover:text-white'">
                      {{ c.code }}
                    </button>
                  </div>
               </div>
               <div class="space-y-4">
                  <label class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em] px-2">بڕی عومولە</label>
                  <input :value="formText.commission_amount" @input="e => updateAmount('commission', e.target.value)" type="text" placeholder="0"
                    class="w-full bg-slate-950/80 border border-white/5 rounded-2xl px-6 py-5 text-emerald-400 font-black text-3xl focus:border-emerald-500 outline-none" />
               </div>
             </div>
          </div>

          <button type="submit" :disabled="loading"
            class="w-full py-6 bg-blue-600 hover:bg-blue-500 text-white font-black text-2xl rounded-2xl shadow-2xl transition-all disabled:opacity-50 flex items-center justify-center gap-4">
            <span v-if="loading" class="animate-spin rounded-full h-6 w-6 border-4 border-white/20 border-t-white"></span>
            {{ loading ? 'خەریکی جێبەجێکردنە...' : 'تەواوکردنی گواستنەوە' }}
          </button>
        </form>
      </div>
    </div>

    <!-- Recent Transfers Table -->
    <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
      <div class="p-8 border-b border-white/5 flex items-center justify-between" dir="rtl">
        <h2 class="text-xl font-black text-white">دوایین گواستنەوە و حەواڵەکان</h2>
        <button @click="fetchData" class="p-2 text-slate-500 hover:text-blue-400 transition-colors bg-slate-950 rounded-xl border border-white/5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-right" dir="rtl">
          <thead>
            <tr class="bg-slate-950/40 text-slate-500 text-[10px] font-black uppercase tracking-widest">
              <th class="px-8 py-5">کات</th>
              <th class="px-8 py-5">لە کوێوە</th>
              <th class="px-8 py-5">بۆ کوێ</th>
              <th class="px-8 py-5 text-center">بڕی حەواڵە</th>
              <th class="px-8 py-5 text-center">عومولە</th>
              <th class="px-8 py-5 text-center">کردارەکان</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="t in transfers" :key="t.id" class="hover:bg-white/[0.02] transition-colors group">
              <td class="px-8 py-6 text-slate-400 font-mono text-xs">{{ formatTime(t.created_at) }}</td>
              <td class="px-8 py-6 text-white font-bold">{{ t.from_account?.name }}</td>
              <td class="px-8 py-6 text-white font-bold">{{ t.to_account?.name }}</td>
              <td class="px-8 py-6 text-center">
                <span class="text-blue-400 font-black text-lg">{{ formatNumber(t.amount) }}</span>
                <span class="text-slate-500 text-[10px] mr-2 font-black">{{ t.currency?.code }}</span>
              </td>
              <td class="px-8 py-6 text-center">
                 <span v-if="t.commission_amount > 0" class="text-emerald-400 font-black">{{ formatNumber(t.commission_amount) }} {{ getCurrencyCode(t.commission_currency_id) }}</span>
                 <span v-else class="text-slate-600">---</span>
              </td>
              <td class="px-8 py-6">
                <div class="flex items-center justify-center gap-3">
                  <button @click="printReceipt(t)" class="w-10 h-10 flex items-center justify-center bg-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-white rounded-xl transition-all border border-blue-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                  </button>
                  <button @click="deleteTransfer(t.id)" class="w-10 h-10 flex items-center justify-center bg-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white rounded-xl transition-all border border-rose-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- PREMIUM A5 RECEIPT (HIDDEN DURING BROWSING) -->
  <div v-if="printingTransfer" id="receipt-print-area" class="print-only-container" dir="rtl">
    <div class="a5-receipt">
      <!-- Top Branding -->
      <div class="header">
        <div class="brand">
          <img src="/logo.png" alt="Logo" class="logo" />
          <div class="brand-text">
             <h1 class="company-name">SARWARY Mukrian Co.</h1>
             <p class="company-slogan">بۆ ئاڵوگۆڕی دراو و گواستنەوەی پارە</p>
          </div>
        </div>
        <div class="receipt-id">
          <span class="label">ژمارەی پسوڵە</span>
          <span class="value">#{{ printingTransfer.id }}</span>
        </div>
      </div>

      <div class="divider"></div>

      <!-- Main Info Grid -->
      <div class="info-grid">
         <div class="info-box">
            <span class="label">بەرواری حەواڵە</span>
            <span class="value">{{ formatTime(printingTransfer.created_at) }}</span>
         </div>
         <div class="info-box">
            <span class="label">لە سندوقی</span>
            <span class="value">{{ printingTransfer.from_account?.name }}</span>
         </div>
         <div class="info-box">
            <span class="label">بۆ حیسابی</span>
            <span class="value">{{ printingTransfer.to_account?.name }}</span>
         </div>
      </div>

      <!-- Financial Summary -->
      <div class="amount-card">
         <div class="row main">
            <span class="text">بڕی پارەی نێردراو:</span>
            <span class="amount">{{ formatNumber(printingTransfer.amount) }} {{ printingTransfer.currency?.code }}</span>
         </div>
         <div class="row commission" v-if="printingTransfer.commission_amount > 0">
            <span class="text">عومولە / کرێ:</span>
            <span class="amount">{{ formatNumber(printingTransfer.commission_amount) }} {{ getCurrencyCode(printingTransfer.commission_currency_id) }}</span>
         </div>
      </div>

      <!-- Notes -->
      <div class="notes-box" v-if="printingTransfer.notes">
         <span class="label">تێبینی:</span>
         <p class="notes-text">{{ printingTransfer.notes }}</p>
      </div>

      <!-- Signature Area -->
      <div class="footer-area">
         <div class="signature">
            <p>مۆری کۆمپانیا</p>
            <div class="stamp-box"></div>
         </div>
         <div class="operator">
            <p>ئەنجامدرا لە لایەن: {{ printingTransfer.user?.name || 'Admin' }}</p>
         </div>
      </div>
      
      <div class="bottom-legal">
        <p>سوپاس بۆ متمانەتان بە کۆمپانیای سەروەری موکریان</p>
      </div>
    </div>
  </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'

const accounts = ref([])
const currencies = ref([])
const transfers = ref([])
const loading = ref(false)
const printingTransfer = ref(null)

async function printReceipt(t) {
  printingTransfer.value = t
  setTimeout(() => {
    window.print()
    printingTransfer.value = null
  }, 100)
}

async function deleteTransfer(id) {
  const result = await Swal.fire({
    title: 'ئایا دڵنیایت؟',
    text: "ئەم حەواڵەیە دەسڕێتەوە و پارەکە دەگەڕێتەوە جێی خۆی!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'بەڵێ، بیسڕەوە',
    cancelButtonText: 'پاشگەزبوونەوە',
    background: '#0f172a',
    color: '#fff'
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/transfers/${id}`)
      transfers.value = transfers.value.filter(t => t.id !== id)
      Swal.fire({ title: 'سڕایەوە', icon: 'success', background: '#0f172a', color: '#fff' })
    } catch (e) {
      Swal.fire({ title: 'هەڵە', text: 'نەتوانرا بسڕێتەوە', icon: 'error', background: '#0f172a', color: '#fff' })
    }
  }
}

const formText = ref({ amount: '', commission_amount: '' })
const form = ref({ from_account_id: '', to_account_id: '', currency_id: '', amount: 0, commission_amount: 0, commission_currency_id: '', notes: '' })

function formatWithCommas(str) {
  if (!str) return '';
  let clean = str.toString().replace(/[^\d.]/g, '');
  const [whole, decimal] = clean.split('.');
  const formattedWhole = whole.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return decimal !== undefined ? `${formattedWhole}.${decimal}` : formattedWhole;
}

function updateAmount(field, value) {
  const clean = value.replace(/,/g, '');
  if (field === 'amount') {
    formText.value.amount = formatWithCommas(clean);
    form.value.amount = parseFloat(clean) || 0;
  } else {
    formText.value.commission_amount = formatWithCommas(clean);
    form.value.commission_amount = parseFloat(clean) || 0;
  }
}

function formatNumber(val) { return new Intl.NumberFormat().format(val || 0) }
function formatTime(dateStr) { return new Date(dateStr).toLocaleString('en-GB', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric' }) }
function getCurrencyCode(id) { return currencies.value.find(c => c.id === id)?.code || '' }

async function fetchData() {
  try {
    const [accRes, curRes, transRes] = await Promise.all([axios.get('/accounts?per_page=1000'), axios.get('/currencies'), axios.get('/transfers')])
    accounts.value = accRes.data.data || accRes.data
    currencies.value = curRes.data
    transfers.value = transRes.data.data || transRes.data
    if (currencies.value.length > 0) { form.value.currency_id = currencies.value[0].id; form.value.commission_currency_id = currencies.value[0].id; }
  } catch (e) { console.error(e) }
}

async function submitTransfer() {
  loading.value = true
  try {
    const { data } = await axios.post('/transfers', form.value)
    Swal.fire({ icon: 'success', title: 'سەرکەوتوو بوو', background: '#0f172a', color: '#fff' })
    formText.value = { amount: '', commission_amount: '' }
    form.value = { from_account_id: '', to_account_id: '', currency_id: form.value.currency_id, amount: 0, commission_amount: 0, commission_currency_id: form.value.currency_id, notes: '' }
    if (data.transfer) transfers.value.unshift(data.transfer)
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'هەڵە', text: e.response?.data?.message, background: '#0f172a', color: '#fff' })
  } finally { loading.value = false }
}

onMounted(fetchData)
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.6s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* PRINTING LOGIC: Hide EVERYTHING except the print area */
@media print {
  body, html { background: white !important; height: auto; }
  .no-print, nav, aside, header, footer { display: none !important; }
  #receipt-print-area { display: block !important; visibility: visible !important; position: absolute; left: 0; top: 0; width: 100%; }
}

/* Receipt Styling */
.print-only-container { display: none; }

.a5-receipt {
  width: 148mm;
  margin: 0 auto;
  padding: 30px;
  background: white;
  color: black;
  font-family: 'Inter', 'Arial', sans-serif;
}

.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.logo { height: 65px; width: auto; }
.company-name { font-size: 24px; font-weight: 900; margin: 0; }
.company-slogan { font-size: 11px; color: #555; margin: 0; }

.receipt-id { text-align: left; }
.receipt-id .label { display: block; font-size: 10px; font-weight: 800; color: #888; }
.receipt-id .value { font-size: 18px; font-weight: 900; color: #e11d48; }

.divider { height: 3px; background: #000; margin: 20px 0; }

.info-grid { display: flex; justify-content: space-between; gap: 15px; margin-bottom: 30px; }
.info-box { flex: 1; }
.info-box .label { display: block; font-size: 9px; font-weight: 800; color: #666; margin-bottom: 3px; }
.info-box .value { font-size: 13px; font-weight: 700; }

.amount-card { background: #f4f4f4; border: 1.5px solid #ddd; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
.row { display: flex; justify-content: space-between; align-items: center; }
.row.main { margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
.row .text { font-weight: 800; font-size: 14px; }
.row .amount { font-size: 22px; font-weight: 900; }
.row.commission .amount { font-size: 16px; color: #059669; }

.notes-box { margin-bottom: 30px; }
.notes-box .label { font-size: 10px; font-weight: 800; text-decoration: underline; }
.notes-text { font-size: 12px; line-height: 1.6; margin-top: 5px; }

.footer-area { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 40px; }
.stamp-box { width: 90px; height: 90px; border: 2px dashed #bbb; border-radius: 50%; margin-top: 10px; }
.signature p { font-size: 11px; font-weight: 800; margin: 0; }
.operator p { font-size: 10px; color: #777; margin: 0; }

.bottom-legal { text-align: center; margin-top: 40px; border-top: 1px solid #eee; padding-top: 10px; font-size: 10px; font-weight: 700; color: #999; }
</style>
