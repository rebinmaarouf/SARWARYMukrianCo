<template>
  <div class="space-y-8 animate-fade-in max-w-[1700px] mx-auto pb-20 text-white font-sans">
    
    <!-- Enterprise Trading Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 bg-slate-900/40 backdrop-blur-3xl p-6 md:p-10 rounded-[2rem] md:rounded-[3rem] border border-white/5 relative overflow-hidden shadow-2xl">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-transparent pointer-events-none"></div>
      <div dir="rtl" class="text-right relative z-10 w-full md:w-auto">
        <div class="flex items-center gap-3 mb-3">
           <span class="w-10 h-1 bg-blue-500 rounded-full"></span>
           <h2 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.4em]">FX Trading Terminal</h2>
        </div>
        <h1 class="text-2xl md:text-4xl font-black text-white tracking-tighter">تێرمیناڵی ئاڵوگۆڕی دراوەکان</h1>
        <p class="text-slate-500 font-medium mt-2 text-sm md:text-lg">کڕین و فرۆشتنی سەرجەم دراوەکان بە نرخی بازاڕ</p>
      </div>
      <div class="flex items-center gap-6 relative z-10 w-full md:w-auto">
        <div class="bg-slate-950 px-6 py-4 rounded-2xl border border-white/5 flex items-center gap-6 shadow-inner w-full md:w-auto justify-between md:justify-start">
          <div class="flex flex-col items-start">
             <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest leading-none mb-1">Status</span>
             <div class="flex items-center gap-2">
               <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_#10b981]"></span>
               <span class="text-[10px] md:text-sm font-black text-emerald-400">Live</span>
             </div>
          </div>
          <div class="w-px h-8 bg-white/10"></div>
          <div class="flex flex-col items-start">
             <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest leading-none mb-1">Latency</span>
             <span class="text-[10px] md:text-sm font-black text-white font-mono">14ms</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Professional Pair Ticker -->
    <div class="flex gap-3 overflow-x-auto pb-4 custom-scrollbar px-2 snap-x">
      <button v-for="p in pairs" :key="p.id" @click="selectPair(p)"
        class="flex-none snap-start min-w-[180px] md:min-w-[220px] relative group p-5 md:p-6 rounded-[2rem] md:rounded-[2.5rem] border transition-all duration-500 overflow-hidden"
        :class="activePair.id === p.id ? 'border-blue-500 bg-blue-500/10' : 'border-white/5 bg-slate-900/40 hover:bg-slate-950 hover:border-white/20'">
        <div class="relative z-10 flex flex-col items-center gap-1 md:gap-2">
          <span class="text-[9px] md:text-[10px] font-black uppercase tracking-[0.2em]" :class="activePair.id === p.id ? 'text-blue-400' : 'text-slate-600'">{{ p.label }}</span>
          <div class="flex items-center gap-1.5">
             <span class="text-lg md:text-2xl font-black text-white tracking-tighter">{{ p.primary }}</span>
             <span class="text-slate-700 font-bold text-xs">/</span>
             <span class="text-lg md:text-2xl font-black text-slate-400 tracking-tighter">{{ p.secondary }}</span>
          </div>
        </div>
        <div v-if="activePair.id === p.id" class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 shadow-[0_0_15px_#3b82f6]"></div>
      </button>
    </div>

    <!-- Dual Trading Engine (Buy/Sell) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10">
      
      <!-- BUY PANEL -->
      <div class="group relative bg-slate-900/40 backdrop-blur-3xl rounded-[3rem] md:rounded-[4rem] border border-white/5 p-8 md:p-12 overflow-hidden transition-all hover:border-emerald-500/40 shadow-3xl">
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-emerald-500/5 rounded-full blur-[100px] pointer-events-none group-hover:bg-emerald-500/10 transition-all"></div>
        
        <div class="flex justify-between items-center mb-10 relative z-10">
          <div class="flex items-center gap-4">
             <div class="w-10 h-10 md:w-12 md:h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-slate-950 shadow-xl shadow-emerald-500/20">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
             </div>
             <div dir="rtl" class="text-right">
                <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">کڕینی {{ activePair.primary }}</h2>
                <span class="text-emerald-500/60 text-[9px] font-black uppercase tracking-widest block mt-0.5">Market Buy</span>
             </div>
          </div>
        </div>

        <div class="space-y-6 md:space-y-8 relative z-10" dir="rtl">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بڕی {{ activePair.primary }}</label>
              <div class="relative">
                <input :value="buyFormText.primary" @input="e => onInput('buy', 'primary', e.target.value)" type="text" 
                  class="w-full bg-slate-950 border border-white/5 rounded-2xl md:rounded-3xl p-5 md:p-7 text-2xl md:text-3xl font-black text-white focus:border-emerald-500 outline-none transition-all shadow-inner pl-16 md:pl-20" />
                <span class="absolute left-6 md:left-8 top-1/2 -translate-y-1/2 text-emerald-500 font-black text-sm md:text-lg">{{ activePair.primary }}</span>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">{{ activePair.rateLabel }}</label>
              <input :value="buyFormText.rate" @input="e => onInput('buy', 'rate', e.target.value)" type="text" 
                class="w-full bg-slate-950 border border-white/5 rounded-2xl md:rounded-3xl p-5 md:p-7 text-2xl md:text-3xl font-black text-emerald-400 focus:border-emerald-500 outline-none transition-all shadow-inner" />
            </div>
          </div>

          <div class="bg-slate-950 p-8 md:p-10 rounded-[2.5rem] md:rounded-[3rem] border border-white/5 shadow-inner group/total relative">
             <label class="text-[9px] font-black text-slate-600 uppercase tracking-widest block text-center mb-3 md:mb-4">کۆی گشتی بە {{ activePair.secondary }}</label>
             <div class="relative flex justify-center items-end gap-2 md:gap-4">
                <span class="text-4xl md:text-6xl font-black text-white tracking-tighter font-mono">{{ buyFormText.secondary || '0' }}</span>
                <span class="text-base md:text-xl font-black text-emerald-500/50 mb-1.5 md:mb-3">{{ activePair.secondary }}</span>
             </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
             <div class="space-y-2 relative">
                <span class="text-[9px] font-black text-slate-600 uppercase px-2 tracking-widest">لە حیسابی کڕیار</span>
                <input v-model="accountSearchBuy" @focus="showResults = 'buy'" @input="searchAccounts('buy')" type="text" placeholder="بگەڕێ..."
                  class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 md:p-5 text-sm font-bold focus:border-emerald-500 outline-none" />
                <div v-if="showResults === 'buy' && filteredAccounts.length" class="absolute top-full left-0 right-0 mt-2 bg-[#020617] border border-emerald-500/50 rounded-2xl z-50 shadow-2xl overflow-hidden max-h-48 overflow-y-auto">
                   <div v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc, 'buy')" class="p-4 border-b border-white/5 hover:bg-emerald-500/10 cursor-pointer flex justify-between items-center transition-all">
                     <span class="font-bold text-white text-xs">{{ acc.name }}</span>
                     <span class="text-[9px] font-black text-emerald-500">{{ acc.code }}</span>
                   </div>
                </div>
             </div>
             <div class="space-y-2">
                <span class="text-[9px] font-black text-slate-600 uppercase px-2 tracking-widest">تێبینی</span>
                <input v-model="buyForm.note" type="text" placeholder="تێبینی..." class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 md:p-5 text-sm font-bold focus:border-emerald-500 outline-none" />
             </div>
          </div>

          <button @click="submitTrade('buy')" :disabled="loading || !buyFormText.primary"
            class="w-full py-6 md:py-8 bg-emerald-500 text-slate-950 font-black text-2xl md:text-3xl rounded-3xl md:rounded-[2.5rem] shadow-xl shadow-emerald-500/20 active:scale-[0.98] transition-all disabled:opacity-20">
            تۆمارکردنی کڕین (BUY)
          </button>
        </div>
      </div>

      <!-- SELL PANEL -->
      <div class="group relative bg-slate-900/40 backdrop-blur-3xl rounded-[3rem] md:rounded-[4rem] border border-white/5 p-8 md:p-12 overflow-hidden transition-all hover:border-rose-500/40 shadow-3xl">
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-rose-500/5 rounded-full blur-[100px] pointer-events-none group-hover:bg-rose-500/10 transition-all"></div>
        
        <div class="flex justify-between items-center mb-10 relative z-10">
          <div class="flex items-center gap-4">
             <div class="w-10 h-10 md:w-12 md:h-12 bg-rose-500 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-rose-500/20">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
             </div>
             <div dir="rtl" class="text-right">
                <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">فرۆشتنی {{ activePair.primary }}</h2>
                <span class="text-rose-500/60 text-[9px] font-black uppercase tracking-widest block mt-0.5">Market Sell</span>
             </div>
          </div>
        </div>

        <div class="space-y-6 md:space-y-8 relative z-10" dir="rtl">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بڕی {{ activePair.primary }}</label>
              <div class="relative">
                <input :value="sellFormText.primary" @input="e => onInput('sell', 'primary', e.target.value)" type="text" 
                  class="w-full bg-slate-950 border border-white/5 rounded-2xl md:rounded-3xl p-5 md:p-7 text-2xl md:text-3xl font-black text-white focus:border-rose-500 outline-none transition-all shadow-inner pl-16 md:pl-20" />
                <span class="absolute left-6 md:left-8 top-1/2 -translate-y-1/2 text-rose-500 font-black text-sm md:text-lg">{{ activePair.primary }}</span>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">{{ activePair.rateLabel }}</label>
              <input :value="sellFormText.rate" @input="e => onInput('sell', 'rate', e.target.value)" type="text" 
                class="w-full bg-slate-950 border border-white/5 rounded-2xl md:rounded-3xl p-5 md:p-7 text-2xl md:text-3xl font-black text-rose-400 focus:border-rose-500 outline-none transition-all shadow-inner" />
            </div>
          </div>

          <div class="bg-slate-950 p-8 md:p-10 rounded-[2.5rem] md:rounded-[3rem] border border-white/5 shadow-inner group/total relative">
             <label class="text-[9px] font-black text-slate-600 uppercase tracking-widest block text-center mb-3 md:mb-4">کۆی گشتی بە {{ activePair.secondary }}</label>
             <div class="relative flex justify-center items-end gap-2 md:gap-4">
                <span class="text-4xl md:text-6xl font-black text-white tracking-tighter font-mono">{{ sellFormText.secondary || '0' }}</span>
                <span class="text-base md:text-xl font-black text-rose-500/50 mb-1.5 md:mb-3">{{ activePair.secondary }}</span>
             </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
             <div class="space-y-2 relative">
                <span class="text-[9px] font-black text-slate-600 uppercase px-2 tracking-widest">بۆ حسابی کڕیار</span>
                <input v-model="accountSearchSell" @focus="showResults = 'sell'" @input="searchAccounts('sell')" type="text" placeholder="بگەڕێ..."
                  class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 md:p-5 text-sm font-bold focus:border-rose-500 outline-none" />
                <div v-if="showResults === 'sell' && filteredAccounts.length" class="absolute top-full left-0 right-0 mt-2 bg-[#020617] border border-rose-500/50 rounded-2xl z-50 shadow-2xl overflow-hidden max-h-48 overflow-y-auto">
                   <div v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc, 'sell')" class="p-4 border-b border-white/5 hover:bg-rose-500/10 cursor-pointer flex justify-between items-center transition-all">
                     <span class="font-bold text-white text-xs">{{ acc.name }}</span>
                     <span class="text-[9px] font-black text-rose-500">{{ acc.code }}</span>
                   </div>
                </div>
             </div>
             <div class="space-y-2">
                <span class="text-[9px] font-black text-slate-600 uppercase px-2 tracking-widest">تێبینی</span>
                <input v-model="sellForm.note" type="text" placeholder="تێبینی..." class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 md:p-5 text-sm font-bold focus:border-rose-500 outline-none" />
             </div>
          </div>

          <button @click="submitTrade('sell')" :disabled="loading || !sellFormText.primary"
            class="w-full py-6 md:py-8 bg-rose-500 text-white font-black text-2xl md:text-3xl rounded-3xl md:rounded-[2.5rem] shadow-xl shadow-rose-500/20 active:scale-[0.98] transition-all disabled:opacity-20">
            تۆمارکردنی فرۆشتن (SELL)
          </button>
        </div>
      </div>
    </div>

    <!-- Live Execution Log -->
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
      
      <!-- Responsive List -->
      <div class="overflow-x-auto">
        <!-- Desktop List -->
        <table class="hidden lg:table w-full text-right" dir="rtl">
          <thead>
            <tr class="bg-slate-950/40 text-slate-600 text-[10px] font-black uppercase tracking-[0.2em] border-b border-white/5">
              <th class="px-12 py-6">کات و بەروار</th>
              <th class="px-12 py-6 text-center">جۆری جوڵە</th>
              <th class="px-12 py-6">حیسابی کڕیار</th>
              <th class="px-12 py-6">بڕی مامەڵە</th>
              <th class="px-12 py-6">نرخی جێبەجێکردن</th>
              <th class="px-12 py-6 text-left">قازانج</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="t in filteredTransactions" :key="t.id" class="group hover:bg-white/[0.02] transition-all">
              <td class="px-12 py-7 text-slate-500 font-bold text-[11px]">{{ formatFullTime(t.created_at) }}</td>
              <td class="px-12 py-7 text-center">
                <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase" :class="t.type === 'buy' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-500 border border-rose-500/30'">
                  {{ t.type === 'buy' ? 'MARKET BUY' : 'MARKET SELL' }}
                </span>
              </td>
              <td class="px-12 py-7">
                <div class="flex flex-col">
                   <span class="text-white font-black text-sm group-hover:text-blue-400">{{ t.client_name || t.account?.name }}</span>
                   <span class="text-[9px] text-slate-600 font-black uppercase">{{ t.account?.code }}</span>
                </div>
              </td>
              <td class="px-12 py-7">
                <div class="flex flex-col">
                   <span class="text-white font-black text-xl tracking-tight">{{ formatNum(t.primary_amount) }} <span class="text-[10px] text-slate-500 uppercase">{{ t.primary_currency }}</span></span>
                   <span class="text-[10px] text-slate-500 font-bold mt-0.5">{{ formatNum(t.secondary_amount) }} {{ t.secondary_currency }}</span>
                </div>
              </td>
              <td class="px-12 py-7 font-black text-slate-400 font-mono text-base">{{ formatNum(t.rate) }}</td>
              <td class="px-12 py-7 text-left font-black text-emerald-500 text-xl font-mono">{{ t.profit > 0 ? '+' + formatNum(t.profit) : '—' }}</td>
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
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'

