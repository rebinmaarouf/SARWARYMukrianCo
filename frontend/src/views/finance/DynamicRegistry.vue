<template>
  <div class="space-y-6 animate-fade-in text-white font-sans pb-32">
    
    <!-- Enterprise Navigation & Currency Ticker -->
    <div class="flex flex-col lg:flex-row gap-6 bg-slate-900/40 p-4 rounded-[2rem] md:rounded-[2.5rem] border border-white/5 backdrop-blur-3xl no-print shadow-2xl">
      <div class="flex flex-wrap gap-2 flex-1">
        <button v-for="c in currencies" :key="c.id" @click="switchCurrency(c)"
          class="flex items-center gap-3 px-4 md:px-5 py-3 rounded-2xl border transition-all duration-500 group relative overflow-hidden"
          :class="currentFilterId === c.id ? 'border-emerald-500/50 bg-emerald-500/10' : 'border-white/5 bg-slate-950/40 hover:bg-slate-950 hover:border-white/20'">
          <div class="w-8 h-8 rounded-xl bg-slate-900 border border-white/5 flex items-center justify-center font-black text-xs text-slate-400 group-hover:text-emerald-400 transition-colors">{{ c.code?.charAt(0) }}</div>
          <div class="flex flex-col items-start">
             <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest leading-none mb-1">Currency</span>
             <span class="text-xs font-black" :class="currentFilterId === c.id ? 'text-white' : 'text-slate-400'">{{ c.name }}</span>
          </div>
          <div v-if="currentFilterId === c.id" class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-500 shadow-[0_0_10px_#10b981]"></div>
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row items-start xl:items-center justify-between gap-6 bg-slate-950/40 p-6 md:p-10 rounded-[2rem] md:rounded-[3rem] border border-white/5 relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/5 to-transparent pointer-events-none"></div>
      <div class="relative z-10 w-full">
        <div class="flex items-center gap-3 mb-2">
           <span class="w-8 h-1 bg-emerald-500 rounded-full"></span>
           <h2 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em]">Universal General Ledger</h2>
        </div>
        <h1 class="text-2xl md:text-4xl font-black text-white tracking-tighter">{{ pageTitle }}</h1>
        <p class="text-slate-500 text-xs md:text-sm font-medium mt-2">ڕێکخستنی جوڵە داراییەکان و تۆمارکردنی ڕۆژانەی سندوق بە {{ activeCurrencyCode }}</p>
      </div>

      <!-- Compact Date Search -->
      <div class="flex flex-wrap items-center gap-2 bg-slate-900/80 p-2 rounded-2xl border border-white/5 shadow-2xl relative z-10 w-full md:w-auto">
        <div class="flex flex-col px-3 flex-1 md:flex-none">
           <span class="text-[8px] font-black text-slate-600 uppercase">From</span>
           <input v-model="fromDate" type="date" class="bg-transparent text-white border-none text-xs font-bold focus:outline-none p-0 cursor-pointer" />
        </div>
        <div class="w-px h-6 bg-white/10 hidden md:block"></div>
        <div class="flex flex-col px-3 flex-1 md:flex-none">
           <span class="text-[8px] font-black text-slate-600 uppercase">To</span>
           <input v-model="toDate" type="date" class="bg-transparent text-white border-none text-xs font-bold focus:outline-none p-0 cursor-pointer" />
        </div>
        <button @click="fetchEntries" class="w-full md:w-10 h-10 bg-emerald-500 text-slate-950 rounded-xl hover:scale-105 active:scale-95 transition-all flex items-center justify-center shadow-lg shadow-emerald-500/20">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </button>
      </div>
    </div>

    <!-- Data Entry Section -->
    <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[2rem] md:rounded-[3rem] overflow-hidden shadow-2xl relative">
      <!-- Desktop & Tablet Table (Visible on MD and up) -->
      <div class="hidden lg:block overflow-x-auto">
        <table class="w-full text-right border-collapse min-w-[1300px]" dir="rtl">
          <thead>
            <tr class="bg-slate-950/80 text-slate-500 text-[10px] font-black tracking-[0.2em] uppercase border-b border-white/5">
              <th class="px-6 py-5 w-32">بەروار</th>
              <th class="px-6 py-5 w-48 text-rose-400">بڕی دراو</th>
              <th class="px-6 py-5 text-center text-emerald-400">حیسابی قەرزار (Debtor)</th>
              <th class="px-6 py-5 w-24 text-amber-500 text-center">ع.١</th>
              <th class="px-6 py-5 text-center text-blue-400">حیسابی لامانە (Creditor)</th>
              <th class="px-6 py-5 w-24 text-amber-500 text-center">ع.٢</th>
              <th class="px-6 py-5">تێبینی / وردەکاری</th>
              <th class="px-6 py-5 w-24 text-center">کردار</th>
            </tr>
          </thead>
          <tbody>
            <!-- Premium Input Row -->
            <tr class="bg-emerald-500/[0.04] border-b-2 border-emerald-500/20 relative z-50 transition-all group">
              <td class="px-2 py-4">
                <input v-model="newEntry.entry_date" type="date" class="w-full bg-slate-950 border border-white/5 rounded-xl px-3 py-3.5 text-xs text-white focus:border-emerald-500/50 outline-none font-bold shadow-inner" />
              </td>
              <td class="px-2 py-4 relative">
                <div class="relative group">
                  <input v-model="newEntry.amount" type="number" placeholder="0.00" class="w-full bg-slate-950 border-2 border-rose-500/20 text-rose-400 text-2xl font-black rounded-2xl px-5 py-4 focus:border-rose-500/50 outline-none text-center shadow-inner tracking-tight" />
                  <div v-if="newEntry.amount" class="absolute -top-14 left-1/2 -translate-x-1/2 bg-rose-500 text-white px-5 py-2.5 rounded-2xl font-black text-xl shadow-2xl animate-bounce whitespace-nowrap z-[110] border-2 border-white/10">
                    {{ formatNum(newEntry.amount) }} {{ activeCurrencyCode }}
                    <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-4 h-4 bg-rose-500 rotate-45 border-b-2 border-r-2 border-white/10"></div>
                  </div>
                </div>
              </td>
              
              <td class="px-2 py-4 relative">
                <div class="relative">
                  <input v-model="debtorSearch" @input="searchAccounts('debtor')" @focus="showDebtorDropdown = true" @blur="onBlur('debtor')"
                    class="w-full bg-slate-950 border border-emerald-500/20 text-white rounded-2xl py-4 pr-5 pl-14 text-sm font-bold focus:border-emerald-500 outline-none transition-all shadow-inner" 
                    placeholder="بگەڕێ بۆ حیسابی مەدین..." dir="rtl" />
                  <div v-if="newEntry.debtor_account_id" class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded-lg font-black border border-emerald-500/30">
                    {{ selectedDebtorCode }}
                  </div>
                  <div v-if="showDebtorDropdown && debtorResults.length > 0" class="absolute top-full left-0 right-0 mt-3 bg-[#020617] border border-emerald-500/30 rounded-2xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.8)] z-[100] max-h-64 overflow-y-auto ring-1 ring-emerald-500/20 p-2 space-y-1 backdrop-blur-3xl">
                    <button v-for="acc in debtorResults" :key="acc.id" @mousedown.prevent="selectAccount('debtor', acc)"
                      class="w-full text-right px-5 py-4 hover:bg-emerald-500/10 rounded-xl transition-all flex items-center justify-between group/item">
                      <div class="flex flex-col items-start">
                         <span class="text-white font-black group-hover/item:text-emerald-400 text-sm">{{ acc.name }}</span>
                         <span class="text-[9px] text-slate-500 font-bold uppercase">{{ acc.type }}</span>
                      </div>
                      <span class="font-mono text-xs bg-slate-900 text-emerald-500 px-3 py-1.5 rounded-xl font-black border border-white/5">{{ acc.code }}</span>
                    </button>
                  </div>
                </div>
              </td>

              <td class="px-2 py-4">
                <input v-model="newEntry.commission_1" type="number" class="w-full bg-slate-950 border border-white/5 rounded-xl px-2 py-4 text-sm text-amber-400 font-bold text-center outline-none focus:border-amber-500/30 shadow-inner" placeholder="0" />
              </td>

              <td class="px-2 py-4 relative">
                <div class="relative">
                  <input v-model="creditorSearch" @input="searchAccounts('creditor')" @focus="showCreditorDropdown = true" @blur="onBlur('creditor')"
                    class="w-full bg-slate-950 border border-blue-500/20 text-white rounded-2xl py-4 pr-5 pl-14 text-sm font-bold focus:border-blue-500 outline-none transition-all shadow-inner" 
                    placeholder="بگەڕێ بۆ حیسابی داین..." dir="rtl" />
                  <div v-if="newEntry.creditor_account_id" class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] bg-blue-500/20 text-blue-400 px-2 py-1 rounded-lg font-black border border-blue-500/30">
                    {{ selectedCreditorCode }}
                  </div>
                  <div v-if="showCreditorDropdown && creditorResults.length > 0" class="absolute top-full left-0 right-0 mt-3 bg-[#020617] border border-blue-500/30 rounded-2xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.8)] z-[100] max-h-64 overflow-y-auto ring-1 ring-blue-500/20 p-2 space-y-1 backdrop-blur-3xl">
                    <button v-for="acc in creditorResults" :key="acc.id" @mousedown.prevent="selectAccount('creditor', acc)"
                      class="w-full text-right px-5 py-4 hover:bg-blue-500/10 rounded-xl transition-all flex items-center justify-between group/item">
                      <div class="flex flex-col items-start">
                         <span class="text-white font-black group-hover/item:text-blue-400 text-sm">{{ acc.name }}</span>
                         <span class="text-[9px] text-slate-500 font-bold uppercase">{{ acc.type }}</span>
                      </div>
                      <span class="font-mono text-xs bg-slate-900 text-blue-400 px-3 py-1.5 rounded-xl font-black border border-white/5">{{ acc.code }}</span>
                    </button>
                  </div>
                </div>
              </td>

              <td class="px-2 py-4">
                <input v-model="newEntry.commission_2" type="number" class="w-full bg-slate-950 border border-white/5 rounded-xl px-2 py-4 text-sm text-amber-400 font-bold text-center outline-none focus:border-amber-500/30 shadow-inner" placeholder="0" />
              </td>

              <td class="px-2 py-4">
                <input v-model="newEntry.notes" type="text" placeholder="تێبینی مامەڵە..." class="w-full bg-slate-950 border border-white/5 rounded-2xl px-5 py-4 text-sm text-white focus:border-emerald-500/30 outline-none shadow-inner" @keydown.enter="submitNewEntry" />
              </td>

              <td class="px-2 py-4">
                <button @click="submitNewEntry" :disabled="!newEntry.amount" class="w-full py-4 bg-emerald-500 text-slate-950 rounded-2xl hover:bg-emerald-400 hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-emerald-500/20 disabled:opacity-20 flex items-center justify-center">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile & Tablet Form (Visible on screen < 1024px) -->
      <div class="lg:hidden p-6 space-y-6 border-b border-white/5">
         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
               <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بڕی مامەڵە</span>
               <input v-model="newEntry.amount" type="number" placeholder="0.00" class="w-full bg-slate-950 border border-rose-500/20 text-rose-400 text-3xl font-black rounded-2xl p-6 focus:border-rose-500 outline-none shadow-inner text-center" />
            </div>
            <div class="space-y-2">
               <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بەرواری مامەڵە</span>
               <input v-model="newEntry.entry_date" type="date" class="w-full bg-slate-950 border border-white/5 rounded-2xl p-5 text-white font-bold outline-none" />
            </div>
         </div>
         
         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2 relative">
               <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest px-2">حیسابی مەدین (Debtor)</span>
               <input v-model="debtorSearch" @input="searchAccounts('debtor')" @focus="showDebtorDropdown = true" @blur="onBlur('debtor')"
                  class="w-full bg-slate-950 border border-white/5 rounded-2xl p-5 text-white font-bold outline-none focus:border-emerald-500" placeholder="بگەڕێ..." />
               <!-- Mobile Dropdown -->
               <div v-if="showDebtorDropdown && debtorResults.length > 0" class="absolute top-full left-0 right-0 mt-2 bg-slate-900 border border-emerald-500/30 rounded-2xl z-[100] max-h-48 overflow-y-auto p-2">
                  <button v-for="acc in debtorResults" :key="acc.id" @mousedown.prevent="selectAccount('debtor', acc)" class="w-full text-right p-4 hover:bg-emerald-500/10 rounded-xl flex justify-between">
                     <span class="text-white font-bold">{{ acc.name }}</span>
                     <span class="text-[10px] text-emerald-500 font-black">{{ acc.code }}</span>
                  </button>
               </div>
               <input v-model="newEntry.commission_1" type="number" placeholder="عومولەی یەکەم" class="w-full mt-2 bg-slate-950 border border-white/5 rounded-xl p-3 text-sm text-amber-500 font-bold" />
            </div>
            <div class="space-y-2 relative">
               <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest px-2">حیسابی داین (Creditor)</span>
               <input v-model="creditorSearch" @input="searchAccounts('creditor')" @focus="showCreditorDropdown = true" @blur="onBlur('creditor')"
                  class="w-full bg-slate-950 border border-white/5 rounded-2xl p-5 text-white font-bold outline-none focus:border-blue-500" placeholder="بگەڕێ..." />
                <!-- Mobile Dropdown -->
                <div v-if="showCreditorDropdown && creditorResults.length > 0" class="absolute top-full left-0 right-0 mt-2 bg-slate-900 border border-blue-500/30 rounded-2xl z-[100] max-h-48 overflow-y-auto p-2">
                  <button v-for="acc in creditorResults" :key="acc.id" @mousedown.prevent="selectAccount('creditor', acc)" class="w-full text-right p-4 hover:bg-blue-500/10 rounded-xl flex justify-between">
                     <span class="text-white font-bold">{{ acc.name }}</span>
                     <span class="text-[10px] text-blue-500 font-black">{{ acc.code }}</span>
                  </button>
               </div>
               <input v-model="newEntry.commission_2" type="number" placeholder="عومولەی دووەم" class="w-full mt-2 bg-slate-950 border border-white/5 rounded-xl p-3 text-sm text-amber-500 font-bold" />
            </div>
         </div>

         <input v-model="newEntry.notes" type="text" placeholder="تێبینی مامەڵە..." class="w-full bg-slate-950 border border-white/5 rounded-2xl p-5 text-sm text-white outline-none" />
         
         <button @click="submitNewEntry" :disabled="!newEntry.amount" class="w-full py-6 bg-emerald-500 text-slate-950 font-black text-xl rounded-3xl shadow-xl shadow-emerald-500/20 active:scale-95 transition-all">
            تۆمارکردنی مامەڵەی نوێ
         </button>
      </div>

      <!-- Results List (Responsive Layout) -->
      <div class="overflow-hidden">
        <!-- Desktop Table List (Hidden on mobile/tablet) -->
        <table class="hidden lg:table w-full text-right border-collapse" dir="rtl">
          <tbody>
            <tr v-for="entry in entries" :key="entry.id" class="border-b border-white/[0.02] hover:bg-white/[0.03] group transition-all">
              <td class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-tighter w-32">{{ formatDate(entry.entry_date) }}</td>
              <td class="px-6 py-5 text-center w-48">
                <div class="flex flex-col items-center">
                   <span class="text-rose-400 font-black text-xl font-mono leading-none">{{ formatNum(entry.amount) }}</span>
                   <span class="text-[9px] font-black text-slate-600 mt-1 uppercase tracking-widest">{{ entry.currency?.code }}</span>
                </div>
              </td>
              <td class="px-6 py-5">
                <div v-if="entry.debtor_account" class="flex items-center justify-center gap-3">
                   <div class="w-2 h-2 rounded-full bg-emerald-500/40"></div>
                   <span class="text-white font-black text-sm">{{ entry.debtor_account.name }}</span>
                   <span class="text-[10px] font-black bg-slate-950 text-emerald-500 px-2 py-1 rounded-lg border border-white/5">{{ entry.debtor_account.code }}</span>
                </div>
              </td>
              <td class="px-6 py-5 text-center text-amber-500 font-mono font-black text-xs w-24">{{ entry.commission_1 > 0 ? formatNum(entry.commission_1) : '—' }}</td>
              <td class="px-6 py-5">
                <div v-if="entry.creditor_account" class="flex items-center justify-center gap-3">
                   <div class="w-2 h-2 rounded-full bg-blue-500/40"></div>
                   <span class="text-white font-black text-sm">{{ entry.creditor_account.name }}</span>
                   <span class="text-[10px] font-black bg-slate-950 text-blue-400 px-2 py-1 rounded-lg border border-white/5">{{ entry.creditor_account.code }}</span>
                </div>
              </td>
              <td class="px-6 py-5 text-center text-amber-500 font-mono font-black text-xs w-24">{{ entry.commission_2 > 0 ? formatNum(entry.commission_2) : '—' }}</td>
              <td class="px-6 py-5 text-[11px] text-slate-400 font-bold leading-relaxed max-w-md truncate">{{ entry.notes || '—' }}</td>
              <td class="px-6 py-5 w-24 text-center">
                 <button @click="confirmDelete(entry)" class="opacity-0 group-hover:opacity-100 p-3 bg-rose-500/10 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                 </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Mobile/Tablet List (Visible on < 1024px) -->
        <div class="lg:hidden divide-y divide-white/[0.03]" dir="rtl">
           <div v-for="entry in entries" :key="entry.id" class="p-6 space-y-4 hover:bg-white/[0.02] transition-all">
              <div class="flex justify-between items-start">
                 <div class="flex flex-col">
                    <span class="text-2xl font-black text-rose-400 font-mono tracking-tight">{{ formatNum(entry.amount) }} <span class="text-[10px] text-slate-500">{{ entry.currency?.code }}</span></span>
                    <span class="text-[10px] font-black text-slate-600 uppercase">{{ formatDate(entry.entry_date) }}</span>
                 </div>
                 <button @click="confirmDelete(entry)" class="p-3 bg-rose-500/10 text-rose-500 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                 </button>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                 <div class="bg-slate-950/50 p-4 rounded-2xl border border-emerald-500/10">
                    <span class="text-[9px] font-black text-emerald-500 uppercase block mb-1">Debtor (مەدین)</span>
                    <p class="text-sm font-bold text-white">{{ entry.debtor_account?.name }}</p>
                    <p v-if="entry.commission_1 > 0" class="text-xs text-amber-500 font-black mt-1">+{{ formatNum(entry.commission_1) }} عومولە</p>
                 </div>
                 <div class="bg-slate-950/50 p-4 rounded-2xl border border-blue-500/10">
                    <span class="text-[9px] font-black text-blue-500 uppercase block mb-1">Creditor (داین)</span>
                    <p class="text-sm font-bold text-white">{{ entry.creditor_account?.name }}</p>
                    <p v-if="entry.commission_2 > 0" class="text-xs text-amber-500 font-black mt-1">+{{ formatNum(entry.commission_2) }} عومولە</p>
                 </div>
              </div>
              
              <p v-if="entry.notes" class="text-xs text-slate-500 italic bg-slate-950/30 p-3 rounded-xl border border-white/5">{{ entry.notes }}</p>
           </div>
        </div>
      </div>

      <!-- Professional Footer Analytics -->
      <div class="p-6 md:p-10 bg-slate-950/80 border-t border-white/5 flex flex-col xl:flex-row justify-between items-center gap-10">
         <div class="flex flex-col md:flex-row gap-8 md:gap-16 w-full md:w-auto">
            <div class="flex items-center gap-4">
               <div class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 border border-rose-500/20">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
               </div>
               <div class="flex flex-col">
                  <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Total Transaction Value</span>
                  <span class="text-2xl md:text-3xl font-black text-white font-mono tracking-tighter">{{ formatNum(totalAmount) }} <span class="text-xs text-slate-500">{{ activeCurrencyCode }}</span></span>
               </div>
            </div>
            <div class="flex items-center gap-4">
               <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 border border-amber-500/20">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
               </div>
               <div class="flex flex-col">
                  <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Total Fees / Comm</span>
                  <span class="text-2xl md:text-3xl font-black text-amber-500 font-mono tracking-tighter">{{ formatNum(totalCommission) }} <span class="text-xs text-amber-600">{{ activeCurrencyCode }}</span></span>
               </div>
            </div>
         </div>
         <div class="bg-slate-900 border border-white/5 px-8 py-3 rounded-full flex items-center gap-4 w-full md:w-auto justify-center md:justify-start">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span class="text-xs font-black text-slate-400 tracking-widest uppercase">{{ entries.length }} Operations Processed</span>
         </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'

