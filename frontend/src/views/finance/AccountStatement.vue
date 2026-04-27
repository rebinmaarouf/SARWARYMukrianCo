<template>
  <div class="p-6 md:p-10 space-y-8 max-w-[1600px] mx-auto" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-10">
      <div>
        <h1 class="text-4xl font-black text-white tracking-tighter">پوختەی حیساب (Statement)</h1>
        <p class="text-slate-400 font-medium mt-2 flex items-center gap-2">
          <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
          ووردەکاری جوڵەی حیساب بۆ هەر کڕیار یان سندوقێک
        </p>
      </div>

      <!-- Filters & Selection -->
      <div class="flex flex-wrap items-center gap-3 bg-slate-900/60 backdrop-blur-xl p-3 rounded-[2rem] border border-slate-800 shadow-2xl">
        <div class="flex flex-col px-4 border-l border-slate-800/50">
          <span class="text-[9px] font-black text-slate-500 uppercase">هەڵبژاردنی حیساب</span>
          <select v-model="filters.account_id" @change="fetchStatement" class="bg-transparent border-none p-0 text-sm font-black text-emerald-400 focus:ring-0 cursor-pointer min-w-[200px]">
            <option :value="null" disabled>حیسابێک هەڵبژێرە...</option>
            <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.code }} - {{ a.name }}</option>
          </select>
        </div>
        <div class="flex flex-col px-4 border-l border-slate-800/50">
          <span class="text-[9px] font-black text-slate-500 uppercase">لە بەرواری</span>
          <input v-model="filters.start_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-white focus:ring-0 cursor-pointer" />
        </div>
        <button @click="fetchStatement" :disabled="loading || !filters.account_id" class="bg-emerald-600 hover:bg-emerald-500 text-white w-12 h-12 rounded-2xl flex items-center justify-center transition-all shadow-lg shadow-emerald-900/20">
          <svg v-if="!loading" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
        </button>
      </div>
    </div>

    <!-- Multi-Currency Balances Summary -->
    <div v-if="selectedAccount && summaries.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div v-for="s in summaries" :key="s.currency_id" 
           class="bg-slate-900/40 border border-slate-800 p-6 rounded-[2rem] flex flex-col justify-between">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">کۆی باڵانسی {{ s.currency?.code }}</span>
        <div class="flex justify-between items-end">
          <h3 class="text-3xl font-black" :class="getBalanceValue(s) >= 0 ? 'text-emerald-400' : 'text-rose-400'">
            {{ formatNum(getBalanceValue(s)) }}
          </h3>
          <span class="text-xs font-bold text-slate-600 uppercase">{{ s.currency?.code }}</span>
        </div>
      </div>
    </div>

    <!-- Statement Table -->
    <div v-if="filters.account_id" class="bg-slate-900/40 border border-slate-800 rounded-[3rem] overflow-hidden shadow-2xl">
      <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
          <thead>
            <tr class="text-[11px] font-black text-slate-500 uppercase bg-slate-950/50">
              <th class="px-8 py-6">بەروار</th>
              <th class="px-8 py-6">وەسف</th>
              <th class="px-8 py-6 text-center">مەدین (قەرز)</th>
              <th class="px-8 py-6 text-center">داین (کەسر)</th>
              <th class="px-8 py-6">دراو</th>
              <th class="px-8 py-6 text-center">باڵانس</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/30">
            <tr v-for="(entry, index) in entries" :key="entry.id" class="hover:bg-slate-800/40 transition-all group">
              <td class="px-8 py-5">
                <p class="text-xs font-bold text-slate-400">{{ formatDate(entry.date) }}</p>
                <p class="text-[9px] text-slate-600 font-mono uppercase">{{ entry.entryable_type?.split('\\').pop() }}</p>
              </td>
              <td class="px-8 py-5">
                <p class="text-sm font-black text-white">{{ entry.description }}</p>
              </td>
              <td class="px-8 py-5 text-center">
                <span v-if="entry.debit > 0" class="text-lg font-black text-emerald-400">
                  {{ formatNum(entry.debit) }}
                </span>
                <span v-else class="text-slate-800">-</span>
              </td>
              <td class="px-8 py-5 text-center">
                <span v-if="entry.credit > 0" class="text-lg font-black text-rose-400">
                  {{ formatNum(entry.credit) }}
                </span>
                <span v-else class="text-slate-800">-</span>
              </td>
              <td class="px-8 py-5">
                <span class="px-3 py-1 bg-slate-800 rounded-lg text-xs font-black text-slate-300">
                  {{ entry.currency?.code }}
                </span>
              </td>
              <td class="px-8 py-5 text-center">
                <!-- Running balance logic would ideally be done on server side for pagination, 
                     but for a single page view, we can calculate it. 
                     For now, showing the Value in Base Currency as a reference? 
                     Actually, running balance is complex with multi-currency. 
                     Better to show the net impact per line. -->
                <span class="text-sm font-bold" :class="entry.base_amount >= 0 ? 'text-emerald-500/50' : 'text-rose-500/50'">
                  {{ formatNum(entry.base_amount) }} <span class="text-[9px]">IQD Equivalent</span>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div v-if="!entries.length && !loading" class="p-20 text-center text-slate-600 font-black uppercase tracking-[0.2em]">
        هیچ جوڵەیەک تۆمار نەکراوە
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from '../../plugins/axios'

const route = useRoute()
const accounts = ref([])
const entries = ref([])
const summaries = ref([])
const selectedAccount = ref(null)
const loading = ref(false)

const filters = reactive({
  account_id: null,
  start_date: '',
  currency_id: null
})

async function fetchAccounts() {
  const { data } = await axios.get('/accounts?per_page=1000')
  accounts.value = data.data
  
  // If ID in URL, select it
  if (route.query.id) {
    filters.account_id = parseInt(route.query.id)
    fetchStatement()
  }
}

async function fetchStatement() {
  if (!filters.account_id) return
  
  loading.value = true
  try {
    // 1. Fetch Journal Entries
    const journalRes = await axios.get('/journals', { 
      params: { ...filters, per_page: 500 } 
    })
    entries.value = journalRes.data.data
    
    // 2. Fetch Account Details (Summaries)
    const accountRes = await axios.get(`/accounts/${filters.account_id}`)
    selectedAccount.value = accountRes.data
    summaries.value = accountRes.data.summaries || []
  } catch (e) {
    console.error('Error fetching statement:', e)
  } finally {
    loading.value = false
  }
}

function getBalanceValue(summary) {
  return parseFloat(summary.total_debit) - parseFloat(summary.total_credit)
}

function formatNum(val) {
  return new Intl.NumberFormat().format(parseFloat(val || 0))
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('ku-IQ', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(() => fetchAccounts())
</script>
