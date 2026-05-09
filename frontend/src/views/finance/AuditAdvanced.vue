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

    <!-- Tab Navigation (No Print) -->
    <div class="flex flex-wrap gap-2 p-2 bg-slate-950 border border-white/5 rounded-3xl mb-10 no-print max-w-fit">
      <button @click="activeTab = 'audit'"
              class="px-6 py-3 rounded-2xl font-black text-xs transition-all flex items-center gap-2"
              :class="activeTab === 'audit' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20' : 'text-slate-400 hover:text-white'">
        📊 ووردبینی دارایی و قەڵغان
      </button>
      <button @click="activeTab = 'predictions'"
              class="px-6 py-3 rounded-2xl font-black text-xs transition-all flex items-center gap-2"
              :class="activeTab === 'predictions' ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/20' : 'text-slate-400 hover:text-white'">
        🧠 پێشبینیکردنی سیولە (Predictive)
      </button>
      <button @click="activeTab = 'anomalies'"
              class="px-6 py-3 rounded-2xl font-black text-xs transition-all flex items-center gap-2"
              :class="activeTab === 'anomalies' ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:text-white'">
        🔍 شیکاری گوماناوییەکان
        <span v-if="anomalies.length > 0" class="px-2 py-0.5 bg-rose-500 text-white rounded-full text-[9px] animate-pulse">
          {{ anomalies.length }}
        </span>
      </button>
    </div>

    <template v-if="canVerifyIntegrity && activeTab === 'audit'">
      <!-- Cryptographic Database Integrity Shield (No Print) -->
      <div class="bg-slate-900/40 border border-white/5 p-8 rounded-[3rem] backdrop-blur-3xl mb-10 no-print flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-4">
          <!-- Shield Icon with Pulsing Glow -->
          <div class="w-16 h-16 rounded-2xl flex items-center justify-center transition-all duration-500 shadow-2xl"
               :class="{
                 'bg-blue-500/10 text-blue-400 shadow-blue-500/10': integrityStatus === null,
                 'bg-emerald-500/10 text-emerald-400 shadow-emerald-500/20 border border-emerald-500/20 animate-pulse': integrityStatus === 'secure',
                 'bg-rose-500/10 text-rose-400 shadow-rose-500/20 border border-rose-500/20 animate-bounce': integrityStatus === 'tampered'
               }">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div>
            <h2 class="text-lg font-black tracking-tight mb-1">سیستەمی پاراستنی کریپتۆگرافی داتابەیس (Database Integrity Shield)</h2>
            <p class="text-xs text-slate-400 font-semibold">
              پشکنینی چڕی داتاکان بە شێوازی زنجیرەیی کریپتۆگرافی (Cryptographic Hash Chain) بۆ دۆزینەوەی گۆڕانکاری یان سڕینەوەی دەرەکی.
            </p>
            <div class="mt-2 flex flex-wrap gap-2 text-[10px] font-bold">
              <span v-if="integrityStatus === null" class="px-3 py-1 bg-slate-800 text-slate-400 rounded-full">سیستەم ئامادەیە بۆ پشکنین</span>
              <span v-else-if="integrityStatus === 'secure'" class="px-3 py-1 bg-emerald-500/10 text-emerald-400 rounded-full border border-emerald-500/20">
                سەرجەم جوڵەکان پارێزراون (Scanned {{ scannedRows }} entries - Hash Chain Intact)
              </span>
              <span v-else-if="integrityStatus === 'tampered'" class="px-3 py-1 bg-rose-500/10 text-rose-400 rounded-full border border-rose-500/20">
                ئاگاداری: دەستکاری دەرەکی دۆزرایەوە! (Chain Breached)
              </span>
            </div>
          </div>
        </div>
        <button @click="runIntegrityCheck" :disabled="integrityLoading" 
                class="px-6 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-blue-500/20 disabled:opacity-50">
          {{ integrityLoading ? 'پشکنینی داتابەیس...' : 'دەستپێکردنی پشکنینی هاوسەنگی' }}
        </button>
      </div>

      <!-- Integrity Breach Details List (Only shown if Tampered, No Print) -->
      <div v-if="integrityStatus === 'tampered'" class="bg-rose-950/20 border border-rose-500/20 p-8 rounded-[3rem] backdrop-blur-3xl mb-10 no-print space-y-4">
        <div class="flex items-center gap-2 border-b border-rose-500/10 pb-3">
          <span class="w-2.5 h-2.5 bg-rose-500 rounded-full animate-ping"></span>
          <h3 class="text-md font-black text-rose-400">لیستی مامەڵە دەستکاری کراوە دۆزراوەکان (Tampered Rows Found)</h3>
        </div>
        <table class="w-full text-right text-xs">
          <thead>
            <tr class="text-slate-400 border-b border-white/5">
              <th class="pb-2">کۆدی دێڕ (ID)</th>
              <th class="pb-2">ڕێکەوت</th>
              <th class="pb-2">وەسف</th>
              <th class="pb-2 text-emerald-400">هاتوو (+)</th>
              <th class="pb-2 text-rose-400">ڕۆیشتوو (-)</th>
              <th class="pb-2">هۆکاری کێشەکە</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5 font-semibold text-slate-300">
            <tr v-for="v in integrityViolations" :key="v.id" class="hover:bg-rose-500/5 transition-colors">
              <td class="py-3 text-rose-400 font-bold">#{{ v.id }}</td>
              <td class="py-3">{{ v.date }}</td>
              <td class="py-3 font-bold text-white">{{ v.description }}</td>
              <td class="py-3 text-emerald-400">{{ v.debit > 0 ? formatNum(v.debit) : '-' }}</td>
              <td class="py-3 text-rose-400">{{ v.credit > 0 ? formatNum(v.credit) : '-' }}</td>
              <td class="py-3 text-rose-400 italic">{{ v.reason }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>

    <!-- Main Report Content (Printable) -->
    <div :class="{ 'hidden': activeTab !== 'audit' }" class="print:block">
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
      </div>

        <!-- Dynamic Running Print Footer -->
        <div class="hidden print:flex fixed bottom-0 left-0 right-0 justify-between items-center border-t border-slate-200 pt-1 text-[8px] font-bold text-slate-400" style="position: fixed; bottom: -0.2cm; left: 0; right: 0; direction: rtl;">
           <span>کۆمپانیای سەروەری موکریان - وەسڵنامەی ووردبینی دارایی</span>
           <span class="print-page-number">پەڕەی </span>
        </div>

      </div>
    </div> <!-- Closes activeTab === 'audit' wrapper -->

    <!-- AI LIQUIDITY PREDICTIONS TAB -->
    <div v-if="activeTab === 'predictions'" class="space-y-8 no-print">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Predictions Cards for each Currency (USD & IQD) -->
        <div v-for="(pred, cur) in predictions" :key="cur" 
             class="bg-slate-900/40 border border-white/5 p-8 rounded-[3rem] backdrop-blur-3xl space-y-6 flex flex-col justify-between">
          
          <!-- Top Row with Currency & Status badge -->
          <div class="flex justify-between items-center border-b border-white/5 pb-4">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center font-black text-lg text-blue-400">
                {{ cur }}
              </div>
              <div>
                <h3 class="text-xl font-black">پێشبینی سیولەی {{ cur }}</h3>
                <p class="text-xs text-slate-400 font-bold">بۆ ٧ ڕۆژی داهاتوو (AI Cash Flow Model)</p>
              </div>
            </div>
            <span class="px-4 py-2 rounded-xl font-black text-xs border"
                  :class="{
                    'bg-emerald-500/10 text-emerald-400 border-emerald-500/20': pred.status === 'secure',
                    'bg-amber-500/10 text-amber-400 border-amber-500/20': pred.status === 'warning',
                    'bg-rose-500/10 text-rose-400 border-rose-500/20': pred.status === 'critical'
                  }">
              {{ pred.status_kurdish }}
            </span>
          </div>

          <!-- Mid Section Stats -->
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-slate-950 p-4 rounded-2xl border border-white/5">
              <p class="text-[10px] font-black text-slate-500 uppercase">سیولەی گشتی سندوقەکان</p>
              <p class="text-lg font-black text-white mt-1">
                {{ formatNum(pred.current_balance) }} <span class="text-xs text-slate-400 font-bold">{{ cur }}</span>
              </p>
            </div>
            <div class="bg-slate-950 p-4 rounded-2xl border border-white/5">
              <p class="text-[10px] font-black text-slate-500 uppercase">تێکڕای ڕۆیشتووی ڕۆژانە</p>
              <p class="text-lg font-black text-slate-400 mt-1">
                {{ formatNum(pred.avg_daily_outflow) }} <span class="text-xs text-slate-500 font-bold">{{ cur }}</span>
              </p>
            </div>
            <div class="bg-slate-950 p-4 rounded-2xl border border-white/5 col-span-2">
              <p class="text-[10px] font-black text-slate-500 uppercase">ڕۆیشتنی پێشبینیکراو (٧ ڕۆژی داهاتوو)</p>
              <p class="text-2xl font-black text-blue-400 mt-1">
                {{ formatNum(pred.predicted_7d_outflow) }} <span class="text-sm text-slate-400 font-bold">{{ cur }}</span>
              </p>
            </div>
          </div>

          <!-- AI Injection Advice Alert Box -->
          <div class="p-4 rounded-2xl border text-xs font-semibold"
               :class="{
                 'bg-emerald-500/5 border-emerald-500/10 text-emerald-300': pred.status === 'secure',
                 'bg-amber-500/5 border-amber-500/10 text-amber-300': pred.status === 'warning',
                 'bg-rose-500/5 border-rose-500/10 text-rose-300': pred.status === 'critical'
               }">
            <p class="font-black text-sm mb-1">💡 شیکاری و ڕێنمایی زیرەکی دەستکرد:</p>
            <p v-if="pred.status === 'secure'">
              ئاستی سیولەی سندوقەکانت نایابە! بڕی پێویست پارەی کاشت لەبەر دەستە بۆ پڕکردنەوەی خواستەکانی بازاڕ لە ٧ ڕۆژی داهاتوودا.
            </p>
            <p v-else-if="pred.status === 'warning'">
              سیولە نزیکە لە ئاستی ئاگادارکردنەوە. پێشنیار دەکرێت بڕی <strong>{{ formatNum(pred.suggested_injection) }} {{ cur }}</strong> پارەی کاش بخەیتە ناو سندوقەکانتەوە بۆ پاراستنی جێگیری بازرگانی.
            </p>
            <p v-else>
              🚨 هۆشداری توند! پێشبینی دەکەین لە ٧ ڕۆژی داهاتوودا تووشی کەمبوونی توندی کاش بیت. تکایە بە خێرایی لانی کەم بڕی <strong>{{ formatNum(pred.suggested_injection) }} {{ cur }}</strong> بخەیتە ناو سندوقەکانەوە.
            </p>
          </div>

        </div>

      </div>
    </div>

    <!-- FORENSIC ANOMALIES TAB -->
    <div v-if="activeTab === 'anomalies'" class="space-y-6 no-print">
      <div class="bg-slate-900/40 border border-white/5 p-8 rounded-[3rem] backdrop-blur-3xl space-y-4">
        
        <div class="flex justify-between items-center border-b border-white/5 pb-4">
          <div>
            <h3 class="text-2xl font-black">شیکاری و دۆزینەوەی مامەڵە گوماناوییەکان</h3>
            <p class="text-xs text-slate-400 font-bold mt-1">پشکنینی چڕ بۆ دۆزینەوەی کاتژمێری نەگونجاو، نرخە دەرەکییەکان، و مامەڵە زۆر گەورەکان.</p>
          </div>
          <span class="px-4 py-2 bg-slate-950 text-slate-400 rounded-full text-xs font-black border border-white/5">
            Total Flagged: {{ anomalies.length }}
          </span>
        </div>

        <!-- Empty state -->
        <div v-if="anomalies.length === 0" class="py-12 text-center text-slate-500 font-semibold space-y-3">
          <svg class="w-12 h-12 mx-auto text-emerald-500/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-sm">سەرجەم مامەڵەکان پاکن و هیچ لادان یان سەرپێچییەک لە کاتژمێر یان نرخدا نەدۆزراوەتەوە!</p>
        </div>

        <!-- Table of anomalies -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-right text-xs">
            <thead>
              <tr class="text-slate-400 border-b border-white/5">
                <th class="pb-3 text-right">مەترسی</th>
                <th class="pb-3 text-right">جۆر / لادان</th>
                <th class="pb-3 text-right">مامەڵە</th>
                <th class="pb-3 text-right">ڕوونکردنەوەی فۆڕێنسیک</th>
                <th class="pb-3 text-right">ئەنجامدەر</th>
                <th class="pb-3 text-right">کاتی تۆمارکردن</th>
                <th class="pb-3 text-left pl-4">کردار</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5 font-semibold text-slate-300">
              <tr v-for="a in anomalies" :key="a.id" class="hover:bg-white/5 transition-colors">
                <td class="py-4">
                  <span class="px-3 py-1.5 rounded-xl text-[10px] font-black border"
                        :class="{
                          'bg-rose-500/10 text-rose-400 border-rose-500/20': a.severity === 'critical',
                          'bg-amber-500/10 text-amber-400 border-amber-500/20': a.severity === 'high',
                          'bg-yellow-500/10 text-yellow-400 border-yellow-500/20': a.severity === 'medium'
                        }">
                    {{ a.severity_kurdish }}
                  </span>
                </td>
                <td class="py-4">
                  <p class="font-black text-white">{{ a.category }}</p>
                </td>
                <td class="py-4">
                  <p class="font-bold text-slate-300">مامەڵەی #{{ a.id }}</p>
                  <p class="text-[10px] text-slate-400 font-semibold mt-0.5">
                    {{ formatNum(a.primary_amount) }} {{ a.primary_currency }}
                  </p>
                </td>
                <td class="py-4 text-slate-400 max-w-sm font-semibold text-right">
                  {{ a.description }}
                </td>
                <td class="py-4 text-slate-300">{{ a.operator }}</td>
                <td class="py-4 text-slate-400 text-[10px]" dir="ltr">
                  {{ a.date }}
                </td>
                <td class="py-4 text-left pl-4">
                  <button @click="openForensicModal(a.id, a)"
                          class="px-4 py-2 bg-blue-600 hover:bg-blue-500 hover:scale-[1.03] text-white font-black rounded-xl text-xs transition-all shadow-lg shadow-blue-500/10 flex items-center gap-1.5">
                    👁️ پشکنین
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
  </div>
