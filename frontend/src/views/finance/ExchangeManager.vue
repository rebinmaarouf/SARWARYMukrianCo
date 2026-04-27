<template>
  <div class="space-y-8 animate-fade-in max-w-[1600px] mx-auto pb-20">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-slate-900/40 backdrop-blur-3xl p-8 rounded-[2.5rem] border border-white/5">
      <div dir="rtl" class="text-right">
        <h1 class="text-3xl font-black text-white tracking-tight">تێرمیناڵی ئاڵوگۆڕی دراو</h1>
        <p class="text-slate-500 font-medium mt-1">کڕین و فرۆشتنی خێرا بەپێی نرخەکانی بازاڕ</p>
      </div>
      <div class="flex items-center gap-4">
        <div class="bg-slate-950 px-6 py-3 rounded-2xl border border-white/5 flex items-center gap-4">
          <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Market Status</span>
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-black text-emerald-400">Live & Connected</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Pair Selector -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
      <button v-for="p in pairs" :key="p.id" @click="selectPair(p)"
        class="relative group p-6 rounded-[2rem] border-2 transition-all duration-500 overflow-hidden"
        :class="activePair.id === p.id ? 'border-emerald-500 bg-emerald-500/5' : 'border-white/5 bg-slate-900/40 hover:border-white/10'">
        <div class="relative z-10 flex flex-col items-center gap-3">
          <span class="text-[10px] font-black uppercase tracking-[0.2em] transition-colors" :class="activePair.id === p.id ? 'text-emerald-400' : 'text-slate-500'">{{ p.label }}</span>
          <span class="text-2xl font-black text-white tracking-tighter">{{ p.primary }}/{{ p.secondary }}</span>
        </div>
        <div v-if="activePair.id === p.id" class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent"></div>
      </button>
    </div>

    <!-- Main Trading Engine -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 h-full">
      <!-- BUY PANEL -->
      <div class="group relative bg-slate-900/40 backdrop-blur-3xl rounded-[3rem] border border-white/5 p-10 overflow-hidden transition-all hover:border-emerald-500/30">
        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
        
        <div class="flex justify-between items-center mb-10 relative z-10">
          <div class="bg-emerald-500 text-slate-950 px-5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">Buy Mode</div>
          <h2 class="text-2xl font-black text-white">کڕینی {{ activePair.primary }}</h2>
        </div>

        <div class="space-y-8 relative z-10" dir="rtl">
          <!-- Amounts Row -->
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بڕی {{ activePair.primary }}</label>
              <input v-model="buyForm.primary_amount" @input="calculate('buy', 'primary')" type="number" 
                class="w-full bg-slate-950 border border-white/5 rounded-2xl p-6 text-3xl font-black text-white focus:border-emerald-500 outline-none transition-all shadow-2xl" />
            </div>
            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">نرخ ({{ activePair.rateLabel }})</label>
              <input v-model="buyForm.rate" @input="calculate('buy', 'rate')" type="number" step="any"
                class="w-full bg-slate-950 border border-white/5 rounded-2xl p-6 text-3xl font-black text-emerald-400 focus:border-emerald-500 outline-none transition-all shadow-2xl" />
            </div>
          </div>

          <!-- Total Display -->
          <div class="bg-slate-950/80 border border-white/5 p-8 rounded-[2rem] space-y-2">
             <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block text-center">کۆی گشتی بە {{ activePair.secondary }}</label>
             <div class="text-4xl font-black text-white text-center tracking-tighter">{{ formatNum(buyForm.secondary_amount) }}</div>
          </div>

          <!-- Advanced Vault Selection -->
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بۆ سندوقی (هاتن)</label>
              <select v-model="buyForm.vault_to_id" class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 text-white font-bold outline-none focus:border-emerald-500">
                <option v-for="v in vaults" :key="v.id" :value="v.id">{{ v.name }}</option>
              </select>
            </div>
            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">لە سندوقی (دەرچوون)</label>
              <select v-model="buyForm.vault_from_id" class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 text-white font-bold outline-none focus:border-emerald-500">
                <option v-for="v in vaults" :key="v.id" :value="v.id">{{ v.name }}</option>
              </select>
            </div>
          </div>

          <!-- Client & Account -->
          <div class="space-y-3">
             <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">لە حسابی (کڕیار / وەکیل)</label>
             <div class="relative group">
                <input v-model="accountSearchBuy" @focus="showResults = 'buy'" @input="searchAccounts('buy')" type="text" placeholder="بگەڕێ بۆ حساب..."
                  class="w-full bg-slate-950 border border-white/5 rounded-2xl p-5 text-white font-bold focus:border-emerald-500 outline-none" />
                <div v-if="showResults === 'buy' && filteredAccounts.length" class="absolute top-full left-0 w-full mt-4 bg-slate-900 border border-emerald-500/50 rounded-2xl z-50 shadow-3xl overflow-hidden backdrop-blur-xl">
                  <div v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc, 'buy')" class="p-5 border-b border-white/5 hover:bg-emerald-500/10 cursor-pointer flex justify-between items-center group/item transition-colors">
                    <span class="font-black text-white group-hover/item:text-emerald-400">{{ acc.name }}</span>
                    <span class="text-[10px] font-black text-emerald-500/60 font-mono">{{ acc.code }}</span>
                  </div>
                </div>
             </div>
          </div>

          <button @click="submitTrade('buy')" :disabled="loading"
            class="w-full py-6 bg-emerald-500 text-slate-950 font-black text-2xl rounded-[2rem] shadow-2xl shadow-emerald-500/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-50">
            {{ loading ? 'خەریکی تۆمارکردنە...' : 'تۆمارکردنی کڕین' }}
          </button>
        </div>
      </div>

      <!-- SELL PANEL -->
      <div class="group relative bg-slate-900/40 backdrop-blur-3xl rounded-[3rem] border border-white/5 p-10 overflow-hidden transition-all hover:border-rose-500/30">
        <div class="absolute top-0 right-0 w-64 h-64 bg-rose-500/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
        
        <div class="flex justify-between items-center mb-10 relative z-10">
          <div class="bg-rose-500 text-white px-5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">Sell Mode</div>
          <h2 class="text-2xl font-black text-white">فرۆشتنی {{ activePair.primary }}</h2>
        </div>

        <div class="space-y-8 relative z-10" dir="rtl">
          <!-- Amounts Row -->
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بڕی {{ activePair.primary }}</label>
              <input v-model="sellForm.primary_amount" @input="calculate('sell', 'primary')" type="number" 
                class="w-full bg-slate-950 border border-white/5 rounded-2xl p-6 text-3xl font-black text-white focus:border-rose-500 outline-none transition-all shadow-2xl" />
            </div>
            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">نرخ ({{ activePair.rateLabel }})</label>
              <input v-model="sellForm.rate" @input="calculate('sell', 'rate')" type="number" step="any"
                class="w-full bg-slate-950 border border-white/5 rounded-2xl p-6 text-3xl font-black text-rose-400 focus:border-rose-500 outline-none transition-all shadow-2xl" />
            </div>
          </div>

          <!-- Total Display -->
          <div class="bg-slate-950/80 border border-white/5 p-8 rounded-[2rem] space-y-2">
             <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block text-center">کۆی گشتی بە {{ activePair.secondary }}</label>
             <div class="text-4xl font-black text-white text-center tracking-tighter">{{ formatNum(sellForm.secondary_amount) }}</div>
          </div>

          <!-- Advanced Vault Selection -->
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بۆ سندوقی (هاتن)</label>
              <select v-model="sellForm.vault_to_id" class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 text-white font-bold outline-none focus:border-rose-500">
                <option v-for="v in vaults" :key="v.id" :value="v.id">{{ v.name }}</option>
              </select>
            </div>
            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">لە سندوقی (دەرچوون)</label>
              <select v-model="sellForm.vault_from_id" class="w-full bg-slate-950 border border-white/5 rounded-2xl p-4 text-white font-bold outline-none focus:border-rose-500">
                <option v-for="v in vaults" :key="v.id" :value="v.id">{{ v.name }}</option>
              </select>
            </div>
          </div>

          <!-- Client & Account -->
          <div class="space-y-3">
             <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بۆ حسابی (کڕیار / وەکیل)</label>
             <div class="relative group">
                <input v-model="accountSearchSell" @focus="showResults = 'sell'" @input="searchAccounts('sell')" type="text" placeholder="بگەڕێ بۆ حساب..."
                  class="w-full bg-slate-950 border border-white/5 rounded-2xl p-5 text-white font-bold focus:border-rose-500 outline-none" />
                <div v-if="showResults === 'sell' && filteredAccounts.length" class="absolute top-full left-0 w-full mt-4 bg-slate-900 border border-rose-500/50 rounded-2xl z-50 shadow-3xl overflow-hidden backdrop-blur-xl">
                  <div v-for="acc in filteredAccounts" :key="acc.id" @click="selectAccount(acc, 'sell')" class="p-5 border-b border-white/5 hover:bg-rose-500/10 cursor-pointer flex justify-between items-center group/item transition-colors">
                    <span class="font-black text-white group-hover/item:text-rose-400">{{ acc.name }}</span>
                    <span class="text-[10px] font-black text-rose-500/60 font-mono">{{ acc.code }}</span>
                  </div>
                </div>
             </div>
          </div>

          <button @click="submitTrade('sell')" :disabled="loading"
            class="w-full py-6 bg-rose-500 text-white font-black text-2xl rounded-[2rem] shadow-2xl shadow-rose-500/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-50">
            {{ loading ? 'خەریکی تۆمارکردنە...' : 'تۆمارکردنی فرۆشتن' }}
          </button>
        </div>
      </div>
    </div>

    <!-- History Table -->
    <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] overflow-hidden shadow-2xl">
      <div class="p-8 border-b border-white/5 flex justify-between items-center">
        <h3 class="text-xl font-black text-white">دوایین مەعامەلات</h3>
        <div class="flex gap-2 bg-slate-950 p-1 rounded-xl">
           <button v-for="f in ['all', 'buy', 'sell']" :key="f" @click="tableFilter = f"
             class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all"
             :class="tableFilter === f ? 'bg-white/10 text-white' : 'text-slate-600 hover:text-slate-400'">
             {{ f }}
           </button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-right" dir="rtl">
          <thead>
            <tr class="bg-slate-950/40 text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] border-b border-white/5">
              <th class="px-10 py-6">کات</th>
              <th class="px-10 py-6 text-center">جۆر</th>
              <th class="px-10 py-6">کڕیار / حساب</th>
              <th class="px-10 py-6">بڕی ئاڵوگۆڕ</th>
              <th class="px-10 py-6">نرخی بازاڕ</th>
              <th class="px-10 py-6 text-left">قازانج (IQD)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="t in filteredTransactions" :key="t.id" class="group hover:bg-white/[0.01] transition-all">
              <td class="px-10 py-6 text-slate-500 font-bold text-xs">{{ formatFullTime(t.created_at) }}</td>
              <td class="px-10 py-6 text-center">
                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest"
                  :class="t.type === 'buy' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border border-rose-500/20'">
                  {{ t.type === 'buy' ? 'BUY' : 'SELL' }}
                </span>
              </td>
              <td class="px-10 py-6">
                <div class="flex flex-col">
                  <span class="text-white font-black group-hover:text-emerald-400 transition-colors">{{ t.client_name || t.account?.name }}</span>
                  <span class="text-[9px] text-slate-600 font-black tracking-widest uppercase">{{ t.account?.name }}</span>
                </div>
              </td>
              <td class="px-10 py-6">
                 <div class="flex flex-col">
                    <span class="text-white font-black text-lg">{{ formatNum(t.primary_amount) }} <span class="text-[10px] text-slate-500">{{ t.primary_currency }}</span></span>
                    <span class="text-xs text-slate-500 font-bold tracking-tighter">{{ formatNum(t.secondary_amount) }} {{ t.secondary_currency }}</span>
                 </div>
              </td>
              <td class="px-10 py-6 font-black text-slate-400 font-mono">{{ formatNum(t.rate) }}</td>
              <td class="px-10 py-6 text-left font-black text-emerald-500 text-lg">
                {{ t.profit > 0 ? '+' + formatNum(t.profit) : '—' }}
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

