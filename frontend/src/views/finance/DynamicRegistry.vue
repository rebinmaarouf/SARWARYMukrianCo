<template>
  <div class="space-y-6 animate-fade-in text-slate-800 font-sans pb-32">
    
    <!-- Enterprise Navigation & Currency Ticker -->
    <div class="flex flex-col lg:flex-row gap-6 bg-white p-4 rounded-[2rem] md:rounded-[2.5rem] border border-slate-200 no-print shadow-sm">
      <div class="flex flex-wrap gap-2 flex-1">
        <button v-for="c in currencies" :key="c.id" @click="switchCurrency(c)"
          class="flex items-center gap-3 px-4 md:px-5 py-3 rounded-2xl border transition-all duration-500 group relative overflow-hidden shadow-xs"
          :class="currentFilterId === c.id ? 'border-emerald-500/50 bg-emerald-50' : 'border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300'">
          <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center font-black text-xs text-slate-600 group-hover:text-emerald-600 transition-colors">{{ c.code?.charAt(0) }}</div>
          <div class="flex flex-col items-start">
             <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">Currency</span>
             <span class="text-xs font-black" :class="currentFilterId === c.id ? 'text-slate-900' : 'text-slate-600'">{{ c.name }}</span>
          </div>
          <div v-if="currentFilterId === c.id" class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600"></div>
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row items-start xl:items-center justify-between gap-6 bg-white p-6 md:p-10 rounded-[2rem] md:rounded-[3rem] border border-slate-200 shadow-sm relative overflow-hidden no-print">
      <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/5 to-transparent pointer-events-none"></div>
      <div class="relative z-10 w-full">
        <div class="flex items-center gap-3 mb-2">
           <span class="w-8 h-1 bg-emerald-600 rounded-full"></span>
           <h2 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.3em]">Universal General Ledger & Audit</h2>
        </div>
        <h1 class="text-2xl md:text-4xl font-black text-slate-900 tracking-tighter">تۆماری گشتی و مەسروفات</h1>
        <p class="text-slate-600 text-xs md:text-sm font-medium mt-3 max-w-3xl leading-relaxed">
          ئەم بەشە تایبەتە بە تۆمارکردنی گشت ڕووداوە داراییە کارگێڕییەکان (وەک مووچە، کرێ، کڕینی کەلوپەل). لێرە دەتوانیت وردبینی (Audit) بۆ هەموو حیسابەکان بکەیت و چاودێری "مەدین و داین" بکەیت بەپێی بنەماکانی ژمێریاریی موەحەد.
        </p>
      </div>

      <!-- Compact Date Search -->
      <div class="flex flex-wrap items-center gap-2 bg-slate-50 p-2 rounded-2xl border border-slate-200 shadow-xs relative z-10 w-full md:w-auto">
        <div class="flex flex-col px-3 flex-1 md:flex-none">
           <span class="text-[8px] font-black text-slate-500 uppercase">From</span>
           <input v-model="fromDate" type="date" class="bg-transparent text-slate-900 border-none text-xs font-bold focus:outline-none p-0 cursor-pointer" />
        </div>
        <div class="w-px h-6 bg-slate-200 hidden md:block"></div>
        <div class="flex flex-col px-3 flex-1 md:flex-none">
           <span class="text-[8px] font-black text-slate-500 uppercase">To</span>
           <input v-model="toDate" type="date" class="bg-transparent text-slate-900 border-none text-xs font-bold focus:outline-none p-0 cursor-pointer" />
        </div>
        <button @click="fetchEntries" class="w-full md:w-10 h-10 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 active:scale-95 transition-all flex items-center justify-center shadow-md shadow-emerald-600/10">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </button>
      </div>
    </div>

    <!-- Universal Ledger Card Container -->
    <div class="bg-white border border-slate-200 rounded-[2rem] md:rounded-[3rem] overflow-hidden shadow-sm relative no-print">
      <!-- Ledger Content Table -->
      <div ref="headerScrollContainer" @scroll="syncScroll('header')" class="hidden lg:block overflow-x-auto scrollbar-none">
        <table class="w-full text-right border-collapse min-w-[1300px]" dir="rtl">
          <thead>
            <tr class="bg-slate-50 text-slate-500 text-[10px] font-black tracking-[0.2em] uppercase border-b border-slate-200">
              <th class="px-6 py-5 w-20 text-center">ژمارە</th>
              <th class="px-6 py-5 w-32">بەروار</th>
              <th class="px-6 py-5 w-48 text-rose-600">بڕی دراو</th>
              <th class="px-6 py-5 text-center text-emerald-600">حیسابی قەرزار (Debtor)</th>
              <th class="px-6 py-5 w-24 text-amber-600 text-center">ع.١</th>
              <th class="px-6 py-5 text-center text-blue-600">حیسابی لامانە (Creditor)</th>
              <th class="px-6 py-5 w-24 text-amber-600 text-center">ع.٢</th>
              <th class="px-6 py-5">تێبینی / وردەکاری</th>
              <th class="px-6 py-5 w-24 text-center">کردار</th>
            </tr>
          </thead>
          <tbody>
            <!-- Entry Input Row -->
            <tr class="bg-emerald-50/50 border-b-2 border-emerald-200 relative z-50 transition-all group">
               <td class="px-2 py-4 text-center text-slate-400 font-black text-xs">—</td>
               <td class="px-2 py-4">
                 <input v-model="newEntry.entry_date" type="date" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-3.5 text-xs text-slate-900 focus:border-emerald-500/50 outline-none font-bold shadow-xs" />
               </td>
               <td class="px-2 py-4 relative">
                 <div class="relative group">
                   <input ref="amountInput" v-model="newEntry.amount" @keydown.enter="focusDebtor" @blur="validateAmount" type="number" placeholder="0.00" class="w-full bg-white border border-rose-300 text-rose-600 text-2xl font-black rounded-2xl px-5 py-4 focus:border-rose-500 outline-none text-center shadow-xs tracking-tight" />
                   <div v-if="newEntry.amount" class="absolute -top-14 left-1/2 -translate-x-1/2 bg-rose-600 text-white px-5 py-2.5 rounded-2xl font-black text-xl shadow-lg animate-bounce whitespace-nowrap z-[110] border border-rose-500">
                     {{ formatNum(newEntry.amount) }} {{ activeCurrencyCode }}
                     <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-4 h-4 bg-rose-600 rotate-45 border-b border-r border-rose-500"></div>
                   </div>
                 </div>
               </td>
                <td class="px-2 py-4 relative group/debtor">
                  <div class="relative">
                    <input ref="debtorSearchInput" v-model="debtorSearch" @input="searchAccounts('debtor')" @focus="showDebtorDropdown = true" @blur="onBlur('debtor')"
                      class="w-full min-w-[200px] bg-white border border-emerald-300 text-slate-900 rounded-2xl py-4 pr-5 pl-14 text-sm font-bold focus:border-emerald-600 outline-none transition-all shadow-xs" 
                      placeholder="بگەڕێ بۆ حیسابی مەدین..." dir="rtl" />
                    <div v-if="newEntry.debtor_account_id" class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] bg-emerald-100 text-emerald-800 px-2 py-1 rounded-lg font-black border border-emerald-300">
                      {{ selectedDebtorCode }}
                    </div>
                    <!-- Advanced Floating Dropdown -->
                    <div v-if="showDebtorDropdown && debtorResults.length > 0" class="fixed mt-3 bg-white border border-emerald-200 rounded-2xl shadow-2xl z-[9999] max-h-80 overflow-y-auto ring-1 ring-emerald-500/20 p-2 space-y-1 min-w-[350px]">
                      <div class="px-3 py-2 border-b border-slate-100 mb-1">
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Select Debtor Account</span>
                      </div>
                      <button v-for="acc in debtorResults" :key="acc.id" @mousedown.prevent="selectAccount('debtor', acc)"
                        class="w-full text-right px-5 py-4 hover:bg-emerald-50 rounded-xl transition-all flex items-center justify-between group/item">
                        <div class="flex flex-col items-start text-right">
                           <span class="text-slate-900 font-black group-hover/item:text-emerald-700 text-sm">{{ acc.name }}</span>
                           <span class="text-[9px] text-slate-500 font-bold uppercase">{{ acc.type }}</span>
                        </div>
                        <span class="font-mono text-xs bg-slate-100 text-emerald-700 px-3 py-1.5 rounded-xl font-black border border-slate-200">{{ acc.code }}</span>
                      </button>
                    </div>
                  </div>
                </td>
                <td class="px-2 py-4">
                  <input v-model="newEntry.commission_1" type="number" class="w-full bg-white border border-slate-200 rounded-xl px-2 py-4 text-sm text-amber-600 font-bold text-center outline-none focus:border-amber-500 shadow-xs" placeholder="0" />
                </td>
                <td class="px-2 py-4 relative group/creditor">
                  <div class="relative">
                    <input ref="creditorSearchInput" v-model="creditorSearch" @input="searchAccounts('creditor')" @focus="showCreditorDropdown = true" @blur="onBlur('creditor')"
                      class="w-full min-w-[200px] bg-white border border-blue-300 text-slate-900 rounded-2xl py-4 pr-5 pl-14 text-sm font-bold focus:border-blue-600 outline-none transition-all shadow-xs" 
                      placeholder="بگەڕێ بۆ حیسابی داین..." dir="rtl" />
                    <div v-if="newEntry.creditor_account_id" class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] bg-blue-100 text-blue-800 px-2 py-1 rounded-lg font-black border border-blue-300">
                      {{ selectedCreditorCode }}
                    </div>
                    <!-- Advanced Floating Dropdown -->
                    <div v-if="showCreditorDropdown && creditorResults.length > 0" class="fixed mt-3 bg-white border border-blue-200 rounded-2xl shadow-2xl z-[9999] max-h-80 overflow-y-auto ring-1 ring-blue-500/20 p-2 space-y-1 min-w-[350px]">
                      <div class="px-3 py-2 border-b border-slate-100 mb-1">
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Select Creditor Account</span>
                      </div>
                      <button v-for="acc in creditorResults" :key="acc.id" @mousedown.prevent="selectAccount('creditor', acc)"
                        class="w-full text-right px-5 py-4 hover:bg-blue-50 rounded-xl transition-all flex items-center justify-between group/item">
                        <div class="flex flex-col items-start text-right">
                           <span class="text-slate-900 font-black group-hover/item:text-blue-700 text-sm">{{ acc.name }}</span>
                           <span class="text-[9px] text-slate-500 font-bold uppercase">{{ acc.type }}</span>
                        </div>
                        <span class="font-mono text-xs bg-slate-100 text-blue-700 px-3 py-1.5 rounded-xl font-black border border-slate-200">{{ acc.code }}</span>
                      </button>
                    </div>
                  </div>
                </td>
               <td class="px-2 py-4">
                 <input v-model="newEntry.commission_2" type="number" class="w-full bg-white border border-slate-200 rounded-xl px-2 py-4 text-sm text-amber-600 font-bold text-center outline-none focus:border-amber-500 shadow-xs" placeholder="0" />
               </td>
               <td class="px-2 py-4">
                 <input ref="notesInput" v-model="newEntry.notes" type="text" placeholder="تێبینی مامەڵە..." class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-sm text-slate-900 focus:border-emerald-500 outline-none shadow-xs" @keydown.enter="submitNewEntry" />
               </td>
               <td class="px-2 py-4">
                 <button @click="submitNewEntry" :disabled="!newEntry.amount || loading" class="w-full py-4 bg-emerald-600 text-white rounded-2xl hover:bg-emerald-700 active:scale-95 transition-all shadow-md shadow-emerald-600/10 disabled:opacity-30 flex items-center justify-center">
                    <svg v-if="loading" class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                 </button>
               </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile/Tablet Registry List -->
      <div class="lg:hidden p-6 space-y-6 border-b border-slate-200 bg-slate-50/50">
         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
               <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بڕی مامەڵە</span>
               <input v-model="newEntry.amount" type="number" placeholder="0.00" class="w-full bg-white border border-rose-300 text-rose-600 text-3xl font-black rounded-2xl p-6 focus:border-rose-500 outline-none shadow-xs text-center" />
            </div>
            <div class="space-y-2">
               <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بەرواری مامەڵە</span>
               <input v-model="newEntry.entry_date" type="date" class="w-full bg-white border border-slate-200 rounded-2xl p-5 text-slate-900 font-bold outline-none shadow-xs" />
            </div>
         </div>
         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2 relative">
               <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest px-2">حیسابی مەدین (Debtor)</span>
               <input v-model="debtorSearch" @input="searchAccounts('debtor')" @focus="showDebtorDropdown = true" @blur="onBlur('debtor')"
                  class="w-full bg-white border border-slate-200 rounded-2xl p-5 text-slate-900 font-bold outline-none focus:border-emerald-600 shadow-xs" placeholder="بگەڕێ..." />
               <div v-if="showDebtorDropdown && debtorResults.length > 0" class="absolute top-full left-0 right-0 mt-2 bg-white border border-emerald-200 rounded-2xl z-[100] max-h-48 overflow-y-auto p-2 shadow-xl">
                  <button v-for="acc in debtorResults" :key="acc.id" @mousedown.prevent="selectAccount('debtor', acc)" class="w-full text-right p-4 hover:bg-emerald-50 rounded-xl flex justify-between">
                     <span class="text-slate-900 font-bold">{{ acc.name }}</span>
                     <span class="text-[10px] text-emerald-700 font-black">{{ acc.code }}</span>
                  </button>
               </div>
               <input v-model="newEntry.commission_1" type="number" placeholder="عومولەی یەکەم" class="w-full mt-2 bg-white border border-slate-200 rounded-xl p-3 text-sm text-amber-600 font-bold shadow-xs" />
            </div>
            <div class="space-y-2 relative">
               <span class="text-[10px] font-black text-blue-700 uppercase tracking-widest px-2">حیسابی داین (Creditor)</span>
               <input v-model="creditorSearch" @input="searchAccounts('creditor')" @focus="showCreditorDropdown = true" @blur="onBlur('creditor')"
                  class="w-full bg-white border border-slate-200 rounded-2xl p-5 text-slate-900 font-bold outline-none focus:border-blue-600 shadow-xs" placeholder="بگەڕێ..." />
                <div v-if="showCreditorDropdown && creditorResults.length > 0" class="absolute top-full left-0 right-0 mt-2 bg-white border border-blue-200 rounded-2xl z-[100] max-h-48 overflow-y-auto p-2 shadow-xl">
                  <button v-for="acc in creditorResults" :key="acc.id" @mousedown.prevent="selectAccount('creditor', acc)" class="w-full text-right p-4 hover:bg-blue-50 rounded-xl flex justify-between">
                     <span class="text-slate-900 font-bold">{{ acc.name }}</span>
                     <span class="text-[10px] text-blue-700 font-black">{{ acc.code }}</span>
                  </button>
               </div>
               <input v-model="newEntry.commission_2" type="number" placeholder="عومولەی دووەم" class="w-full mt-2 bg-white border border-slate-200 rounded-xl p-3 text-sm text-amber-600 font-bold shadow-xs" />
            </div>
         </div>
         <input v-model="newEntry.notes" type="text" placeholder="تێبینی مامەڵە..." class="w-full bg-white border border-slate-200 rounded-2xl p-5 text-sm text-slate-900 outline-none shadow-xs" />
         <button @click="submitNewEntry" :disabled="!newEntry.amount || loading" class="w-full py-6 bg-emerald-600 text-white font-black text-xl rounded-3xl shadow-lg shadow-emerald-600/10 active:scale-95 transition-all flex items-center justify-center gap-3">
            <template v-if="loading">
              <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              چاوەڕێ بکە...
            </template>
            <template v-else>
              تۆمارکردنی مامەڵەی نوێ
            </template>
         </button>
      </div>

      <!-- Execution Log -->
      <div ref="bodyScrollContainer" @scroll="syncScroll('body')" class="overflow-x-auto scrollbar-thin">
        <table class="hidden lg:table w-full text-right border-collapse min-w-[1300px]" dir="rtl">
          <tbody>
            <tr v-for="entry in entries" :key="entry.id" class="border-b border-slate-100 hover:bg-slate-50 group transition-all font-semibold">
              <td class="px-6 py-5 text-center font-black text-slate-400 font-mono text-sm">#{{ entry.id }}</td>
              <td class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-tighter w-32">{{ formatDate(entry.entry_date) }}</td>
              <td class="px-6 py-5 text-center w-48">
                <div class="flex flex-col items-center">
                   <span class="text-rose-600 font-black text-xl font-mono leading-none">{{ formatNum(entry.amount) }}</span>
                   <span class="text-[9px] font-black text-slate-500 mt-1 uppercase tracking-widest">{{ entry.currency?.code }}</span>
                </div>
              </td>
              <td class="px-6 py-5">
                <div v-if="entry.debtor_account" class="flex items-center justify-center gap-3">
                   <div class="w-2 h-2 rounded-full bg-emerald-600"></div>
                   <span class="text-slate-900 font-black text-sm">{{ entry.debtor_account.name }}</span>
                   <span class="text-[10px] font-black bg-slate-100 text-emerald-700 px-2 py-1 rounded-lg border border-slate-200">{{ entry.debtor_account.code }}</span>
                </div>
              </td>
              <td class="px-6 py-5 text-center text-amber-600 font-mono font-black text-xs w-24">{{ entry.commission_1 > 0 ? formatNum(entry.commission_1) : '—' }}</td>
              <td class="px-6 py-5">
                <div v-if="entry.creditor_account" class="flex items-center justify-center gap-3">
                   <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                   <span class="text-slate-900 font-black text-sm">{{ entry.creditor_account.name }}</span>
                   <span class="text-[10px] font-black bg-slate-100 text-blue-700 px-2 py-1 rounded-lg border border-slate-200">{{ entry.creditor_account.code }}</span>
                </div>
              </td>
              <td class="px-6 py-5 text-center text-amber-600 font-mono font-black text-xs w-24">{{ entry.commission_2 > 0 ? formatNum(entry.commission_2) : '—' }}</td>
              <td class="px-6 py-5 text-[11px] text-slate-600 font-bold leading-relaxed max-w-md truncate">{{ entry.notes || '—' }}</td>
              <td class="px-6 py-5 w-24 text-center">
                 <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                    <button @click="printInvoice(entry)" class="p-3 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all border border-emerald-200 shadow-xs">
                       <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                    <button @click="confirmDelete(entry)" class="p-3 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all border border-rose-200 shadow-xs">
                       <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                 </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Mobile Execution List -->
        <div class="lg:hidden divide-y divide-slate-100" dir="rtl">
           <div v-for="entry in entries" :key="entry.id" class="p-6 space-y-4 hover:bg-slate-50 transition-all font-semibold">
              <div class="flex justify-between items-start">
                 <div class="flex flex-col">
                    <div class="flex items-center gap-2 mb-1">
                       <span class="text-[10px] font-black bg-slate-200 text-slate-600 px-2 py-0.5 rounded-md">#{{ entry.id }}</span>
                       <span class="text-[10px] font-black text-slate-500 uppercase">{{ formatDate(entry.entry_date) }}</span>
                    </div>
                    <span class="text-2xl font-black text-rose-600 font-mono tracking-tight">{{ formatNum(entry.amount) }} <span class="text-[10px] text-slate-500">{{ entry.currency?.code }}</span></span>
                 </div>
                 <div class="flex gap-2">
                    <button @click="printInvoice(entry)" class="p-3 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-xl shadow-xs">
                       <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                 </div>
              </div>
              <div class="grid grid-cols-2 gap-4">
                 <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <span class="text-[9px] font-black text-emerald-700 uppercase block mb-1">Debtor</span>
                    <p class="text-sm font-bold text-slate-900">{{ entry.debtor_account?.name }}</p>
                 </div>
                 <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <span class="text-[9px] font-black text-blue-700 uppercase block mb-1">Creditor</span>
                    <p class="text-sm font-bold text-slate-900">{{ entry.creditor_account?.name }}</p>
                 </div>
              </div>
           </div>
        </div>
      </div>

      <!-- Footer Analytics -->
      <div class="p-6 md:p-10 bg-slate-50 border-t border-slate-200 flex flex-col xl:flex-row justify-between items-center gap-10 no-print">
         <div class="flex flex-col md:flex-row gap-8 md:gap-16 w-full md:w-auto">
            <div class="flex items-center gap-4">
               <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 border border-rose-200 shadow-xs">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
               </div>
               <div class="flex flex-col">
                  <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Total Transaction Value</span>
                  <span class="text-2xl md:text-3xl font-black text-slate-900 font-mono tracking-tighter">{{ formatNum(totalAmount) }} <span class="text-xs text-slate-500">{{ activeCurrencyCode }}</span></span>
               </div>
            </div>
         </div>
      </div>
    </div> <!-- END Universal Ledger Card -->

    <!-- PREMIUM TRANSACTION VOUCHER PRINT TEMPLATE -->
    <!-- 80MM THERMAL PRINT TEMPLATE -->
    <div v-if="printingEntry" id="print-area-registry-thermal" class="print-only-container text-black" dir="rtl">
       <div class="print-voucher">
          <!-- Header -->
          <div class="text-center mb-4 border-b border-dashed border-slate-300 pb-3">
             <div class="flex items-center justify-center gap-2 mb-1">
                <img src="/logo.png" class="w-10 h-10 object-contain grayscale" @error="(e) => e.target.style.display='none'" />
                <h2 class="text-base font-black tracking-tight text-black">کۆمپانیای سەروەری موکریان</h2>
             </div>
             <p class="text-[9px] font-bold text-black opacity-75">تۆماری گشتی / GENERAL LEDGER REGISTRY</p>
             <p class="text-[9px] font-bold text-slate-700 mt-0.5">{{ printingEntry.debtor_account?.branch?.name || '---' }} ➔ {{ printingEntry.creditor_account?.branch?.name || '---' }}</p>
             <p class="text-[8px] text-slate-500 font-mono">Ref ID: #REG-{{ printingEntry.id }}</p>
          </div>

          <!-- Basic Info Grid -->
          <div class="space-y-1 text-[10px] border-b border-dashed border-slate-300 pb-2 mb-3">
             <div class="flex justify-between">
                <span class="font-bold opacity-60">بەروار و کات:</span>
                <span class="font-bold font-mono">{{ formatDate(printingEntry.entry_date) }}</span>
             </div>
             <div class="flex justify-between">
                <span class="font-bold opacity-60">دۆخی کارەکە:</span>
                <span class="font-black text-emerald-700">✓ PROCESSED & VERIFIED</span>
             </div>
          </div>

          <!-- Transaction Detail Table -->
          <div class="border border-black rounded-lg p-2.5 mb-3 text-black">
             <div class="mb-2">
                <span class="text-[8px] font-black text-slate-500 uppercase block font-sans">حیسابی قەرزار / DEBTOR (FROM)</span>
                <p class="text-xs font-black leading-tight text-black">{{ printingEntry.debtor_account?.name }}</p>
                <p class="text-[8px] font-bold text-slate-600">Code: {{ printingEntry.debtor_account?.code }}</p>
             </div>
             <div class="border-t border-slate-200 pt-2">
                <span class="text-[8px] font-black text-slate-500 uppercase block font-sans">حیسابی داین / CREDITOR (TO)</span>
                <p class="text-xs font-black leading-tight text-black">{{ printingEntry.creditor_account?.name }}</p>
                <p class="text-[8px] font-bold text-slate-600">Code: {{ printingEntry.creditor_account?.code }}</p>
             </div>
          </div>

          <!-- Big Amount Block -->
          <div class="bg-slate-100 p-2.5 rounded-lg mb-3 flex justify-between items-center">
             <span class="text-[8px] font-black uppercase text-slate-600 font-sans">TOTAL AMOUNT:</span>
             <div class="text-left">
                <p class="text-base font-black font-mono text-black leading-none">{{ formatNum(printingEntry.amount) }}</p>
                <p class="text-[9px] font-black text-slate-600 uppercase mt-0.5 leading-none">{{ printingEntry.currency?.code }}</p>
             </div>
          </div>

          <!-- Notes Section -->
          <div v-if="printingEntry.notes" class="border border-black p-1.5 rounded mb-2 text-[8px] text-right text-black">
             <span class="font-black block text-slate-500">تێبینی / Notes</span>
             <p class="font-bold text-[10px]">{{ printingEntry.notes }}</p>
          </div>

          <!-- Signatures -->
          <div class="flex justify-between mt-6 px-4 text-black">
             <div class="text-center w-24 border-t border-black pt-1">
                <p class="text-[8px] font-black uppercase">واژۆی ژمێریار</p>
             </div>
             <div class="text-center w-24 border-t border-black pt-1">
                <p class="text-[8px] font-black uppercase">کڕیار / Client</p>
             </div>
          </div>

          <!-- Legal Disclaimer & Contact -->
          <div class="mt-4 border-t border-slate-100 pt-2 flex justify-between items-end text-black">
             <div class="text-[8px] font-bold text-slate-500 leading-tight">
                <p>• پێش دەرچوون دڵنیابەرەوە.</p>
             </div>
             <div class="text-left text-[7px] font-black opacity-30 uppercase tracking-tighter">
                <p>SM-v2-{{ printingEntry.id }} | VERIFIED</p>
             </div>
          </div>
       </div>
    </div>

    <!-- A4/A5 OFFICE PRINT TEMPLATE -->
    <div v-if="printingEntry" id="print-area-registry-a4" class="print-only-container text-black" dir="rtl">
       <template v-for="i in 2" :key="i">
          <div class="print-voucher">
             <!-- Header -->
             <div class="flex justify-between items-center border-b border-black pb-1 mb-2">
                <div class="flex items-center gap-2">
                   <img src="/logo.png" class="h-10 w-10 object-contain grayscale" />
                   <div>
                      <h1 class="text-base font-black text-black">کۆمپانیای سەروەری موکریان</h1>
                      <p class="text-[10px] font-bold text-black uppercase">SARWARY MUKRIAN / GENERAL LEDGER REGISTRY</p>
                      <p class="text-[10px] font-bold text-slate-700 mt-0.5">لقی: {{ printingEntry.debtor_account?.branch?.name || '---' }} ➔ {{ printingEntry.creditor_account?.branch?.name || '---' }}</p>
                   </div>
                </div>
                <div class="text-left" dir="ltr">
                   <h2 class="text-sm font-black text-black leading-none">TRANSACTION VOUCHER</h2>
                   <p class="text-[10px] font-black mt-0.5">REF: #REG-{{ printingEntry.id }}</p>
                </div>
             </div>
   
             <!-- Basic Info Grid -->
             <div class="grid grid-cols-2 gap-2 mb-2 text-[10px] text-black">
                <div class="border border-black p-1.5 rounded">
                   <span class="font-black block text-slate-500 text-[8px]">بەروار / Date</span>
                   <span class="text-xs font-black">{{ formatDate(printingEntry.entry_date) }}</span>
                </div>
                <div class="border border-black p-1.5 rounded text-left" dir="ltr">
                   <span class="font-black block text-slate-500 text-[8px]">Operation Status</span>
                   <span class="text-xs font-black text-emerald-700">✓ PROCESSED & VERIFIED</span>
                </div>
             </div>
   
             <!-- Transaction Detail Table -->
             <div class="border border-black mb-2 text-black">
                <div class="bg-black text-white px-3 py-1 flex justify-between text-[9px] font-black uppercase">
                   <span>حیسابەکان / Accounts Details</span>
                   <span>بڕی پارە / Amount</span>
                </div>
                <div class="flex">
                   <!-- Account Info -->
                   <div class="flex-1 p-2 border-l border-black flex flex-col gap-1 text-right">
                      <div>
                         <span class="text-[8px] font-black text-slate-500 uppercase block font-sans">حیسابی قەرزار / DEBTOR (FROM)</span>
                         <p class="text-xs font-black leading-tight">{{ printingEntry.debtor_account?.name }}</p>
                         <p class="text-[8px] font-bold text-slate-600">Code: {{ printingEntry.debtor_account?.code }}</p>
                      </div>
                      <div class="border-t border-slate-200 pt-1">
                         <span class="text-[8px] font-black text-slate-500 uppercase block font-sans">حیسابی داین / CREDITOR (TO)</span>
                         <p class="text-xs font-black leading-tight">{{ printingEntry.creditor_account?.name }}</p>
                         <p class="text-[8px] font-bold text-slate-600">Code: {{ printingEntry.creditor_account?.code }}</p>
                      </div>
                   </div>
                   <!-- Big Amount -->
                   <div class="w-1/3 p-2 flex flex-col items-center justify-center bg-slate-50">
                      <span class="text-[8px] font-black text-slate-500 uppercase block font-sans">TOTAL AMOUNT</span>
                      <p class="text-lg font-black font-mono tracking-tighter text-black">{{ formatNum(printingEntry.amount) }}</p>
                      <p class="text-xs font-black text-slate-600 uppercase mt-0.5">{{ printingEntry.currency?.code }}</p>
                   </div>
                </div>
             </div>
   
             <!-- Notes Section -->
             <div v-if="printingEntry.notes" class="border border-black p-1.5 rounded mb-2 text-[9px] text-right text-black">
                <span class="font-black block text-slate-500 text-[8px]">تێبینی / Notes</span>
                <p class="font-bold text-xs">{{ printingEntry.notes }}</p>
             </div>
   
             <!-- Signatures -->
             <div class="flex justify-between mt-4 px-4 text-black">
                <div class="text-center w-28 border-t border-black pt-1">
                   <p class="text-[9px] font-black uppercase">ژمێریار / Accountant</p>
                </div>
                <div class="text-center w-28 border-t border-black pt-1">
                   <p class="text-[9px] font-black uppercase">کۆمپانیا / Office Stamp</p>
                </div>
                <div class="text-center w-28 border-t border-black pt-1">
                   <p class="text-[9px] font-black uppercase">کڕیار / Client</p>
                </div>
             </div>
   
             <!-- Legal Disclaimer & Contact -->
             <div class="mt-4 border-t border-slate-100 pt-1 flex justify-between items-end text-black">
                <div class="text-[9px] font-bold text-slate-500 leading-tight">
                   <p>• تکایە پێش دەرچوون لە نوسینگە دڵنیابەرەوە لە بڕی پارەکە.</p>
                   <p>• نوسینگە بەرپرسیار نییە لە هەر هەڵەیەک دوای ڕۆیشتن.</p>
                </div>
                <div class="text-left text-[8px] font-black opacity-35 uppercase tracking-tighter">
                   <p>Sarwary Mukrian Co. | Registry Audit Trail</p>
                   <p>System Hash: SM-v2-{{ printingEntry.id }} | {{ i === 1 ? 'OFFICE COPY' : 'CUSTOMER COPY' }}</p>
                </div>
             </div>
          </div>
          <!-- Cut Line -->
          <div v-if="i === 1" class="w-full border-t border-dashed border-slate-400 relative py-1">
             <span class="absolute left-1/2 -translate-x-1/2 -top-2.5 bg-white px-3 text-[10px] text-slate-400">✂️ ببڕدرێت لێرەوە / CUT HERE (OFFICE / CUSTOMER COPY)</span>
          </div>
       </template>
    </div>

    <!-- PREMIUM INVOICE PREVIEW MODAL -->
    <div v-if="showPreviewModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[99999] flex items-center justify-center p-4 md:p-8 overflow-y-auto no-print animate-fade-in text-right">
       <div class="bg-white border border-slate-200 w-full max-w-3xl rounded-[2.5rem] shadow-2xl overflow-hidden relative">
          <!-- Modal Header -->
          <div class="px-8 py-6 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
             <div class="flex items-center gap-3">
                <span class="w-3 h-3 bg-emerald-600 rounded-full animate-pulse"></span>
                <h3 class="text-base font-black text-slate-900">پێشداڕشتنی پسوڵە (Invoice Preview)</h3>
             </div>
             <button @click="showPreviewModal = false" class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-900 transition-all shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
             </button>
          </div>

          <!-- Preview Content (Scrollable) -->
          <div class="p-8 max-h-[60vh] overflow-y-auto custom-scrollbar bg-slate-50/50">
             <!-- Embedded Print-ready Layout for Preview -->
             <div class="bg-white text-black p-8 rounded-3xl border border-slate-200 font-sans shadow-md relative" dir="rtl">
                <!-- Header -->
                <div class="flex justify-between items-center border-b-2 border-black pb-4 mb-4">
                   <div class="flex items-center gap-3">
                      <img src="/logo.png" class="h-12 w-12 object-contain grayscale" />
                      <div>
                         <h1 class="text-base font-black text-black leading-none">کۆمپانیای سەروەری موکریان</h1>
                         <p class="text-[8px] font-bold text-black uppercase mt-1">SARWARY MUKRIAN / GENERAL LEDGER REGISTRY</p>
                      </div>
                   </div>
                   <div class="text-left" dir="ltr">
                      <h2 class="text-base font-black text-black leading-none">TRANSACTION VOUCHER</h2>
                      <p class="text-[8px] font-black mt-1">REF: #REG-{{ previewingEntry.id }}</p>
                   </div>
                </div>

                <!-- Basic Info Grid -->
                <div class="grid grid-cols-2 gap-3 mb-4 text-[9px]">
                   <div class="border border-black p-2 rounded">
                      <span class="font-black block text-slate-500">بەروار / Date</span>
                      <span class="text-xs font-black">{{ formatDate(previewingEntry.entry_date) }}</span>
                   </div>
                   <div class="border border-black p-2 rounded text-left" dir="ltr">
                      <span class="font-black block text-slate-500">Operation Status</span>
                      <span class="text-xs font-black text-emerald-700">✓ PROCESSED & VERIFIED</span>
                   </div>
                </div>

                <!-- Transaction Detail Table -->
                <div class="border-2 border-black mb-4">
                   <div class="bg-black text-white px-3 py-1.5 flex justify-between text-[9px] font-black uppercase">
                      <span>حیسابەکان / Accounts Details</span>
                      <span>بڕی پارە / Amount</span>
                   </div>
                   <div class="flex">
                      <!-- Account Info -->
                      <div class="flex-1 p-3 border-l-2 border-black flex flex-col gap-3 text-right">
                         <div>
                            <span class="text-[7px] font-black text-slate-400 uppercase block font-sans">حیسابی قەرزار / DEBTOR (FROM)</span>
                            <p class="text-sm font-black leading-tight">{{ previewingEntry.debtor_account?.name }}</p>
                            <p class="text-[8px] font-bold text-slate-600">Code: {{ previewingEntry.debtor_account?.code }}</p>
                         </div>
                         <div class="border-t border-slate-200 pt-2">
                            <span class="text-[7px] font-black text-slate-400 uppercase block font-sans">حیسابی داین / CREDITOR (TO)</span>
                            <p class="text-sm font-black leading-tight">{{ previewingEntry.creditor_account?.name }}</p>
                            <p class="text-[8px] font-bold text-slate-600">Code: {{ previewingEntry.creditor_account?.code }}</p>
                         </div>
                      </div>
                      <!-- Big Amount -->
                      <div class="w-1/3 p-3 flex flex-col items-center justify-center bg-slate-50">
                         <span class="text-[7px] font-black text-slate-400 uppercase block mb-1">TOTAL AMOUNT</span>
                         <p class="text-2xl font-black font-mono tracking-tighter">{{ formatNum(previewingEntry.amount) }}</p>
                         <p class="text-xs font-black text-slate-600 uppercase">{{ previewingEntry.currency?.code }}</p>
                      </div>
                   </div>
                </div>

                <!-- Notes Section -->
                <div v-if="previewingEntry.notes" class="border border-black p-2 rounded mb-3 text-[9px] text-right">
                   <span class="font-black block text-slate-500">تێبینی / Notes</span>
                   <p class="font-bold text-xs">{{ previewingEntry.notes }}</p>
                </div>
             </div>
          </div>

          <!-- Modal Footer (Action Buttons) -->
          <div class="px-8 py-6 bg-slate-50 border-t border-slate-200 flex flex-wrap gap-4 justify-end">
             <button @click="showPreviewModal = false" class="px-6 py-4 bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 hover:text-slate-900 rounded-2xl font-black text-xs uppercase tracking-wider transition-all shadow-xs">
                پەشیمانبوونەوە
             </button>
             <!-- Print 80mm -->
             <button @click="executePrint('80mm')" class="px-6 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-xs flex items-center gap-2 shadow-md shadow-emerald-600/10 active:scale-95 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                چاپی حەراری (80mm)
             </button>
             <!-- Print A4 -->
             <button @click="executePrint('a4')" class="px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs flex items-center gap-2 shadow-md shadow-blue-600/10 active:scale-95 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                چاپی گەورە (A4)
             </button>
          </div>
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'
import { useAuthStore } from '../../stores/auth'
const auth = useAuthStore()

