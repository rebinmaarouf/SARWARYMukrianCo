<template>
  <div class="space-y-6">
    <!-- Quick Type Selector -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 no-print">
      <button v-for="p in pairs" :key="p.id" 
        @click="activePair = p"
        class="p-4 rounded-2xl border-2 transition-all duration-300 flex flex-col items-center justify-center gap-2 group relative overflow-hidden"
        :class="activePair.id === p.id ? 'border-emerald-500 bg-emerald-500/10' : 'border-slate-800 bg-slate-900/40 hover:border-slate-700'">
        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-emerald-400">{{ p.label }}</span>
        <span class="text-xl font-black text-white">{{ p.primary }}/{{ p.secondary }}</span>
        <div v-if="activePair.id === p.id" class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500 shadow-[0_0_10px_#10b981]"></div>
      </button>
    </div>

    <!-- Main Trading Form (Dual Box) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 no-print">
      <!-- BUY BOX (Green Theme) -->
      <div class="bg-[#0f172a]/60 backdrop-blur-xl rounded-[2rem] border-2 border-emerald-500/30 overflow-hidden shadow-2xl shadow-emerald-950/20 group hover:border-emerald-500/50 transition-all">
         <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-6 flex justify-between items-center">
            <h2 class="text-xl font-black text-white tracking-tight">کڕینی {{ activePair.primary }}</h2>
            <div class="bg-white/20 px-4 py-1 rounded-full text-[10px] font-black text-white uppercase">Buy Mode</div>
         </div>
         <div class="p-8 space-y-5" dir="rtl">
            <div class="grid grid-cols-2 gap-4">
               <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">بڕی {{ activePair.primary }}</label>
                  <input v-model="buyForm.primary_amount" @input="calcBuy('primary')" type="number" step="any" placeholder="0.00" 
                    class="w-full bg-slate-900/80 text-2xl font-black text-white border-2 border-slate-800 rounded-2xl p-4 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" />
               </div>
               <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">نرخ (بۆ هەر 100 دۆلار)</label>
                  <input v-model="buyForm.rate" @input="calcBuy('rate')" type="number" step="any"
                    class="w-full bg-slate-900/80 text-2xl font-black text-emerald-400 border-2 border-slate-800 rounded-2xl p-4 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" />
               </div>
            </div>
            <div class="space-y-2">
               <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">کۆی گشتی بە {{ activePair.secondary }}</label>
               <input v-model="buyForm.secondary_amount" @input="calcBuy('secondary')" type="number" step="any"
                class="w-full bg-emerald-500/5 text-3xl font-black text-white border-2 border-emerald-500/20 rounded-2xl p-6 focus:border-emerald-500 outline-none transition-all" />
            </div>

            <!-- Smart Search Account -->
            <div class="relative">
               <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">لە حسابی (کۆد یان ناو بنووسە)</label>
               <div class="relative">
                  <input v-model="accountSearch" @focus="showResults = 'buy'" @input="filterAccounts" type="text" placeholder="بگەرێ..."
                    class="w-full bg-slate-900 text-white border-2 border-slate-800 rounded-2xl p-4 font-bold outline-none focus:border-emerald-500" />
                  <div v-if="showResults === 'buy' && filteredAccounts.length" class="absolute top-full left-0 w-full mt-2 bg-slate-900 border-2 border-emerald-500/50 rounded-2xl z-50 max-h-60 overflow-y-auto shadow-2xl">
                     <div v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc, 'buy')"
                        class="p-4 border-b border-slate-800 hover:bg-emerald-500/10 cursor-pointer flex justify-between items-center transition-colors">
                        <span class="font-black text-white">{{ acc.name }}</span>
                        <span class="font-mono font-bold text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded-lg">{{ acc.code }}</span>
                     </div>
                  </div>
               </div>
               <p v-if="buyForm.account_id" class="text-[11px] font-bold text-emerald-400 mt-2">دیاریکراوە: {{ getAccountName(buyForm.account_id) }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
               <input v-model="buyForm.client_name" type="text" placeholder="ناوی کڕیار" class="w-full bg-slate-900 border-2 border-slate-800 rounded-xl p-3 text-sm text-white outline-none focus:border-emerald-500" />
               <input v-model="buyForm.note" type="text" placeholder="تێبینی..." class="w-full bg-slate-900 border-2 border-slate-800 rounded-xl p-3 text-sm text-white outline-none focus:border-emerald-500" />
            </div>

            <button @click="submitTrade('buy')" class="w-full py-5 bg-emerald-500 text-white font-black text-xl rounded-2xl shadow-lg shadow-emerald-500/30 hover:-translate-y-1 active:scale-95 transition-all">تۆمارکردنی کڕین</button>
         </div>
      </div>

      <!-- SELL BOX (Rose Theme) -->
      <div class="bg-[#0f172a]/60 backdrop-blur-xl rounded-[2rem] border-2 border-rose-500/30 overflow-hidden shadow-2xl shadow-rose-950/20 group hover:border-rose-500/50 transition-all">
         <div class="bg-gradient-to-r from-rose-500 to-pink-500 p-6 flex justify-between items-center">
            <h2 class="text-xl font-black text-white tracking-tight">فرۆشتنی {{ activePair.primary }}</h2>
            <div class="bg-white/20 px-4 py-1 rounded-full text-[10px] font-black text-white uppercase">Sell Mode</div>
         </div>
         <div class="p-8 space-y-5" dir="rtl">
            <div class="grid grid-cols-2 gap-4">
               <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">بڕی {{ activePair.primary }}</label>
                  <input v-model="sellForm.primary_amount" @input="calcSell('primary')" type="number" step="any" placeholder="0.00" 
                    class="w-full bg-slate-900/80 text-2xl font-black text-white border-2 border-slate-800 rounded-2xl p-4 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all outline-none" />
               </div>
               <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">نرخ (بۆ هەر 100 دۆلار)</label>
                  <input v-model="sellForm.rate" @input="calcSell('rate')" type="number" step="any"
                    class="w-full bg-slate-900/80 text-2xl font-black text-rose-400 border-2 border-slate-800 rounded-2xl p-4 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all outline-none" />
               </div>
            </div>
            <div class="space-y-2">
               <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">کۆی گشتی بە {{ activePair.secondary }}</label>
               <input v-model="sellForm.secondary_amount" @input="calcSell('secondary')" type="number" step="any"
                class="w-full bg-rose-500/5 text-3xl font-black text-white border-2 border-rose-500/20 rounded-2xl p-6 focus:border-rose-500 outline-none transition-all" />
            </div>

            <!-- Smart Search Account (Sell) -->
            <div class="relative">
               <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">بۆ حسابی (کۆد یان ناو بنووسە)</label>
               <div class="relative">
                  <input v-model="accountSearchSell" @focus="showResults = 'sell'" @input="filterAccounts" type="text" placeholder="بگەرێ..."
                    class="w-full bg-slate-900 text-white border-2 border-slate-800 rounded-2xl p-4 font-bold outline-none focus:border-rose-500" />
                  <div v-if="showResults === 'sell' && filteredAccounts.length" class="absolute top-full left-0 w-full mt-2 bg-slate-900 border-2 border-rose-500/50 rounded-2xl z-50 max-h-60 overflow-y-auto shadow-2xl">
                     <div v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc, 'sell')"
                        class="p-4 border-b border-slate-800 hover:bg-rose-500/10 cursor-pointer flex justify-between items-center transition-colors">
                        <span class="font-black text-white">{{ acc.name }}</span>
                        <span class="font-mono font-bold text-rose-400 bg-rose-400/10 px-2 py-0.5 rounded-lg">{{ acc.code }}</span>
                     </div>
                  </div>
               </div>
               <p v-if="sellForm.account_id" class="text-[11px] font-bold text-rose-400 mt-2">دیاریکراوە: {{ getAccountName(sellForm.account_id) }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
               <input v-model="sellForm.client_name" type="text" placeholder="ناوی کڕیار" class="w-full bg-slate-900 border-2 border-slate-800 rounded-xl p-3 text-sm text-white outline-none focus:border-rose-500" />
               <input v-model="sellForm.note" type="text" placeholder="تێبینی..." class="w-full bg-slate-900 border-2 border-slate-800 rounded-xl p-3 text-sm text-white outline-none focus:border-rose-500" />
            </div>

            <button @click="submitTrade('sell')" class="w-full py-5 bg-rose-500 text-white font-black text-xl rounded-2xl shadow-lg shadow-rose-500/30 hover:-translate-y-1 active:scale-95 transition-all">تۆمارکردنی فرۆشتن</button>
         </div>
      </div>
    </div>

    <!-- Transaction List (Subform Style) -->
    <div class="bg-[#1e293b]/60 backdrop-blur-xl rounded-3xl border border-slate-700/50 overflow-hidden shadow-xl no-print">
       <div class="p-6 border-b border-slate-800 flex justify-between items-center">
          <h3 class="text-lg font-black text-white">دوایین مەعامەلەکان (ئاڵوگۆڕ)</h3>
          <div class="flex gap-2">
             <button @click="fetchData" class="p-2 bg-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
             </button>
          </div>
       </div>
       <div class="overflow-x-auto overflow-y-auto max-h-[600px]">
          <table class="w-full text-right" dir="rtl">
             <thead class="sticky top-0 bg-[#0f172a] z-10 shadow-md">
                <tr>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">کات</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">حساب</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">کڕیار/تێبینی</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">جۆر</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">بڕ</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">نرخ</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">کۆی گشتی</th>
                   <th class="px-6 py-4 text-[10px] font-black text-emerald-500 uppercase tracking-widest">قازانج</th>
                   <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">کردار</th>
                </tr>
             </thead>
             <tbody class="divide-y divide-slate-800/50">
                <tr v-for="t in transactions" :key="t.id" class="hover:bg-slate-400/5 transition-colors group">
                   <td class="px-6 py-4 text-xs font-bold text-slate-500">{{ formatTime(new Date(t.created_at)) }}</td>
                   <td class="px-6 py-4">
                      <div class="flex flex-col">
                         <span class="font-black text-white text-sm">{{ t.account?.name }}</span>
                         <span class="font-mono text-[10px] text-emerald-400">{{ t.account?.code }}</span>
                      </div>
                   </td>
                   <td class="px-6 py-4">
                      <p class="text-xs font-bold text-white">{{ t.client_name || '---' }}</p>
                      <p class="text-[10px] text-slate-500 italic">{{ t.note || '---' }}</p>
                   </td>
                   <td class="px-6 py-4">
                      <span class="px-3 py-1 rounded-full text-[10px] font-black" :class="t.type === 'buy' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'">
                         {{ t.type === 'buy' ? 'کڕین' : 'فرۆشتن' }}
                      </span>
                   </td>
                   <td class="px-6 py-4 font-black text-white">{{ formatNum(t.primary_amount) }} {{ t.primary_currency }}</td>
                   <td class="px-6 py-4 font-bold text-slate-400 text-sm">{{ formatNum(t.rate) }}</td>
                   <td class="px-6 py-4 font-black text-slate-200">{{ formatNum(t.secondary_amount) }} {{ t.secondary_currency }}</td>
                   <td class="px-6 py-4 font-black text-emerald-400">
                      +{{ formatNum(t.profit) }}
                   </td>
                   <td class="px-6 py-4">
                      <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                         <button @click="printReceipt(t)" class="p-2 bg-slate-800 text-slate-300 rounded-lg hover:bg-emerald-500/20 hover:text-emerald-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h12z"/></svg>
                         </button>
                      </div>
                   </td>
                </tr>
             </tbody>
          </table>
       </div>
       <!-- Pagination -->
       <div v-if="pagination.last_page > 1" class="p-4 border-t border-slate-800 flex justify-center gap-4">
          <button @click="fetchTransactions(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-4 py-2 bg-slate-800 rounded-lg text-white disabled:opacity-30">پێشوو</button>
          <span class="text-slate-400 font-bold self-center">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
          <button @click="fetchTransactions(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-4 py-2 bg-slate-800 rounded-lg text-white disabled:opacity-30">دواتر</button>
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2'

const pairs = [
  { id: 1, primary: 'USD', secondary: 'IQD', label: 'دینار - دۆلار', divider: 100 },
  { id: 2, primary: 'USD', secondary: 'IRR', label: 'تمەن - دۆلار', divider: 100 },
  { id: 3, primary: 'USD', secondary: 'TRY', label: 'لیرە - دۆلار', divider: 100 },
  { id: 4, primary: 'USD', secondary: 'EUR', label: 'یۆرۆ - دۆلار', divider: 1 },
  { id: 5, primary: 'GBP', secondary: 'USD', label: 'پاوەن - دۆلار', divider: 1 },
  { id: 6, primary: 'AED', secondary: 'USD', label: 'درهەم - دۆلار', divider: 1 },
]

const activePair = ref(pairs[0])
const accounts = ref([])
const transactions = ref([])
const pagination = ref({})
const accountSearch = ref('')
const accountSearchSell = ref('')
const showResults = ref(null)

const buyForm = ref({ primary_amount: null, rate: 151000, secondary_amount: null, account_id: null, client_name: '', note: '' })
const sellForm = ref({ primary_amount: null, rate: 150000, secondary_amount: null, account_id: null, client_name: '', note: '' })

async function fetchData() {
  const [accRes] = await Promise.all([axios.get('/accounts?per_page=1000')])
  accounts.value = accRes.data.data
  fetchTransactions(1)
}

async function fetchTransactions(page = 1) {
  const { data } = await axios.get(`/exchanges?page=${page}`)
  transactions.value = data.data
  pagination.value = data
}

const filteredAccounts = computed(() => {
  const q = (showResults.value === 'buy' ? accountSearch.value : accountSearchSell.value).toLowerCase()
  if (!q) return []
  return accounts.value.filter(a => a.name.toLowerCase().includes(q) || a.code.toString().includes(q)).slice(0, 10)
})

function selectAccount(acc, type) {
  if (type === 'buy') {
    buyForm.value.account_id = acc.id
    accountSearch.value = acc.name
  } else {
    sellForm.value.account_id = acc.id
    accountSearchSell.value = acc.name
  }
  showResults.value = null
}

function getAccountName(id) {
  return accounts.value.find(a => a.id === id)?.name
}

// Logic fix for market (Divider 100)
function calcBuy(source) {
  const divider = activePair.value.divider
  if (source === 'primary' || source === 'rate') {
    buyForm.value.secondary_amount = ((buyForm.value.primary_amount / divider) * buyForm.value.rate).toFixed(2)
  } else {
    buyForm.value.primary_amount = ((buyForm.value.secondary_amount / buyForm.value.rate) * divider).toFixed(2)
  }
}

function calcSell(source) {
  const divider = activePair.value.divider
  if (source === 'primary' || source === 'rate') {
    sellForm.value.secondary_amount = ((sellForm.value.primary_amount / divider) * sellForm.value.rate).toFixed(2)
  } else {
    sellForm.value.primary_amount = ((sellForm.value.secondary_amount / sellForm.value.rate) * divider).toFixed(2)
  }
}

async function submitTrade(type) {
  const form = type === 'buy' ? buyForm.value : sellForm.value
  if (!form.account_id || !form.primary_amount) {
    Swal.fire('ئاگاداری', 'تکایە حساب و بڕ هەڵبژێرە', 'warning')
    return
  }

  try {
    const payload = { ...form, type, pair: `${activePair.value.primary}/${activePair.value.secondary}`, primary_currency: activePair.value.primary, secondary_currency: activePair.value.secondary }
    const { data } = await axios.post('/exchanges', payload)
    transactions.value.unshift(data)
    
    // Reset
    if(type === 'buy') { buyForm.value.primary_amount = null; buyForm.value.secondary_amount = null; accountSearch.value = '' }
    else { sellForm.value.primary_amount = null; sellForm.value.secondary_amount = null; accountSearchSell.value = '' }
    
    Swal.fire({ icon: 'success', title: 'تۆمار کرا', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false })
  } catch (e) {
    Swal.fire('هەڵە', 'کێشەیەک لە سێرڤەر هەیە', 'error')
  }
}

const formatNum = (val) => new Intl.NumberFormat().format(val)
const formatTime = (date) => new Intl.DateTimeFormat('en-GB', { hour: '2-digit', minute: '2-digit' }).format(date)

function printReceipt(t) {
  window.open(`/print-invoice?id=${t.id}&type=${t.type}&client=${t.client_name || t.account?.name}`, '_blank')
}

onMounted(fetchData)
</script>

<style scoped>
input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>