const pairs = ref([])
const activePair = ref({ id: 0, primary: 'USD', secondary: 'IQD', label: 'دینار - دۆلار', multiplier: 0.01, rateLabel: 'بۆ هەر 100$' })

async function generatePairs() {
  try {
    const { data } = await axios.get('/currencies')
    const activeCurrencies = data.data || data
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
          label: `${quote.code} - ${c.code}`, multiplier, rateLabel
        })
      }
    })
    pairs.value = newPairs
    if (newPairs.length > 0) activePair.value = newPairs[0]
  } catch (e) { console.error(e) }
}

const accounts = ref([])
const transactions = ref([])
const loading = ref(false)
const showResults = ref(null)
const accountSearchBuy = ref('')
const accountSearchSell = ref('')
const tableFilter = ref('all')

const buyFormText = ref({ primary: '', rate: '151,000', secondary: '' })
const sellFormText = ref({ primary: '', rate: '152,000', secondary: '' })

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
  if (newPair.primary === 'USD') { buyFormText.value.rate = '151,000'; sellFormText.value.rate = '152,000' }
  else if (newPair.primary === 'IRR') { buyFormText.value.rate = '2,500'; sellFormText.value.rate = '2,550' }
  else { buyFormText.value.rate = '1,000'; sellFormText.value.rate = '1,100' }
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
  return accounts.value.filter(a => a.name.toLowerCase().includes(q) || a.code.toString().includes(q)).slice(0, 8)
})

