<template>
  <div class="space-y-8 animate-fade-in max-w-[1700px] mx-auto pb-20 text-white font-sans">
    
    <!-- Ultra-Compact High-Performance Header -->
    <div class="flex flex-col md:flex-row justify-between items-center bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-2xl p-3 md:p-4 mb-4 gap-4 shadow-xl relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 to-transparent pointer-events-none"></div>
      
      <!-- Left side: Status & Latency -->
      <div class="flex items-center gap-4 relative z-10 order-2 md:order-1">

        <div class="h-8 w-px bg-white/10 mx-1"></div>

        <div class="flex flex-col">
          <span class="text-[8px] font-black text-slate-600 uppercase leading-none">Latency</span>
          <span class="text-[11px] font-black text-white">14ms</span>
        </div>
      </div>

      <!-- Right side: Title & Info -->
      <div dir="rtl" class="text-right relative z-10 order-1 md:order-2">
        <div class="flex items-center gap-3">
          <div class="w-1 h-6 bg-blue-500 rounded-full hidden md:block"></div>
          <div>
            <h1 class="text-base md:text-lg font-black text-white tracking-tighter uppercase leading-none">تێرمیناڵی ئاڵوگۆڕی دراوەکان</h1>
            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-1">Real-time Professional FX Trading</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Professional Compact Pair Selector Tabs -->
    <div class="flex gap-2 mb-6 overflow-x-auto pb-1 no-scrollbar scroll-smooth snap-x">
      <button v-for="p in pairs" :key="p.id" @click="selectPair(p)"
        :class="['flex-none snap-start min-w-[120px] md:min-w-[150px] relative p-3 md:p-4 rounded-xl md:rounded-2xl border transition-all duration-300 group overflow-hidden', 
          activePair.id === p.id ? 'bg-blue-600/15 border-blue-500/40 shadow-lg shadow-blue-500/5' : 'bg-slate-900/40 border-white/5 hover:border-white/20']">
        <div class="relative z-10 flex flex-col items-center gap-0.5">
           <span class="text-[8px] font-black uppercase tracking-widest transition-colors" :class="activePair.id === p.id ? 'text-blue-400' : 'text-slate-600'">{{ p.label }}</span>
           <div class="flex items-center gap-1">
              <span class="text-sm md:text-lg font-black text-white tracking-tighter">{{ p.primary }}</span>
              <span class="text-[10px] font-bold text-slate-700">/</span>
              <span class="text-sm md:text-lg font-black text-slate-400 tracking-tighter">{{ p.secondary }}</span>
           </div>
        </div>
        <div v-if="activePair.id === p.id" class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-500 shadow-[0_0_12px_#3b82f6]"></div>
      </button>
    </div>

    <!-- Dual Trading Engine (Buy/Sell) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
      
      <!-- BUY PANEL -->
      <div class="group relative bg-slate-900/50 backdrop-blur-3xl rounded-[2.5rem] border border-white/5 p-6 md:p-8 overflow-hidden transition-all hover:border-emerald-500/30 shadow-2xl">
        <div class="absolute -top-24 -right-24 w-60 h-60 bg-emerald-500/5 rounded-full blur-[80px] pointer-events-none group-hover:bg-emerald-500/10 transition-all"></div>
        
        <div class="flex justify-between items-center mb-6 relative z-10">
          <div class="flex items-center gap-3">
             <div class="w-10 h-10 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
             </div>
             <div dir="rtl" class="text-right">
                <h2 class="text-lg md:text-xl font-black text-white tracking-tight uppercase">کڕینی {{ activePair.primary }}</h2>
                <span class="text-emerald-500/60 text-[8px] font-black uppercase tracking-widest block">Market Buy</span>
             </div>
          </div>
        </div>

        <div class="space-y-4 md:space-y-6 relative z-10" dir="rtl">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest px-2">بڕی {{ activePair.primary }}</label>
              <div class="relative">
                <input :value="buyFormText.primary" @input="e => onInput('buy', 'primary', e.target.value)" type="text" 
                  class="w-full bg-slate-950/50 border border-white/5 rounded-2xl p-4 text-xl md:text-2xl font-black text-white focus:border-emerald-500 outline-none transition-all shadow-inner pl-12" />
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-500 font-black text-[10px] uppercase">{{ activePair.primary }}</span>
              </div>
            </div>
            <div class="space-y-1.5">
              <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest px-2">{{ activePair.rateLabel }}</label>
              <input :value="buyFormText.rate" @input="e => onInput('buy', 'rate', e.target.value)" type="text" 
                class="w-full bg-slate-950/50 border border-white/5 rounded-2xl p-4 text-xl md:text-2xl font-black text-emerald-400 focus:border-emerald-500 outline-none transition-all shadow-inner" />
            </div>
          </div>

          <div class="bg-slate-950/40 p-6 md:p-8 rounded-3xl border border-white/5 shadow-inner relative group/total">
             <label class="text-[8px] font-black text-slate-600 uppercase tracking-widest block text-center mb-2">کۆی گشتی بە {{ activePair.secondary }}</label>
             <div class="relative flex justify-center items-baseline gap-2">
                <span class="text-3xl md:text-5xl font-black text-white tracking-tighter font-mono">{{ buyFormText.secondary || '0' }}</span>
                <span class="text-xs font-black text-emerald-500/50">{{ activePair.secondary }}</span>
             </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
             <div class="space-y-1.5 relative">
                <span class="text-[8px] font-black text-emerald-500/70 uppercase px-2 tracking-widest">Receive {{ activePair.primary }} To</span>
                <div class="relative">
                  <input v-model="vaultToSearchBuy" @focus="showResults = 'vaultToBuy'" @input="searchAccounts('vaultToBuy')" type="text" placeholder="کۆدی سندوق..."
                    class="w-full bg-slate-950 border border-emerald-500/10 rounded-xl p-3 text-[11px] font-bold text-white focus:border-emerald-500 outline-none" />
                  <div v-if="showResults === 'vaultToBuy' && filteredVaults.length" class="absolute bottom-full left-0 right-0 mb-2 bg-[#020617]/95 border border-emerald-500/30 rounded-xl z-50 shadow-2xl backdrop-blur-xl p-1.5 space-y-0.5">
                    <button v-for="acc in filteredVaults" :key="acc.id" @click="selectAccount(acc, 'vaultToBuy')" class="w-full text-right p-2.5 hover:bg-emerald-500/10 rounded-lg flex justify-between items-center group">
                      <span class="font-bold text-white text-[11px] group-hover:text-emerald-400">{{ acc.name }}</span>
                      <span class="text-[8px] font-black bg-slate-900 text-emerald-500 px-1.5 py-0.5 rounded-md">{{ acc.code }}</span>
                    </button>
                  </div>
                </div>
             </div>
             <div class="space-y-1.5 relative">
                <span class="text-[8px] font-black text-rose-500/70 uppercase px-2 tracking-widest text-right">Pay {{ activePair.secondary }} From</span>
                <div class="relative">
                  <input v-model="vaultFromSearchBuy" @focus="showResults = 'vaultFromBuy'" @input="searchAccounts('vaultFromBuy')" type="text" placeholder="کۆدی سندوق..."
                    class="w-full bg-slate-950 border border-rose-500/10 rounded-xl p-3 text-[11px] font-bold text-white focus:border-rose-500 outline-none" />
                  <div v-if="showResults === 'vaultFromBuy' && filteredVaults.length" class="absolute bottom-full left-0 right-0 mb-2 bg-[#020617]/95 border border-rose-500/30 rounded-xl z-50 shadow-2xl backdrop-blur-xl p-1.5 space-y-0.5">
                    <button v-for="acc in filteredVaults" :key="acc.id" @click="selectAccount(acc, 'vaultFromBuy')" class="w-full text-right p-2.5 hover:bg-rose-500/10 rounded-lg flex justify-between items-center group">
                      <span class="font-bold text-white text-[11px] group-hover:text-rose-400">{{ acc.name }}</span>
                      <span class="text-[8px] font-black bg-slate-900 text-rose-500 px-1.5 py-0.5 rounded-md">{{ acc.code }}</span>
                    </button>
                  </div>
                </div>
             </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
             <div class="space-y-1.5 relative">
                <span class="text-[8px] font-black text-slate-600 uppercase px-2 tracking-widest">Customer (مشتەری)</span>
                <div class="relative">
                  <input v-model="accountSearchBuy" @focus="showResults = 'buy'" @input="searchAccounts('buy')" type="text" placeholder="بگەڕێ..."
                    class="w-full bg-slate-950 border border-white/5 rounded-xl p-3 text-[11px] font-bold focus:border-emerald-500 outline-none" />
                  <div v-if="showResults === 'buy' && filteredAccounts.length" class="absolute bottom-full left-0 right-0 mb-2 bg-[#020617]/95 border border-emerald-500/30 rounded-xl z-50 shadow-2xl p-1.5 space-y-0.5">
                    <button v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc, 'buy')" class="w-full text-right p-2.5 hover:bg-emerald-500/10 rounded-lg flex justify-between items-center group">
                      <span class="font-bold text-white text-[11px] group-hover:text-emerald-400">{{ acc.name }}</span>
                      <span class="text-[8px] font-black bg-slate-900 text-emerald-500 px-1.5 py-0.5 rounded-md">{{ acc.code }}</span>
                    </button>
                  </div>
                </div>
             </div>
             <div class="space-y-1.5">
                <span class="text-[8px] font-black text-slate-600 uppercase px-2 tracking-widest">تێبینی</span>
                <input v-model="buyForm.note" type="text" placeholder="تێبینی..." class="w-full bg-slate-950 border border-white/5 rounded-xl p-3 text-[11px] font-bold focus:border-emerald-500 outline-none" />
             </div>
          </div>

          <button @click="submitTrade('buy')" :disabled="loading || !buyFormText.primary"
            class="w-full py-5 bg-emerald-500 text-slate-950 font-black text-lg rounded-2xl shadow-lg shadow-emerald-500/10 active:scale-[0.98] transition-all disabled:opacity-20 uppercase tracking-tighter">
            تۆمارکردنی کڕین (BUY)
          </button>
        </div>
      </div>

      <!-- SELL PANEL -->
      <div class="group relative bg-slate-900/50 backdrop-blur-3xl rounded-[2.5rem] border border-white/5 p-6 md:p-8 overflow-hidden transition-all hover:border-rose-500/30 shadow-2xl">
        <div class="absolute -top-24 -right-24 w-60 h-60 bg-rose-500/5 rounded-full blur-[80px] pointer-events-none group-hover:bg-rose-500/10 transition-all"></div>
        
        <div class="flex justify-between items-center mb-6 relative z-10">
          <div class="flex items-center gap-3">
             <div class="w-10 h-10 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-center justify-center text-rose-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
             </div>
             <div dir="rtl" class="text-right">
                <h2 class="text-lg md:text-xl font-black text-white tracking-tight uppercase">فرۆشتنی {{ activePair.primary }}</h2>
                <span class="text-rose-500/60 text-[8px] font-black uppercase tracking-widest block">Market Sell</span>
             </div>
          </div>
        </div>

        <div class="space-y-4 md:space-y-6 relative z-10" dir="rtl">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest px-2">بڕی {{ activePair.primary }}</label>
              <div class="relative">
                <input :value="sellFormText.primary" @input="e => onInput('sell', 'primary', e.target.value)" type="text" 
                  class="w-full bg-slate-950/50 border border-white/5 rounded-2xl p-4 text-xl md:text-2xl font-black text-white focus:border-rose-500 outline-none transition-all shadow-inner pl-12" />
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-rose-500 font-black text-[10px] uppercase">{{ activePair.primary }}</span>
              </div>
            </div>
            <div class="space-y-1.5">
              <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest px-2">{{ activePair.rateLabel }}</label>
              <input :value="sellFormText.rate" @input="e => onInput('sell', 'rate', e.target.value)" type="text" 
                class="w-full bg-slate-950/50 border border-white/5 rounded-2xl p-4 text-xl md:text-2xl font-black text-rose-400 focus:border-rose-500 outline-none transition-all shadow-inner" />
            </div>
          </div>

          <div class="bg-slate-950/40 p-6 md:p-8 rounded-3xl border border-white/5 shadow-inner relative group/total">
             <label class="text-[8px] font-black text-slate-600 uppercase tracking-widest block text-center mb-2">کۆی گشتی بە {{ activePair.secondary }}</label>
             <div class="relative flex justify-center items-baseline gap-2">
                <span class="text-3xl md:text-5xl font-black text-white tracking-tighter font-mono">{{ sellFormText.secondary || '0' }}</span>
                <span class="text-xs font-black text-rose-500/50">{{ activePair.secondary }}</span>
             </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
             <div class="space-y-1.5 relative">
                <span class="text-[8px] font-black text-rose-500/70 uppercase px-2 tracking-widest">Pay {{ activePair.primary }} From</span>
                <div class="relative">
                  <input v-model="vaultFromSearchSell" @focus="showResults = 'vaultFromSell'" @input="searchAccounts('vaultFromSell')" type="text" placeholder="کۆدی سندوق..."
                    class="w-full bg-slate-950 border border-rose-500/10 rounded-xl p-3 text-[11px] font-bold text-white focus:border-rose-500 outline-none" />
                  <div v-if="showResults === 'vaultFromSell' && filteredVaults.length" class="absolute bottom-full left-0 right-0 mb-2 bg-[#020617]/95 border border-rose-500/30 rounded-xl z-50 shadow-2xl backdrop-blur-xl p-1.5 space-y-0.5">
                    <button v-for="acc in filteredVaults" :key="acc.id" @click="selectAccount(acc, 'vaultFromSell')" class="w-full text-right p-2.5 hover:bg-rose-500/10 rounded-lg flex justify-between items-center group">
                      <span class="font-bold text-white text-[11px] group-hover:text-rose-400">{{ acc.name }}</span>
                      <span class="text-[8px] font-black bg-slate-900 text-rose-500 px-1.5 py-0.5 rounded-md">{{ acc.code }}</span>
                    </button>
                  </div>
                </div>
             </div>
             <div class="space-y-1.5 relative">
                <span class="text-[8px] font-black text-emerald-500/70 uppercase px-2 tracking-widest text-right">Receive {{ activePair.secondary }} Into</span>
                <div class="relative">
                  <input v-model="vaultToSearchSell" @focus="showResults = 'vaultToSell'" @input="searchAccounts('vaultToSell')" type="text" placeholder="کۆدی سندوق..."
                    class="w-full bg-slate-950 border border-emerald-500/10 rounded-xl p-3 text-[11px] font-bold text-white focus:border-emerald-500 outline-none" />
                  <div v-if="showResults === 'vaultToSell' && filteredVaults.length" class="absolute bottom-full left-0 right-0 mb-2 bg-[#020617]/95 border border-emerald-500/30 rounded-xl z-50 shadow-2xl backdrop-blur-xl p-1.5 space-y-0.5">
                    <button v-for="acc in filteredVaults" :key="acc.id" @click="selectAccount(acc, 'vaultToSell')" class="w-full text-right p-2.5 hover:bg-emerald-500/10 rounded-lg flex justify-between items-center group">
                      <span class="font-bold text-white text-[11px] group-hover:text-emerald-400">{{ acc.name }}</span>
                      <span class="text-[8px] font-black bg-slate-900 text-emerald-500 px-1.5 py-0.5 rounded-md">{{ acc.code }}</span>
                    </button>
                  </div>
                </div>
             </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
             <div class="space-y-1.5 relative">
                <span class="text-[8px] font-black text-slate-600 uppercase px-2 tracking-widest">Customer (مشتەری)</span>
                <div class="relative">
                  <input v-model="accountSearchSell" @focus="showResults = 'sell'" @input="searchAccounts('sell')" type="text" placeholder="بگەڕێ..."
                    class="w-full bg-slate-950 border border-white/5 rounded-xl p-3 text-[11px] font-bold focus:border-rose-500 outline-none" />
                  <div v-if="showResults === 'sell' && filteredAccounts.length" class="absolute bottom-full left-0 right-0 mb-2 bg-[#020617]/95 border border-rose-500/30 rounded-xl z-50 shadow-2xl p-1.5 space-y-0.5">
                    <button v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc, 'sell')" class="w-full text-right p-2.5 hover:bg-rose-500/10 rounded-lg flex justify-between items-center group">
                      <span class="font-bold text-white text-[11px] group-hover:text-rose-400">{{ acc.name }}</span>
                      <span class="text-[8px] font-black bg-slate-900 text-rose-500 px-1.5 py-0.5 rounded-md">{{ acc.code }}</span>
                    </button>
                  </div>
                </div>
             </div>
             <div class="space-y-1.5">
                <span class="text-[8px] font-black text-slate-600 uppercase px-2 tracking-widest">تێبینی</span>
                <input v-model="sellForm.note" type="text" placeholder="تێبینی..." class="w-full bg-slate-950 border border-white/5 rounded-xl p-3 text-[11px] font-bold focus:border-rose-500 outline-none" />
             </div>
          </div>

          <button @click="submitTrade('sell')" :disabled="loading || !sellFormText.primary"
            class="w-full py-5 bg-rose-500 text-white font-black text-lg rounded-2xl shadow-lg shadow-rose-500/10 active:scale-[0.98] transition-all disabled:opacity-20 uppercase tracking-tighter">
            تۆمارکردنی فرۆشتن (SELL)
          </button>
        </div>
      </div>
    </div>

    <!-- Live Execution Log Section -->
    <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[2rem] md:rounded-[4rem] overflow-hidden shadow-2xl">
      <div class="p-6 md:p-10 border-b border-white/5 flex flex-col md:flex-row justify-between items-start md:items-center gap-6" dir="rtl">
        <div>
           <h3 class="text-xl md:text-2xl font-black text-white">مێژووی ئاڵوگۆڕەکان</h3>
           <p class="text-slate-500 text-[10px] font-bold mt-1">دوایین مامەڵە جێبەجێکراوەکان</p>
        </div>
        <div class="flex gap-2 bg-slate-950 p-1.5 rounded-2xl border border-white/5 w-full md:w-auto overflow-x-auto">
           <button v-for="f in ['all', 'buy', 'sell']" :key="f" @click="tableFilter = f"
             class="flex-1 md:flex-none px-6 py-2.5 rounded-xl text-[9px] md:text-[10px] font-black uppercase tracking-widest transition-all"
             :class="tableFilter === f ? 'bg-emerald-500 text-slate-950' : 'text-slate-500 hover:text-white'">
             {{ f }}
           </button>
        </div>
      </div>
      
      <!-- Responsive List Container -->
      <div class="overflow-x-auto">
        <!-- Desktop List -->
        <table class="hidden lg:table w-full text-right" dir="rtl">
          <thead>
            <tr class="bg-slate-950/40 text-slate-600 text-[9px] font-black uppercase tracking-[0.2em] border-b border-white/5">
              <th class="px-6 py-5">کات و بەروار</th>
              <th class="px-6 py-5 text-center">جۆری جوڵە</th>
              <th class="px-6 py-5">حیسابی کڕیار</th>
              <th class="px-6 py-5">سەرچاوە (داین)</th>
              <th class="px-6 py-5">شوێن (مەدین)</th>
              <th class="px-6 py-5">بڕی مامەڵە</th>
              <th class="px-6 py-5 text-center">نرخ</th>
              <th class="px-6 py-5 text-left">قازانج</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="t in filteredTransactions" :key="t.id" class="group hover:bg-white/[0.02] transition-all">
              <td class="px-6 py-4 text-slate-500 font-bold text-[10px]">{{ formatFullTime(t.created_at) }}</td>
              <td class="px-6 py-4 text-center">
                <span class="px-3 py-1 rounded-lg text-[8px] font-black uppercase" :class="t.type === 'buy' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500'">
                  {{ t.type === 'buy' ? 'BUY' : 'SELL' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-col">
                   <span class="text-white font-bold text-xs">{{ t.account?.name }}</span>
                   <span class="text-[8px] text-slate-600 font-black">{{ t.account?.code }}</span>
                </div>
              </td>
              <!-- Source (Cr) -->
              <td class="px-6 py-4">
                 <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500/40"></span>
                    <span class="text-[10px] font-bold text-slate-400">{{ t.vault_from?.name || '—' }}</span>
                 </div>
              </td>
              <!-- Destination (Dr) -->
              <td class="px-6 py-4">
                 <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                    <span class="text-[10px] font-bold text-slate-400">{{ t.vault_to?.name || '—' }}</span>
                 </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-col">
                   <span class="text-white font-black text-sm tracking-tight">{{ formatNum(t.primary_amount) }} <span class="text-[9px] text-slate-500 uppercase">{{ t.primary_currency }}</span></span>
                   <span class="text-[9px] text-slate-600 font-bold">{{ formatNum(t.secondary_amount) }} {{ t.secondary_currency }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-center font-black text-slate-500 font-mono text-xs">{{ formatNum(t.rate) }}</td>
              <td class="px-6 py-4 text-left flex items-center justify-end gap-4">
                 <span class="font-black text-emerald-500 text-sm font-mono">{{ t.profit > 0 ? '+' + formatNum(t.profit) : '—' }}</span>
                 <button @click="printInvoice(t)" class="p-1.5 hover:bg-white/10 rounded-lg text-slate-600 hover:text-white transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                 </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Mobile/Tablet Card List -->
        <div class="lg:hidden divide-y divide-white/[0.03]" dir="rtl">
           <div v-for="t in filteredTransactions" :key="t.id" class="p-6 space-y-4 hover:bg-white/[0.02] transition-all">
              <div class="flex justify-between items-center">
                 <span class="text-[10px] font-black text-slate-600 uppercase">{{ formatFullTime(t.created_at) }}</span>
                 <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase" :class="t.type === 'buy' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500'">
                    {{ t.type === 'buy' ? 'BUY' : 'SELL' }}
                 </span>
              </div>
              <div class="flex justify-between items-end">
                 <div class="flex flex-col">
                    <span class="text-[9px] font-black text-slate-500 uppercase mb-1">بڕی مامەڵە</span>
                    <span class="text-2xl font-black text-white font-mono leading-none">{{ formatNum(t.primary_amount) }} <span class="text-[10px] text-slate-500">{{ t.primary_currency }}</span></span>
                 </div>
                 <div class="text-left">
                    <span class="text-[9px] font-black text-slate-500 uppercase block mb-1">نرخی گۆڕینەوە</span>
                    <span class="text-lg font-black text-slate-400 font-mono">{{ formatNum(t.rate) }}</span>
                 </div>
              </div>
              <div class="bg-slate-950/50 p-4 rounded-xl border border-white/5 flex justify-between items-center">
                 <div class="flex flex-col">
                    <span class="text-[9px] font-black text-slate-600 uppercase mb-1">بۆ حیسابی</span>
                    <span class="text-sm font-bold text-white">{{ t.client_name || t.account?.name }}</span>
                 </div>
                 <div class="text-left">
                    <span class="text-[9px] font-black text-emerald-500 uppercase block mb-1">قازانج</span>
                    <span class="text-base font-black text-emerald-500 font-mono">{{ t.profit > 0 ? '+' + formatNum(t.profit) : '—' }}</span>
                 </div>
              </div>
           </div>
        </div>
      </div>
    </div>

    <!-- OPTIMIZED PRINT TEMPLATE -->
    <div v-if="printingTx" id="print-area" class="print-area-wrapper" dir="rtl">
       <div v-for="i in 2" :key="i" class="print-voucher">
          <div class="flex justify-between items-center border-b-4 border-black pb-4 mb-6">
             <div class="flex items-center gap-4">
                <img src="/logo.png" class="h-16 w-16 object-contain grayscale" />
                <div>
                   <h1 class="text-xl font-black text-black">کۆمپانیای سەروەری موکریان</h1>
                   <p class="text-[10px] font-bold text-black uppercase">Sarwary Mukrian Co. for Currency Exchange</p>
                </div>
             </div>
             <div class="text-left" dir="ltr">
                <h2 class="text-xl font-black text-black leading-none">EXCHANGE VOUCHER</h2>
                <p class="text-xs font-black mt-1">ID: #{{ printingTx.id }}</p>
             </div>
          </div>

          <div class="grid grid-cols-3 gap-2 mb-6 text-[10px]">
             <div class="border border-black p-2 rounded">
                <span class="font-black block text-slate-500">بەروار / Date</span>
                <span class="font-black">{{ formatFullTime(printingTx.created_at) }}</span>
             </div>
             <div class="border border-black p-2 rounded">
                <span class="font-black block text-slate-500">کڕیار / Client</span>
                <span class="font-black">{{ printingTx.client_name || printingTx.account?.name }}</span>
             </div>
             <div class="border border-black p-2 rounded text-center">
                <span class="font-black block text-slate-500">جۆری مامەڵە / Type</span>
                <span class="font-black" :class="printingTx.type === 'buy' ? 'text-emerald-700' : 'text-rose-700'">{{ printingTx.type === 'buy' ? 'کڕین (BUY)' : 'فرۆشتن (SELL)' }}</span>
             </div>
          </div>

          <table class="w-full border-2 border-black mb-6 text-xs">
             <thead class="bg-black text-white">
                <tr>
                   <th class="p-2 text-right">وەسف / Desc</th>
                   <th class="p-2 text-center">بڕ / Amount</th>
                   <th class="p-2 text-center">نرخ / Rate</th>
                   <th class="p-2 text-left">کۆی گشتی / Total</th>
                </tr>
             </thead>
             <tbody>
                <tr class="font-black">
                   <td class="border-t border-black p-4">{{ printingTx.primary_currency }} / {{ printingTx.secondary_currency }}</td>
                   <td class="border-t border-black p-4 text-center font-mono">{{ formatNum(printingTx.primary_amount) }} {{ printingTx.primary_currency }}</td>
                   <td class="border-t border-black p-4 text-center font-mono">{{ formatNum(printingTx.rate) }}</td>
                   <td class="border-t border-black p-4 text-left font-mono text-xl">{{ formatNum(printingTx.secondary_amount) }} {{ printingTx.secondary_currency }}</td>
                </tr>
             </tbody>
          </table>

          <div v-if="printingTx.note" class="border border-black p-2 rounded mb-6 text-[10px]">
             <span class="font-black block text-slate-500">تێبینی / Notes</span>
             <p class="font-bold">{{ printingTx.note }}</p>
          </div>

          <div class="flex justify-between mt-12 px-10">
             <div class="text-center w-36 border-t border-black pt-2">
                <p class="text-[9px] font-black uppercase">نوسینگە / Office</p>
             </div>
             <div class="text-center w-36 border-t border-black pt-2">
                <p class="text-[9px] font-black uppercase">کڕیار / Client</p>
             </div>
          </div>

          <!-- Legal Disclaimer & Contact -->
          <div class="mt-10 border-t border-slate-100 pt-4 flex justify-between items-end">
             <div class="text-[9px] font-bold text-slate-500 leading-tight">
                <p>• تکایە پێش دەرچوون لە نوسینگە دڵنیابەرەوە لە بڕی پارەکە.</p>
                <p>• نوسینگە بەرپرسیار نییە لە هەر هەڵەیەک دوای ڕۆیشتن.</p>
             </div>
             <div class="text-left text-[8px] font-black opacity-30 uppercase tracking-tighter">
                <p>Sarwary Mukrian Co. | Exchange Division</p>
                <p>Transaction Hash: EX-{{ printingTx.id }} | {{ i === 1 ? 'OFFICE COPY' : 'CUSTOMER COPY' }}</p>
             </div>
          </div>

          <div v-if="i === 1" class="my-14 border-t-2 border-dashed border-slate-300 relative">
             <span class="absolute left-1/2 -translate-x-1/2 -top-2 bg-white px-2 text-[6px] text-slate-400">ببڕدرێت لێرەوە / CUT HERE</span>
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
const auth = useAuthStore()

const allCurrencies = ref([])
const pairs = ref([])
const isLoading = ref(false)
const activePair = ref({ id: 0, primary: 'USD', secondary: 'IQD', label: 'دینار - دۆلار', multiplier: 0.01, rateLabel: 'بۆ هەر 100$' })

async function generatePairs() {
  try {
    const { data } = await axios.get('/currencies')
    const activeCurrencies = data.data || data
    allCurrencies.value = activeCurrencies 
    const quote = activeCurrencies.find(c => c.code === 'IQD') || activeCurrencies.find(c => c.is_base)
    
    const newPairs = []
    activeCurrencies.forEach(c => {
      if (quote && c.id !== quote.id) {
        let multiplier = 1;
        let rateLabel = `بۆ هەر 1 ${c.code}`;
        if (['USD', 'EUR', 'GBP', 'TRY'].includes(c.code)) { multiplier = 0.01; rateLabel = `بۆ هەر 100 ${c.code}`; }
        else if (c.code === 'IRR') { multiplier = 0.0000001; rateLabel = `بۆ هەر 1,000,000 تمەن`; }

        newPairs.push({
          id: c.id, primary: c.code, secondary: quote.code,
          label: `${quote.code} - ${c.code}`, multiplier, rateLabel,
          official_rate: c.exchange_rate
        })
      }
    })
    pairs.value = newPairs
    if (newPairs.length > 0) activePair.value = newPairs[0]
  } catch (e) { console.error(e) }
}

onMounted(async () => {
  isLoading.value = true
  try {
    await Promise.all([
      fetchData(),
      generatePairs()
    ])
  } catch (error) {
    console.error('Initialization failed:', error)
  } finally {
    isLoading.value = false
  }
})

const accounts = ref([])
const transactions = ref([])
const loading = ref(false)
const showResults = ref(null)

const accountSearchBuy = ref('')
const accountSearchSell = ref('')
const vaultToSearchBuy = ref('')
const vaultFromSearchBuy = ref('')
const vaultToSearchSell = ref('')
const vaultFromSearchSell = ref('')
const tableFilter = ref('all')
const printingTx = ref(null)

async function printInvoice(tx) {
  printingTx.value = tx
  setTimeout(() => {
    window.print()
    printingTx.value = null
  }, 100)
}

const buyFormText = ref({ primary: '', rate: '', secondary: '' })
const sellFormText = ref({ primary: '', rate: '', secondary: '' })

const buyForm = ref({ vault_from_id: null, vault_to_id: null, account_id: null, client_name: '', note: '' })
const sellForm = ref({ vault_from_id: null, vault_to_id: null, account_id: null, client_name: '', note: '' })

function formatWithCommas(str) {
  if (!str) return '';
  let clean = str.toString().replace(/[^\d.]/g, '');
  const parts = clean.split('.');
  if (parts.length > 2) clean = parts[0] + '.' + parts.slice(1).join('');
  const [whole, decimal] = clean.split('.');
  const formattedWhole = whole.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return decimal !== undefined ? `${formattedWhole}.${decimal}` : formattedWhole;
}

function onInput(type, source, rawValue) {
  const formText = type === 'buy' ? buyFormText.value : sellFormText.value;
  formText[source] = formatWithCommas(rawValue);
  if (rawValue && rawValue !== '') calculate(type, source);
}

function calculate(type, source) {
  const formText = type === 'buy' ? buyFormText.value : sellFormText.value;
  const m = activePair.value.multiplier;
  const p = parseFloat(formText.primary.replace(/,/g, '')) || 0;
  const r = parseFloat(formText.rate.replace(/,/g, '')) || 0;
  const s = parseFloat(formText.secondary.replace(/,/g, '')) || 0;

  if (source === 'primary' || source === 'rate') {
     formText.secondary = formatWithCommas(((p * m) * r).toFixed(0));
  } else if (source === 'secondary') {
     if (r > 0 && m > 0) formText.primary = formatWithCommas(((s / r) / m).toFixed(2).replace(/\.00$/, '')); 
  }
}

watch(activePair, (newPair) => {
  let dbRate = parseFloat(newPair.official_rate) || 1515
  
  // Scale the rate based on the multiplier (e.g., 1515 for 1 USD -> 151,500 for 100 USD)
  if (newPair.multiplier === 0.01) {
    dbRate = dbRate * 100
  } else if (newPair.multiplier === 0.0000001) {
    dbRate = dbRate * 1000000
  }

  buyFormText.value.rate = formatWithCommas(Math.round(dbRate - 500))
  sellFormText.value.rate = formatWithCommas(Math.round(dbRate + 500))
  
  buyFormText.value.primary = ''; buyFormText.value.secondary = '';
  sellFormText.value.primary = ''; sellFormText.value.secondary = '';
})

const filteredTransactions = computed(() => {
  if (tableFilter.value === 'all') return transactions.value
  return transactions.value.filter(t => t.type === tableFilter.value)
})

const filteredAccounts = computed(() => {
  const q = (showResults.value === 'buy' ? accountSearchBuy.value : accountSearchSell.value).toLowerCase()
  if (!q) return []
  return accounts.value.filter(a => a.type !== 'vault' && (a.name.toLowerCase().includes(q) || a.code.toString().includes(q))).slice(0, 8)
})

const filteredVaults = computed(() => {
  let q = '';
  if (showResults.value === 'vaultToBuy') q = vaultToSearchBuy.value;
  else if (showResults.value === 'vaultFromBuy') q = vaultFromSearchBuy.value;
  else if (showResults.value === 'vaultToSell') q = vaultToSearchSell.value;
  else if (showResults.value === 'vaultFromSell') q = vaultFromSearchSell.value;
  
  q = q.toLowerCase();
  
  // If no search query, show the most relevant (vaults) first
  if (!q) {
    return accounts.value.filter(a => a.type === 'vault').slice(0, 8);
  }

  // Search all accounts except internal ones
  return accounts.value.filter(a => 
    !['equity', 'revenue', 'expense'].includes(a.type) && 
    (a.name.toLowerCase().includes(q) || a.code.toString().includes(q))
  ).slice(0, 50)
})

function searchAccounts(type) { 
  showResults.value = type;
  
  // Smart Code-First Selection
  let q = '';
  if (type === 'buy') q = accountSearchBuy.value;
  else if (type === 'sell') q = accountSearchSell.value;
  else if (type === 'vaultToBuy') q = vaultToSearchBuy.value;
  else if (type === 'vaultFromBuy') q = vaultFromSearchBuy.value;
  else if (type === 'vaultToSell') q = vaultToSearchSell.value;
  else if (type === 'vaultFromSell') q = vaultFromSearchSell.value;

  if (q.length >= 2) {
    const exactMatch = accounts.value.find(a => a.code.toString() === q);
    if (exactMatch) selectAccount(exactMatch, type);
  }
}
function selectPair(p) { activePair.value = p }

async function fetchData() {
  try {
    const [accRes, transRes] = await Promise.all([
      axios.get('/accounts?per_page=1000'),
      axios.get('/exchanges')
    ])
    let allAccounts = accRes.data.data || accRes.data
    
    // Protection: Filter out Equity accounts for non-admins
    const isAuthorized = auth.isSuperAdmin || auth.user?.roles?.some(r => r === 'Manager' || r.name === 'Manager' || r === 'Admin' || r.name === 'Admin');
    
    if (!isAuthorized && !auth.permissions.includes('manage_finances')) {
      allAccounts = allAccounts.filter(acc => acc.type !== 'equity')
    }
    
    accounts.value = allAccounts
    transactions.value = transRes.data.data || transRes.data
    const firstVault = accounts.value.find(a => a.type === 'vault')
    if (firstVault) {
      buyForm.value.vault_from_id = firstVault.id; buyForm.value.vault_to_id = firstVault.id;
      sellForm.value.vault_from_id = firstVault.id; sellForm.value.vault_to_id = firstVault.id;
      vaultFromSearchBuy.value = firstVault.name; vaultToSearchBuy.value = firstVault.name;
      vaultFromSearchSell.value = firstVault.name; vaultToSearchSell.value = firstVault.name;
    }
  } catch (e) { console.error(e) }
}

function selectAccount(acc, type) {
  if (type === 'buy') { buyForm.value.account_id = acc.id; accountSearchBuy.value = acc.name }
  else if (type === 'sell') { sellForm.value.account_id = acc.id; accountSearchSell.value = acc.name }
  else if (type === 'vaultToBuy') { buyForm.value.vault_to_id = acc.id; vaultToSearchBuy.value = acc.name }
  else if (type === 'vaultFromBuy') { buyForm.value.vault_from_id = acc.id; vaultFromSearchBuy.value = acc.name }
  else if (type === 'vaultToSell') { sellForm.value.vault_to_id = acc.id; vaultToSearchSell.value = acc.name }
  else if (type === 'vaultFromSell') { sellForm.value.vault_from_id = acc.id; vaultFromSearchSell.value = acc.name }
  showResults.value = null
}

async function submitTrade(type) {
  const formObj = type === 'buy' ? buyForm.value : sellForm.value;
  const formText = type === 'buy' ? buyFormText.value : sellFormText.value;

  if (!formObj.account_id) return Swal.fire({ icon: 'warning', title: 'حیساب هەڵبژێرە', background: '#020617', color: '#fff' })

  loading.value = true
  try {
    const payload = { 
      ...formObj,
      primary_amount: parseFloat(formText.primary.replace(/,/g, '')),
      rate: parseFloat(formText.rate.replace(/,/g, '')),
      secondary_amount: parseFloat(formText.secondary.replace(/,/g, '')),
      type, pair: `${activePair.value.primary}/${activePair.value.secondary}`,
      primary_currency: activePair.value.primary, secondary_currency: activePair.value.secondary
    }
    const { data } = await axios.post('/exchanges', payload)
    transactions.value.unshift(data)
    formText.primary = ''; formText.secondary = '';
    Swal.fire({ icon: 'success', title: 'مامەڵەکە تۆمارکرا', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false, background: '#10b981', color: '#fff' })
  } catch (e) {
    const errorMsg = e.response?.data?.error || 'هەڵە لە تۆمارکردن';
    Swal.fire({ 
      icon: 'error', 
      title: 'شکستی هێنا', 
      text: errorMsg,
      background: '#020617', 
      color: '#fff',
      confirmButtonColor: '#ef4444'
    })
  } finally { loading.value = false }
}

const formatNum = (val) => new Intl.NumberFormat().format(val || 0)
const formatFullTime = (dateStr) => {
  const d = new Date(dateStr)
  return d.toLocaleString('en-GB', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short' })
}
</script>

<style scoped>
@media print {
  /* Hide EVERYTHING by default */
  body * { display: none !important; }
  
  /* Show ONLY the print area and its children */
  #print-area, #print-area * { display: block !important; visibility: visible !important; }
  #print-area div, #print-area p, #print-area span, #print-area table, #print-area thead, #print-area tr, #print-area th, #print-area td, #print-area img {
    display: block !important;
  }
  
  #print-area table { display: table !important; }
  #print-area thead { display: table-header-group !important; }
  #print-area tr { display: table-row !important; }
  #print-area th, #print-area td { display: table-cell !important; }
  #print-area .flex { display: flex !important; }
  #print-area .grid { display: grid !important; }

  #print-area {
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: white !important;
    color: black !important;
    padding: 2cm;
    margin: 0;
    z-index: 9999;
  }

  .print-voucher {
    width: 100%;
    background: white !important;
    color: black !important;
  }

  @page {
    size: A4;
    margin: 0;
  }
}

.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.custom-scrollbar::-webkit-scrollbar { height: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>