const route = useRoute()
const entries = ref([])
const currencies = ref([])
const fromDate = ref('')
const toDate = ref('')
const loading = ref(false)

const currentFilterId = ref(null)
const currencyId = computed(() => currentFilterId.value || Number(route.params.currencyId) || (currencies.value.length ? currencies.value[0].id : 1))
const activeCurrency = computed(() => currencies.value.find(c => c.id === (newEntry.value.currency_id || currencyId.value)) || {})
const activeCurrencyCode = computed(() => activeCurrency.value.code || 'دراو')
const currencyName = computed(() => route.query.name || 'دراو')
const pageTitle = computed(() => 'ڕۆژنامەی گشتی (Universal Journal)')

function switchCurrency(c) {
  currentFilterId.value = c.id
  newEntry.value.currency_id = c.id
  fetchEntries()
}

async function fetchCurrencies() {
  try {
    const { data } = await axios.get('/currencies')
    currencies.value = data.data || data
    if (!currentFilterId.value && currencies.value.length) {
      currentFilterId.value = Number(route.params.currencyId) || currencies.value[0].id
      newEntry.value.currency_id = currentFilterId.value
    }
  } catch (e) { console.error(e) }
}

// Input States
const debtorSearch = ref('')
const creditorSearch = ref('')
const debtorResults = ref([])
const creditorResults = ref([])
const showDebtorDropdown = ref(false)
const showCreditorDropdown = ref(false)
const selectedDebtorCode = ref('')
const selectedCreditorCode = ref('')

