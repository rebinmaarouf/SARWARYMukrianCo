<template>
  <div> <!-- Single Root Element -->
    <div class="space-y-8 animate-fade-in max-w-5xl mx-auto pb-20 no-print">
      <!-- Header Section -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
        <div dir="rtl" class="text-right flex-1">
          <h1 class="text-3xl font-black text-slate-900 tracking-tight">بزوێنەری گواستنەوە و حەواڵەکان</h1>
          <p class="text-slate-500 text-sm font-medium mt-3 leading-relaxed">
            ئەم پەیجە تایبەتە بە بەڕێوەبردنی گشت جوڵە داراییەکانی کۆمپانیا. لێرەدا دەتوانیت حەواڵە بۆ مشتەری بنێریت یان پارە لە نێوان سندوقەکانتدا بگوازیتەوە.
          </p>
        </div>
        <div class="p-4 bg-blue-50 text-blue-700 rounded-3xl border border-blue-200 shadow-xs">
          <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        </div>
      </div>

    <!-- Transfer Form Card -->
    <div class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm relative group">
      <div class="p-8 md:p-12 relative">
        <form @submit.prevent="submitTransfer" class="space-y-12" dir="rtl">
          
          <!-- Accounts Selection Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4 relative">
              <label class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] px-2 flex items-center gap-2">
                <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                لە کوێوە دەڕوات (سەرچاوە)
              </label>
              
              <!-- Smart Toggle -->
              <div class="flex gap-2 p-1 bg-slate-100 rounded-2xl">
                 <button type="button" @click="applySourceType('cash')" :class="sourceType === 'cash' ? 'bg-rose-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-200'" class="flex-1 py-3 text-sm font-black rounded-xl transition-all">لە قاصەوە (نەقد)</button>
                 <button type="button" @click="applySourceType('debt')" :class="sourceType === 'debt' ? 'bg-rose-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-200'" class="flex-1 py-3 text-sm font-black rounded-xl transition-all">لە وەکیلەوە (قەرز)</button>
              </div>

              <div class="relative" v-if="sourceType === 'debt'">
                <input ref="fromAccountInput" v-model="fromAccountSearch" @focus="showResults = 'from'" @input="searchAccounts('from')" type="text" placeholder="بگەڕێ بۆ ناو یان کۆد..."
                  class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-5 text-slate-900 font-black text-lg focus:border-rose-500 outline-none transition-all" />
                <div v-if="showResults === 'from' && filteredAccountsFrom.length" class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-200 rounded-2xl z-50 shadow-2xl p-2 space-y-1 max-h-60 overflow-y-auto">
                  <button v-for="acc in filteredAccountsFrom" :key="acc.id" @click="selectAccount(acc, 'from')" type="button" class="w-full text-right p-3 hover:bg-slate-50 rounded-xl flex justify-between items-center group">
                    <div class="flex flex-col text-right">
                      <span class="font-bold text-slate-900 text-sm group-hover:text-rose-600">{{ acc.name }}</span>
                      <span v-if="acc.branch" class="text-[10px] text-slate-500 font-medium">{{ acc.branch.name }}</span>
                    </div>
                    <span class="text-[10px] font-black bg-slate-100 text-slate-600 px-2 py-1 rounded-lg">{{ acc.code }}</span>
                  </button>
                </div>
              </div>
              <div v-else class="w-full bg-rose-50 border border-rose-200 rounded-2xl px-6 py-5 flex items-center justify-between cursor-not-allowed opacity-90">
                 <span class="text-rose-700 font-black text-lg">{{ fromAccountSearch || 'سندوق هەڵنەبژێردراوە' }}</span>
                 <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
            </div>

            <div class="space-y-4 relative">
              <label class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] px-2 flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                بۆ کوێ دەچێت (وەرگر)
              </label>

              <!-- Smart Toggle -->
              <div class="flex gap-2 p-1 bg-slate-100 rounded-2xl">
                 <button type="button" @click="applyDestType('cash')" :class="destType === 'cash' ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-200'" class="flex-1 py-3 text-sm font-black rounded-xl transition-all">بۆ قاصە (نەقد)</button>
                 <button type="button" @click="applyDestType('debt')" :class="destType === 'debt' ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-200'" class="flex-1 py-3 text-sm font-black rounded-xl transition-all">بۆ وەکیل (قەرز)</button>
              </div>

              <div class="relative" v-if="destType === 'debt'">
                <input ref="toAccountInput" v-model="toAccountSearch" @focus="showResults = 'to'" @input="searchAccounts('to')" type="text" placeholder="بگەڕێ بۆ ناو یان کۆد..."
                  class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-5 text-slate-900 font-black text-lg focus:border-emerald-500 outline-none transition-all" />
                <div v-if="showResults === 'to' && filteredAccountsTo.length" class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-200 rounded-2xl z-50 shadow-2xl p-2 space-y-1 max-h-60 overflow-y-auto">
                  <button v-for="acc in filteredAccountsTo" :key="acc.id" @click="selectAccount(acc, 'to')" type="button" class="w-full text-right p-3 hover:bg-slate-50 rounded-xl flex justify-between items-center group">
                    <div class="flex flex-col text-right">
                      <span class="font-bold text-slate-900 text-sm group-hover:text-emerald-600">{{ acc.name }}</span>
                      <span v-if="acc.branch" class="text-[10px] text-slate-500 font-medium">{{ acc.branch.name }}</span>
                    </div>
                    <span class="text-[10px] font-black bg-slate-100 text-slate-600 px-2 py-1 rounded-lg">{{ acc.code }}</span>
                  </button>
                </div>
              </div>
              <div v-else class="w-full bg-emerald-50 border border-emerald-200 rounded-2xl px-6 py-5 flex items-center justify-between cursor-not-allowed opacity-90">
                 <span class="text-emerald-700 font-black text-lg">{{ toAccountSearch || 'سندوق هەڵنەبژێردراوە' }}</span>
                 <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
            </div>
          </div>

          <!-- Principal Transfer Amount -->
          <div class="p-8 rounded-[2rem] bg-blue-50/50 border border-blue-100 space-y-6">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
               <div class="space-y-4">
                  <label class="text-xs font-black text-blue-700 uppercase tracking-[0.2em] px-2">دراوی حەواڵە</label>
                  <div class="flex gap-2 p-1 bg-white rounded-2xl border border-slate-200">
                    <button v-for="c in currencies" :key="c.id" type="button"
                      @click="form.currency_id = c.id"
                      class="flex-1 py-5 rounded-xl text-sm font-black uppercase transition-all"
                      :class="form.currency_id === c.id ? 'bg-blue-600 text-white' : 'text-slate-600 hover:text-slate-900'">
                      {{ c.code }}
                    </button>
                  </div>
               </div>
               <div class="space-y-4">
                  <label class="text-xs font-black text-blue-700 uppercase tracking-[0.2em] px-2">بڕی پارە</label>
                  <input ref="amountInput" :value="formText.amount" @input="e => updateAmount('amount', e.target.value)" @blur="validateAmount('amount')" type="text" required placeholder="0"
                    class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-5 text-slate-900 font-black text-3xl focus:border-blue-600 outline-none shadow-xs" />
               </div>
             </div>
          </div>

          <!-- Commission Section -->
          <div class="p-8 rounded-[2rem] bg-emerald-50/50 border border-emerald-100 space-y-6">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
               <div class="space-y-4">
                  <label class="text-xs font-black text-emerald-700 uppercase tracking-[0.2em] px-2">دراوی عومولە</label>
                  <div class="flex gap-2 p-1 bg-white rounded-2xl border border-slate-200">
                    <button v-for="c in currencies" :key="c.id" type="button"
                      @click="form.commission_currency_id = c.id"
                      class="flex-1 py-5 rounded-xl text-sm font-black uppercase transition-all"
                      :class="form.commission_currency_id === c.id ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:text-slate-900'">
                      {{ c.code }}
                    </button>
                  </div>
               </div>
               <div class="space-y-4">
                  <label class="text-xs font-black text-emerald-700 uppercase tracking-[0.2em] px-2">بڕی عومولە</label>
                  <input :value="formText.commission_amount" @input="e => updateAmount('commission', e.target.value)" type="text" placeholder="0"
                    class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-5 text-emerald-600 font-black text-3xl focus:border-emerald-600 outline-none shadow-xs" />
               </div>
             </div>
          </div>

          <!-- Notes Section -->
          <div class="p-8 rounded-[2rem] bg-slate-50/50 border border-slate-100 space-y-4">
             <label class="text-xs font-black text-slate-700 uppercase tracking-[0.2em] px-2">بەیان / تێبینی (ئارەزوومەندانە)</label>
             <input v-model="form.notes" type="text" placeholder="بۆ نموونە: حەواڵەکردنەوەی پارەی زیادەی لق بۆ سەرەکی..."
               class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-5 text-slate-700 font-bold text-lg focus:border-blue-600 outline-none shadow-xs" />
          </div>

          <button type="submit" :disabled="loading"
            class="w-full py-6 bg-blue-600 hover:bg-blue-700 text-white font-black text-2xl rounded-2xl shadow-lg transition-all disabled:opacity-50 flex items-center justify-center gap-4">
            <span v-if="loading" class="animate-spin rounded-full h-6 w-6 border-4 border-white/20 border-t-white"></span>
            {{ loading ? 'خەریکی جێبەجێکردنە...' : 'تەواوکردنی گواستنەوە' }}
          </button>
        </form>
      </div>
    </div>

    <!-- Recent Transfers Table -->
    <div class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm">
      <div class="p-8 border-b border-slate-100 flex items-center justify-between" dir="rtl">
        <h2 class="text-xl font-black text-slate-900">دوایین گواستنەوە و حەواڵەکان</h2>
        <button @click="fetchData" class="p-2 text-slate-500 hover:text-blue-600 transition-colors bg-slate-50 rounded-xl border border-slate-200 shadow-xs">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-right" dir="rtl">
          <thead>
            <tr class="bg-slate-50 text-slate-500 text-[10px] font-black uppercase tracking-widest border-b border-slate-200">
              <th class="px-8 py-5">کات</th>
              <th class="px-8 py-5">لە کوێوە</th>
              <th class="px-8 py-5">بۆ کوێ</th>
              <th class="px-8 py-5 text-center">بڕی حەواڵە</th>
              <th class="px-8 py-5 text-center">عومولە</th>
              <th class="px-8 py-5 text-center">کردارەکان</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
            <tr v-for="t in transfers" :key="t.id" class="hover:bg-slate-50 transition-colors group">
              <td class="px-8 py-6 text-slate-500 font-mono text-xs">{{ formatTime(t.created_at) }}</td>
              <td class="px-8 py-6 text-slate-900 font-bold">{{ t.from_account?.name }}</td>
              <td class="px-8 py-6 text-slate-900 font-bold">{{ t.to_account?.name }}</td>
              <td class="px-8 py-6 text-center">
                <span class="text-blue-600 font-black text-lg">{{ formatNumber(t.amount) }}</span>
                <span class="text-slate-500 text-[10px] mr-2 font-black">{{ t.currency?.code }}</span>
              </td>
              <td class="px-8 py-6 text-center">
                 <span v-if="t.commission_amount > 0" class="text-emerald-600 font-black">{{ formatNumber(t.commission_amount) }} {{ getCurrencyCode(t.commission_currency_id) }}</span>
                 <span v-else class="text-slate-400">---</span>
              </td>
              <td class="px-8 py-6">
                <div class="flex items-center justify-center gap-3">
                  <button @click="printReceipt(t)" class="w-10 h-10 flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all border border-blue-200 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                  </button>
                  <button @click="deleteTransfer(t.id)" class="w-10 h-10 flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl transition-all border border-rose-200 shadow-xs">
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
    <div class="pb-6">
      <!-- Top Branding -->
      <div class="text-center mb-4">
         <div class="flex items-center justify-center gap-2 mb-1">
            <img src="/logo.png" class="w-10 h-10 grayscale" @error="(e) => e.target.style.display='none'" />
            <h1 class="text-base font-black tracking-tight text-black">کۆمپانیای سەروەری موکریان</h1>
         </div>
         <p class="text-[9px] font-bold text-black opacity-70">پسوڵەی فەرمی گواستنەوەی ناوخۆیی</p>
         <p class="text-[9px] font-bold text-slate-700 mt-0.5">{{ printingTransfer.from_account?.branch?.name || '---' }} ➔ {{ printingTransfer.to_account?.branch?.name || '---' }}</p>
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
    </div>
  </div>

  <!-- PREMIUM A4 TRANSFER RECEIPT -->
  <div v-if="printingTransfer" id="receipt-print-area-a4" class="print-only-container text-black" dir="rtl">
    <template v-for="i in 2" :key="i">
       <div class="print-voucher">
      <!-- Top Branding -->
      <div class="flex justify-between items-center border-b border-black pb-1 mb-2">
         <div class="flex items-center gap-2">
            <img src="/logo.png" class="h-10 w-10 object-contain grayscale" />
            <div>
               <h1 class="text-base font-black text-black">کۆمپانیای سەروەری موکریان</h1>
               <p class="text-[10px] font-bold text-black uppercase">SARWARY MUKRIAN / INTERNAL MONEY TRANSFER</p>
               <p class="text-[10px] font-bold text-slate-700 mt-0.5">لقی: {{ printingTransfer.from_account?.branch?.name || '---' }} ➔ {{ printingTransfer.to_account?.branch?.name || '---' }}</p>
            </div>
         </div>
         <div class="text-left" dir="ltr">
            <h2 class="text-sm font-black text-black leading-none">TRANSFER VOUCHER</h2>
            <p class="text-[10px] font-black mt-0.5">REF: #TR-{{ printingTransfer.id }}</p>
         </div>
      </div>

      <!-- Basic Info Grid -->
      <div class="grid grid-cols-3 gap-2 mb-2 text-[10px] text-black">
         <div class="border border-black p-1.5 rounded">
            <span class="font-black block text-slate-500 text-[8px]">بەروار / Date</span>
            <span class="text-xs font-black">{{ formatTime(printingTransfer.created_at) }}</span>
         </div>
         <div class="border border-black p-1.5 rounded">
            <span class="font-black block text-slate-500 text-[8px]">لە سندوقی / From Vault</span>
            <span class="text-xs font-black">{{ printingTransfer.from_account?.name }}</span>
         </div>
         <div class="border border-black p-1.5 rounded text-right">
            <span class="font-black block text-slate-500 text-[8px]">بۆ سندوقی / To Vault</span>
            <span class="text-xs font-black">{{ printingTransfer.to_account?.name }}</span>
         </div>
      </div>

      <!-- Financial Summary -->
      <div class="border border-black mb-2">
         <div class="bg-black text-white px-3 py-1 flex justify-between text-[9px] font-black uppercase">
            <span>وردەکاری گواستنەوە / Transfer Summary</span>
            <span>بڕی کۆتایی / Final Amount</span>
         </div>
         <div class="flex">
            <!-- Details -->
            <div class="flex-1 p-2 border-l border-black flex flex-col gap-1 text-right">
               <div class="grid grid-cols-2 gap-2">
                  <div>
                     <span class="text-[8px] font-black text-slate-500 uppercase block font-sans">دراوی سەرەکی / Currency</span>
                     <span class="text-xs font-black">{{ printingTransfer.currency?.code }}</span>
                  </div>
                  <div>
                     <span class="text-[8px] font-black text-slate-500 uppercase block font-sans">ئەنجامدەر / Authorized By</span>
                     <span class="text-xs font-black text-black">{{ printingTransfer.user?.name || 'Admin' }}</span>
                  </div>
               </div>
               <div class="border-t border-slate-200 pt-1" v-if="printingTransfer.notes">
                  <span class="text-[8px] font-black text-slate-500 uppercase block font-sans">تێبینی / Remarks</span>
                  <p class="text-xs font-bold text-black">{{ printingTransfer.notes }}</p>
               </div>
            </div>
            <!-- Big Amount -->
            <div class="w-1/3 p-2 flex flex-col items-center justify-center bg-slate-50">
               <span class="text-[8px] font-black text-slate-500 uppercase block font-sans">TOTAL TRANSFERRED</span>
               <p class="text-lg font-black font-mono tracking-tighter text-black">{{ formatNumber(printingTransfer.amount) }} {{ printingTransfer.currency?.code }}</p>
               <div class="mt-1 text-center text-emerald-600" v-if="printingTransfer.commission_amount > 0">
                  <span class="text-[8px] block font-black uppercase opacity-60">عومولە / COMMISSION</span>
                  <p class="text-xs font-black font-mono">+ {{ formatNumber(printingTransfer.commission_amount) }} {{ getCurrencyCode(printingTransfer.commission_currency_id) }}</p>
               </div>
            </div>
         </div>
      </div>

      <!-- Signatures -->
      <div class="flex justify-between mt-4 px-4 text-black">
         <div class="text-center w-28 border-t border-black pt-1">
            <p class="text-[9px] font-black uppercase">واژۆی ڕادەستکار / Handed Over By</p>
         </div>
         <div class="text-center w-28 border-t border-black pt-1">
            <p class="text-[9px] font-black uppercase">کۆمپانیا / Office Stamp</p>
         </div>
         <div class="text-center w-28 border-t border-black pt-1">
            <p class="text-[9px] font-black uppercase">واژۆی وەرگر / Received By</p>
         </div>
      </div>

      <!-- Legal Disclaimer & Contact -->
      <div class="mt-4 border-t border-slate-100 pt-1 flex justify-between items-end text-black">
         <div class="text-[9px] font-bold text-slate-500 leading-tight">
            <p>• تکایە پێش دەرچوون لە نوسینگە دڵنیابەرەوە لە بڕی پارەکە.</p>
            <p>• نوسینگە بەرپرسیار نییە لە هەر هەڵەیەک دوای ڕۆیشتن.</p>
         </div>
         <div class="text-left text-[8px] font-black opacity-35 uppercase tracking-tighter">
            <p>Sarwary Mukrian Co. | Transfer Audit Trail</p>
            <p>System Hash: SM-v2-TR-{{ printingTransfer.id }} | {{ i === 1 ? 'OFFICE COPY' : 'CUSTOMER COPY' }}</p>
         </div>
      </div>

       </div>
       <!-- Cut Line -->
       <div v-if="i === 1" class="w-full border-t border-dashed border-slate-400 relative py-1">
          <span class="absolute left-1/2 -translate-x-1/2 -top-2.5 bg-white px-3 text-[10px] text-slate-400">✂️ ببڕدرێت لێرەوە / CUT HERE (OFFICE / DEPT COPY)</span>
       </div>
    </template>
  </div>

  <!-- Print Options Modal -->
  <div v-if="showPrintOptions" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[99999] flex items-center justify-center p-4 md:p-8 overflow-y-auto no-print animate-fade-in text-right">
     <div class="bg-white border border-slate-200 w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden relative">
        <!-- Modal Header -->
        <div class="px-8 py-6 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
           <div class="flex items-center gap-3">
              <span class="w-3 h-3 bg-blue-600 rounded-full animate-pulse"></span>
              <h3 class="text-base font-black text-slate-900">شێوازی چاپکردن هەڵبژێرە</h3>
           </div>
           <button @click="showPrintOptions = false" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-900 transition-all shadow-xs">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
           </button>
        </div>

        <!-- Selection Options -->
        <div class="p-8 space-y-4">
           <!-- Option 1: Thermal Printer -->
           <button @click="executePrint('80mm')" class="w-full text-right p-6 rounded-3xl bg-slate-50 border border-slate-200 hover:border-emerald-500/50 hover:bg-emerald-50 transition-all group flex items-center gap-6 shadow-xs">
              <div class="w-14 h-14 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                 <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              </div>
              <div class="flex-1">
                 <h4 class="text-slate-900 font-black text-lg mb-1 group-hover:text-emerald-700 transition-colors">پسوڵەی حەراری بچووک (80mm)</h4>
                 <p class="text-slate-500 text-xs font-semibold">گونجاوە بۆ پرینتەری حەراری بچووک و مۆبایل و تابلێت.</p>
              </div>
           </button>

           <!-- Option 2: Laser Printer -->
           <button @click="executePrint('a4')" class="w-full text-right p-6 rounded-3xl bg-slate-50 border border-slate-200 hover:border-blue-500/50 hover:bg-blue-50 transition-all group flex items-center gap-6 shadow-xs">
              <div class="w-14 h-14 bg-blue-50 text-blue-700 border border-blue-200 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                 <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
              </div>
              <div class="flex-1">
                 <h4 class="text-slate-900 font-black text-lg mb-1 group-hover:text-blue-700 transition-colors">پسوڵەی فەرمی گەورە (A4 / A5)</h4>
                 <p class="text-slate-500 text-xs font-semibold">گونجاوە بۆ پرینتەری گەورەی نوسینگە و لێزەری.</p>
              </div>
           </button>
        </div>
     </div>
  </div>

