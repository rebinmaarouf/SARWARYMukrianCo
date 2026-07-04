<template>
  <div>
    <!-- Main UI Section -->
    <div class="space-y-8 animate-fade-in text-slate-800 no-print pb-20">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm relative overflow-hidden">
      <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-50 rounded-full blur-3xl opacity-50"></div>
      <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
      
      <div dir="rtl" class="text-right relative z-10">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">پسوڵەکان (قەبز و سەرف)</h1>
        <p class="text-slate-500 font-medium mt-1 text-sm md:text-base">بەڕێوەبردنی وەرگرتن و پێدانی پارەی نەختینەیی ڕۆژانە بە سادەترین شێوە</p>
      </div>
    </div>

    <!-- Main Tabs -->
    <div class="flex gap-4 p-2 bg-slate-100 rounded-[2rem] w-full md:w-max mx-auto shadow-inner" dir="rtl">
      <button @click="activeTab = 'receipt'" 
        class="flex-1 md:w-48 py-4 px-6 rounded-3xl font-black text-lg transition-all flex items-center justify-center gap-3"
        :class="activeTab === 'receipt' ? 'bg-white text-emerald-600 shadow-md transform scale-105' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
        پسوڵەی قەبز
      </button>
      <button @click="activeTab = 'payment'" 
        class="flex-1 md:w-48 py-4 px-6 rounded-3xl font-black text-lg transition-all flex items-center justify-center gap-3"
        :class="activeTab === 'payment' ? 'bg-white text-rose-600 shadow-md transform scale-105' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
        پسوڵەی سەرف
      </button>
    </div>

    <!-- Forms Area -->
    <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm" dir="rtl">
      
      <!-- Explanation Alert -->
      <div class="mb-8 p-6 rounded-2xl border" 
           :class="activeTab === 'receipt' ? 'bg-emerald-50 border-emerald-100 text-emerald-800' : 'bg-rose-50 border-rose-100 text-rose-800'">
        <div class="flex items-start gap-4">
          <div class="p-3 rounded-full bg-white shadow-sm mt-1">
            <span class="text-2xl">{{ activeTab === 'receipt' ? '📥' : '📤' }}</span>
          </div>
          <div>
            <h3 class="font-black text-xl mb-1">{{ activeTab === 'receipt' ? 'کەی ئەمە بەکاردێت؟ (قەبز)' : 'کەی ئەمە بەکاردێت؟ (سەرف)' }}</h3>
            <p class="font-medium text-sm leading-relaxed opacity-90">
              {{ activeTab === 'receipt' 
                ? 'کاتێک کەسێک پارە دەهێنێت بۆ کۆمپانیا و پارەکە دەچێتە ناو قاصەکەتەوە. (بۆ نموونە: کڕیارێک قەرز دەداتەوە، یان کەسێک پارە دەهێنێت بۆت). ئەمە قاصەکەت زیاد دەکات.' 
                : 'کاتێک تۆ پارە دەدەیت بە کەسێک یان خەرجییەک دەکەیت. (بۆ نموونە: پارە دەدەیت بە وەکیلێک، یان کرێی دوکان دەدەیت). ئەمە پارەی ناو قاصەکەت کەم دەکات.' }}
            </p>
          </div>
        </div>
      </div>

      <form @submit.prevent="submitVoucher" class="space-y-8">
        <!-- Amount & Currency -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-3">
            <label class="text-sm font-black text-slate-700 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full" :class="activeTab === 'receipt' ? 'bg-emerald-500' : 'bg-rose-500'"></span>
              چەند پارە {{ activeTab === 'receipt' ? 'وەردەگریت؟' : 'دەدەیت؟' }}
            </label>
            <div class="relative">
              <input ref="amountInput" v-model="form.amount" type="text" required placeholder="بڕی پارەکە بە ژمارە"
                @input="formatInputAmount" @blur="validateAmount"
                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-5 text-2xl text-slate-900 font-mono font-black focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all shadow-inner" />
            </div>
          </div>

          <div class="space-y-3">
            <label class="text-sm font-black text-slate-700">جۆری دراو</label>
            <div class="flex gap-3 h-[74px]">
              <button v-for="c in currencies" :key="c.id" type="button" @click="form.currency_id = c.id"
                class="flex-1 rounded-2xl font-black text-lg border-2 transition-all"
                :class="form.currency_id === c.id ? 'border-blue-600 bg-blue-50 text-blue-700 shadow-md' : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300'">
                {{ c.code }}
              </button>
            </div>
          </div>
        </div>

        <!-- Accounts Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-slate-50 p-6 rounded-[2rem] border border-slate-100 relative">
          
          <!-- Direction Arrow -->
          <div class="hidden md:flex absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-white rounded-full shadow-md items-center justify-center text-slate-400 z-10">
            <svg class="w-6 h-6 transform" :class="activeTab === 'receipt' ? 'rotate-180 text-emerald-500' : 'text-rose-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
          </div>

          <!-- The Vault -->
          <div class="space-y-3" :class="activeTab === 'receipt' ? 'order-2' : 'order-1'">
            <label class="text-sm font-black flex items-center gap-2" :class="activeTab === 'receipt' ? 'text-emerald-700' : 'text-rose-700'">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
              کام قاصەیە پارەکە {{ activeTab === 'receipt' ? 'وەردەگرێت؟' : 'لێ دەردەچێت؟' }}
            </label>
            <select v-model="form.vault_id" required class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none appearance-none cursor-pointer shadow-sm">
              <option value="" disabled>قاصە هەڵبژێرە...</option>
              <option v-for="v in vaults" :key="v.id" :value="v.id">{{ v.name }}</option>
            </select>
          </div>

          <!-- The Client/Expense -->
          <div class="space-y-3" :class="activeTab === 'receipt' ? 'order-1' : 'order-2'">
            <label class="text-sm font-black text-slate-700 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              بە ناوی کێوە تۆمار بکرێت؟ {{ activeTab === 'receipt' ? '(کێ پارەکەی هێناوە؟)' : '(بۆ کێت خەرج کرد؟)' }}
            </label>
            
            <div class="relative">
               <!-- Simple search dropdown for accounts -->
               <input ref="accountSearchInput" type="text" v-model="accountSearch" @focus="showAccountDropdown = true" placeholder="گەڕان بۆ حساب..." 
                 class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none shadow-sm transition-all" />
               
               <div v-if="showAccountDropdown && accountSearch" class="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-xl max-h-60 overflow-y-auto">
                 <div v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc)"
                   class="px-5 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0 flex justify-between items-center">
                   <div class="flex items-center gap-2">
                     <span v-if="acc.code" class="text-xs font-mono font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ acc.code }}</span>
                     <span class="font-bold text-slate-800">{{ acc.name }}</span>
                   </div>
                   <span class="text-[10px] font-black px-2 py-1 bg-slate-100 rounded text-slate-500 uppercase tracking-widest">{{ acc.type }}</span>
                 </div>
                 <div v-if="filteredAccounts.length === 0" class="p-4 text-center text-slate-500 font-bold">هیچ حیسابێک نەدۆزرایەوە</div>
               </div>
            </div>
            
            <!-- Selected Account Tag -->
            <div v-if="form.account_id" class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded-xl text-sm font-black shadow-sm mt-2">
               {{ selectedAccountName }}
               <button type="button" @click="clearAccount" class="hover:text-rose-600 transition-colors">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
               </button>
            </div>
          </div>
        </div>

        <!-- Date & Notes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="space-y-3">
            <label class="text-sm font-black text-slate-700">بەروار</label>
            <input v-model="form.date" type="date" required
              class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 font-bold focus:border-blue-500 outline-none shadow-sm" />
          </div>
          <div class="space-y-3" :class="activeTab === 'receipt' ? 'lg:col-span-2' : ''">
            <label class="text-sm font-black text-slate-700">بەیان (تێبینی بۆ سەر پسوڵەکە)</label>
            <input ref="notesInput" v-model="form.notes" type="text" required placeholder="بۆ نموونە: دانەوەی قەرزی مانگی پێشوو..."
              class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 font-bold focus:border-blue-500 outline-none shadow-sm" />
          </div>
          <!-- Due Date (Only for Payment/Sarf) -->
          <div v-if="activeTab === 'payment'" class="space-y-3">
            <label class="text-sm font-black text-slate-700 flex items-center gap-1">
               کاتی گەڕاندنەوە 
               <span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full uppercase tracking-widest">ئارەزوومەندانە</span>
            </label>
            <input v-model="form.due_date" type="date"
              class="w-full bg-orange-50 border border-orange-200 rounded-2xl px-5 py-4 text-orange-900 font-bold focus:border-orange-500 outline-none shadow-sm transition-all" />
          </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
          <button type="submit" :disabled="loading || !form.account_id" 
            class="w-full py-6 text-white font-black text-2xl rounded-2xl transition-all shadow-xl active:scale-[0.98] disabled:opacity-50 disabled:active:scale-100 flex items-center justify-center gap-3"
            :class="activeTab === 'receipt' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20' : 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20'">
            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ loading ? 'خەریکی جێبەجێکردنە...' : `تۆمارکردنی پسوڵەی ${activeTab === 'receipt' ? 'قەبز' : 'سەرف'}` }}
          </button>
        </div>
      </form>
    </div>

    <!-- Recent Vouchers Table -->
    <div class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm mt-8">
      <div class="p-6 border-b border-slate-200 bg-slate-50 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4" dir="rtl">
        <h2 class="text-xl font-black text-slate-800 shrink-0">دوایین پسوڵەکان</h2>
        
        <!-- Advanced Filters -->
        <div class="flex flex-col md:flex-row items-center gap-3 w-full xl:w-auto">
          <div class="relative w-full md:w-64 shrink-0">
            <svg class="w-5 h-5 absolute right-4 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input v-model="tableSearch" type="text" placeholder="ناوی حیساب، ژمارەی پسوڵە..." class="w-full bg-white border border-slate-200 rounded-xl pl-4 pr-11 py-2.5 text-sm font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none shadow-sm transition-all" />
          </div>
          
          <div class="flex gap-2 w-full md:w-auto">
             <select v-model="tableFilterType" class="flex-1 md:w-auto bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold focus:border-blue-500 outline-none shadow-sm cursor-pointer appearance-none text-center">
                <option value="all">هەموو جۆرەکان</option>
                <option value="receipt">تەنها قەبز</option>
                <option value="payment">تەنها سەرف</option>
             </select>
             
             <select v-model="tableFilterDate" class="flex-1 md:w-auto bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold focus:border-blue-500 outline-none shadow-sm cursor-pointer appearance-none text-center">
                <option value="all">هەموو کاتێک</option>
                <option value="today">هی ئەمڕۆ</option>
                <option value="yesterday">هی دوێنێ</option>
                <option value="this_month">ئەم مانگە</option>
             </select>
          </div>
        </div>
      </div>
      <div class="overflow-x-auto scrollbar-thin">
        <table class="w-full text-right border-collapse min-w-[900px]" dir="rtl">
          <thead>
            <tr class="bg-white text-slate-500 uppercase text-[10px] font-black tracking-widest border-b border-slate-200">
              <th class="px-6 py-4">ژمارەی پسوڵە</th>
              <th class="px-6 py-4">جۆر</th>
              <th class="px-6 py-4">کەس/حیساب</th>
              <th class="px-6 py-4">بڕی پارە</th>
              <th class="px-6 py-4">ڕێکەوت</th>
              <th class="px-6 py-4 text-center">کردارەکان</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-semibold">
            <tr v-for="v in filteredVouchers" :key="v.id" class="hover:bg-slate-50 transition-colors group">
              <td class="px-6 py-4 font-mono font-black text-slate-700">{{ v.voucher_number }}</td>
              <td class="px-6 py-4">
                 <span class="px-3 py-1 rounded-lg text-xs font-black shadow-xs"
                   :class="v.type === 'receipt' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                   {{ v.type === 'receipt' ? 'قەبز (وەرگرتن)' : 'سەرف (دان)' }}
                 </span>
              </td>
              <td class="px-6 py-4 text-slate-900 font-bold">{{ v.account?.name }}</td>
              <td class="px-6 py-4 font-mono font-black text-lg" :class="v.type === 'receipt' ? 'text-emerald-600' : 'text-rose-600'">
                {{ formatNum(v.amount) }} <span class="text-xs">{{ v.currency?.code }}</span>
              </td>
              <td class="px-6 py-4 text-slate-500">{{ formatFullTime(v.created_at) }}</td>
              <td class="px-6 py-4 flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <div class="flex items-center gap-2">
                  <!-- Print Button -->
                  <button @click="printInvoice(v)" class="p-2 bg-slate-100 text-slate-600 hover:bg-slate-800 hover:text-white rounded-xl transition-all shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                  </button>
                  <!-- Delete Button -->
                  <button v-if="can('delete_records') || can('manage_vouchers')" @click="deleteVoucher(v.id)" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl transition-all shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredVouchers.length === 0">
               <td colspan="6" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center space-y-3">
                     <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                     </div>
                     <p class="text-slate-500 font-bold text-lg">هیچ پسوڵەیەک نەدۆزرایەوە</p>
                  </div>
               </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    </div> <!-- End Main UI Section -->
    <!-- A4 Print Template (Invisible) -->
    <div v-if="printingTx" id="print-area-a4" class="print-only-container text-black bg-white" dir="rtl">
       <template v-for="i in 2" :key="i">
          <div class="print-voucher flex flex-col" style="height: 140mm; padding: 10mm 15mm; box-sizing: border-box;">
             <!-- Header -->
             <div class="flex justify-between items-end border-b-2 border-black pb-3 mb-6">
                <div class="flex items-center gap-4">
                   <img src="/logo.png" class="h-16 w-16 object-contain grayscale" />
                   <div>
                      <h1 class="text-2xl font-black text-black leading-none mb-1">کۆمپانیای سەروەری موکریان</h1>
                      <p class="text-[11px] font-bold text-black uppercase tracking-widest">SARWARY MUKRIAN / VOUCHER</p>
                      <p class="text-[12px] font-black text-slate-700 mt-1">لقی: {{ printingTx.branch?.name || '---' }}</p>
                   </div>
                </div>
                <div class="text-left" dir="ltr">
                   <h2 class="text-xl font-black leading-none mb-1" :class="printingTx.type === 'receipt' ? 'text-black' : 'text-black'">
                     {{ printingTx.type === 'receipt' ? 'CASH RECEIPT' : 'CASH PAYMENT' }}
                   </h2>
                   <p class="text-sm font-black">REF: {{ printingTx.voucher_number }}</p>
                </div>
             </div>

             <!-- Basic Info Grid -->
             <div class="grid grid-cols-3 gap-4 mb-6 text-sm text-black">
                <div class="border border-black p-3 rounded-lg bg-white">
                   <span class="font-black block text-slate-500 text-[10px] uppercase mb-1">بەروار / Date</span>
                   <span class="text-base font-black">{{ formatFullTime(printingTx.created_at) }}</span>
                </div>
                <div class="border border-black p-3 rounded-lg bg-white">
                   <span class="font-black block text-slate-500 text-[10px] uppercase mb-1">کەس/حیساب / Account</span>
                   <span class="text-base font-black">{{ printingTx.account?.name || 'نەزانراو' }}</span>
                </div>
                <div class="border border-black p-3 rounded-lg text-left bg-white" dir="ltr">
                   <span class="font-black block text-slate-500 text-[10px] uppercase mb-1">Operation Status</span>
                   <span class="text-base font-black text-black">✓ APPROVED</span>
                </div>
             </div>

             <!-- Voucher Details -->
             <div class="border-2 border-black rounded-xl mb-6 flex border-collapse">
                <div class="w-1/3 bg-slate-100 p-6 flex flex-col justify-center items-center border-l-2 border-black text-center rounded-r-xl" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
                   <span class="text-[11px] font-black text-slate-600 uppercase tracking-widest mb-2">TOTAL AMOUNT</span>
                   <span class="text-3xl font-black font-mono tracking-tighter">
                     {{ formatNum(printingTx.amount) }} <span class="text-xl">{{ printingTx.currency?.code }}</span>
                   </span>
                </div>
                <div class="w-2/3 p-6 flex flex-col justify-center space-y-5 bg-white">
                   <div class="flex justify-between items-center border-b border-dashed border-slate-300 pb-3">
                     <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest">بڕی پارە بە نووسین / AMOUNT IN WORDS</span>
                     <span class="font-black text-base text-left block w-3/4 max-w-full truncate overflow-hidden text-black">
                       * {{ formatNum(printingTx.amount) }} {{ printingTx.currency?.code }} ONLY *
                     </span>
                   </div>
                   <div class="flex justify-between items-center border-b border-dashed border-slate-300 pb-3">
                     <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest">سندوق / VAULT</span>
                     <span class="font-black text-base text-black">{{ printingTx.vault?.name }}</span>
                   </div>
                   <div v-if="printingTx.balance_before !== undefined" class="flex justify-between items-center border-b border-dashed border-slate-300 pb-3">
                     <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest">باڵانسی پێشتر / PREVIOUS BALANCE</span>
                     <span class="font-black text-base text-black">{{ formatNum(printingTx.balance_before) }} {{ printingTx.currency?.code }}</span>
                   </div>
                   <div v-if="printingTx.balance_after !== undefined" class="flex justify-between items-center border-b border-dashed border-slate-300 pb-3">
                     <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest">باڵانسی نوێ / NEW BALANCE</span>
                     <span class="font-black text-base text-black">{{ formatNum(printingTx.balance_after) }} {{ printingTx.currency?.code }}</span>
                   </div>
                   <div class="flex justify-between items-center pb-1">
                     <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest">بەیان / NOTES</span>
                     <span class="font-black text-sm text-left max-w-[70%] line-clamp-2 leading-tight text-black">{{ printingTx.notes || 'بێ تێبینی' }}</span>
                   </div>
                </div>
             </div>

             <!-- Signatures -->
             <div class="grid grid-cols-2 gap-12 text-center mt-6 text-sm font-black text-black">
                <div class="space-y-12">
                   <div class="h-px bg-black w-48 mx-auto"></div>
                   <p class="uppercase text-slate-800 tracking-widest">واژۆی کەسی پەیوەندیدار<br/><span class="text-[10px]">Client Signature</span></p>
                </div>
                <div class="space-y-12">
                   <div class="h-px bg-black w-48 mx-auto relative">
                      <div class="absolute -top-6 left-1/2 -translate-x-1/2 font-mono text-[9px] text-slate-500">/e-signed/ {{ printingTx.user?.name }}</div>
                   </div>
                   <p class="uppercase text-slate-800 tracking-widest">واژۆی ڕێگەپێدراو<br/><span class="text-[10px]">Authorized Signature</span></p>
                </div>
             </div>

             <!-- Footer -->
             <div class="mt-6 flex justify-between items-end border-t-2 border-black pt-3">
                <div class="text-[10px] font-bold text-slate-700 leading-relaxed">
                   <p>• ئەسڵی وەسڵەکە لای کۆمپانیا دەمێنێتەوە، وێنەیەک دەدرێت بە کڕیار.</p>
                   <p>• لەکاتی بوونی هەر هەڵەیەک یان کێشەیەک ڕاستەوخۆ پەیوەندی بە بەڕێوەبەرایەتی بکەن.</p>
                </div>
                <div class="text-left text-[9px] font-black opacity-60 uppercase tracking-tighter text-black">
                   <p>Sarwary Mukrian Co. | Accounting Audit Trail</p>
                   <p>System Hash: SM-v2-VCH-{{ printingTx.id }} | {{ i === 1 ? 'OFFICE COPY' : 'CUSTOMER COPY' }}</p>
                </div>
             </div>
          </div>

          <!-- Cut Line -->
          <div v-if="i === 1" class="border-b-2 border-dashed border-slate-400 relative flex justify-center items-center" style="margin-top: 5mm; margin-bottom: 5mm;">
             <div class="absolute bg-white px-4 py-1 rounded-full text-[10px] text-slate-500 flex items-center gap-2 uppercase tracking-widest font-black border border-slate-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"></path></svg>
                بڕین لەم هێڵەوە / FOLD AND CUT HERE
             </div>
          </div>
       </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
