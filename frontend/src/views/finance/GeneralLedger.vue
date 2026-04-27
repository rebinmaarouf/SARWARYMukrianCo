<template>
  <div class="p-6 md:p-10 space-y-8 max-w-[1600px] mx-auto" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-10">
      <div>
        <h1 class="text-4xl font-black text-white tracking-tighter">ڕۆژنامەی گشتی (Journal)</h1>
        <p class="text-slate-400 font-medium mt-2 flex items-center gap-2">
          <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
          بینینی هەموو جوڵە داراییەکان بە شێوازی موەحەد
        </p>
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap items-center gap-3 bg-slate-900/60 backdrop-blur-xl p-3 rounded-[2rem] border border-slate-800 shadow-2xl">
        <div class="flex flex-col px-4 border-l border-slate-800/50">
          <span class="text-[9px] font-black text-slate-500 uppercase">لە بەرواری</span>
          <input v-model="filters.start_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-white focus:ring-0 cursor-pointer" />
        </div>
        <div class="flex flex-col px-4 border-l border-slate-800/50">
          <span class="text-[9px] font-black text-slate-500 uppercase">بۆ بەرواری</span>
          <input v-model="filters.end_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-white focus:ring-0 cursor-pointer" />
        </div>
        <button @click="fetchEntries" :disabled="loading" class="bg-blue-600 hover:bg-blue-500 text-white w-12 h-12 rounded-2xl flex items-center justify-center transition-all shadow-lg">
          <svg v-if="!loading" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
        </button>
      </div>
    </div>

    <!-- Journal Table -->
    <div class="bg-slate-900/40 border border-slate-800 rounded-[3rem] overflow-hidden shadow-2xl">
      <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
          <thead>
            <tr class="text-[11px] font-black text-slate-500 uppercase bg-slate-950/50">
              <th class="px-8 py-6">بەروار</th>
              <th class="px-8 py-6">حساب</th>
              <th class="px-8 py-6 text-center">مەدین (Debit)</th>
              <th class="px-8 py-6 text-center">داین (Credit)</th>
              <th class="px-8 py-6">دراو</th>
              <th class="px-8 py-6">وەسف</th>
              <th class="px-8 py-6">بەکارھێنەر</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/30">
            <tr v-for="entry in entries" :key="entry.id" class="hover:bg-slate-800/40 transition-all group">
              <td class="px-8 py-5">
                <p class="text-xs font-bold text-slate-400">{{ formatDate(entry.date) }}</p>
                <p class="text-[10px] text-slate-600 font-mono">#{{ entry.id }}</p>
              </td>
              <td class="px-8 py-5">
                <div class="flex flex-col">
                  <span class="text-sm font-black text-white group-hover:text-blue-400 transition-colors">{{ entry.account?.name }}</span>
                  <span class="text-[10px] text-slate-500 font-mono">{{ entry.account?.code }}</span>
                </div>
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
              <td class="px-8 py-5 max-w-[300px]">
                <p class="text-xs font-medium text-slate-400 truncate">{{ entry.description }}</p>
                <p v-if="entry.entryable_type" class="text-[9px] text-slate-600 mt-1 uppercase tracking-widest">Source: {{ entry.entryable_type.split('\\').pop() }}</p>
              </td>
              <td class="px-8 py-5">
                <span class="text-xs font-bold text-slate-500">{{ entry.user?.name }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-8 border-t border-slate-800 flex justify-center gap-4">
        <button @click="fetchEntries(pagination.current_page - 1)" :disabled="pagination.current_page <= 1"
          class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-slate-400 disabled:opacity-20 hover:bg-blue-600 hover:text-white transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div class="px-6 flex items-center text-xs font-black text-slate-500 uppercase tracking-widest">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </div>
        <button @click="fetchEntries(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page"
          class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-slate-400 disabled:opacity-20 hover:bg-blue-600 hover:text-white transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'

const entries = ref([])
const loading = ref(false)
const filters = ref({
  start_date: new Date().toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0],
  account_id: null,
  currency_id: null
})

const pagination = ref({ current_page: 1, last_page: 1 })

async function fetchEntries(page = 1) {
  loading.value = true
  try {
    const { data } = await axios.get('/journals', { params: { ...filters.value, page } })
    entries.value = data.data
    pagination.value = { current_page: data.current_page, last_page: data.last_page }
  } catch (e) {
    console.error('Error fetching journal entries:', e)
  } finally {
    loading.value = false
  }
}

function formatNum(val) {
  return new Intl.NumberFormat().format(parseFloat(val || 0))
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('ku-IQ', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(() => fetchEntries())
</script>