</div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'
import { useAuthStore } from '../../stores/auth'

const accounts = ref([])
const currencies = ref([])
const transfers = ref([])
const loading = ref(false)
const printingTransfer = ref(null)
const showPrintOptions = ref(false)
const selectedTransferToPrint = ref(null)
const printMode = ref('80mm')

const auth = useAuthStore()
const sourceType = ref('debt')
const destType = ref('debt')

function applySourceType(type) {
  sourceType.value = type
  if (type === 'cash') {
    const vault = accounts.value.find(a => a.type === 'vault' && a.branch_id === auth.user?.branch_id)
    if (vault) {
      form.value.from_account_id = vault.id
      fromAccountSearch.value = vault.name
      showResults.value = null
    } else {
      Swal.fire({icon: 'error', title: 'سندوق نەدۆزرایەوە', text: 'سندوقی ئەم لقە بوونی نییە!', background: '#0f172a', color: '#fff'})
      sourceType.value = 'debt'
    }
  } else {
    form.value.from_account_id = ''
    fromAccountSearch.value = ''
  }
}

function applyDestType(type) {
  destType.value = type
  if (type === 'cash') {
    const vault = accounts.value.find(a => a.type === 'vault' && a.branch_id === auth.user?.branch_id)
    if (vault) {
      form.value.to_account_id = vault.id
      toAccountSearch.value = vault.name
      showResults.value = null
    } else {
      Swal.fire({icon: 'error', title: 'سندوق نەدۆزرایەوە', text: 'سندوقی ئەم لقە بوونی نییە!', background: '#0f172a', color: '#fff'})
      destType.value = 'debt'
    }
  } else {
    form.value.to_account_id = ''
    toAccountSearch.value = ''
  }
}

