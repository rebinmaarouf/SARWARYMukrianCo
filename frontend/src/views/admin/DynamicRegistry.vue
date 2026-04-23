<template>
  <div class="space-y-6">
    <!-- Dynamic Header (changes based on currency) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-white tracking-tight">{{ pageTitle }}</h1>
        <p class="text-slate-400 text-sm mt-1">تۆمارکردن و بەڕێوەبردنی {{ currencyName }}</p>
      </div>
      <div class="flex items-center gap-3">
        <!-- Date Range Filter -->
        <div class="flex items-center gap-2 bg-[#1e293b]/60 backdrop-blur-xl rounded-xl border border-slate-700/50 px-4 py-2">
          <label class="text-xs text-slate-400 font-bold">لە</label>
          <input v-model="fromDate" type="date" class="bg-transparent text-white border-none text-sm focus:outline-none font-medium" />
          <label class="text-xs text-slate-400 font-bold">بۆ</label>
          <input v-model="toDate" type="date" class="bg-transparent text-white border-none text-sm focus:outline-none font-medium" />
          <button @click="fetchEntries()" class="p-1.5 rounded-lg bg-[#10B981]/20 text-[#10B981] hover:bg-[#10B981]/30 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-[#1e293b]/60 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-4">
      <div class="relative">
        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input v-model="searchTerm" @input="onSearch" type="text" 
          :placeholder="'بگەرێ لە تۆمارەکانی ' + currencyName + '...'" 
          class="w-full bg-[#0f172a]/80 text-white placeholder-slate-500 border border-slate-700/50 rounded-xl py-3 pr-12 pl-4 text-base font-medium focus:outline-none focus:ring-2 focus:ring-[#10B981]/50 focus:border-[#10B981]/50 transition-all" dir="rtl" />
      </div>
    </div>

    <!-- Main Interactive Grid -->
    <div class="bg-[#1e293b]/60 backdrop-blur-xl rounded-2xl border border-slate-700/50 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-right">
          <thead>
            <tr class="bg-[#0f172a]/60 border-b border-slate-700/50">
              <th class="px-4 py-3 text-xs font-bold text-[#10B981] w-28">بەروار</th>
              <th class="px-4 py-3 text-xs font-bold text-red-400 w-32">{{ currencyLabel }} (دینار)</th>
              <th class="px-4 py-3 text-xs font-bold text-[#10B981] w-28">قەرزارە</th>
              <th class="px-4 py-3 text-xs font-bold text-amber-400 w-20">عمولە</th>
              <th class="px-4 py-3 text-xs font-bold text-blue-400 w-28">لامانە</th>
              <th class="px-4 py-3 text-xs font-bold text-amber-400 w-20">عمولە</th>
              <th class="px-4 py-3 text-xs font-bold text-slate-400 w-28">نێردر</th>
              <th class="px-4 py-3 text-xs font-bold text-slate-400 w-28">وەرگر</th>
              <th class="px-4 py-3 text-xs font-bold text-slate-400 w-36">ملاحظە</th>
              <th class="px-4 py-3 text-xs font-bold text-slate-400 w-16">کردار</th>
            </tr>
          </thead>
          <tbody>
            <!-- New Entry Row (Always First) -->
            <tr class="bg-[#10B981]/5 border-b-2 border-[#10B981]/30">
              <td class="px-3 py-2">
                <input v-model="newEntry.entry_date" type="date" class="w-full bg-[#0f172a] text-white border border-[#10B981]/30 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#10B981]" />
              </td>
              <td class="px-3 py-2">
                <input v-model="newEntry.amount" type="number" class="w-full bg-[#0f172a] text-red-400 border border-[#10B981]/30 rounded-lg px-2 py-2 text-sm font-bold text-center focus:outline-none focus:ring-1 focus:ring-[#10B981]" placeholder="0" @keydown.enter="submitNewEntry" />
              </td>
              <td class="px-3 py-2">
                <!-- Fast Account Combobox for Debtor -->
                <div class="relative">
                  <input v-model="debtorSearch" @input="searchAccounts('debtor')" @focus="showDebtorDropdown = true" @blur="hideDropdown('debtor')" @keydown.enter="selectFirstAccount('debtor')"
                    class="w-full bg-[#0f172a] text-white border border-[#10B981]/30 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#10B981]" 
                    :placeholder="newEntry.debtor_account_id ? '' : 'کۆد/ناو'" dir="rtl" />
                  <div v-if="newEntry.debtor_account_id" class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] bg-[#10B981]/20 text-[#10B981] px-1.5 rounded font-mono font-bold">{{ selectedDebtorCode }}</div>
                  <!-- Dropdown -->
                  <div v-if="showDebtorDropdown && debtorResults.length > 0" class="absolute top-full left-0 right-0 mt-1 bg-[#1e293b] border border-slate-600 rounded-xl shadow-2xl z-50 max-h-48 overflow-y-auto">
                    <button v-for="acc in debtorResults" :key="acc.id" @mousedown.prevent="selectAccount('debtor', acc)"
                      class="w-full text-right px-3 py-2 hover:bg-[#10B981]/10 transition-colors flex items-center gap-2 border-b border-slate-700/30 last:border-0">
                      <span class="font-mono text-[#10B981] text-xs bg-[#10B981]/10 px-2 py-0.5 rounded font-bold min-w-[2rem] text-center">{{ acc.code || '—' }}</span>
                      <span class="text-white text-sm font-bold truncate">{{ acc.name }}</span>
                    </button>
                  </div>
                </div>
              </td>
              <td class="px-3 py-2">
                <input v-model="newEntry.commission_1" type="number" class="w-full bg-[#0f172a] text-amber-400 border border-[#10B981]/30 rounded-lg px-2 py-2 text-sm text-center focus:outline-none focus:ring-1 focus:ring-[#10B981]" placeholder="0" />
              </td>
              <td class="px-3 py-2">
                <!-- Fast Account Combobox for Creditor -->
                <div class="relative">
                  <input v-model="creditorSearch" @input="searchAccounts('creditor')" @focus="showCreditorDropdown = true" @blur="hideDropdown('creditor')" @keydown.enter="selectFirstAccount('creditor')"
                    class="w-full bg-[#0f172a] text-white border border-[#10B981]/30 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#10B981]" 
                    :placeholder="newEntry.creditor_account_id ? '' : 'کۆد/ناو'" dir="rtl" />
                  <div v-if="newEntry.creditor_account_id" class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] bg-blue-500/20 text-blue-400 px-1.5 rounded font-mono font-bold">{{ selectedCreditorCode }}</div>
                  <div v-if="showCreditorDropdown && creditorResults.length > 0" class="absolute top-full left-0 right-0 mt-1 bg-[#1e293b] border border-slate-600 rounded-xl shadow-2xl z-50 max-h-48 overflow-y-auto">
                    <button v-for="acc in creditorResults" :key="acc.id" @mousedown.prevent="selectAccount('creditor', acc)"
                      class="w-full text-right px-3 py-2 hover:bg-blue-500/10 transition-colors flex items-center gap-2 border-b border-slate-700/30 last:border-0">
                      <span class="font-mono text-blue-400 text-xs bg-blue-500/10 px-2 py-0.5 rounded font-bold min-w-[2rem] text-center">{{ acc.code || '—' }}</span>
                      <span class="text-white text-sm font-bold truncate">{{ acc.name }}</span>
                    </button>
                  </div>
                </div>
              </td>
              <td class="px-3 py-2">
                <input v-model="newEntry.commission_2" type="number" class="w-full bg-[#0f172a] text-amber-400 border border-[#10B981]/30 rounded-lg px-2 py-2 text-sm text-center focus:outline-none focus:ring-1 focus:ring-[#10B981]" placeholder="0" />
              </td>
              <td class="px-3 py-2">
                <input v-model="newEntry.sender" type="text" class="w-full bg-[#0f172a] text-white border border-[#10B981]/30 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#10B981]" placeholder="نێردر" dir="rtl" />
              </td>
              <td class="px-3 py-2">
                <input v-model="newEntry.receiver" type="text" class="w-full bg-[#0f172a] text-white border border-[#10B981]/30 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#10B981]" placeholder="وەرگر" dir="rtl" />
              </td>
              <td class="px-3 py-2">
                <input v-model="newEntry.notes" type="text" class="w-full bg-[#0f172a] text-white border border-[#10B981]/30 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#10B981]" placeholder="ملاحظە..." dir="rtl" @keydown.enter="submitNewEntry" />
              </td>
              <td class="px-3 py-2">
                <button @click="submitNewEntry" :disabled="!newEntry.amount"
                  class="w-full p-2 rounded-lg bg-[#10B981] text-white hover:bg-emerald-600 transition-colors disabled:opacity-30 active:scale-90">
                  <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
              </td>
            </tr>

            <!-- Existing Entries -->
            <tr v-for="(entry, idx) in entries" :key="entry.id"
              class="border-b border-slate-800/50 hover:bg-slate-800/30 transition-colors group"
              :class="idx % 2 === 0 ? 'bg-transparent' : 'bg-[#0f172a]/20'">
              
              <td class="px-4 py-2.5 text-sm text-slate-300 font-medium">{{ formatDate(entry.entry_date) }}</td>
              <td class="px-4 py-2.5 text-sm font-bold text-red-400 text-center font-mono">{{ formatNumber(entry.amount) }}</td>
              <td class="px-4 py-2.5 text-sm">
                <span v-if="entry.debtor_account" class="flex items-center gap-1">
                  <span class="font-mono text-[10px] bg-[#10B981]/10 text-[#10B981] px-1.5 rounded font-bold">{{ entry.debtor_account.code }}</span>
                  <span class="text-white font-medium truncate">{{ entry.debtor_account.name }}</span>
                </span>
                <span v-else class="text-slate-600">—</span>
              </td>
              <td class="px-4 py-2.5 text-sm text-amber-400 text-center font-mono">{{ entry.commission_1 > 0 ? formatNumber(entry.commission_1) : '' }}</td>
              <td class="px-4 py-2.5 text-sm">
                <span v-if="entry.creditor_account" class="flex items-center gap-1">
                  <span class="font-mono text-[10px] bg-blue-500/10 text-blue-400 px-1.5 rounded font-bold">{{ entry.creditor_account.code }}</span>
                  <span class="text-white font-medium truncate">{{ entry.creditor_account.name }}</span>
                </span>
                <span v-else class="text-slate-600">—</span>
              </td>
              <td class="px-4 py-2.5 text-sm text-amber-400 text-center font-mono">{{ entry.commission_2 > 0 ? formatNumber(entry.commission_2) : '' }}</td>
              <td class="px-4 py-2.5 text-sm text-slate-400">{{ entry.sender || '—' }}</td>
              <td class="px-4 py-2.5 text-sm text-slate-400">{{ entry.receiver || '—' }}</td>
              <td class="px-4 py-2.5 text-sm text-slate-500 truncate max-w-[200px]">{{ entry.notes || '—' }}</td>
              <td class="px-4 py-2.5">
                <button @click="confirmDelete(entry)" class="p-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors opacity-0 group-hover:opacity-100">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Summary Footer -->
      <div class="flex items-center justify-between px-6 py-4 border-t border-slate-700/50 bg-[#0f172a]/40">
        <div class="flex items-center gap-6">
          <div class="text-sm">
            <span class="text-slate-400">کۆی گشتی:</span>
            <span class="text-white font-black text-lg mr-2 font-mono">{{ formatNumber(totalAmount) }}</span>
          </div>
          <div class="text-sm">
            <span class="text-slate-400">کۆی عمولە:</span>
            <span class="text-amber-400 font-bold mr-2 font-mono">{{ formatNumber(totalCommission) }}</span>
          </div>
        </div>
        <span class="text-sm text-slate-500">{{ entries.length }} تۆمار</span>
      </div>
    </div>

    <!-- Delete Confirmation -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="deleteTarget" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="deleteTarget = null"></div>
          <div class="relative bg-[#1e293b] rounded-3xl border border-red-500/30 shadow-2xl w-full max-w-md p-8 text-center space-y-6">
            <div class="w-16 h-16 mx-auto rounded-full bg-red-500/10 flex items-center justify-center">
              <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-xl font-black text-white">ئایا دڵنیایت لە سڕینەوە؟</h3>
            <p class="text-slate-400">ئەم تۆمارە بە بڕی <span class="text-red-400 font-bold font-mono">{{ formatNumber(deleteTarget?.amount) }}</span> دەسڕێتەوە.</p>
            <div class="flex gap-3 justify-center">
              <button @click="doDelete" class="px-8 py-3 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition-colors active:scale-95">بەڵێ</button>
              <button @click="deleteTarget = null" class="px-8 py-3 bg-slate-800 text-slate-300 font-bold rounded-xl hover:bg-slate-700 transition-colors">نەخێر</button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