const pairs = ref([])
const activePair = ref({ id: 0, primary: 'USD', secondary: 'IQD', label: 'دینار - دۆلار', multiplier: 0.01, rateLabel: 'بۆ هەر 100$' })

async function generatePairs() {
  try {
    const { data } = await axios.get('/currencies')
    const activeCurrencies = data.data || data
    
    // We assume USD is our reference for pairs (standard for exchange offices)
    const base = activeCurrencies.find(c => c.code === 'USD') || activeCurrencies[0]
    
    const newPairs = []
    activeCurrencies.forEach(c => {
      if (c.id !== base.id) {
        newPairs.push({
          id: c.id,
          primary: base.code,
          secondary: c.code,
          label: `${c.name} - ${base.name}`,
          multiplier: c.code === 'IQD' || c.code === 'IRR' || c.code === 'TRY' ? 0.01 : 1,
          rateLabel: c.code === 'IQD' || c.code === 'IRR' || c.code === 'TRY' ? `بۆ هەر 100${base.code}` : `بۆ هەر 1 ${base.code}`
        })
      }
    })
    pairs.value = newPairs
    if (newPairs.length > 0) activePair.value = newPairs[0]
  } catch (e) { console.error(e) }
}
const accounts = ref([])
const vaults = ref([])
const transactions = ref([])
const loading = ref(false)
const showResults = ref(null)
const accountSearchBuy = ref('')
const accountSearchSell = ref('')
const tableFilter = ref('all')