const can = (permission) => {
  if (!auth.user) return false
  if (auth.user.email === 'rebin.maaruf@gmail.com') return true
  const roleName = (typeof auth.user.role === 'object' ? auth.user.role.name : auth.user.role)?.toLowerCase() || ''
  if (roleName.includes('admin')) return true
  const hasDirect = auth.user.permissions?.some(p => p.name === permission)
  if (hasDirect) return true
  if (typeof auth.user.role === 'object' && auth.user.role.permissions) {
    return auth.user.role.permissions.some(p => p.name === permission)
  }
  return false
}

const activeTab = ref('receipt')
const loading = ref(false)
const printingTx = ref(null)

const currencies = ref([])
const allAccounts = ref([])
const vaults = ref([])
const vouchers = ref([])

const tableFilterType = ref('all')
const tableFilterDate = ref('all')
const tableSearch = ref('')

const filteredVouchers = computed(() => {
  return vouchers.value.filter(v => {
    // 1. Type Match
    if (tableFilterType.value !== 'all' && v.type !== tableFilterType.value) return false;
    
    // 2. Date Match
    if (tableFilterDate.value !== 'all') {
      const today = new Date().toISOString().split('T')[0]
      const yesterday = new Date(Date.now() - 86400000).toISOString().split('T')[0]
      const thisMonth = today.substring(0, 7)
      
      if (tableFilterDate.value === 'today' && v.date !== today) return false;
      if (tableFilterDate.value === 'yesterday' && v.date !== yesterday) return false;
      if (tableFilterDate.value === 'this_month' && !v.date.startsWith(thisMonth)) return false;
    }
    
    // 3. Search Match
    if (tableSearch.value) {
      const q = tableSearch.value.toLowerCase()
      const match = (
        (v.voucher_number && v.voucher_number.toLowerCase().includes(q)) ||
        (v.account?.name && v.account.name.toLowerCase().includes(q)) ||
        (v.notes && v.notes.toLowerCase().includes(q)) ||
        (v.amount && v.amount.toString().includes(q))
      )
      if (!match) return false;
    }
    
    return true;
  })
})