// Dynamic based on route params
const currencyId = computed(() => Number(route.params.currencyId) || 1)
const currencyName = computed(() => {
  const map = { 1: 'دینار', 2: 'دۆلار' }
  return route.query.name || map[currencyId.value] || 'دراو'
})
const currencyLabel = computed(() => currencyName.value)
const pageTitle = computed(() => `تۆماری ${currencyName.value}`)

const entries = ref([])
const searchTerm = ref('')
const fromDate = ref('')
const toDate = ref('')
const deleteTarget = ref(null)
let searchTimeout = null

// Account search states
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

const totalAmount = computed(() => entries.value.reduce((sum, e) => sum + Number(e.amount), 0))
const totalCommission = computed(() => entries.value.reduce((sum, e) => sum + Number(e.commission_1 || 0) + Number(e.commission_2 || 0), 0))

async function fetchEntries() {
  try {
    const params = { currency_id: currencyId.value, per_page: 200 }
    if (fromDate.value) params.from_date = fromDate.value
    if (toDate.value) params.to_date = toDate.value
    if (searchTerm.value) params.search = searchTerm.value

    const { data } = await axios.get(`${API_BASE}/registries`, { params })
    entries.value = data.data
  } catch (e) {
    console.error('Error fetching entries:', e)
  }
}

function onSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => fetchEntries(), 150)
}

async function searchAccounts(type) {
  const term = type === 'debtor' ? debtorSearch.value : creditorSearch.value
  if (!term || term.length < 1) {
    if (type === 'debtor') debtorResults.value = []
    else creditorResults.value = []
    return
  }

  try {
    const { data } = await axios.get(`${API_BASE}/accounts`, { params: { search: term, per_page: 10 } })
    if (type === 'debtor') debtorResults.value = data.data
    else creditorResults.value = data.data
  } catch (e) {
    console.error('Error searching accounts:', e)
  }
}

function selectAccount(type, acc) {
  if (type === 'debtor') {
    newEntry.value.debtor_account_id = acc.id
    debtorSearch.value = acc.name
    selectedDebtorCode.value = acc.code
    showDebtorDropdown.value = false
    debtorResults.value = []
  } else {
    newEntry.value.creditor_account_id = acc.id
    creditorSearch.value = acc.name
    selectedCreditorCode.value = acc.code
    showCreditorDropdown.value = false
    creditorResults.value = []
  }
}

function selectFirstAccount(type) {
  const results = type === 'debtor' ? debtorResults.value : creditorResults.value
  if (results.length > 0) selectAccount(type, results[0])
}

function hideDropdown(type) {
  setTimeout(() => {
    if (type === 'debtor') showDebtorDropdown.value = false
    else showCreditorDropdown.value = false
  }, 200)
}

