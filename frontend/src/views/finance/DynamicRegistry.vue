<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Universal Currency Switcher (Odoo Style) -->
    <div class="flex flex-wrap gap-3 bg-slate-900/40 p-4 rounded-[2rem] border border-white/5 backdrop-blur-xl no-print">
      <button v-for="c in currencies" :key="c.id" @click="switchCurrency(c)"
        class="flex items-center gap-3 px-6 py-3 rounded-2xl border-2 transition-all duration-500"
        :class="currentFilterId === c.id ? 'border-emerald-500 bg-emerald-500/10' : 'border-white/5 bg-slate-950/40 hover:border-white/10'">
        <span class="w-6 h-6 rounded-lg bg-white/5 flex items-center justify-center font-black text-[10px] text-slate-400">{{ c.symbol }}</span>
        <span class="text-xs font-black" :class="currentFilterId === c.id ? 'text-white' : 'text-slate-500'">{{ c.name }}</span>
        <div v-if="currentFilterId === c.id" class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
      </button>
    </div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-950/40 p-8 rounded-[2.5rem] border border-white/5">
      <div>
        <h1 class="text-3xl font-black text-white tracking-tighter">{{ pageTitle }}</h1>
        <p class="text-slate-500 text-sm font-medium mt-1">تۆمارکردنی حەواڵە و جوڵەی سندوق - {{ activeCurrencyCode }}</p>
      </div>
      <!-- Date Filters -->
      <div class="flex items-center gap-3 bg-slate-900/60 p-2 rounded-2xl border border-white/5 shadow-inner">
        <input v-model="fromDate" type="date" class="bg-transparent text-white border-none text-xs font-bold focus:outline-none px-2" />
        <div class="w-px h-4 bg-white/10"></div>
        <input v-model="toDate" type="date" class="bg-transparent text-white border-none text-xs font-bold focus:outline-none px-2" />
        <button @click="fetchEntries" class="p-2 bg-emerald-500/10 text-emerald-500 rounded-xl hover:bg-emerald-500/20 transition-all">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </button>
      </div>
    </div>

    <!-- Main Data Entry Table -->
    <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl relative">
      <div class="overflow-x-auto overflow-y-visible">
        <table class="w-full text-right border-collapse min-w-[1200px]" dir="rtl">
          <thead>
            <tr class="bg-slate-950/60 text-slate-500 text-[10px] font-black tracking-widest uppercase border-b border-white/5">
              <th class="px-6 py-4 w-32">بەروار</th>
              <th class="px-6 py-4 w-40 text-rose-400">بڕ ({{ currencyName }})</th>
              <th class="px-6 py-4 w-56 text-emerald-400 text-center">قەرزارە (Debtor)</th>
              <th class="px-6 py-4 w-28 text-amber-400 text-center">ع.١</th>
              <th class="px-6 py-4 w-56 text-blue-400 text-center">لامانە (Creditor)</th>
              <th class="px-6 py-4 w-28 text-amber-400 text-center">ع.٢</th>
              <th class="px-6 py-4">نێردر/وەرگر</th>
              <th class="px-6 py-4">ملاحظات</th>
              <th class="px-6 py-4 w-20">کردار</th>
            </tr>
          </thead>
          <tbody>
            <!-- Advanced Input Row (Universal) -->
            <tr class="bg-emerald-500/[0.03] border-b-2 border-emerald-500/20 relative z-50">
              <td class="px-2 py-3">
                <input v-model="newEntry.entry_date" type="date" class="w-full bg-slate-950 border border-white/5 rounded-xl px-3 py-3 text-xs text-white focus:border-emerald-500/50 outline-none font-bold" />
              </td>
              <td class="px-2 py-3 relative">
                <div class="flex flex-col gap-2">
                  <div class="relative group">
                    <input v-model="newEntry.amount" type="number" placeholder="0" class="w-full bg-slate-950 border-2 border-rose-500/20 text-rose-400 text-xl font-black rounded-xl px-4 py-3 focus:border-rose-500/50 outline-none text-center" />
                    <!-- Formatted Amount Tooltip (Live) -->
                    <div v-if="newEntry.amount" class="absolute -top-12 left-1/2 -translate-x-1/2 bg-rose-500 text-white px-4 py-2 rounded-2xl font-black text-lg shadow-2xl animate-bounce whitespace-nowrap z-[110]">
                      {{ formatNum(newEntry.amount) }} {{ activeCurrencyCode }}
                      <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-3 h-3 bg-rose-500 rotate-45"></div>
                    </div>
                  </div>
                  <!-- Currency Selector Inside Row -->
                  <select v-model="newEntry.currency_id" class="w-full bg-slate-900 border border-white/5 rounded-lg py-1 px-2 text-[10px] font-black text-emerald-500 outline-none">
                    <option v-for="c in currencies" :key="c.id" :value="c.id">{{ c.code }} ({{ c.name }})</option>
                  </select>
                </div>
              </td>
              
              <!-- Debtor Field -->
              <td class="px-2 py-3 relative">
                <div class="relative group">
                  <input ref="debtorSearchRef" v-model="debtorSearch" @input="searchAccounts('debtor')" @focus="showDebtorDropdown = true" @blur="onBlur('debtor')"
                    class="w-full bg-slate-950 border border-emerald-500/20 text-white rounded-xl py-3 pr-4 pl-10 text-sm font-bold focus:border-emerald-500 outline-none transition-all" 
                    placeholder="کۆد یان ناو..." dir="rtl" />
                  <div v-if="newEntry.debtor_account_id" class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded-lg font-black border border-emerald-500/30">
                    {{ selectedDebtorCode }}
                  </div>
                  <!-- Professional Dropdown -->
                  <div v-if="showDebtorDropdown && debtorResults.length > 0" class="absolute top-full left-0 right-0 mt-2 bg-[#0f172a] border border-emerald-500/30 rounded-2xl shadow-2xl z-[100] max-h-60 overflow-y-auto ring-1 ring-emerald-500/20 p-2 space-y-1">
                    <button v-for="acc in debtorResults" :key="acc.id" @mousedown.prevent="selectAccount('debtor', acc)"
                      class="w-full text-right px-4 py-3 hover:bg-emerald-500/10 rounded-xl transition-all flex items-center justify-between group/item">
                      <span class="text-white font-bold group-hover/item:text-emerald-400">{{ acc.name }}</span>
                      <span class="font-mono text-[10px] bg-slate-800 text-emerald-500 px-2 py-1 rounded-lg font-black">{{ acc.code }}</span>
                    </button>
                  </div>
                </div>
              </td>

              <td class="px-2 py-3">
                <input v-model="newEntry.commission_1" type="number" class="w-full bg-slate-950 border border-white/5 rounded-xl px-2 py-3 text-sm text-amber-400 font-bold text-center outline-none focus:border-amber-500/30" placeholder="0" />
              </td>

              <!-- Creditor Field -->
              <td class="px-2 py-3 relative">
                <div class="relative group">
                  <input v-model="creditorSearch" @input="searchAccounts('creditor')" @focus="showCreditorDropdown = true" @blur="onBlur('creditor')"
                    class="w-full bg-slate-950 border border-blue-500/20 text-white rounded-xl py-3 pr-4 pl-10 text-sm font-bold focus:border-blue-500 outline-none transition-all" 
                    placeholder="کۆد یان ناو..." dir="rtl" />
                  <div v-if="newEntry.creditor_account_id" class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] bg-blue-500/20 text-blue-400 px-2 py-1 rounded-lg font-black border border-blue-500/30">
                    {{ selectedCreditorCode }}
                  </div>
                  <!-- Professional Dropdown -->
                  <div v-if="showCreditorDropdown && creditorResults.length > 0" class="absolute top-full left-0 right-0 mt-2 bg-[#0f172a] border border-blue-500/30 rounded-2xl shadow-2xl z-[100] max-h-60 overflow-y-auto ring-1 ring-blue-500/20 p-2 space-y-1">
                    <button v-for="acc in creditorResults" :key="acc.id" @mousedown.prevent="selectAccount('creditor', acc)"
                      class="w-full text-right px-4 py-3 hover:bg-blue-500/10 rounded-xl transition-all flex items-center justify-between group/item">
                      <span class="text-white font-bold group-hover/item:text-blue-400">{{ acc.name }}</span>
                      <span class="font-mono text-[10px] bg-slate-800 text-blue-400 px-2 py-1 rounded-lg font-black">{{ acc.code }}</span>
                    </button>
                  </div>
                </div>
              </td>

              <td class="px-2 py-3">
                <input v-model="newEntry.commission_2" type="number" class="w-full bg-slate-950 border border-white/5 rounded-xl px-2 py-3 text-sm text-amber-400 font-bold text-center outline-none focus:border-amber-500/30" placeholder="0" />
              </td>

              <td class="px-2 py-3">
                <div class="flex flex-col gap-1">
                  <input v-model="newEntry.sender" type="text" placeholder="نێردر" class="w-full bg-slate-950 border border-white/5 rounded-lg px-2 py-1 text-[10px] text-white focus:border-emerald-500/30 outline-none" />
                  <input v-model="newEntry.receiver" type="text" placeholder="وەرگر" class="w-full bg-slate-950 border border-white/5 rounded-lg px-2 py-1 text-[10px] text-white focus:border-emerald-500/30 outline-none" />
                </div>
              </td>

              <td class="px-2 py-3">
                <input v-model="newEntry.notes" type="text" placeholder="ملاحظات..." class="w-full bg-slate-950 border border-white/5 rounded-xl px-3 py-3 text-xs text-white focus:border-emerald-500/30 outline-none" @keydown.enter="submitNewEntry" />
              </td>

              <td class="px-2 py-3">
                <button @click="submitNewEntry" :disabled="!newEntry.amount" class="w-full h-full p-4 bg-emerald-500 text-slate-950 rounded-xl hover:scale-105 active:scale-95 transition-all shadow-lg shadow-emerald-500/20 disabled:opacity-20 flex items-center justify-center">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </button>
              </td>
            </tr>

            <!-- Existing Entries -->
            <tr v-for="entry in entries" :key="entry.id" class="border-b border-white/[0.02] hover:bg-white/[0.02] group transition-colors">
              <td class="px-6 py-4 text-[11px] font-bold text-slate-500">{{ formatDate(entry.entry_date) }}</td>
              <td class="px-6 py-4 text-center">
                <span class="text-rose-400 font-black text-lg font-mono">{{ formatNum(entry.amount) }}</span>
                <span class="text-[9px] font-black text-slate-500 ml-1">{{ entry.currency?.code }}</span>
              </td>
              <td class="px-6 py-4">
                <div v-if="entry.debtor_account" class="flex items-center justify-center gap-2">
                   <span class="text-white font-bold text-sm">{{ entry.debtor_account.name }}</span>
                   <span class="text-[9px] font-black bg-emerald-500/10 text-emerald-500 px-1.5 py-0.5 rounded border border-emerald-500/20">{{ entry.debtor_account.code }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-center text-amber-500/60 font-mono font-bold text-xs">{{ entry.commission_1 || '' }}</td>
              <td class="px-6 py-4">
                <div v-if="entry.creditor_account" class="flex items-center justify-center gap-2">
                   <span class="text-white font-bold text-sm">{{ entry.creditor_account.name }}</span>
                   <span class="text-[9px] font-black bg-blue-500/10 text-blue-500 px-1.5 py-0.5 rounded border border-blue-500/20">{{ entry.creditor_account.code }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-center text-amber-500/60 font-mono font-bold text-xs">{{ entry.commission_2 || '' }}</td>
              <td class="px-6 py-4">
                 <div class="flex flex-col text-[10px] text-slate-500 font-bold">
                    <span>نێردر: {{ entry.sender || '—' }}</span>
                    <span>وەرگر: {{ entry.receiver || '—' }}</span>
                 </div>
              </td>
              <td class="px-6 py-4 text-[11px] text-slate-400 font-medium italic truncate max-w-xs">{{ entry.notes }}</td>
              <td class="px-6 py-4">
                 <button @click="confirmDelete(entry)" class="opacity-0 group-hover:opacity-100 p-2 bg-rose-500/10 text-rose-500 rounded-lg hover:bg-rose-500 hover:text-white transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                 </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer Summary -->
      <div class="p-6 bg-slate-950/60 border-t border-white/5 flex justify-between items-center px-12">
         <div class="flex gap-10">
            <div class="flex flex-col">
               <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Total Amount</span>
               <span class="text-2xl font-black text-rose-400 font-mono">{{ formatNum(totalAmount) }}</span>
            </div>
            <div class="flex flex-col">
               <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Total Commission</span>
               <span class="text-2xl font-black text-amber-500 font-mono">{{ formatNum(totalCommission) }}</span>
            </div>
         </div>
         <div class="text-slate-500 text-sm font-bold">{{ entries.length }} تۆمارکراوە</div>
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
      background: '#0f172a',
      color: '#fff',
      confirmButtonColor: '#f59e0b',
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
    
    // Advanced Success Toast
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
      background: '#10b981',
      color: '#fff',
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    })
    Toast.fire({ icon: 'success', title: 'تۆمارەکە بە سەرکەوتوویی پاشەکەوت کرا' })
  } catch (e) {
    let title = 'هەڵەیەک ڕوویدا'
    let errorMsg = 'ببورە، نەتوانرا ئەم تۆمارە پاشەکەوت بکرێت. تکایە دڵنیابەرەوە لەوەی هەموو حیسابەکانت بە دروستی هەڵبژاردووە و پەیوەندی ئینتەرنێتت جێگیرە.'
    
    if (e.response?.status === 422) {
       title = 'زانیاری هەڵە'
       errorMsg = 'هەندێک لەو زانیارییانەی داخڵت کردوون تەواو نین یان لەگەڵ یاساکانی دارایی ناگونجێن. تکایە خانە سوورەکان بپشکنە.'
    } else if (e.response?.status === 500) {
       title = 'کێشەی سێرڤەر'
       errorMsg = 'ببورە، کێشەیەکی کاتی لە سێرڤەری سیستمەکەدا هەیە. تیمی تەکنیکی ئاگادارکراونەتەوە. تکایە کەمێکی تر هەوڵ بدەرەوە.'
    }

    Swal.fire({
      icon: 'error',
      title: title,
      html: `<div dir="rtl" class="text-right text-sm leading-relaxed font-bold">${errorMsg}</div>`,
      background: '#0f172a',
      color: '#fff',
      confirmButtonColor: '#ef4444',
      confirmButtonText: 'تێگەیشتم'
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
    background: '#0f172a',
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
function formatDate(d) { return new Date(d).toLocaleDateString('en-GB') }

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
</style>