const route = useRoute()
const entries = ref([])
const currencies = ref([])
const fromDate = ref('')
const toDate = ref('')
const loading = ref(false)
const currentFilterId = ref(null)
const printingEntry = ref(null)
const previewingEntry = ref(null)
const showPreviewModal = ref(false)
const printMode = ref('80mm')

// Premium Synchronized Scroll Refs
const headerScrollContainer = ref(null)
const bodyScrollContainer = ref(null)

function syncScroll(source) {
  const header = headerScrollContainer.value
  const body = bodyScrollContainer.value
  if (!header || !body) return

  if (source === 'header') {
    body.scrollLeft = header.scrollLeft
  } else {
    header.scrollLeft = body.scrollLeft
  }
}

async function printInvoice(entry) {
  previewingEntry.value = entry
  showPreviewModal.value = true
}

async function executePrint(mode) {
  printMode.value = mode
  printingEntry.value = previewingEntry.value
  showPreviewModal.value = false
  
  // Apply body print class
  document.body.classList.add(`print-${mode}`)
  
  await nextTick()
  
  setTimeout(() => {
    window.print()
    printingEntry.value = null
    document.body.classList.remove(`print-${mode}`)
  }, 350)
}
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

const debtorSearch = ref('')
const creditorSearch = ref('')
const debtorResults = ref([])
const creditorResults = ref([])
const showDebtorDropdown = ref(false)
const showCreditorDropdown = ref(false)
const selectedDebtorCode = ref('')
const selectedCreditorCode = ref('')