function searchAccounts(type) { showResults.value = type }
function selectPair(p) { activePair.value = p }

async function fetchData() {
  try {
    const [accRes, transRes] = await Promise.all([
      axios.get('/accounts?per_page=1000'),
      axios.get('/exchanges')
    ])
    accounts.value = accRes.data.data || accRes.data
    transactions.value = transRes.data.data || transRes.data
    const firstVault = accounts.value.find(a => a.type === 'vault')
    if (firstVault) {
      buyForm.value.vault_from_id = firstVault.id; buyForm.value.vault_to_id = firstVault.id;
      sellForm.value.vault_from_id = firstVault.id; sellForm.value.vault_to_id = firstVault.id;
    }
  } catch (e) { console.error(e) }
}

function selectAccount(acc, type) {
  if (type === 'buy') { buyForm.value.account_id = acc.id; accountSearchBuy.value = acc.name }
  else { sellForm.value.account_id = acc.id; accountSearchSell.value = acc.name }
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
    Swal.fire({ icon: 'error', title: 'هەڵە لە تۆمارکردن', background: '#020617', color: '#fff' })
  } finally { loading.value = false }
}

const formatNum = (val) => new Intl.NumberFormat().format(val || 0)
const formatFullTime = (dateStr) => {
  const d = new Date(dateStr)
  return d.toLocaleString('en-GB', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short' })
}

onMounted(() => { fetchData(); generatePairs() })
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.custom-scrollbar::-webkit-scrollbar { height: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>
