<template>
  <div class="min-h-screen bg-slate-100 py-10 no-print-bg">
    <!-- Main A4 Wrapper -->
    <div class="a4-page mx-auto bg-white shadow-2xl overflow-hidden print:shadow-none print:m-0">
      
      <!-- Loop for two copies (Top and Bottom) -->
      <div v-for="i in 2" :key="i" class="invoice-section relative">
        
        <!-- Header Section -->
        <div class="p-8 pb-4">
          <div class="flex justify-between items-start">
            <!-- Left Side: Date and Vouch No -->
            <div class="text-right space-y-1" dir="rtl">
              <p class="text-[11px] font-bold text-slate-600">بەروار: <span class="font-mono">{{ formatDate(new Date()) }}</span></p>
              <p class="text-[11px] font-bold text-slate-600">کات: <span class="font-mono">{{ formatTime(new Date()) }}</span></p>
              <p class="text-[11px] font-bold text-slate-600">ژ. وەسڵ: <span class="font-mono text-emerald-600">{{ String(Math.floor(Math.random() * 9000) + 1000) }}</span></p>
            </div>

            <!-- Middle Side: Logo and Company Info -->
            <div class="text-center flex-1">
              <img :src="logoPath" alt="Logo" class="w-24 mx-auto mb-2 opacity-90" @error="onLogoError" />
              <h1 class="text-xl font-black text-emerald-900 leading-tight">کۆمپانیای سەروەر موکریان</h1>
              <p class="text-[10px] font-bold text-emerald-700">بۆ ئاڵوگۆڕی دراو / سنووردار</p>
              <h2 class="inline-block border-b-2 border-emerald-500 text-sm font-black text-slate-800 mt-2 pb-0.5">بسوولەی ئاڵوگۆڕی دراو</h2>
            </div>

            <!-- Right Side: Contact info (Empty in screenshot header but for balance) -->
            <div class="w-32"></div>
          </div>
        </div>

        <!-- Main Form Table -->
        <div class="px-12 py-4">
          <div class="border-2 border-emerald-500/20 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-right border-collapse" dir="rtl">
              <tbody>
                <!-- Row: Client Name -->
                <tr class="border-b border-emerald-100">
                  <td class="w-32 bg-emerald-50 p-3 text-[12px] font-black text-emerald-900 border-l border-emerald-200">بەڕێز</td>
                  <td class="p-3 text-sm font-black text-slate-700">{{ clientName || 'کۆمپانیای ناتڕۆن' }}</td>
                </tr>
                <!-- Row: Amount Received -->
                <tr class="border-b border-emerald-100">
                  <td class="bg-emerald-50 p-3 text-[12px] font-black text-emerald-900 border-l border-emerald-200">وەرمان گرت مەبلەغی</td>
                  <td class="p-3">
                    <div class="flex items-center gap-4">
                       <span class="text-lg font-black text-slate-800 tracking-wider">10,000</span>
                       <span class="text-[10px] font-bold text-slate-400 border-r border-slate-200 pr-4">تەنها دە هەزار دۆلار</span>
                    </div>
                  </td>
                </tr>
                <!-- Row: Exchanged For -->
                <tr class="border-b border-emerald-100">
                  <td class="bg-emerald-50 p-3 text-[12px] font-black text-emerald-900 border-l border-emerald-200">گۆڕاوەتەوە مقابل</td>
                  <td class="p-3">
                    <div class="flex items-center gap-4">
                       <span class="text-lg font-black text-slate-800 tracking-wider">15,000,000</span>
                       <span class="text-[10px] font-bold text-slate-400 border-r border-slate-200 pr-4">تەنها پازدە ملیۆن دینار</span>
                    </div>
                  </td>
                </tr>
                <!-- Row: Exchange Rate -->
                <tr class="border-b border-emerald-100">
                  <td class="bg-emerald-50 p-3 text-[12px] font-black text-emerald-900 border-l border-emerald-200">نرخی گۆڕینەوە</td>
                  <td class="p-3 text-lg font-black text-emerald-600">150,000</td>
                </tr>
                <!-- Row: Note -->
                <tr class="border-b border-emerald-100">
                  <td class="bg-emerald-50 p-3 text-[12px] font-black text-emerald-900 border-l border-emerald-200">تێبینی</td>
                  <td class="p-3 text-sm font-bold text-slate-500 italic">دۆلاری شین</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Signature Section -->
        <div class="px-12 py-4 flex justify-between items-center" dir="rtl">
           <div class="text-center w-40">
             <p class="text-[11px] font-black text-emerald-900 bg-emerald-50 py-1 px-4 rounded-lg border border-emerald-200">ئیمزای وەرگر</p>
             <div class="h-16"></div>
           </div>
           <div class="flex-1 px-8 text-center">
             <p class="text-[10px] font-bold text-slate-400 leading-relaxed">ناونیشان: سلێمانی - بازاڕی شێخ وەڵا - نهۆمی یەکەم دووکانی ژمارە ١٢٨</p>
             <p class="text-[11px] font-black text-slate-800 mt-1 tracking-widest">0750 098 1919 | 0770 098 1919</p>
           </div>
        </div>

        <!-- Cutting Line (Only shown on screen or between copies) -->
        <div v-if="i === 1" class="border-t-2 border-dashed border-slate-300 my-4 relative">
           <div class="absolute left-1/2 -top-3 -translate-x-1/2 bg-white px-4">
              <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758L5 19m0-14l4.121 4.121"/></svg>
           </div>
        </div>
      </div>

    </div>

    <!-- Print Controls -->
    <div class="fixed bottom-8 right-8 flex gap-4 no-print">
       <button @click="$router.back()" class="px-6 py-3 bg-white text-slate-600 font-bold rounded-2xl shadow-xl hover:bg-slate-50 transition-all">گەڕانەوە</button>
       <button @click="printInvoice" class="px-8 py-3 bg-emerald-600 text-white font-black rounded-2xl shadow-xl shadow-emerald-500/20 hover:-translate-y-1 transition-all">چاپکردن (A4)</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const clientName = ref(route.query.client)
const logoPath = ref('/img/logo.png')

const onLogoError = () => {
  // If local logo is missing, use a placeholder so the UI doesn't break
  logoPath.value = 'https://api.dicebear.com/7.x/initials/svg?seed=SM&backgroundColor=10b981'
}

const formatDate = (date) => {
  return new Intl.DateTimeFormat('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(date)
}

const formatTime = (date) => {
  return new Intl.DateTimeFormat('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true }).format(date)
}

const printInvoice = () => {
  window.print()
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');

.a4-page {
  width: 210mm;
  height: 297mm;
  padding: 10mm;
}

@media print {
  body {
    background: white !important;
    padding: 0 !important;
  }
  .no-print {
    display: none !important;
  }
  .no-print-bg {
    background: white !important;
    padding: 0 !important;
  }
  .a4-page {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    box-shadow: none;
    border: none;
  }
  .invoice-section {
    height: 148.5mm; /* Exactly half A4 */
  }
}

.invoice-section {
  height: 138.5mm; /* Slightly less for screen padding */
}
</style>
