<template>
  <div class="space-y-6">
    <!-- Quick Type Selector -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 no-print">
      <button v-for="p in pairs" :key="p.id" 
        @click="selectPair(p)"
        class="p-4 rounded-2xl border-2 transition-all duration-300 flex flex-col items-center justify-center gap-2 group relative overflow-hidden"
        :class="activePair.id === p.id ? 'border-emerald-500 bg-emerald-500/10' : 'border-slate-800 bg-slate-900/40 hover:border-slate-700'">
        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-emerald-400">{{ p.label }}</span>
        <span class="text-xl font-black text-white">{{ p.primary }}/{{ p.secondary }}</span>
        <div v-if="liveRates[p.primary]" class="text-[9px] font-bold text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded-full">Live</div>
        <div v-if="activePair.id === p.id" class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500 shadow-[0_0_10px_#10b981]"></div>
      </button>
    </div>

    <!-- Actions Bar -->
    <div class="flex justify-between items-center no-print px-2">
       <div class="text-emerald-400 text-xs font-black bg-emerald-500/10 px-4 py-2 rounded-xl border border-emerald-500/20">
          دۆخی کاراکردن: {{ activePair.label }}
       </div>
       <button @click="fetchLiveRates" :disabled="loadingRates" class="flex items-center gap-2 px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs font-black text-slate-300 hover:text-emerald-400 transition-all">
          <svg :class="{'animate-spin': loadingRates}" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          {{ loadingRates ? 'وەرگرتنی نرخەکان...' : 'وەرگرتنی نرخ لە بازاڕەوە' }}
       </button>
    </div>

    <!-- Main Trading Form -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 no-print">
      <!-- BUY BOX -->
      <div class="bg-[#0f172a]/60 backdrop-blur-xl rounded-[2rem] border-2 border-emerald-500/30 overflow-hidden shadow-2xl">
         <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-6 flex justify-between items-center">
            <h2 class="text-xl font-black text-white">کڕینی {{ activePair.primary }}</h2>
            <div class="bg-white/20 px-4 py-1 rounded-full text-[10px] font-black text-white">BUY MODE</div>
         </div>
         <div class="p-8 space-y-5" dir="rtl">
            <div class="grid grid-cols-2 gap-4">
               <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">بڕی {{ activePair.primary }}</label>
                  <input v-model="buyForm.primary_amount" @input="calculate('buy', 'primary')" type="number" placeholder="0.00" 
                    class="w-full bg-slate-900 border-2 border-slate-800 rounded-2xl p-4 text-2xl font-black text-white focus:border-emerald-500 outline-none transition-all shadow-inner" />
               </div>
               <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">نرخ ({{ activePair.rateLabel }})</label>
                  <input v-model="buyForm.rate" @input="calculate('buy', 'rate')" type="number" step="any"
                    class="w-full bg-slate-900 border-2 border-slate-800 rounded-2xl p-4 text-2xl font-black text-emerald-400 focus:border-emerald-500 outline-none transition-all shadow-inner" />
               </div>
            </div>
            <div class="space-y-2">
               <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">کۆی گشتی بە {{ activePair.secondary }}</label>
               <input v-model="buyForm.secondary_amount" @input="calculate('buy', 'secondary')" type="number" step="any"
                class="w-full bg-emerald-500/5 text-3xl font-black text-white border-2 border-emerald-500/20 rounded-2xl p-6 focus:border-emerald-500 outline-none" />
            </div>

            <!-- Smart Search Account -->
            <div class="relative">
               <label class="text-[10px] font-black text-slate-400 uppercase block mb-2">لە حسابی (کۆد یان ناو)</label>
               <input v-model="accountSearch" @focus="showResults = 'buy'" @input="filterAccounts" type="text" placeholder="بگەرێ بۆ حسابەکە..."
                 class="w-full bg-slate-900 text-white border-2 border-slate-800 rounded-2xl p-4 font-bold outline-none focus:border-emerald-500" />
               <div v-if="showResults === 'buy' && filteredAccounts.length" class="absolute top-full left-0 w-full mt-2 bg-slate-900 border-2 border-emerald-500 rounded-2xl z-50 max-h-48 overflow-y-auto shadow-2xl">
                  <div v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc, 'buy')" class="p-4 border-b border-slate-800 hover:bg-emerald-500/10 cursor-pointer flex justify-between items-center group">
                     <span class="font-black text-white group-hover:text-emerald-400 transition-colors">{{ acc.name }}</span>
                     <span class="text-emerald-500 font-mono text-xs">{{ acc.code }}</span>
                  </div>
               </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
               <input v-model="buyForm.client_name" type="text" placeholder="ناوی کڕیار" class="bg-slate-900 border-2 border-slate-800 rounded-xl p-3 text-white focus:border-emerald-500 outline-none" />
               <input v-model="buyForm.note" type="text" placeholder="تێبینی..." class="bg-slate-900 border-2 border-slate-800 rounded-xl p-3 text-white focus:border-emerald-500 outline-none" />
            </div>
            <button @click="submitTrade('buy')" class="w-full py-5 bg-emerald-500 text-white font-black text-xl rounded-2xl shadow-lg shadow-emerald-500/20 hover:-translate-y-1 transition-all">تۆمارکردنی کڕین</button>
         </div>
      </div>

      <!-- SELL BOX -->
      <div class="bg-[#0f172a]/60 backdrop-blur-xl rounded-[2rem] border-2 border-rose-500/30 overflow-hidden shadow-2xl">
         <div class="bg-gradient-to-r from-rose-500 to-pink-500 p-6 flex justify-between items-center">
            <h2 class="text-xl font-black text-white">فرۆشتنی {{ activePair.primary }}</h2>
            <div class="bg-white/20 px-4 py-1 rounded-full text-[10px] font-black text-white">SELL MODE</div>
         </div>
         <div class="p-8 space-y-5" dir="rtl">
            <div class="grid grid-cols-2 gap-4">
               <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">بڕی {{ activePair.primary }}</label>
                  <input v-model="sellForm.primary_amount" @input="calculate('sell', 'primary')" type="number" placeholder="0.00" 
                    class="w-full bg-slate-900 border-2 border-slate-800 rounded-2xl p-4 text-2xl font-black text-white focus:border-rose-500 outline-none transition-all shadow-inner" />
               </div>
               <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">نرخ ({{ activePair.rateLabel }})</label>
                  <input v-model="sellForm.rate" @input="calculate('sell', 'rate')" type="number" step="any"
                    class="w-full bg-slate-900 border-2 border-slate-800 rounded-2xl p-4 text-2xl font-black text-rose-400 focus:border-rose-500 outline-none transition-all shadow-inner" />
               </div>
            </div>
            <div class="space-y-2">
               <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">کۆی گشتی بە {{ activePair.secondary }}</label>
               <input v-model="sellForm.secondary_amount" @input="calculate('sell', 'secondary')" type="number" step="any"
                class="w-full bg-rose-500/5 text-3xl font-black text-white border-2 border-rose-500/20 rounded-2xl p-6 focus:border-rose-500 outline-none" />
            </div>

            <div class="relative">
               <label class="text-[10px] font-black text-slate-400 uppercase block mb-2">بۆ حسابی (کۆد یان ناو)</label>
               <input v-model="accountSearchSell" @focus="showResults = 'sell'" @input="filterAccounts" type="text" placeholder="بگەرێ بۆ حسابەکە..."
                 class="w-full bg-slate-900 text-white border-2 border-slate-800 rounded-2xl p-4 font-bold outline-none focus:border-rose-500" />
               <div v-if="showResults === 'sell' && filteredAccounts.length" class="absolute top-full left-0 w-full mt-2 bg-slate-900 border-2 border-rose-500 rounded-2xl z-50 max-h-48 overflow-y-auto shadow-2xl">
                  <div v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc, 'sell')" class="p-4 border-b border-slate-800 hover:bg-rose-500/10 cursor-pointer flex justify-between items-center group">
                     <span class="font-black text-white group-hover:text-rose-400 transition-colors">{{ acc.name }}</span>
                     <span class="text-rose-500 font-mono text-xs">{{ acc.code }}</span>
                  </div>
               </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
               <input v-model="sellForm.client_name" type="text" placeholder="ناوی کڕیار" class="bg-slate-900 border-2 border-slate-800 rounded-xl p-3 text-white focus:border-rose-500 outline-none" />
               <input v-model="sellForm.note" type="text" placeholder="تێبینی..." class="bg-slate-900 border-2 border-slate-800 rounded-xl p-3 text-white focus:border-rose-500 outline-none" />
            </div>
            <button @click="submitTrade('sell')" class="w-full py-5 bg-rose-500 text-white font-black text-xl rounded-2xl shadow-lg shadow-rose-500/20 hover:-translate-y-1 transition-all">تۆمارکردنی فرۆشتن</button>
         </div>
      </div>
    </div>

    <!-- Transaction Table -->
    <div class="bg-[#1e293b]/60 backdrop-blur-xl rounded-3xl border border-slate-700/50 overflow-hidden shadow-xl no-print">
       <div class="p-6 border-b border-slate-800 flex justify-between items-center">
          <h3 class="text-lg font-black text-white">دوایین مەعامەلەکان (سات بە سات)</h3>
       </div>
       <div class="overflow-x-auto">
          <table class="w-full text-right" dir="rtl">
             <thead class="bg-[#0f172a] shadow-md border-b border-slate-800">
                <tr>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase">کات (چرکە)</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase">حساب</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase">جۆر</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase">بڕ</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase">نرخ</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase">کۆی گشتی</th>
                   <th class="px-6 py-4 text-[10px] font-black text-emerald-500 uppercase">قازانج</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase text-center">کردار</th>
                </tr>
             </thead>
             <tbody class="divide-y divide-slate-800/50">
                <tr v-for="t in transactions" :key="t.id" class="hover:bg-slate-400/5 transition-colors group">
                   <td class="px-6 py-4 text-xs font-bold text-slate-400">{{ formatFullTime(t.created_at) }}</td>
                   <td class="px-6 py-4 font-black text-white">{{ t.account?.name }}</td>
                   <td class="px-6 py-4">
                      <span class="px-3 py-1 rounded-full text-[10px] font-black" :class="t.type === 'buy' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'">{{ t.type === 'buy' ? 'کڕین' : 'فرۆشتن' }}</span>
                   </td>
                   <td class="px-6 py-4 font-black text-white">{{ formatNum(t.primary_amount) }} {{ t.primary_currency }}</td>
                   <td class="px-6 py-4 font-bold text-slate-400">{{ formatNum(t.rate) }}</td>
                   <td class="px-6 py-4 font-black text-slate-200">{{ formatNum(t.secondary_amount) }} {{ t.secondary_currency }}</td>
                   <td class="px-6 py-4 font-black text-emerald-400 tracking-tight">+{{ formatNum(t.profit) }}</td>
                   <td class="px-6 py-4">
                      <div class="flex justify-center gap-2">
                         <button @click="printReceipt(t)" class="p-2 bg-slate-800 text-emerald-400 rounded-lg hover:bg-emerald-500 hover:text-white transition-all shadow-sm" title="چاپکردنی وەسڵ">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h12z"/></svg>
                         </button>
                         <button @click="deleteTransaction(t.id)" class="p-2 bg-slate-800 text-rose-400 rounded-lg hover:bg-rose-500 hover:text-white transition-all shadow-sm" title="سڕینەوە">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                         </button>
                      </div>
                   </td>
                </tr>
             </tbody>
          </table>
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2'