</div>

    <!-- FORENSIC INVESTIGATOR MODAL -->
    <div v-if="showForensicModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-xl z-[99999] flex items-center justify-center p-4 overflow-y-auto" dir="rtl">
      <div class="bg-slate-900 border border-white/10 rounded-[3rem] max-w-2xl w-full p-8 space-y-6 shadow-2xl relative text-right text-white">
        
        <!-- Close button -->
        <button @click="showForensicModal = false" class="absolute top-6 left-6 text-slate-400 hover:text-white font-bold text-lg">
          ✕
        </button>

        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
          <div class="w-12 h-12 bg-amber-500/10 text-amber-400 rounded-2xl flex items-center justify-center font-black text-xl">
            🔍
          </div>
          <div>
            <h3 class="text-xl font-black">پشکنەری وردی فۆڕێنسیک (Forensic Inspector)</h3>
            <p class="text-xs text-slate-400 font-bold mt-0.5">بەدواداچوونی گومانی ژمارە #{{ selectedAnomalyId }}</p>
          </div>
        </div>

        <div v-if="forensicLoading" class="py-12 text-center space-y-3">
          <div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
          <p class="text-xs text-slate-400 font-bold">بارکردنی زانیارییەکانی مێژووی مامەڵە...</p>
        </div>

        <div v-else-if="selectedTransaction" class="space-y-6">
          
          <!-- Danger/Alert Warning Badge -->
          <div class="p-4 rounded-2xl border flex items-start gap-3"
               :class="{
                 'bg-rose-500/10 text-rose-300 border-rose-500/20': selectedAnomaly?.severity === 'high' || selectedAnomaly?.severity === 'critical',
                 'bg-amber-500/10 text-amber-300 border-amber-500/20': selectedAnomaly?.severity === 'medium'
               }">
            <span class="text-lg">⚠️</span>
            <div class="text-xs space-y-1">
              <p class="font-black">مەترسی لادان: {{ selectedAnomaly?.severity_kurdish }}</p>
              <p class="font-bold leading-relaxed text-slate-300">{{ selectedAnomaly?.description }}</p>
            </div>
          </div>

          <!-- Transaction Info Fields Grid -->
          <div class="grid grid-cols-2 gap-4 text-xs font-semibold">
            <div class="bg-slate-950/40 p-4 rounded-2xl border border-white/5 space-y-1">
              <span class="text-slate-500 text-[10px] block">ناسنامەی سەرەکی (Transaction ID)</span>
              <p class="text-white font-black text-sm">#{{ selectedTransaction.id }}</p>
            </div>
            <div class="bg-slate-950/40 p-4 rounded-2xl border border-white/5 space-y-1">
              <span class="text-slate-500 text-[10px] block">ئەنجامدەر (Operator)</span>
              <p class="text-white font-black text-sm">{{ selectedTransaction.user?.name || 'سیستم' }}</p>
            </div>
            <div class="bg-slate-950/40 p-4 rounded-2xl border border-white/5 space-y-1">
              <span class="text-slate-500 text-[10px] block">دراوی یەکەم (Primary Amount)</span>
              <p class="text-white font-black text-sm">{{ formatNum(selectedTransaction.primary_amount) }} {{ selectedTransaction.primary_currency }}</p>
            </div>
            <div class="bg-slate-950/40 p-4 rounded-2xl border border-white/5 space-y-1">
              <span class="text-slate-500 text-[10px] block">دراوی دووەم (Secondary Amount)</span>
              <p class="text-white font-black text-sm">
                {{ formatNum(selectedTransaction.secondary_amount) }} {{ selectedTransaction.secondary_currency }}
                <span v-if="selectedTransaction.rate" class="text-xs text-slate-400 font-bold block mt-0.5">نرخی ئاڵوگۆڕ: {{ formatNum(selectedTransaction.rate) }}</span>
              </p>
            </div>
          </div>

          <!-- Description Box -->
          <div class="bg-slate-950/40 p-4 rounded-2xl border border-white/5 space-y-1 text-xs">
            <span class="text-slate-500 text-[10px] block font-semibold">تێبینی / وەسفی مامەڵە</span>
            <p class="text-white font-bold leading-relaxed">{{ selectedTransaction.note || 'بێ تێبینی' }}</p>
          </div>

          <!-- Cryptographic Hash Integrity Verification -->
          <div class="bg-slate-950/60 p-4 rounded-2xl border border-white/10 flex items-center justify-between text-xs">
            <div class="space-y-1">
              <p class="font-black">پشکنینی مۆری کریپتۆگرافی (Cryptographic Seal Check)</p>
              <p class="text-[10px] text-slate-400 font-bold">بەرنامەکە فۆڕێنسیکی زنجیرەی واڵت دەکات بۆ دڵنیابوونەوە لە پاکی داتاکە.</p>
            </div>
            <span class="px-4 py-2 rounded-xl font-black text-xs border"
                  :class="isTampered(selectedTransaction.id) ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'">
              {{ isTampered(selectedTransaction.id) ? '🔴 دەستکاری کراوە (Tampered)' : '🟢 سەلامەتە (Seal Intact)' }}
            </span>
          </div>

        </div>

        <!-- Footer Buttons -->
        <div class="flex justify-end gap-3 pt-4 border-t border-white/5">
          <button @click="showForensicModal = false" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-black rounded-xl text-xs transition-all">
            داخستن
          </button>
        </div>

      </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()