const buyForm = ref({ primary_amount: null, rate: 151000, secondary_amount: 0, account_id: null, vault_from_id: null, vault_to_id: null, client_name: '', note: '' })
const sellForm = ref({ primary_amount: null, rate: 152000, secondary_amount: 0, account_id: null, vault_from_id: null, vault_to_id: null, client_name: '', note: '' })

const filteredTransactions = computed(() => {
  if (tableFilter.value === 'all') return transactions.value
  return transactions.value.filter(t => t.type === tableFilter.value)
})

const filteredAccounts = computed(() => {
  const q = (showResults.value === 'buy' ? accountSearchBuy.value : accountSearchSell.value).toLowerCase()
  if (!q) return []
  return accounts.value.filter(a => a.name.toLowerCase().includes(q) || a.code.toString().includes(q)).slice(0, 8)
})

function searchAccounts(type) {
  showResults.value = type
}

function selectPair(p) {
  activePair.value = p
  if (p.primary === 'USD' && p.secondary === 'IQD') {
    buyForm.value.rate = 151000
    sellForm.value.rate = 152000
  } else {
    buyForm.value.rate = 1
    sellForm.value.rate = 1
  }
  calculate('buy', 'primary')
  calculate('sell', 'primary')
}

function calculate(type, source) {
  const form = type === 'buy' ? buyForm.value : sellForm.value
  const m = activePair.value.multiplier
  if (source === 'primary' || source === 'rate') {
    form.secondary_amount = (form.primary_amount * m) * form.rate
  } else {
    form.primary_amount = (form.secondary_amount / form.rate) / m
  }
}