const pairs = [
  { id: 1, primary: 'USD', secondary: 'IQD', label: 'دینار - دۆلار', multiplier: 0.01, rateLabel: 'بۆ هەر 100$' },
  { id: 2, primary: 'USD', secondary: 'IRR', label: 'تمەن - دۆلار', multiplier: 0.01, rateLabel: 'بۆ هەر 100$' },
  { id: 3, primary: 'GBP', secondary: 'USD', label: 'پاوەن - دۆلار', multiplier: 1, rateLabel: 'بۆ هەر 1 پاوەن' },
  { id: 4, primary: 'EUR', secondary: 'USD', label: 'یۆرۆ - دۆلار', multiplier: 1, rateLabel: 'بۆ هەر 1 یۆرۆ' },
  { id: 5, primary: 'TRY', secondary: 'USD', label: 'لیرە - دۆلار', multiplier: 0.01, rateLabel: 'بۆ هەر 100 لیرە' },
]

const activePair = ref(pairs[0])
const liveRates = ref({})
const loadingRates = ref(false)
const accounts = ref([])
const transactions = ref([])
const showResults = ref(null)
const accountSearch = ref('')
const accountSearchSell = ref('')

const buyForm = ref({ primary_amount: null, rate: 151000, secondary_amount: null, account_id: null, client_name: '', note: '' })
const sellForm = ref({ primary_amount: null, rate: 152000, secondary_amount: null, account_id: null, client_name: '', note: '' })

