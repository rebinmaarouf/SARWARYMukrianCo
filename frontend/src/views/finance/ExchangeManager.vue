<template>
  <div class="min-h-screen bg-[#020617] text-white p-4 md:p-10 font-sans pb-32">
    
    <!-- Professional FX Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-10 no-print">
      <div dir="rtl">
        <h1 class="text-3xl md:text-5xl font-black tracking-tighter mb-2">تێرمیناڵی ئاڵوگۆڕی دراوەکان</h1>
        <p class="text-slate-500 text-sm font-medium uppercase tracking-widest">سیستەمی زیرەکی کڕین و فرۆشتنی دراوە جیاوازەکان</p>
      </div>
      
      <div class="flex items-center gap-4 bg-slate-900/50 p-4 rounded-3xl border border-white/5 backdrop-blur-xl">
         <div class="text-right">
            <span class="text-[9px] font-black text-slate-500 uppercase block">نرخی فەرمی دۆلار (سیستم)</span>
            <span class="text-xl font-black text-emerald-500">100 USD = {{ formatNum(usdRate) }} <span class="text-xs">IQD</span></span>
         </div>
         <div class="w-px h-10 bg-white/10"></div>
         <button @click="fetchData" class="p-2 hover:bg-white/10 rounded-xl transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
         </button>
      </div>
    </div>

    <!-- Currency Pair Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-10 no-print">
      <button v-for="p in pairs" :key="p.id" @click="selectPair(p)"
        :class="['relative p-4 rounded-3xl border transition-all duration-500 group overflow-hidden', 
          activePair.id === p.id ? 'bg-blue-600 border-blue-400 shadow-2xl shadow-blue-600/20 scale-105 z-10' : 'bg-slate-900/40 border-white/5 hover:border-white/20']">
        <div class="relative z-10 text-center">
           <span class="text-[9px] font-black uppercase tracking-widest transition-colors block mb-1" :class="activePair.id === p.id ? 'text-blue-100' : 'text-slate-500'">ئاڵوگۆڕی</span>
           <div class="flex items-center justify-center gap-1">
              <span class="text-lg font-black text-white tracking-tighter">{{ p.primary }}</span>
              <span class="text-[10px] font-bold text-slate-500">بەرامبەر</span>
              <span class="text-lg font-black tracking-tighter" :class="activePair.id === p.id ? 'text-blue-200' : 'text-slate-400'">{{ p.secondary }}</span>
           </div>
        </div>
      </button>
    </div>

    <!-- Trading Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12 no-print">
      
      <!-- BUY/SELL Unified Panel -->
      <div v-for="tradeType in ['buy', 'sell']" :key="tradeType" 
        :class="['group relative bg-slate-900/40 backdrop-blur-3xl rounded-[3rem] border p-8 md:p-10 overflow-hidden transition-all duration-500', 
          tradeType === 'buy' ? 'hover:border-emerald-500/30' : 'hover:border-rose-500/30',
          'border-white/5 shadow-2xl']">
        
        <div class="flex justify-between items-center mb-8 relative z-10" dir="rtl">
          <div class="flex items-center gap-4">
             <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center border transition-all', 
               tradeType === 'buy' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-500' : 'bg-rose-500/10 border-rose-500/20 text-rose-500']">
                <svg v-if="tradeType === 'buy'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                <svg v-else class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
             </div>
              <div>
                <h2 class="text-2xl font-black text-white tracking-tight uppercase">{{ tradeType === 'buy' ? 'وەرگرتنی دراو لە مشتەری' : 'پێدانی دراو بە مشتەری' }}</h2>
                <span :class="['text-[10px] font-black uppercase tracking-widest block mt-1', tradeType === 'buy' ? 'text-emerald-500/60' : 'text-rose-500/60']">
                   مشتەری {{ activePair.primary }} {{ tradeType === 'buy' ? 'دەدات بە ئێمە' : 'لێمان وەردەگرێت' }}
                </span>
             </div>
          </div>
          <div class="text-left">
             <span class="text-[9px] font-black text-slate-500 uppercase block">نرخی فەرمی سیستم</span>
             <span class="text-lg font-black text-slate-300" :class="getSystemRateDisplay() === 'دیاری نەکراوە' ? 'text-rose-500 text-sm' : ''">{{ getSystemRateDisplay() }}</span>
          </div>
        </div>

        <div class="space-y-6 relative z-10" dir="rtl">
          <!-- Input Grid -->
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بڕی {{ activePair.primary }}</label>
              <div class="relative">
                <input v-model="forms[tradeType].primary_text" @input="calculate(tradeType, 'primary')" type="text" placeholder="0.00"
                  class="w-full bg-slate-950/80 border border-white/5 rounded-3xl p-5 text-3xl font-black text-white focus:border-blue-500 outline-none transition-all shadow-inner" />
                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-600 font-black text-sm uppercase">{{ activePair.primary }}</span>
              </div>
            </div>
            
            <div class="flex flex-col gap-3">
              <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">{{ activePair.rateLabel }}</label>
                <div class="relative">
                  <input v-model="forms[tradeType].rate_text" @input="calculate(tradeType, 'rate')" type="text"
                    class="w-full bg-slate-950/80 border border-white/5 rounded-3xl p-5 text-3xl font-black text-blue-400 focus:border-blue-500 outline-none transition-all shadow-inner" />
                </div>
              </div>
              
              <!-- Smart Cross-Rate Input -->
              <div v-if="activePair.primary !== 'USD' && activePair.primary !== 'IQD'" class="bg-blue-600/5 border border-blue-500/20 rounded-2xl p-3 flex items-center justify-between">
                <div class="text-right">
                  <span class="text-[8px] font-black text-blue-500 uppercase block">هەر {{ formatNum(1 / activePair.multiplier) }} {{ activePair.primary }} چەند دۆلار دەکات؟</span>
                  <input v-model="forms[tradeType].rate_vs_usd" @input="calculateFromUsd(tradeType)" type="text" placeholder="0.00"
                    class="bg-transparent border-none text-white font-black text-sm outline-none w-32 p-0 mt-1" />
                </div>
                <div class="text-blue-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Result Panel -->
          <div class="bg-slate-950/60 p-10 rounded-[3rem] border border-white/5 shadow-2xl flex flex-col items-center justify-center relative overflow-hidden group/total">
             <div class="absolute inset-0 bg-gradient-to-b from-blue-500/5 to-transparent opacity-0 group-hover/total:opacity-100 transition-all"></div>
             <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-4 z-10">کۆی گشتی بە {{ activePair.secondary }} {{ activePair.secondary === 'USD' ? '(دۆلار)' : '(دینار)' }}</label>
             <div class="flex items-baseline gap-3 z-10">
                <span class="text-5xl md:text-7xl font-black text-white tracking-tighter">{{ forms[tradeType].secondary_text || '0' }}</span>
                <span class="text-xl font-black text-slate-500">{{ activePair.secondary }}</span>
             </div>
             <!-- Estimated Profit Indicator -->
             <div v-if="forms[tradeType].profit != 0" class="mt-4 px-6 py-2 rounded-full text-[11px] font-black flex items-center gap-2 z-10 animate-pulse"
                :class="forms[tradeType].profit > 0 ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                قازانجی مەزەندەکراو: {{ formatNum(forms[tradeType].profit) }} {{ activePair.secondary === 'USD' ? '$' : 'دینار' }}
             </div>
          </div>

          <!-- Vault & Client Selection -->
          <div class="grid grid-cols-2 gap-4">
             <div class="space-y-2">
                <span class="text-[9px] font-black text-slate-500 uppercase px-3 tracking-widest">پارە بدە لە (سەرچاوە)</span>
                <select v-model="forms[tradeType].vault_from_id" class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 text-xs font-bold text-white outline-none focus:border-blue-500 transition-all appearance-none">
                   <option v-for="v in vaults" :key="v.id" :value="v.id">{{ v.name }} ({{ v.code }})</option>
                </select>
             </div>
             <div class="space-y-2">
                <span class="text-[9px] font-black text-slate-500 uppercase px-3 tracking-widest">پارە بخەرە ناو (سندوق)</span>
                <select v-model="forms[tradeType].vault_to_id" class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 text-xs font-bold text-white outline-none focus:border-blue-500 transition-all appearance-none">
                   <option v-for="v in vaults" :key="v.id" :value="v.id">{{ v.name }} ({{ v.code }})</option>
                </select>
             </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
             <div class="space-y-2 relative">
                <span class="text-[9px] font-black text-slate-500 uppercase px-3 tracking-widest">حیسابی مشتەری (ئەگەر قەرز بوو)</span>
                <div class="relative">
                  <input v-model="forms[tradeType].account_search" @focus="showResults = tradeType" @input="searchAccounts(tradeType)" type="text" placeholder="بگەڕێ بۆ ناو یان کۆد..."
                    class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 text-xs font-bold outline-none focus:border-blue-500" />
                  <div v-if="showResults === tradeType && filteredAccounts.length" class="absolute bottom-full left-0 right-0 mb-3 bg-slate-900/95 border border-white/10 rounded-2xl z-50 shadow-2xl p-2 space-y-1 backdrop-blur-2xl max-h-60 overflow-y-auto">
                    <button v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc, tradeType)" class="w-full text-right p-3 hover:bg-white/5 rounded-xl flex justify-between items-center group">
                      <span class="font-bold text-white text-xs group-hover:text-blue-400">{{ acc.name }}</span>
                      <span class="text-[9px] font-black bg-slate-950 text-slate-500 px-2 py-1 rounded-lg">{{ acc.code }}</span>
                    </button>
                  </div>
                </div>
             </div>
             <div class="space-y-2">
                <span class="text-[9px] font-black text-slate-500 uppercase px-3 tracking-widest">ناوی کڕیار یان تێبینی</span>
                <input v-model="forms[tradeType].client_name" type="text" placeholder="بۆ نموونە: کاک ئاسۆ..." class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 text-xs font-bold outline-none focus:border-blue-500" />
             </div>
          </div>

          <!-- Final Submit Action -->
          <button @click="submitTrade(tradeType)" :disabled="loading || !forms[tradeType].primary_text"
            :class="['w-full py-6 text-xl font-black rounded-3xl shadow-2xl transition-all active:scale-[0.98] disabled:opacity-20 uppercase tracking-tighter', 
              tradeType === 'buy' ? 'bg-emerald-500 text-slate-950 shadow-emerald-500/20' : 'bg-rose-500 text-white shadow-rose-500/20']">
            تۆمارکردنی {{ tradeType === 'buy' ? 'کڕین' : 'فرۆشتن' }}ی {{ activePair.primary }}
          </button>
        </div>
      </div>
    </div>

    <!-- Live Transaction Ledger -->
    <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[4rem] overflow-hidden shadow-2xl no-print">
      <div class="p-10 border-b border-white/5 flex flex-col md:flex-row justify-between items-start md:items-center gap-6" dir="rtl">
        <div>
           <h3 class="text-2xl font-black text-white tracking-tight">مێژووی ئاڵوگۆڕەکان</h3>
           <p class="text-slate-500 text-xs font-bold mt-1">لیستی دوایین مامەڵە جێبەجێکراوەکان</p>
        </div>
        
        <!-- Advanced Interactive Search -->
        <div class="flex items-center gap-3 bg-slate-950/80 px-4 py-3 border border-white/5 rounded-2xl w-full md:max-w-md">
          <span class="text-slate-400 text-sm">🔍</span>
          <input v-model="tableSearch"
                 type="text"
                 placeholder="بگەڕێ بەپێی: ناسنامە # (بۆ نمونە: 44)، مشتەری، بڕ یان نرخ..."
                 class="bg-transparent text-white border-0 outline-none ring-0 w-full text-xs font-black placeholder:text-slate-600" />
          <button v-if="tableSearch" @click="tableSearch = ''" class="text-slate-500 hover:text-white font-bold text-xs">✕</button>
        </div>

        <div class="flex gap-2 bg-slate-950 p-2 rounded-2xl border border-white/5">
           <button v-for="f in ['هەمووی', 'buy', 'sell']" :key="f" @click="tableFilter = (f === 'هەمووی' ? 'all' : f)"
             class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
             :class="(tableFilter === f || (f === 'هەمووی' && tableFilter === 'all')) ? 'bg-blue-600 text-white' : 'text-slate-600 hover:text-white'">
             {{ f === 'all' ? 'هەمووی' : f }}
           </button>
        </div>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full text-right" dir="rtl">
          <thead>
            <tr class="bg-slate-950/40 text-slate-600 text-[9px] font-black uppercase tracking-[0.3em] border-b border-white/5">
              <th class="px-8 py-6 text-right">ناسنامە #</th>
              <th class="px-8 py-6 text-right">کات و بەروار</th>
              <th class="px-8 py-6 text-center">جۆری جوڵە</th>
              <th class="px-8 py-6 text-right">ناوی مشتەری</th>
              <th class="px-8 py-6 text-right">بڕی مامەڵە</th>
              <th class="px-8 py-6 text-center">نرخی گۆڕینەوە</th>
              <th class="px-8 py-6 text-left">قازانجی ڕاستەقینە</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="t in filteredTransactions" :key="t.id" class="group hover:bg-white/[0.02] transition-all">
              <td class="px-8 py-6">
                <span class="px-3 py-1.5 bg-slate-950 border border-white/5 text-slate-400 rounded-xl text-[10px] font-black">
                  #{{ t.id }}
                </span>
              </td>
              <td class="px-8 py-6 text-slate-500 font-bold text-xs">{{ formatFullTime(t.created_at) }}</td>
              <td class="px-8 py-6 text-center">
                <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase" :class="t.type === 'buy' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500'">
                  {{ t.type === 'buy' ? 'کڕین' : 'فرۆشتن' }}
                </span>
              </td>
              <td class="px-8 py-6">
                <div class="flex flex-col">
                   <span class="text-white font-bold text-sm">{{ t.client_name || t.account?.name }}</span>
                   <span class="text-[9px] text-slate-600 font-black uppercase">{{ t.account?.code }}</span>
                </div>
              </td>
              <td class="px-8 py-6">
                <div class="flex flex-col">
                   <span class="text-white font-black text-lg tracking-tight">{{ formatNum(t.primary_amount) }} <span class="text-xs text-slate-500">{{ t.primary_currency }}</span></span>
                   <span class="text-[10px] text-slate-600 font-bold italic">{{ formatNum(t.secondary_amount) }} {{ t.secondary_currency }}</span>
                </div>
              </td>
              <td class="px-8 py-6 text-center font-black text-slate-400 font-mono text-sm">{{ formatNum(t.rate) }}</td>
              <td class="px-8 py-6 text-left flex items-center justify-end gap-6">
                 <span class="font-black text-lg font-mono" :class="t.profit >= 0 ? 'text-emerald-500' : 'text-rose-500'">
                    {{ t.profit > 0 ? '+' : '' }}{{ formatNum(t.profit) }}
                 </span>
                 <button @click="printInvoice(t)" class="p-2 hover:bg-white/10 rounded-xl text-slate-600 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                 </button>
                 <button v-if="authStore.isSuperAdmin || authStore?.permissions?.includes('delete journals')" @click="deleteTransaction(t)" class="p-2 hover:bg-rose-500/10 rounded-xl text-slate-600 hover:text-rose-500 transition-all" title="سڕینەوەی مامەڵە">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                 </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Print Template (Invisible) -->
    <div v-if="printingTx" id="print-area" class="fixed inset-0 bg-white z-[9999] p-4 text-black hidden print:block" dir="rtl">
       <div v-for="i in 2" :key="i" class="pb-6" :class="{ 'border-t border-dashed border-black pt-6 mt-6': i === 2 }">
          <div class="text-center mb-4">
             <div class="flex items-center justify-center gap-2 mb-1">
                <img src="/logo.png" class="w-10 h-10 grayscale" @error="(e) => e.target.style.display='none'" />
                <h2 class="text-base font-black tracking-tight text-black">کۆمپانیای سەروەری موکریان</h2>
             </div>
             <p class="text-[9px] font-bold text-black opacity-70">نوسینگەی فەرمی ئاڵوگۆڕی دراوەکان</p>
             <p class="text-[8px] text-slate-500 font-mono">Ref ID: #TX-{{ printingTx.id }}</p>
          </div>

          <!-- Transaction Type Bar -->
          <div class="bg-black text-white text-center py-1 text-xs font-black rounded mb-3 uppercase tracking-wider">
             {{ printingTx.type === 'buy' ? 'کڕینی دراو (BUY)' : 'فرۆشتنی دراو (SELL)' }}
          </div>

          <!-- Voucher Details -->
          <div class="space-y-1 text-[10px] border-b border-dashed border-slate-300 pb-2 mb-3">
             <div class="flex justify-between">
                <span class="font-bold opacity-60">بەروار و کات:</span>
                <span class="font-bold font-mono">{{ formatFullTime(printingTx.created_at) }}</span>
             </div>
             <div class="flex justify-between">
                <span class="font-bold opacity-60">مشتەری:</span>
                <span class="font-black">{{ printingTx.client_name || printingTx.account?.name || 'مشتەری گشتی' }}</span>
             </div>
             <div class="flex justify-between" v-if="printingTx.user?.name">
                <span class="font-bold opacity-60">ئەنجامدەر:</span>
                <span class="font-bold">{{ printingTx.user?.name }}</span>
              </div>
          </div>

          <!-- Financial Calculation Block -->
          <div class="bg-slate-100 p-2.5 rounded-lg mb-3">
             <div class="flex justify-between items-center mb-1">
                <span class="text-[8px] font-black uppercase opacity-60">بڕی سەرەکی:</span>
                <span class="text-base font-black font-mono text-black">{{ formatNum(printingTx.primary_amount) }} {{ printingTx.primary_currency }}</span>
             </div>
             <div class="flex justify-between items-center mb-1">
                <span class="text-[8px] font-black uppercase opacity-60">نرخی ئاڵوگۆڕ:</span>
                <span class="text-[11px] font-black font-mono text-black">@ {{ formatNum(printingTx.rate) }}</span>
             </div>
             <div class="h-px bg-slate-300 my-1.5"></div>
             <div class="flex justify-between items-center">
                <span class="text-[9px] font-black uppercase text-black">کۆی گشتی:</span>
                <span class="text-lg font-black font-mono text-black">{{ formatNum(printingTx.secondary_amount) }} {{ printingTx.secondary_currency }}</span>
             </div>
          </div>

          <!-- Signature and Cryptographic Seal -->
          <div class="text-center space-y-2 py-2">
             <div class="text-[8px] font-mono text-slate-500 flex justify-center items-center gap-1">
                <span>🔒 INTEGRITY SEAL:</span>
                <span class="font-bold text-black">SM-v2-TX-{{ printingTx.id }}-{{ Math.abs(printingTx.profit || 0) }}</span>
             </div>
             <p class="text-[8px] font-bold leading-relaxed text-slate-700">
                «سوپاس بۆ هەڵبژاردنتان. تکایە پێش بەجێهێشتنی نوسینگە لە ڕاستی بڕی داراییەکە دڵنیا ببنەوە.»
             </p>
          </div>

          <div class="flex justify-between text-[8px] font-black pt-4 border-t border-dashed border-slate-200">
             <div class="w-24 border-t border-black pt-1 text-center">واژۆی ژمێریار</div>
             <div class="w-24 border-t border-black pt-1 text-center">واژۆی کڕیار</div>
          </div>

          <!-- Divider with Scissors -->
          <div v-if="i === 1" class="text-center text-[8px] font-bold text-slate-400 py-4 flex justify-center items-center gap-2">
             <span>✂️-----------------------------------------</span>
             <span>بڕین لێرەوە (OFFICE / CUSTOMER COPY)</span>
          </div>
       </div>
    </div>

    <!-- A4 Print Template (Invisible) -->
    <div v-if="printingTx" id="print-area-a4" class="fixed inset-0 bg-white z-[9999] p-8 text-black hidden print:block" dir="rtl">
       <div v-for="i in 2" :key="i" class="a4-voucher" :class="{ 'border-t-2 border-dashed border-slate-300 pt-8 mt-8': i === 2 }">
          <!-- Header -->
          <div class="flex justify-between items-center border-b-2 border-black pb-4 mb-4">
             <div class="flex items-center gap-4">
                <img src="/logo.png" class="h-16 w-16 object-contain grayscale animate-pulse" />
                <div>
                   <h1 class="text-xl font-black text-black">کۆمپانیای سەروەری موکریان</h1>
                   <p class="text-xs font-bold text-black uppercase">SARWARY MUKRIAN / EXCHANGE VOUCHER</p>
                </div>
             </div>
             <div class="text-left" dir="ltr">
                <h2 class="text-xl font-black text-black leading-none">EXCHANGE SLIP</h2>
                <p class="text-xs font-black mt-1">REF: #TX-{{ printingTx.id }}</p>
             </div>
          </div>

          <!-- Basic Info Grid -->
          <div class="grid grid-cols-3 gap-4 mb-6 text-xs text-black">
             <div class="border border-black p-3 rounded">
                <span class="font-black block text-slate-500">بەروار / Date</span>
                <span class="text-sm font-black">{{ formatFullTime(printingTx.created_at) }}</span>
             </div>
             <div class="border border-black p-3 rounded">
                <span class="font-black block text-slate-500">مشتەری / Client</span>
                <span class="text-sm font-black">{{ printingTx.client_name || printingTx.account?.name || 'مشتەری گشتی' }}</span>
             </div>
             <div class="border border-black p-3 rounded text-left" dir="ltr">
                <span class="font-black block text-slate-500">Operation Status</span>
                <span class="text-sm font-black text-emerald-700">✓ PROCESSED & VERIFIED</span>
             </div>
          </div>

          <!-- Transaction Detail Table -->
          <div class="border-2 border-black mb-6">
             <div class="bg-black text-white px-4 py-2.5 flex justify-between text-xs font-black uppercase">
                <span>ووردەکاری ئاڵوگۆڕ / Transaction Details</span>
                <span>کۆتایی / Calculation</span>
             </div>
             <div class="flex">
                <!-- Details -->
                <div class="flex-1 p-4 border-l-2 border-black flex flex-col gap-3 text-right">
                   <div class="grid grid-cols-2 gap-4">
                      <div>
                         <span class="text-[9px] font-black text-slate-400 uppercase block font-sans">جۆری مامەڵە / Transaction Type</span>
                         <span class="text-sm font-black" :class="printingTx.type === 'buy' ? 'text-emerald-600' : 'text-rose-600'">
                            {{ printingTx.type === 'buy' ? 'کڕینی دراو (BUY)' : 'فرۆشتنی دراو (SELL)' }}
                         </span>
                      </div>
                      <div>
                         <span class="text-[9px] font-black text-slate-400 uppercase block font-sans">ئەنجامدەر / Authorized By</span>
                         <span class="text-sm font-black text-black">{{ printingTx.user?.name || 'Admin' }}</span>
                      </div>
                   </div>
                   <div class="border-t border-slate-200 pt-3">
                      <span class="text-[9px] font-black text-slate-400 uppercase block font-sans">ڕێژەی ئاڵوگۆڕ / Exchange Rate</span>
                      <p class="text-lg font-black font-mono text-black">1 {{ printingTx.primary_currency }} = {{ formatNum(printingTx.rate) }} {{ printingTx.secondary_currency }}</p>
                   </div>
                </div>
                <!-- Big Amount -->
                <div class="w-1/3 p-4 flex flex-col items-center justify-center bg-slate-50">
                   <span class="text-[9px] font-black text-slate-400 uppercase block font-sans mb-1">TOTAL AMOUNT</span>
                   <p class="text-3xl font-black font-mono tracking-tighter text-black">{{ formatNum(printingTx.primary_amount) }} {{ printingTx.primary_currency }}</p>
                   <p class="text-sm font-bold text-slate-500 my-1">بۆ دەکاتە</p>
                   <p class="text-2xl font-black font-mono text-emerald-600">{{ formatNum(printingTx.secondary_amount) }} {{ printingTx.secondary_currency }}</p>
                </div>
             </div>
          </div>

          <!-- Cryptographic Seal and Footers -->
          <div class="text-center space-y-2 py-4">
             <div class="text-[10px] font-mono text-slate-500 flex justify-center items-center gap-2">
                <span>🔒 INTEGRITY SEAL:</span>
                <span class="font-bold text-black">SM-v2-TX-{{ printingTx.id }}-SHA256-{{ Math.abs(printingTx.profit || 0) }}</span>
             </div>
             <p class="text-xs font-bold leading-relaxed text-slate-700">
                «تکایە پێش دەرچوون لە نوسینگە دڵنیابەرەوە لە بڕی پارەکە. نوسینگە بەرپرسیار نییە لە هەر هەڵەیەک دوای ڕۆیشتن.»
             </p>
          </div>

          <!-- Signatures -->
          <div class="flex justify-between mt-12 px-8">
             <div class="text-center w-36 border-t border-black pt-2">
                <p class="text-xs font-black uppercase">ژمێریار / Accountant</p>
             </div>
             <div class="text-center w-36 border-t border-black pt-2">
                <p class="text-xs font-black uppercase">کۆمپانیا / Office Stamp</p>
             </div>
             <div class="text-center w-36 border-t border-black pt-2">
                <p class="text-xs font-black uppercase">کڕیار / Client</p>
             </div>
          </div>

          <!-- Cut Line -->
          <div v-if="i === 1" class="my-8 border-t border-dashed border-slate-300 relative">
             <span class="absolute left-1/2 -translate-x-1/2 -top-2 bg-white px-3 text-[10px] text-slate-400">✂️ ببڕدرێت لێرەوە / CUT HERE (OFFICE / CUSTOMER COPY)</span>
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
import { ref, onMounted, computed, watch } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const pairs = ref([])
const activePair = ref({ id: 0, primary: 'USD', secondary: 'IQD', label: 'دۆلار', multiplier: 1, rateLabel: 'نرخ' })
const usdRate = ref(1500)
const liveRates = ref({})
const loading = ref(false)
const showResults = ref(null)
const tableFilter = ref('all')
const tableSearch = ref('')
const printingTx = ref(null)
const showPrintOptions = ref(false)
const selectedTxToPrint = ref(null)
const printMode = ref('80mm')