const data = ref({})
const branches = ref([])
const loading = ref(false)

const canVerifyIntegrity = computed(() => {
  return authStore.isSuperAdmin || 
         authStore.permissions.includes('verify_database_integrity') || 
         authStore.user?.email === 'rebin.maaruf@gmail.com'
})

const integrityLoading = ref(false)
const integrityStatus = ref(null)
const integrityViolations = ref([])
const scannedRows = ref(0)

const activeTab = ref('audit')
const smartLoading = ref(false)
const predictions = ref({})
const anomalies = ref([])

const showForensicModal = ref(false)
const forensicLoading = ref(false)
const selectedAnomalyId = ref(null)
const selectedAnomaly = ref(null)
const selectedTransaction = ref(null)

async function openForensicModal(id, anomaly) {
  selectedAnomalyId.value = id
  selectedAnomaly.value = anomaly
  showForensicModal.value = true
  forensicLoading.value = true
  selectedTransaction.value = null
  try {
    const { data: res } = await axios.get(`/exchanges/${id}`)
    selectedTransaction.value = res
  } catch (e) {
    console.error(e)
    Swal.fire({ icon: 'error', title: 'هەڵە', text: 'شکستی هێنا لە وەرگرتنی وردەکارییەکانی مامەڵە.' })
  } finally {
    forensicLoading.value = false
  }
}