const amountInput = ref(null)
const debtorSearchInput = ref(null)
const creditorSearchInput = ref(null)
const notesInput = ref(null)

function focusDebtor() {
  debtorSearchInput.value?.focus()
}

const today = new Date().toISOString().split('T')[0]
const newEntry = ref({ entry_date: today, currency_id: null, amount: '', debtor_account_id: null, creditor_account_id: null, commission_1: '', commission_2: '', notes: '' })

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
  if (!term) {
    if (type === 'debtor') debtorResults.value = []
    else creditorResults.value = []
    return
  }
  try {
    const { data } = await axios.get('/accounts', { params: { search: term, per_page: 20 } })
    let results = data.data || data
    
    // Protection: Filter out Equity accounts for non-admins/authorized users
    const isAuthorized = auth.isSuperAdmin || auth.user?.roles?.some(r => r === 'Manager' || r.name === 'Manager' || r === 'Admin' || r.name === 'Admin');
    if (!isAuthorized && !auth.permissions.includes('manage_finances')) {
      results = results.filter(acc => acc.type !== 'equity')
    }

    // SMART AUTO-SELECT: If exact code match and only one result
    if (results.length === 1 && results[0].code.toString() === term.toString()) {
       selectAccount(type, results[0]);
       return;
    }
    
    if (type === 'debtor') { debtorResults.value = results; showDebtorDropdown.value = true }
    else { creditorResults.value = results; showCreditorDropdown.value = true }
  } catch (e) { console.error(e) }
}