const form = ref({
  amount: '',
  currency_id: '',
  vault_id: '',
  account_id: '',
  date: new Date().toISOString().split('T')[0],
  due_date: '',
  notes: ''
})

const accountSearch = ref('')
const showAccountDropdown = ref(false)
const selectedAccountName = ref('')

const amountInput = ref(null)
const accountSearchInput = ref(null)
const notesInput = ref(null)

const filteredAccounts = computed(() => {
  const nonVaults = allAccounts.value.filter(a => a.type !== 'vault')
  if (!accountSearch.value) return nonVaults.slice(0, 10) // Show top 10 immediately when clicked
  
  const q = accountSearch.value.toLowerCase()
  return nonVaults.filter(a => 
    (a.name && a.name.toLowerCase().includes(q)) || 
    (a.code && a.code.toString().includes(q))
  ).slice(0, 10)
})

function selectAccount(acc) {
  form.value.account_id = acc.id
  selectedAccountName.value = acc.name
  accountSearch.value = ''
  showAccountDropdown.value = false
  nextTick(() => {
    notesInput.value?.focus()
  })
}

function clearAccount() {
  form.value.account_id = ''
  selectedAccountName.value = ''
  form.value.date = new Date().toISOString().split('T')[0]
  form.value.due_date = ''
  form.value.notes = ''
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
  if (!e.target.closest('.relative')) {
    showAccountDropdown.value = false
  }
})