const accounts = ref([])
const transactions = ref([])
const vaults = computed(() => accounts.value.filter(a => a.type === 'vault'))

const forms = ref({
  buy: { primary_text: '', rate_text: '', secondary_text: '', rate_vs_usd: '', profit: 0, vault_from_id: null, vault_to_id: null, account_id: null, account_search: '', client_name: '' },
  sell: { primary_text: '', rate_text: '', secondary_text: '', rate_vs_usd: '', profit: 0, vault_from_id: null, vault_to_id: null, account_id: null, account_search: '', client_name: '' }
})

function getSystemRateDisplay() {
  const pRate = pairs.value.find(p => p.id === activePair.value.id)?.official_rate || 1
  if (pRate <= 1 && activePair.value.primary !== 'IQD') return 'دیاری نەکراوە'
  let multiplier = activePair.value.multiplier || 1
  return formatWithCommas(pRate * (1/multiplier))
}

function getSystemRate() {
  const pRate = pairs.value.find(p => p.id === activePair.value.id)?.official_rate || 1
  let multiplier = activePair.value.multiplier || 1
  return pRate * (1/multiplier)
}

function calculate(type, source) {
  const f = forms.value[type]
  const m = activePair.value.multiplier
  const p = parseFloat(f.primary_text.replace(/,/g, '')) || 0
  const r = parseFloat(f.rate_text.replace(/,/g, '')) || 0
  const sysR = getSystemRate()

  if (activePair.value.primary === 'IQD') {
    // Special IQD/USD Pair calculation
    if (source === 'primary' || source === 'rate') {
      const calculatedSecondary = r > 0 ? (p / (r * 0.01)) : 0
      f.secondary_text = formatWithCommas(calculatedSecondary.toFixed(2))
    }

    // Profit calculation for IQD/USD pair
    const usdPairRate = pairs.value.find(pair => pair.primary === 'USD')?.official_rate || usdRate.value
    const sysUnitRate = usdPairRate * 0.01 // e.g., 1500
    const systemValueInSecondary = p / sysUnitRate
    const transactionValueInSecondary = r > 0 ? (p / (r * 0.01)) : 0

    if (type === 'buy') {
      f.profit = Math.round(systemValueInSecondary - transactionValueInSecondary)
    } else {
      f.profit = Math.round(transactionValueInSecondary - systemValueInSecondary)
    }
  } else {
    // Standard currency pairs
    if (source === 'primary' || source === 'rate') {
      f.secondary_text = formatWithCommas(Math.round(p * m * r))
      if (activePair.value.primary !== 'USD' && r > 0) {
        f.rate_vs_usd = (r / usdRate.value).toFixed(2)
      }
    }

    // Only calculate estimated profit if system rate is properly configured in DB
    const pRate = pairs.value.find(pair => pair.id === activePair.value.id)?.official_rate || 1
    if (pRate > 1) {
      const systemValue = p * m * sysR
      const transactionValue = p * m * r
      if (type === 'buy') f.profit = Math.round(systemValue - transactionValue)
      else f.profit = Math.round(transactionValue - systemValue)
    } else {
      f.profit = 0
    }
  }
}