const today = new Date().toISOString().split('T')[0]
const newEntry = ref({
  entry_date: today,
  amount: '',
  debtor_account_id: null,
  creditor_account_id: null,
  commission_1: '',
  commission_2: '',
  sender: '',
  receiver: '',
  notes: '',
})

async function fetchEntries() {
  try {
    const params = { currency_id: currencyId.value, per_page: 100 }
    if (fromDate.value) params.from_date = fromDate.value
    if (toDate.value) params.to_date = toDate.value
    const { data } = await axios.get('/registries', { params })
    entries.value = data.data || data
  } catch (e) { console.error(e) }
}

async function searchAccounts(type) {
  const term = type === 'debtor' ? debtorSearch.value : creditorSearch.value
  if (!term || term.length < 1) return

  try {
    const { data } = await axios.get('/accounts', { params: { search: term, per_page: 8 } })
    if (type === 'debtor') {
      debtorResults.value = data.data || data
      showDebtorDropdown.value = true
    } else {
      creditorResults.value = data.data || data
      showCreditorDropdown.value = true
    }
  } catch (e) { console.error(e) }
}

function selectAccount(type, acc) {
  if (type === 'debtor') {
    newEntry.value.debtor_account_id = acc.id
    debtorSearch.value = acc.name
    selectedDebtorCode.value = acc.code
    showDebtorDropdown.value = false
  } else {
    newEntry.value.creditor_account_id = acc.id
    creditorSearch.value = acc.name
    selectedCreditorCode.value = acc.code
    showCreditorDropdown.value = false
  }
}