function isTampered(id) {
  return integrityViolations.value.some(v => v.id === id)
}


async function fetchSmartAnalytics() {
  smartLoading.value = true
  try {
    const branchId = filters.value.branch_id || 'all'
    const { data: res } = await axios.get('/smart-analytics', {
      params: { branch_id: branchId }
    })
    predictions.value = res.predictions || {}
    anomalies.value = res.anomalies || []
  } catch (e) {
    console.error(e)
  } finally {
    smartLoading.value = false
  }
}

async function runIntegrityCheck() {
  integrityLoading.value = true
  integrityStatus.value = null
  integrityViolations.value = []
  try {
    const { data: res } = await axios.get('/audit-advanced/verify')
    integrityStatus.value = res.status
    integrityViolations.value = res.violations || []
    scannedRows.value = res.scanned_rows || 0
    
    if (res.status === 'secure') {
      Swal.fire({
        icon: 'success',
        title: 'داتابەیس تەواو سەلامەتە!',
        text: `سەرجەم ${res.scanned_rows} جوڵەی حیسابی زنجیرەیی پشکنینیان بۆ کرا و هیچ گۆڕانکاری دەرەکی نەدۆزرایەوە.`,
        confirmButtonText: 'زۆر باشە',
        confirmButtonColor: '#10b981'
      })
    } else {
      Swal.fire({
        icon: 'error',
        title: '🚨 زەنگی مەترسی: دەستکاری دەرەکی دۆزرایەوە!',
        html: `
          <div dir="rtl" class="text-right text-sm space-y-4 leading-relaxed">
            <p class="font-black text-rose-400 text-base">کاک ڕێبین، زانیارییەکانت بە فەرمی پارێزراون بەڵام:</p>
            <p class="text-slate-300 font-semibold">سیستەمی پاراستنی کریپتۆگرافی دۆزیویەتییەوە کە <strong>کەسێک یان بەرنامەیەک لە پشتەوە چووەتە ناو بنکەدراوە (Database) و زانیارییەکی مێژوویی گۆڕیوە بەبێ مۆڵەتی سیستم!</strong></p>
            
            <div class="bg-rose-500/10 p-4 rounded-2xl border border-rose-500/20 text-rose-300 font-black text-xs space-y-2">
              <p>📍 مامەڵەی تێکچوو: <strong>دێڕی ژمارە #${res.violations[0]?.id}</strong></p>
              <p>📝 هۆکاری کێشەکە: <strong>${res.violations[0]?.reason}</strong></p>
            </div>
            
            <div class="bg-slate-900 p-4 rounded-2xl border border-white/5 text-slate-400 font-bold text-xs space-y-1">
              <p class="text-amber-400 font-black">💡 بۆچی ئەمە مەترسیدارە؟</p>
              <p>ئەگەر کارمەندێک بیەوێت ساختەکاری یان پارە دزین بشارێتەوە، دەچێت لە داتابەیس بڕی پارەکە یان وەسفەکە دەگۆڕێت. ئەم سیستمە ڕێگری لێدەکات و دەستبەجێ فەزاحەتی دەکات!</p>
            </div>
          </div>
        `,
        confirmButtonText: 'سەیرکردنی لیستی وردەکارییەکان',
        confirmButtonColor: '#ef4444'
      })
    }
  } catch (e) {
    console.error(e)
    Swal.fire({ icon: 'error', title: 'هەڵە', text: 'شکستی هێنا لە ئەنجامدانی پشکنین.' })
  } finally {
    integrityLoading.value = false
  }
}

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
    await fetchSmartAnalytics()
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