async function fetchData() {
  try {
    const [accRes, transRes] = await Promise.all([
      axios.get('/accounts?per_page=1000'),
      axios.get('/exchanges')
    ])
    accounts.value = accRes.data.data || accRes.data
    vaults.value = accounts.value.filter(a => a.type === 'vault')
    transactions.value = transRes.data.data || transRes.data
    
    // Auto-select first vaults
    if (vaults.value.length >= 1) {
      buyForm.value.vault_from_id = vaults.value[0].id
      buyForm.value.vault_to_id = vaults.value[0].id
      sellForm.value.vault_from_id = vaults.value[0].id
      sellForm.value.vault_to_id = vaults.value[0].id
    }
  } catch (e) { console.error(e) }
}

function selectAccount(acc, type) {
  if (type === 'buy') { buyForm.value.account_id = acc.id; accountSearchBuy.value = acc.name }
  else { sellForm.value.account_id = acc.id; accountSearchSell.value = acc.name }
  showResults.value = null
}

async function submitTrade(type) {
  const form = type === 'buy' ? buyForm.value : sellForm.value
  if (!form.account_id || !form.vault_from_id || !form.vault_to_id) {
    return Swal.fire({ icon: 'error', title: 'تکایە هەموو خانەکان پڕ بکەرەوە', background: '#0f172a', color: '#fff' })
  }

  loading.value = true
  try {
    const payload = { 
      ...form, 
      type, 
      pair: `${activePair.value.primary}/${activePair.value.secondary}`,
      primary_currency: activePair.value.primary,
      secondary_currency: activePair.value.secondary
    }
    const { data } = await axios.post('/exchanges', payload)
    transactions.value.unshift(data)
    
    // Reset forms partially
    form.primary_amount = null
    form.secondary_amount = 0
    
    Swal.fire({ icon: 'success', title: 'مەعامەلەکە بە سەرکەوتوویی تۆمارکرا', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false })
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'هەڵە لە تۆمارکردن', text: e.response?.data?.error || 'سێرڤەر وەڵامی نییە', background: '#0f172a', color: '#fff' })
  } finally {
    loading.value = false
  }
}

const formatNum = (val) => new Intl.NumberFormat().format(val || 0)
const formatFullTime = (dateStr) => {
  const d = new Date(dateStr)
  return d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' })
}

onMounted(() => {
  fetchData()
  generatePairs()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>