function onBlur(type) {
  setTimeout(() => {
    if (type === 'debtor') showDebtorDropdown.value = false
    else showCreditorDropdown.value = false
  }, 250)
}

async function submitNewEntry() {
  if (!newEntry.value.amount || !newEntry.value.debtor_account_id || !newEntry.value.creditor_account_id) {
    Swal.fire({
      icon: 'warning',
      title: 'زانیاری ناتەواو',
      text: 'تکایە بڕی پارە و هەردوو لایەنی قەرزار و لامانە بە دروستی دیاری بکە پێش پاشەکەوتکردن.',
      background: '#020617',
      color: '#fff',
      confirmButtonColor: '#10b981',
      confirmButtonText: 'باشە'
    })
    return
  }

  try {
    const payload = { ...newEntry.value }
    const { data } = await axios.post('/registries', payload)
    entries.value.unshift(data)
    
    // Reset Form
    newEntry.value = { entry_date: today, amount: '', debtor_account_id: null, creditor_account_id: null, commission_1: '', commission_2: '', sender: '', receiver: '', notes: '' }
    debtorSearch.value = ''
    creditorSearch.value = ''
    selectedDebtorCode.value = ''
    selectedCreditorCode.value = ''
    
    // Success Toast
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
      background: '#10b981',
      color: '#fff'
    })
    Toast.fire({ icon: 'success', title: 'تۆمارەکە بە سەرکەوتوویی پاشەکەوت کرا' })
  } catch (e) {
    Swal.fire({
      icon: 'error',
      title: 'هەڵە لە تۆمارکردن',
      text: e.response?.data?.error || 'سێرڤەر وەڵامی نییە',
      background: '#020617',
      color: '#fff'
    })
  }
}

async function confirmDelete(entry) {
  const result = await Swal.fire({
    title: 'دڵنیایت؟',
    text: "ئەم تۆمارە دەسڕێتەوە و کاریگەری لەسەر حیسابات دەبێت",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#1e293b',
    confirmButtonText: 'بەڵێ، بیسرەوە',
    cancelButtonText: 'پەشیمانم',
    background: '#020617',
    color: '#fff'
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/registries/${entry.id}`)
      entries.value = entries.value.filter(e => e.id !== entry.id)
    } catch (e) { console.error(e) }
  }
}

const totalAmount = computed(() => entries.value.reduce((sum, e) => sum + Number(e.amount), 0))
const totalCommission = computed(() => entries.value.reduce((sum, e) => sum + Number(e.commission_1 || 0) + Number(e.commission_2 || 0), 0))

function formatNum(n) { return new Intl.NumberFormat().format(n || 0) }
function formatDate(d) { return new Date(d).toLocaleDateString('ku-IQ', { year: 'numeric', month: 'short', day: 'numeric' }) }

watch(() => route.params.currencyId, fetchEntries)
onMounted(() => {
  fetchCurrencies()
  fetchEntries()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.custom-scrollbar::-webkit-scrollbar { height: 6px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
</style>
