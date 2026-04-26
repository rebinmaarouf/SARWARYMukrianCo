<template>
  <div class="print-container bg-white" dir="rtl">
    <div v-if="loading" class="flex justify-center items-center h-64 no-print">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600"></div>
    </div>

    <div v-else class="max-w-[210mm] mx-auto overflow-hidden">
      <!-- 2 Copies strictly managed for A4 -->
      <div v-for="i in 2" :key="i" class="invoice-wrapper relative overflow-hidden border-b border-dashed border-slate-200 last:border-0">
        
        <div class="p-4 h-full flex flex-col justify-between">
          <!-- Header (Compact) -->
          <div class="flex justify-between items-center border-b border-emerald-600 pb-2 mb-2">
            <div class="flex items-center gap-3">
              <img :src="'http://sarwary-api.test/img/logo.jpg'" @error="(e) => e.target.style.display='none'" class="w-12 h-12 object-contain" alt="Logo">
              <div>
                <h1 class="text-xl font-black text-emerald-800 leading-tight">کۆمپانیای سەروەر موکریان</h1>
                <p class="text-[8px] font-bold text-slate-500 uppercase">بۆ ئاڵوگۆڕی دراو و حەواڵە</p>
              </div>
            </div>
            <div class="text-left bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-100">
              <p class="text-[8px] font-black text-slate-400">ژ. وەسڵ: <span class="text-sm text-emerald-700">#{{ invoiceData.id }}</span></p>
              <p class="text-[8px] font-bold text-slate-500">{{ formatDate(invoiceData.created_at) }} | {{ formatTime(invoiceData.created_at) }}</p>
            </div>
          </div>

          <!-- Content -->
          <div class="space-y-2">
            <div class="flex justify-between border-b border-slate-100 pb-1">
              <span class="text-slate-400 font-bold text-[10px]">بەڕێز:</span>
              <span class="font-black text-slate-800 text-sm">{{ invoiceData.client_name || invoiceData.account?.name }}</span>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div class="bg-emerald-50 p-2 rounded-xl border border-emerald-100">
                <p class="text-[8px] font-black text-emerald-600 mb-1 tracking-tighter">وەرمان گرت (Received)</p>
                <p class="text-base font-black text-slate-900">{{ formatNum(invoiceData.primary_amount) }} <span class="text-[10px]">{{ invoiceData.primary_currency }}</span></p>
              </div>
              <div class="bg-rose-50 p-2 rounded-xl border border-rose-100 text-left">
                <p class="text-[8px] font-black text-rose-600 mb-1 tracking-tighter">گۆڕاوەتەوە بۆ (Exchanged)</p>
                <p class="text-base font-black text-slate-900">{{ formatNum(invoiceData.secondary_amount) }} <span class="text-[10px]">{{ invoiceData.secondary_currency }}</span></p>
              </div>
            </div>

            <div class="flex justify-between items-center py-1 border-b border-slate-100">
              <span class="text-slate-400 font-bold text-[10px]">نرخی گۆڕینەوە (Rate):</span>
              <span class="text-base font-black text-slate-800">{{ formatNum(invoiceData.rate) }}</span>
            </div>

            <!-- Total Bar -->
            <div class="bg-slate-900 text-white px-4 py-2 rounded-xl flex justify-between items-center">
               <p class="text-[9px] font-black text-slate-400">کۆی گشتی (TOTAL)</p>
               <p class="text-xl font-black">{{ formatNum(invoiceData.secondary_amount) }} <span class="text-[10px] font-bold opacity-70">{{ invoiceData.secondary_currency }}</span></p>
            </div>

            <p class="text-[9px] font-bold text-slate-500 border-t border-slate-100 pt-1">تێبینی: <span class="text-slate-900">{{ invoiceData.note || '---' }}</span></p>
          </div>

          <!-- Footer (One Row to save space) -->
          <div class="mt-4 flex justify-between items-center pt-2 border-t border-slate-100">
            <div class="text-[7px] text-slate-400 space-y-0.5">
              <p>سلێمانی - بازاڕی شێخ وەڵا - نهۆمی یەکەم</p>
              <p>0770 098 1919 | 0750 098 1919</p>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-24 h-6 border-b border-slate-200 mb-0.5"></div>
              <p class="text-[8px] font-black text-slate-500 uppercase">ئیمزای وەرگر</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Floating Bar -->
      <div class="fixed bottom-10 left-1/2 -translate-x-1/2 no-print">
         <div class="bg-white/80 backdrop-blur-2xl px-6 py-3 rounded-2xl shadow-2xl border border-slate-200 flex items-center gap-4">
            <button @click="$router.back()" class="p-2 text-slate-500 hover:text-slate-800 transition-all">
               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </button>
            <button @click="printPage" class="px-8 py-3 bg-emerald-600 text-white font-black rounded-xl shadow-lg hover:bg-emerald-700 transition-all flex items-center gap-2">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h12z"/></svg>
               چاپکردن
            </button>
         </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from '../../plugins/axios'

const route = useRoute()
const invoiceData = ref({})
const loading = ref(true)

async function fetchInvoice() {
  try {
    const id = route.query.id
    if (!id) return
    const { data } = await axios.get(`/exchanges/${id}`)
    invoiceData.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

function printPage() { window.print() }
function formatNum(val) { return val ? new Intl.NumberFormat().format(val) : '0' }
function formatDate(date) { return date ? new Date(date).toLocaleDateString('en-GB') : '' }
function formatTime(date) { return date ? new Date(date).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }) : '' }

onMounted(fetchInvoice)
</script>

<style scoped>
@media print {
  @page { size: A4; margin: 0 !important; }
  body { margin: 0 !important; padding: 0 !important; background: white; }
  .no-print { display: none !important; }
}

.invoice-wrapper {
  height: 140mm; /* Reduced to ensure it never spills to 2nd page */
  width: 100%;
  box-sizing: border-box;
}
</style>