async function fetchLiveRates() {
  loadingRates.value = true
  try {
    const res = await fetch('https://api.exchangerate-api.com/v4/latest/USD')
    const data = await res.json()
    liveRates.value = data.rates
    updateSuggestedRates()
    Swal.fire({ icon: 'success', title: 'نرخەکان نوێ کرانەوە', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false })
  } catch (e) {
    console.error('Failed to fetch rates', e)
  } finally {
    loadingRates.value = false
  }
}

function updateSuggestedRates() {
  if (activePair.value.primary === 'USD' && activePair.value.secondary === 'IQD') {
    buyForm.value.rate = 151000
    sellForm.value.rate = 152000
  } else if (liveRates.value[activePair.value.primary]) {
     const marketRate = liveRates.value[activePair.value.secondary] / liveRates.value[activePair.value.primary]
     buyForm.value.rate = (marketRate * 0.995).toFixed(4)
     sellForm.value.rate = (marketRate * 1.005).toFixed(4)
  }
}

function selectPair(p) {
  activePair.value = p
  updateSuggestedRates()
}

function calculate(type, source) {
  const form = type === 'buy' ? buyForm.value : sellForm.value
  const m = activePair.value.multiplier
  if (source === 'primary' || source === 'rate') {
    form.secondary_amount = ((form.primary_amount * m) * form.rate).toFixed(2)
  } else {
    form.primary_amount = ((form.secondary_amount / form.rate) / m).toFixed(2)
  }
}