async function submitNewEntry() {
  if (!newEntry.value.amount) return

  try {
    const payload = {
      ...newEntry.value,
      currency_id: currencyId.value,
      commission_1: newEntry.value.commission_1 || 0,
      commission_2: newEntry.value.commission_2 || 0,
    }
    
    const { data } = await axios.post(`${API_BASE}/registries`, payload)
    entries.value.unshift(data)
    
    // Reset form but keep date
    newEntry.value = {
      entry_date: today,
      amount: '',
      debtor_account_id: null,
      creditor_account_id: null,
      commission_1: '',
      commission_2: '',
      sender: '',
      receiver: '',
      notes: '',
    }
    debtorSearch.value = ''
    creditorSearch.value = ''
    selectedDebtorCode.value = ''
    selectedCreditorCode.value = ''
  } catch (e) {
    console.error('Error creating entry:', e)
  }
}

function confirmDelete(entry) {
  deleteTarget.value = entry
}

async function doDelete() {
  try {
    await axios.delete(`${API_BASE}/registries/${deleteTarget.value.id}`)
    entries.value = entries.value.filter(e => e.id !== deleteTarget.value.id)
    deleteTarget.value = null
  } catch (e) {
    console.error('Error deleting entry:', e)
  }
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-GB')
}

function formatNumber(n) {
  if (!n && n !== 0) return '0'
  return Number(n).toLocaleString('en-US')
}

// Watch for route changes (switching between Dinar/Dollar registry)
watch(() => route.params.currencyId, () => {
  fetchEntries()
})

onMounted(() => {
  fetchEntries()
})
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
