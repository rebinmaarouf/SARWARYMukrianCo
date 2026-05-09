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

  <!-- PREMIUM 80MM THERMAL RECEIPT -->
  <div v-if="printingTransfer" id="receipt-print-area" class="print-only-container text-black" dir="rtl">
    <div v-for="i in 2" :key="i" class="pb-6" :class="{ 'border-t border-dashed border-black pt-6 mt-6': i === 2 }">
      <!-- Top Branding -->
      <div class="text-center mb-4">
         <div class="flex items-center justify-center gap-2 mb-1">
            <img src="/logo.png" class="w-10 h-10 grayscale" @error="(e) => e.target.style.display='none'" />
            <h1 class="text-base font-black tracking-tight text-black">کۆمپانیای سەروەری موکریان</h1>
         </div>
         <p class="text-[9px] font-bold text-black opacity-70">پسوڵەی فەرمی گواستنەوەی ناوخۆیی</p>
         <p class="text-[8px] text-slate-500 font-mono">Ref ID: #TR-{{ printingTransfer.id }}</p>
      </div>

      <!-- Transaction Type Bar -->
      <div class="bg-black text-white text-center py-1 text-xs font-black rounded mb-3 uppercase tracking-wider">
         گواستنەوەی دارایی (MONEY TRANSFER)
      </div>

      <!-- Main Info Grid -->
      <div class="space-y-1 text-[10px] border-b border-dashed border-slate-300 pb-2 mb-3">
         <div class="flex justify-between">
            <span class="font-bold opacity-60">بەرواری حەواڵە:</span>
            <span class="font-bold font-mono">{{ formatTime(printingTransfer.created_at) }}</span>
         </div>
         <div class="flex justify-between">
            <span class="font-bold opacity-60">لە سندوقی:</span>
            <span class="font-black">{{ printingTransfer.from_account?.name }}</span>
         </div>
         <div class="flex justify-between">
            <span class="font-bold opacity-60">بۆ سندوقی:</span>
            <span class="font-black">{{ printingTransfer.to_account?.name }}</span>
         </div>
         <div class="flex justify-between" v-if="printingTransfer.user?.name">
            <span class="font-bold opacity-60">ئەنجامدەر:</span>
            <span class="font-bold">{{ printingTransfer.user?.name }}</span>
         </div>
      </div>

      <!-- Financial Summary -->
      <div class="bg-slate-100 p-2.5 rounded-lg mb-3">
         <div class="flex justify-between items-center mb-1">
            <span class="text-[9px] font-black text-black">بڕی گواستراوە:</span>
            <span class="text-base font-black font-mono text-black">{{ formatNumber(printingTransfer.amount) }} {{ printingTransfer.currency?.code }}</span>
         </div>
         <div class="flex justify-between items-center text-emerald-600" v-if="printingTransfer.commission_amount > 0">
            <span class="text-[9px] font-bold">عومولە / کرێ:</span>
            <span class="text-xs font-black font-mono">+ {{ formatNumber(printingTransfer.commission_amount) }} {{ getCurrencyCode(printingTransfer.commission_currency_id) }}</span>
         </div>
      </div>

      <!-- Notes -->
      <div class="notes-box mb-3" v-if="printingTransfer.notes">
         <span class="text-[9px] font-black opacity-60">تێبینی:</span>
         <p class="text-xs font-bold leading-relaxed text-slate-800 mt-1 bg-white border border-slate-200 p-2 rounded">{{ printingTransfer.notes }}</p>
      </div>

      <!-- Signature Area and Cryptographic Seal -->
      <div class="text-center space-y-2 py-2">
         <div class="text-[8px] font-mono text-slate-500 flex justify-center items-center gap-1">
            <span>🔒 INTEGRITY SEAL:</span>
            <span class="font-bold text-black">SM-v2-TR-{{ printingTransfer.id }}-{{ printingTransfer.amount }}</span>
         </div>
         <p class="text-[8px] font-bold leading-relaxed text-slate-700">
            «مۆری فەرمی کۆمپانیای سەروەری موکریان - سوپاس بۆ متمانەتان.»
         </p>
      </div>

      <div class="flex justify-between text-[8px] font-black pt-4 border-t border-dashed border-slate-200">
         <div class="w-24 border-t border-black pt-1 text-center">واژۆی ڕادەستکار</div>
         <div class="w-24 border-t border-black pt-1 text-center">واژۆی وەرگر</div>
      </div>

      <!-- Divider with Scissors -->
      <div v-if="i === 1" class="text-center text-[8px] font-bold text-slate-400 py-4 flex justify-center items-center gap-2">
         <span>✂️-----------------------------------------</span>
         <span>بڕین لێرەوە (OFFICE / DEPT COPY)</span>
      </div>
    </div>
  </div>

  <!-- PREMIUM A4 TRANSFER RECEIPT -->
  <div v-if="printingTransfer" id="receipt-print-area-a4" class="print-only-container text-black" dir="rtl">
    <div v-for="i in 2" :key="i" class="a4-voucher" :class="{ 'border-t-2 border-dashed border-slate-300 pt-8 mt-8': i === 2 }">
      <!-- Top Branding -->
      <div class="flex justify-between items-center border-b-2 border-black pb-4 mb-4">
         <div class="flex items-center gap-4">
            <img src="/logo.png" class="h-16 w-16 object-contain grayscale animate-pulse" />
            <div>
               <h1 class="text-xl font-black text-black">کۆمپانیای سەروەری موکریان</h1>
               <p class="text-xs font-bold text-black uppercase">SARWARY MUKRIAN / INTERNAL MONEY TRANSFER</p>
            </div>
         </div>
         <div class="text-left" dir="ltr">
            <h2 class="text-xl font-black text-black leading-none">TRANSFER VOUCHER</h2>
            <p class="text-xs font-black mt-1">REF: #TR-{{ printingTransfer.id }}</p>
         </div>
      </div>

      <!-- Basic Info Grid -->
      <div class="grid grid-cols-3 gap-4 mb-6 text-xs text-black">
         <div class="border border-black p-3 rounded">
            <span class="font-black block text-slate-500">بەروار / Date</span>
            <span class="text-sm font-black">{{ formatTime(printingTransfer.created_at) }}</span>
         </div>
         <div class="border border-black p-3 rounded">
            <span class="font-black block text-slate-500">لە سندوقی / From Vault</span>
            <span class="text-sm font-black">{{ printingTransfer.from_account?.name }}</span>
         </div>
         <div class="border border-black p-3 rounded text-right">
            <span class="font-black block text-slate-500">بۆ سندوقی / To Vault</span>
            <span class="text-sm font-black">{{ printingTransfer.to_account?.name }}</span>
         </div>
      </div>

      <!-- Financial Summary -->
      <div class="border-2 border-black mb-6">
         <div class="bg-black text-white px-4 py-2.5 flex justify-between text-xs font-black uppercase">
            <span>وردەکاری گواستنەوە / Transfer Summary</span>
            <span>بڕی کۆتایی / Final Amount</span>
         </div>
         <div class="flex">
            <!-- Details -->
            <div class="flex-1 p-4 border-l-2 border-black flex flex-col gap-3 text-right">
               <div class="grid grid-cols-2 gap-4">
                  <div>
                     <span class="text-[9px] font-black text-slate-400 uppercase block font-sans">دراوی سەرەکی / Currency</span>
                     <span class="text-sm font-black">{{ printingTransfer.currency?.code }}</span>
                  </div>
                  <div>
                     <span class="text-[9px] font-black text-slate-400 uppercase block font-sans">ئەنجامدەر / Authorized By</span>
                     <span class="text-sm font-black text-black">{{ printingTransfer.user?.name || 'Admin' }}</span>
                  </div>
               </div>
               <div class="border-t border-slate-200 pt-3" v-if="printingTransfer.notes">
                  <span class="text-[9px] font-black text-slate-400 uppercase block font-sans">تێبینی / Remarks</span>
                  <p class="text-xs font-bold text-black">{{ printingTransfer.notes }}</p>
               </div>
            </div>
            <!-- Big Amount -->
            <div class="w-1/3 p-4 flex flex-col items-center justify-center bg-slate-50">
               <span class="text-[9px] font-black text-slate-400 uppercase block font-sans mb-1">TOTAL TRANSFERRED</span>
               <p class="text-3xl font-black font-mono tracking-tighter text-black">{{ formatNumber(printingTransfer.amount) }} {{ printingTransfer.currency?.code }}</p>
               <div class="mt-2 text-center text-emerald-600" v-if="printingTransfer.commission_amount > 0">
                  <span class="text-[8px] block font-black uppercase opacity-60">عومولە / COMMISSION</span>
                  <p class="text-sm font-black font-mono">+ {{ formatNumber(printingTransfer.commission_amount) }} {{ getCurrencyCode(printingTransfer.commission_currency_id) }}</p>
               </div>
            </div>
         </div>
      </div>

      <!-- Signature Area and Cryptographic Seal -->
      <div class="text-center space-y-2 py-4">
         <div class="text-[10px] font-mono text-slate-500 flex justify-center items-center gap-2">
            <span>🔒 INTEGRITY SEAL:</span>
            <span class="font-bold text-black">SM-v2-TR-{{ printingTransfer.id }}-SHA256-{{ printingTransfer.amount }}</span>
         </div>
         <p class="text-xs font-bold leading-relaxed text-slate-700">
            «مۆری فەرمی کۆمپانیای سەروەری موکریان - سوپاس بۆ متمانەتان.»
         </p>
      </div>

      <div class="flex justify-between mt-12 px-8">
         <div class="text-center w-36 border-t border-black pt-2">
            <p class="text-xs font-black uppercase">واژۆی ڕادەستکار / Handed Over By</p>
         </div>
         <div class="text-center w-36 border-t border-black pt-2">
            <p class="text-xs font-black uppercase">کۆمپانیا / Office Stamp</p>
         </div>
         <div class="text-center w-36 border-t border-black pt-2">
            <p class="text-xs font-black uppercase">واژۆی وەرگر / Received By</p>
         </div>
      </div>

      <!-- Cut Line -->
      <div v-if="i === 1" class="my-8 border-t border-dashed border-slate-300 relative">
         <span class="absolute left-1/2 -translate-x-1/2 -top-2 bg-white px-3 text-[10px] text-slate-400">✂️ ببڕدرێت لێرەوە / CUT HERE (OFFICE / DEPT COPY)</span>
      </div>
    </div>
  </div>

  <!-- Print Options Modal -->
  <div v-if="showPrintOptions" class="fixed inset-0 bg-slate-950/80 backdrop-blur-xl z-[99999] flex items-center justify-center p-4 md:p-8 overflow-y-auto no-print animate-fade-in text-right">
     <div class="bg-slate-900 border border-white/10 w-full max-w-lg rounded-[2.5rem] shadow-[0_50px_100px_rgba(0,0,0,0.8)] overflow-hidden relative">
        <!-- Modal Header -->
        <div class="px-8 py-6 bg-slate-950/50 border-b border-white/5 flex items-center justify-between">
           <div class="flex items-center gap-3">
              <span class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></span>
              <h3 class="text-base font-black text-white">شێوازی چاپکردن هەڵبژێرە</h3>
           </div>
           <button @click="showPrintOptions = false" class="w-10 h-10 bg-slate-950 hover:bg-slate-800 rounded-xl flex items-center justify-center text-slate-400 hover:text-white transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
           </button>
        </div>

        <!-- Selection Options -->
        <div class="p-8 space-y-4">
           <!-- Option 1: Thermal Printer -->
           <button @click="executePrint('80mm')" class="w-full text-right p-6 rounded-3xl bg-slate-950/40 border border-white/5 hover:border-emerald-500/50 hover:bg-emerald-500/5 transition-all group flex items-center gap-6">
              <div class="w-14 h-14 bg-emerald-500/10 text-emerald-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                 <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              </div>
              <div class="flex-1">
                 <h4 class="text-white font-black text-lg mb-1 group-hover:text-emerald-400 transition-colors">پسوڵەی حەراری بچووک (80mm)</h4>
                 <p class="text-slate-400 text-xs">گونجاوە بۆ پرینتەری حەراری بچووک و مۆبایل و تابلێت.</p>
              </div>
           </button>

           <!-- Option 2: Laser Printer -->
           <button @click="executePrint('a4')" class="w-full text-right p-6 rounded-3xl bg-slate-950/40 border border-white/5 hover:border-blue-500/50 hover:bg-blue-500/5 transition-all group flex items-center gap-6">
              <div class="w-14 h-14 bg-blue-500/10 text-blue-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                 <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
              </div>
              <div class="flex-1">
                 <h4 class="text-white font-black text-lg mb-1 group-hover:text-blue-400 transition-colors">پسوڵەی فەرمی گەورە (A4 / A5)</h4>
                 <p class="text-slate-400 text-xs">گونجاوە بۆ پرینتەری گەورەی نوسینگە و لێزەری.</p>
              </div>
           </button>
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
const showPrintOptions = ref(false)
const selectedTransferToPrint = ref(null)
const printMode = ref('80mm')