async function fetchData() {
  const [accRes, transRes] = await Promise.all([
    axios.get('/accounts?per_page=1000'),
    axios.get('/exchanges')
  ])
  accounts.value = accRes.data.data
  transactions.value = transRes.data.data
}

const filteredAccounts = computed(() => {
  const q = (showResults.value === 'buy' ? accountSearch.value : accountSearchSell.value).toLowerCase()
  if (!q) return []
  return accounts.value.filter(a => a.name.toLowerCase().includes(q) || a.code.toString().includes(q)).slice(0, 10)
})

function selectAccount(acc, type) {
  if (type === 'buy') { buyForm.value.account_id = acc.id; accountSearch.value = acc.name }
  else { sellForm.value.account_id = acc.id; accountSearchSell.value = acc.name }
  showResults.value = null
}

async function submitTrade(type) {
  const form = type === 'buy' ? buyForm.value : sellForm.value
  if (!form.account_id) return Swal.fire('هەڵە', 'تکایە حسابێک دیاری بکە', 'error')
  
  try {
    const payload = { ...form, type, pair: `${activePair.value.primary}/${activePair.value.secondary}`, primary_currency: activePair.value.primary, secondary_currency: activePair.value.secondary }
    const { data } = await axios.post('/exchanges', payload)
    transactions.value.unshift(data)
    
    // Clear amounts
    form.primary_amount = null
    form.secondary_amount = null
    
    Swal.fire({ icon: 'success', title: 'تۆمار کرا', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false })
  } catch (e) {
    Swal.fire('هەڵە', 'سێرڤەر وەڵامی نییە', 'error')
  }
}

async function deleteTransaction(id) {
  const result = await Swal.fire({
    title: 'دڵنیای؟',
    text: "ئەم مەعامەلەیە دەسڕێتەوە و کاریگەری لەسەر بالانس نامێنێت!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'بەڵێ، بسڕەوە',
    cancelButtonText: 'نەخێر'
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/exchanges/${id}`)
      transactions.value = transactions.value.filter(t => t.id !== id)
      Swal.fire('سڕایەوە', 'مەعامەلەکە بە سەرکەوتوویی سڕایەوە', 'success')
    } catch (e) {
      Swal.fire('هەڵە', 'نەکرا مەعامەلەکە بسڕدرێتەوە', 'error')
    }
  }
}

function printReceipt(t) {
  window.open(`/print-invoice?id=${t.id}&type=${t.type}&client=${t.client_name || t.account?.name}`, '_blank')
}

const formatNum = (val) => new Intl.NumberFormat().format(val)
const formatFullTime = (dateStr) => {
  const d = new Date(dateStr)
  return d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
}

onMounted(() => {
  fetchData()
  fetchLiveRates()
})
</script>

<style scoped>
input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>