function formatInputAmount() {
  let val = form.value.amount.replace(/,/g, '')
  if (!isNaN(val) && val.length > 0) {
    // Keep decimal points
    if (!val.includes('.')) {
      form.value.amount = new Intl.NumberFormat('en-US').format(val)
    }
  }
}

const amountError = ref(false)

function validateAmount() {
  if (form.value.amount === '') { amountError.value = false; return }
  const val = parseFloat(form.value.amount.replace(/,/g, ''))
  amountError.value = (isNaN(val) || val <= 0)
}

function formatNum(val) {
  return new Intl.NumberFormat().format(val || 0)
}

async function fetchData() {
  try {
    const [currRes, accRes, vouchRes] = await Promise.all([
      axios.get('/currencies'),
      axios.get('/accounts'),
      axios.get('/vouchers')
    ])
    
    currencies.value = currRes.data.data || currRes.data
    allAccounts.value = accRes.data.data || accRes.data
    vaults.value = allAccounts.value.filter(a => a.type === 'vault')
    vouchers.value = vouchRes.data.data
    
    // Auto-select first currency and vault
    if (currencies.value.length > 0) form.value.currency_id = currencies.value.find(c => c.code === 'IQD')?.id || currencies.value[0].id
    if (vaults.value.length > 0) form.value.vault_id = vaults.value[0].id
    
  } catch (e) {
    console.error('Failed to load initial data', e)
  }
}