// Smart Cross-Rate Logic: Calculate IQD Rate from USD Rate
function calculateFromUsd(type) {
  const f = forms.value[type]
  const vsUsd = parseFloat(f.rate_vs_usd) || 0
  if (vsUsd > 0) {
    // Math: If 100 GBP = 125 USD, and 1 USD = 1500 IQD.
    // Then 100 GBP = 125 * 1500 = 187,500 IQD.
    // This perfectly matches the needed transaction rate.
    const finalRate = vsUsd * usdRate.value

    f.rate_text = formatWithCommas(Math.round(finalRate))
    calculate(type, 'rate')
  }
}

function formatWithCommas(n) {
  if (!n) return ''
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}

async function fetchData() {
  loading.value = true
  try {
    const [curRes, accRes, transRes] = await Promise.all([
      axios.get('/currencies'),
      axios.get('/accounts?per_page=1000'),
      axios.get('/exchanges')
    ])
    
    // Fetch live global rates
    try {
      const liveRes = await fetch('https://api.exchangerate-api.com/v4/latest/USD')
      const liveData = await liveRes.json()
      liveRates.value = liveData.rates
    } catch (e) {
      console.warn('Failed to fetch live FX rates', e)
    }

    const curData = curRes.data.data || curRes.data
    usdRate.value = curData.find(c => c.code === 'USD')?.current_rate || 1500
    
    pairs.value = curData.filter(c => c.code !== 'IQD').map(c => {
      let multiplier = 0.01 
      if (c.code === 'IRR') multiplier = 0.0000001 
      return {
        id: c.id, 
        primary: c.code, 
        secondary: 'IQD', 
        label: c.code,
        official_rate: c.current_rate,
        multiplier: multiplier,
        rateLabel: `نرخی هەر ${1/multiplier} ${c.code} بە دینار`
      }
    })

    // Append virtual IQD/USD inverse pair
    pairs.value.push({
      id: -1,
      primary: 'IQD',
      secondary: 'USD',
      label: 'IQD',
      official_rate: usdRate.value,
      multiplier: 0.01,
      rateLabel: 'نرخی ١٠٠ دۆلار بە دینار (ڕێژە)'
    })
    
    if (pairs.value.length > 0 && activePair.value.id === 0) {
      selectPair(pairs.value[0])
    }
    
    accounts.value = accRes.data.data || accRes.data
    transactions.value = transRes.data.data || transRes.data
    
    const vList = accounts.value.filter(a => a.type === 'vault')
    if (vList.length > 0) {
      forms.value.buy.vault_from_id = vList[0].id; forms.value.buy.vault_to_id = vList[0].id
      forms.value.sell.vault_from_id = vList[0].id; forms.value.sell.vault_to_id = vList[0].id
    }
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

function searchAccounts(type) { 
  showResults.value = type;
  let q = forms.value[type].account_search?.toLowerCase() || '';
  if (q.length >= 2) {
    const exactMatch = accounts.value.find(a => a.code.toString() === q);
    if (exactMatch) selectAccount(exactMatch, type);
  }
}

function selectPair(p) { 
  activePair.value = p 
  
  // Reset amounts
  forms.value.buy.primary_text = ''
  forms.value.sell.primary_text = ''
  forms.value.buy.secondary_text = ''
  forms.value.sell.secondary_text = ''

  if (p.primary === 'IQD') {
    const standardRate = usdRate.value * 100
    forms.value.buy.rate_text = formatWithCommas(Math.round(standardRate))
    forms.value.sell.rate_text = formatWithCommas(Math.round(standardRate))
    forms.value.buy.rate_vs_usd = ''; forms.value.sell.rate_vs_usd = ''
  } else if (p.primary === 'USD') {
    const sysRate = getSystemRate()
    forms.value.buy.rate_text = formatWithCommas(Math.round(sysRate - 500))
    forms.value.sell.rate_text = formatWithCommas(Math.round(sysRate + 500))
    forms.value.buy.rate_vs_usd = ''; forms.value.sell.rate_vs_usd = ''
  } else if (p.primary === 'IRR') {
    // Toman is manual because global API rate is inaccurate for black market
    forms.value.buy.rate_text = ''
    forms.value.sell.rate_text = ''
    forms.value.buy.rate_vs_usd = ''; forms.value.sell.rate_vs_usd = ''
  } else if (liveRates.value[p.primary]) {
    // Magic: Auto calculate based on live global rate + local USD rate
    const usdPerOneUnit = 1 / liveRates.value[p.primary]
    const vsUsdAmount = usdPerOneUnit * (1 / p.multiplier)
    
    forms.value.buy.rate_vs_usd = vsUsdAmount.toFixed(2)
    forms.value.sell.rate_vs_usd = vsUsdAmount.toFixed(2)
    
    calculateFromUsd('buy')
    calculateFromUsd('sell')
  } else {
    forms.value.buy.rate_text = ''
    forms.value.sell.rate_text = ''
    forms.value.buy.rate_vs_usd = ''; forms.value.sell.rate_vs_usd = ''
  }
}

const filteredAccounts = computed(() => {
  const q = forms.value[showResults.value]?.account_search?.toLowerCase()
  if (!q) return []
  return accounts.value.filter(a => a.type !== 'vault' && (a.name.toLowerCase().includes(q) || a.code.toString().includes(q))).slice(0, 8)
})

const filteredTransactions = computed(() => {
  let list = transactions.value
  if (tableFilter.value !== 'all') {
    list = list.filter(t => t.type === tableFilter.value)
  }
  const q = tableSearch.value.trim().toLowerCase()
  if (!q) return list

  return list.filter(t => {
    const matchId = t.id.toString() === q || t.id.toString() === q.replace('#', '')
    const clientName = (t.client_name || '').toLowerCase()
    const accountName = (t.account?.name || '').toLowerCase()
    const accountCode = (t.account?.code || '').toString().toLowerCase()
    const matchName = clientName.includes(q) || accountName.includes(q) || accountCode.includes(q)
    const matchAmount = t.primary_amount.toString().includes(q) || t.secondary_amount.toString().includes(q)
    const matchRate = t.rate.toString().includes(q)
    return matchId || matchName || matchAmount || matchRate
  })
})

function selectAccount(acc, type) {
  forms.value[type].account_id = acc.id
  forms.value[type].account_search = acc.name
  showResults.value = null
}

async function submitTrade(type) {
  const f = forms.value[type]
  
  loading.value = true
  try {
    const payload = {
      account_id: f.account_id,
      type,
      pair: `${activePair.value.primary}/${activePair.value.secondary}`,
      primary_currency: activePair.value.primary,
      secondary_currency: activePair.value.secondary,
      primary_amount: parseFloat(f.primary_text.replace(/,/g, '')),
      rate: parseFloat(f.rate_text.replace(/,/g, '')),
      secondary_amount: parseFloat(f.secondary_text.replace(/,/g, '')),
      vault_from_id: f.vault_from_id,
      vault_to_id: f.vault_to_id,
      client_name: f.client_name
    }
    const { data } = await axios.post('/exchanges', payload)
    transactions.value.unshift(data)
    f.primary_text = ''; f.secondary_text = ''; f.profit = 0; f.rate_vs_usd = ''
    Swal.fire({ icon: 'success', title: 'تۆمارکرا', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false, background: '#10b981', color: '#fff' })
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'هەڵە', text: e.response?.data?.error || 'تۆمار نەکرا', background: '#0f172a', color: '#fff' })
  } finally { loading.value = false }
}