const fromAccountSearch = ref('')
const toAccountSearch = ref('')
const showResults = ref(null)

const fromAccountInput = ref(null)
const toAccountInput = ref(null)
const amountInput = ref(null)

const filteredAccountsFrom = computed(() => {
  const q = fromAccountSearch.value.toLowerCase()
  if (!q) return accounts.value.slice(0, 10)
  return accounts.value.filter(a => 
    a.name.toLowerCase().includes(q) || 
    a.code.toString().includes(q) ||
    (a.branch && a.branch.name.toLowerCase().includes(q))
  ).slice(0, 8)
})

const filteredAccountsTo = computed(() => {
  const q = toAccountSearch.value.toLowerCase()
  if (!q) return accounts.value.slice(0, 10)
  return accounts.value.filter(a => 
    a.name.toLowerCase().includes(q) || 
    a.code.toString().includes(q) ||
    (a.branch && a.branch.name.toLowerCase().includes(q))
  ).slice(0, 8)
})

function searchAccounts(type) {
  showResults.value = type
}

function selectAccount(acc, type) {
  if (type === 'from') {
    form.value.from_account_id = acc.id
    fromAccountSearch.value = acc.name
    nextTick(() => {
      toAccountInput.value?.focus()
    })
  } else {
    form.value.to_account_id = acc.id
    toAccountSearch.value = acc.name
    nextTick(() => {
      amountInput.value?.focus()
    })
  }
  showResults.value = null
}