function selectAccount(type, acc) {
  if (type === 'debtor') { 
    newEntry.value.debtor_account_id = acc.id; 
    debtorSearch.value = acc.name; 
    selectedDebtorCode.value = acc.code; 
    showDebtorDropdown.value = false 
    nextTick(() => {
      creditorSearchInput.value?.focus()
    })
  } else { 
    newEntry.value.creditor_account_id = acc.id; 
    creditorSearch.value = acc.name; 
    selectedCreditorCode.value = acc.code; 
    showCreditorDropdown.value = false 
    nextTick(() => {
      notesInput.value?.focus()
    })
  }
}

function onBlur(type) { setTimeout(() => { if (type === 'debtor') showDebtorDropdown.value = false; else showCreditorDropdown.value = false }, 250) }

const amountError = ref(false)

function validateAmount() {
  if (newEntry.value.amount === '') { amountError.value = false; return }
  const val = parseFloat(newEntry.value.amount)
  amountError.value = (isNaN(val) || val <= 0)
}

async function submitNewEntry() {
  const amount = parseFloat(newEntry.value.amount)
  if (isNaN(amount) || amount <= 0) {
    Swal.fire({ icon: 'error', title: 'هەڵە', text: 'بڕی پارە دەبێت گەورەتر بێت لە سفر!', background: '#090d16', color: '#fff', confirmButtonColor: '#ef4444' })
    return
  }
  if (!newEntry.value.debtor_account_id || !newEntry.value.creditor_account_id) {
    Swal.fire({ icon: 'error', title: 'هەڵە', text: 'تکایە حیسابی مەدین و داین هەڵبژێرە!', background: '#090d16', color: '#fff', confirmButtonColor: '#ef4444' })
    return
  }
  
  loading.value = true
  try {
    const payload = {
      ...newEntry.value,
      amount: amount,
      currency_id: newEntry.value.currency_id || currencyId.value,
      commission_1: newEntry.value.commission_1 === '' ? 0 : newEntry.value.commission_1,
      commission_2: newEntry.value.commission_2 === '' ? 0 : newEntry.value.commission_2
    }
    const { data } = await axios.post('/registries', payload)
    entries.value.unshift(data)
    newEntry.value = { entry_date: today, currency_id: currencyId.value, amount: '', debtor_account_id: null, creditor_account_id: null, commission_1: '', commission_2: '', notes: '' }
    debtorSearch.value = ''; creditorSearch.value = ''; selectedDebtorCode.value = ''; selectedCreditorCode.value = '';
    Swal.fire({ icon: 'success', title: 'Saved', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false, background: '#10b981', color: '#fff' })
  } catch (e) {
    console.error(e)
    if (e.response && e.response.status === 422) {
      const errors = e.response.data.errors || {}
      const errorList = Object.values(errors).flat()
      const errorHtml = `<ul class="text-right list-disc list-inside space-y-2 mt-2">${errorList.map(err => `<li class="text-rose-400 font-bold">${err}</li>`).join('')}</ul>`
      Swal.fire({
        icon: 'error',
        title: 'شکستهێنان لە تۆمارکردن',
        html: errorHtml,
        confirmButtonText: 'تێگەیشتم',
        confirmButtonColor: '#ef4444',
        background: '#090d16',
        color: '#fff',
        customClass: { popup: 'rounded-[2.5rem] border border-slate-200 shadow-2xl' }
      })
    } else {
      Swal.fire({ icon: 'error', title: 'کێشەیەک ڕوویدا', text: 'شکستی هێنا لە تۆمارکردنی مامەڵە.', confirmButtonText: 'داخستن', confirmButtonColor: '#ef4444', background: '#090d16', color: '#fff' })
    }
  }
  finally { loading.value = false }
}

async function confirmDelete(entry) {
  const res = await Swal.fire({ title: 'Are you sure?', icon: 'warning', showCancelButton: true, background: '#020617', color: '#fff' })
  if (res.isConfirmed) {
    try { await axios.delete(`/registries/${entry.id}`); entries.value = entries.value.filter(e => e.id !== entry.id) }
    catch (e) { console.error(e) }
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
  nextTick(() => {
    amountInput.value?.focus()
  })
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.custom-scrollbar::-webkit-scrollbar { height: 6px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
</style>