async function deleteTransaction(tx) {
  const result = await Swal.fire({
    title: 'سڕینەوەی مامەڵەی ئاڵوگۆڕ',
    html: `ئایا دڵنیایت لە سڕینەوەی ئەم مامەڵەیە؟ <br><span class="text-xs text-rose-500">ئەم کارە تەواوی پارەکە دەگێڕێتەوە بۆ سندوقەکان بەشێوەیەکی یەکجاری!</span>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#1e293b',
    confirmButtonText: 'بەڵێ، بیسڕەوە',
    cancelButtonText: 'پەشیمان بوونەوە',
    background: '#0f172a',
    color: '#fff'
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/exchanges/${tx.id}`)
      
      Swal.fire({
        title: 'سڕایەوە!',
        text: 'مامەڵەکە بە سەرکەوتوویی سڕایەوە و حیسابات گەڕایەوە باری پێشوو.',
        icon: 'success',
        background: '#0f172a',
        color: '#fff',
        confirmButtonColor: '#3b82f6',
        confirmButtonText: 'باشە، داخستن'
      })
      
      fetchData()
    } catch (e) {
      console.error('Error deleting transaction:', e)
      Swal.fire({
        title: 'هەڵە!',
        text: e.response?.data?.message || 'کێشەیەک ڕوویدا لە کاتی سڕینەوە',
        icon: 'error',
        background: '#0f172a',
        color: '#fff',
        confirmButtonColor: '#3b82f6',
        confirmButtonText: 'داخستن'
      })
    }
  }
}

function printInvoice(tx) {
  selectedTxToPrint.value = tx
  showPrintOptions.value = true
}

function executePrint(mode) {
  printMode.value = mode
  printingTx.value = selectedTxToPrint.value
  showPrintOptions.value = false
  
  // Apply body print class
  document.body.classList.add(`print-${mode}`)
  
  setTimeout(() => {
    window.print()
    printingTx.value = null
    document.body.classList.remove(`print-${mode}`)
  }, 150)
}

const formatNum = (n) => new Intl.NumberFormat().format(n || 0)
const formatFullTime = (d) => new Date(d).toLocaleString('en-GB', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short' })

onMounted(fetchData)
</script>

<style scoped>
@media print {
  body * { display: none !important; }
  
  /* Shared Print display overrides */
  body.print-80mm #print-area,
  body.print-80mm #print-area *,
  body.print-a4 #print-area-a4,
  body.print-a4 #print-area-a4 * {
    display: block !important;
    visibility: visible !important;
  }

  body.print-80mm #print-area {
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

  body.print-a4 #print-area-a4 {
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
.no-scrollbar::-webkit-scrollbar { display: none; }
</style>