async function submitVoucher() {
  const amount = parseFloat(form.value.amount.replace(/,/g, ''))
  if (isNaN(amount) || amount <= 0) {
    Swal.fire({ icon: 'error', title: 'هەڵە', text: 'بڕی پارە دەبێت گەورەتر بێت لە سفر!', background: '#ffffff', color: '#0f172a', confirmButtonColor: '#dc2626' })
    return
  }

  loading.value = true
  try {
    const payload = {
      type: activeTab.value,
      amount: amount,
      currency_id: form.value.currency_id,
      vault_id: form.value.vault_id,
      account_id: form.value.account_id,
      date: form.value.date,
      due_date: form.value.due_date || null,
      notes: form.value.notes
    }

    const res = await axios.post('/vouchers', payload)
    
    const createdVoucher = {
      ...res.data.voucher,
      balance_before: res.data.balance_before,
      balance_after: res.data.balance_after
    }

    Swal.fire({
      icon: 'success',
      title: 'سەرکەوتوو بوو',
      text: 'پسوڵەکە بە سەرکەوتوویی تۆمارکرا لە دەفتەری ڕۆژنامە',
      background: '#ffffff',
      color: '#0f172a',
      showConfirmButton: false,
      timer: 2000,
      customClass: { popup: 'rounded-[2.5rem] border border-slate-200 shadow-2xl', title: 'font-black text-2xl' }
    })

    // Reset Form
    form.value.amount = ''
    form.value.notes = ''
    clearAccount()
    
    // Add to list (preserving balances for print!)
    vouchers.value.unshift(createdVoucher)
    if (vouchers.value.length > 50) vouchers.value.pop()

  } catch (e) {
    let errorHtml = 'نەتوانرا پسوڵەکە تۆمار بکرێت'
    if (e.response?.data?.errors) {
      const errors = Object.values(e.response.data.errors).flat()
      errorHtml = `<ul class="text-right list-disc list-inside space-y-2 mt-2">${errors.map(err => `<li class="text-rose-600 font-bold">${err}</li>`).join('')}</ul>`
    } else if (e.response?.data?.message) {
      errorHtml = `<p class="text-rose-600 font-bold">${e.response.data.message}</p>`
    }

    Swal.fire({
      icon: 'error',
      title: 'شکستهێنان لە تۆمارکردن',
      html: errorHtml,
      background: '#ffffff',
      color: '#0f172a',
      confirmButtonColor: '#dc2626',
      confirmButtonText: 'تێگەیشتم',
      customClass: { popup: 'rounded-[2.5rem] border border-slate-200 shadow-2xl' }
    })
  } finally {
    loading.value = false
  }
}