async function printReceipt(t) {
  selectedTransferToPrint.value = t
  showPrintOptions.value = true
}

async function executePrint(mode) {
  printMode.value = mode
  printingTransfer.value = selectedTransferToPrint.value
  showPrintOptions.value = false
  
  // Apply body print class
  document.body.classList.add(`print-${mode}`)
  
  await nextTick()
  
  setTimeout(() => {
    window.print()
    printingTransfer.value = null
    document.body.classList.remove(`print-${mode}`)
  }, 350)
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

const amountError = ref(false)

function validateAmount(field) {
  if (formText.value[field] === '') { if (field === 'amount') amountError.value = false; return }
  const val = form.value[field];
  if (field === 'amount') {
    amountError.value = (val <= 0)
  }
}

function formatNumber(val) { return new Intl.NumberFormat().format(val || 0) }
function formatTime(dateStr) { return new Date(dateStr).toLocaleString('en-GB', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric' }) }
function getCurrencyCode(id) { return currencies.value.find(c => c.id === id)?.code || '' }

async function fetchData() {
  try {
    const [accRes, curRes, transRes] = await Promise.all([axios.get('/accounts?per_page=1000&all_branches=true'), axios.get('/currencies'), axios.get('/transfers')])
    accounts.value = accRes.data.data || accRes.data
    currencies.value = curRes.data
    transfers.value = transRes.data.data || transRes.data
    if (currencies.value.length > 0) { form.value.currency_id = currencies.value[0].id; form.value.commission_currency_id = currencies.value[0].id; }
  } catch (e) { console.error(e) }
}

async function submitTransfer() {
  if (form.value.from_account_id === form.value.to_account_id) {
    Swal.fire({ icon: 'error', title: 'هەڵە', text: 'ناتوانیت هەمان حیساب هەڵبژێریت بۆ ناردن و وەرگرتن!', background: '#0f172a', color: '#fff' })
    return
  }
  if (form.value.amount <= 0) {
    Swal.fire({ icon: 'error', title: 'هەڵە', text: 'بڕی پارە دەبێت گەورەتر بێت لە سفر!', background: '#0f172a', color: '#fff' })
    return
  }

  loading.value = true
  try {
    const { data } = await axios.post('/transfers', form.value)
    Swal.fire({ icon: 'success', title: 'سەرکەوتوو بوو', background: '#0f172a', color: '#fff' })
    formText.value = { amount: '', commission_amount: '' }
    form.value = { from_account_id: '', to_account_id: '', currency_id: form.value.currency_id, amount: 0, commission_amount: 0, commission_currency_id: form.value.currency_id, notes: '' }
    fromAccountSearch.value = ''
    toAccountSearch.value = ''
    sourceType.value = 'debt'
    destType.value = 'debt'
    if (data.transfer) transfers.value.unshift(data.transfer)
  } catch (e) {
    let errorHtml = 'تۆمار نەکرا'
    if (e.response?.data?.errors) {
      const errors = Object.values(e.response.data.errors).flat()
      errorHtml = `<ul class="text-right list-disc list-inside space-y-2 mt-2">${errors.map(err => `<li class="text-rose-400 font-bold">${err}</li>`).join('')}</ul>`
    } else if (e.response?.data?.message) {
      errorHtml = `<p class="text-rose-400 font-bold">${e.response.data.message}</p>`
    }

    Swal.fire({ 
      icon: 'error', 
      title: 'شکستهێنان لە تۆمارکردن', 
      html: errorHtml, 
      background: '#0f172a', 
      color: '#fff',
      confirmButtonColor: '#ef4444',
      confirmButtonText: 'تێگەیشتم',
      customClass: {
        popup: 'rounded-[2.5rem] border border-slate-700 shadow-2xl'
      }
    })
  } finally { loading.value = false }
}

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.6s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