async function printReceipt(t) {
  selectedTransferToPrint.value = t
  showPrintOptions.value = true
}

function executePrint(mode) {
  printMode.value = mode
  printingTransfer.value = selectedTransferToPrint.value
  showPrintOptions.value = false
  
  // Apply body print class
  document.body.classList.add(`print-${mode}`)
  
  setTimeout(() => {
    window.print()
    printingTransfer.value = null
    document.body.classList.remove(`print-${mode}`)
  }, 150)
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

/* PRINTING LOGIC: Dual format print-mode selectors */
@media print {
  body * { display: none !important; }
  
  /* Shared overrides */
  body.print-80mm #receipt-print-area,
  body.print-80mm #receipt-print-area *,
  body.print-a4 #receipt-print-area-a4,
  body.print-a4 #receipt-print-area-a4 * {
    display: block !important;
    visibility: visible !important;
  }

  body.print-80mm #receipt-print-area { 
    position: absolute !important; 
    left: 0 !important; 
    top: 0 !important; 
    width: 80mm !important; 
    max-width: 80mm !important; 
    padding: 2mm !important; 
    box-sizing: border-box !important;
    background: white !important;
    color: black !important;
  }

  body.print-a4 #receipt-print-area-a4 {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    padding: 1.5cm !important;
    box-sizing: border-box !important;
    background: white !important;
    color: black !important;
  }

  body.print-a4 .a4-voucher {
    width: 100% !important;
    page-break-inside: avoid !important;
  }
}

.print-only-container { display: none; }
</style>