async function deleteVoucher(id) {
  const result = await Swal.fire({
    title: 'دڵنیایت لە سڕینەوە؟',
    text: "ئەم کارە ناگەڕێتەوە و هەموو تۆمارەکانی ناو دەفتەری ڕۆژنامەش دەسڕێتەوە",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'بەڵێ، بیسرەوە',
    cancelButtonText: 'پەشیمانم',
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#64748b',
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/vouchers/${id}`)
      vouchers.value = vouchers.value.filter(v => v.id !== id)
      Swal.fire({ icon: 'success', title: 'سڕایەوە', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false })
    } catch (e) {
      Swal.fire({ icon: 'error', title: 'هەڵە', text: 'نەتوانرا بسڕێتەوە' })
    }
  }
}

import { nextTick } from 'vue'

async function printInvoice(tx) {
  printingTx.value = tx
  document.body.classList.add('print-a4')
  
  await nextTick()
  
  setTimeout(() => {
    window.print()
    printingTx.value = null
    document.body.classList.remove('print-a4')
  }, 350)
}

const formatFullTime = (d) => new Date(d).toLocaleString('en-GB', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short' })

// Keep search clear when switching tabs to avoid confusion
watch(activeTab, () => {
  clearAccount()
  form.value.amount = ''
  form.value.notes = ''
})

onMounted(() => {
  fetchData()
})
</script>
