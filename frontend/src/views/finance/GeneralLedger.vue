<template>
  <div class="p-6 md:p-10 space-y-8 max-w-[1600px] mx-auto text-slate-800" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-10">
      <div>
        <h1 class="text-4xl font-black text-slate-900 tracking-tighter">ڕۆژنامەی گشتی (Journal)</h1>
        <p class="text-slate-600 font-medium mt-2 flex items-center gap-2">
          <span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></span>
          بینینی هەموو جوڵە داراییەکان بە شێوازی موەحەد
        </p>
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap items-center gap-3 bg-white p-3 rounded-[2rem] border border-slate-200 shadow-sm">
        <div class="flex flex-col px-4 border-l border-slate-100">
          <span class="text-[9px] font-black text-slate-500 uppercase">لە بەرواری</span>
          <input v-model="filters.start_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-slate-900 focus:ring-0 cursor-pointer outline-none" />
        </div>
        <div class="flex flex-col px-4 border-l border-slate-100">
          <span class="text-[9px] font-black text-slate-500 uppercase">بۆ بەرواری</span>
          <input v-model="filters.end_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-slate-900 focus:ring-0 cursor-pointer outline-none" />
        </div>
        <button @click="fetchEntries" :disabled="loading" class="bg-blue-600 hover:bg-blue-700 text-white w-12 h-12 rounded-2xl flex items-center justify-center transition-all shadow-md active:scale-95">
          <svg v-if="!loading" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <div v-else class="w-5 h-5 border-2 border-slate-200 border-t-blue-600 rounded-full animate-spin"></div>
        </button>
      </div>
    </div>

    <!-- Journal Table -->
    <div class="bg-white border border-slate-200 rounded-[3rem] overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
          <thead>
            <tr class="text-[11px] font-black text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
              <th class="px-8 py-6">بەروار</th>
              <th class="px-8 py-6">حساب</th>
              <th class="px-8 py-6 text-center">مەدین (Debit)</th>
              <th class="px-8 py-6 text-center">داین (Credit)</th>
              <th class="px-8 py-6">دراو</th>
              <th class="px-8 py-6">وەسف</th>
              <th class="px-8 py-6">بەکارھێنەر</th>
              <th v-if="authStore.isSuperAdmin || authStore?.permissions?.includes('delete journals')" class="px-8 py-6 text-center">کردارەکان</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-semibold">
            <tr v-for="entry in entries" :key="entry.id" class="hover:bg-slate-50 transition-all group">
              <td class="px-8 py-5">
                <p class="text-xs font-bold text-slate-700">{{ formatDate(entry.date) }}</p>
                <p class="text-[10px] text-slate-500 font-mono">#{{ entry.id }}</p>
              </td>
              <td class="px-8 py-5">
                <div class="flex flex-col">
                  <span class="text-sm font-black text-slate-900 group-hover:text-blue-600 transition-colors">{{ entry.account?.name }}</span>
                  <span class="text-[10px] text-slate-500 font-mono">{{ entry.account?.code }}</span>
                </div>
              </td>
              <td class="px-8 py-5 text-center">
                <span v-if="entry.debit > 0" class="text-lg font-black text-emerald-700 font-mono">
                  {{ formatNum(entry.debit) }}
                </span>
                <span v-else class="text-slate-400">-</span>
              </td>
              <td class="px-8 py-5 text-center">
                <span v-if="entry.credit > 0" class="text-lg font-black text-rose-600 font-mono">
                  {{ formatNum(entry.credit) }}
                </span>
                <span v-else class="text-slate-400">-</span>
              </td>
              <td class="px-8 py-5">
                <span class="px-3 py-1 bg-slate-100 border border-slate-200 rounded-lg text-xs font-black text-slate-700 shadow-xs font-mono">
                  {{ entry.currency?.code }}
                </span>
              </td>
              <td class="px-8 py-5 max-w-[300px]">
                <p class="text-xs font-medium text-slate-700 truncate">{{ entry.description }}</p>
                <p v-if="entry.entryable_type" class="text-[9px] text-slate-500 mt-1 uppercase tracking-widest font-mono">Source: {{ entry.entryable_type.split('\\').pop() }}</p>
              </td>
              <td class="px-8 py-5">
                <span class="text-xs font-bold text-slate-600">{{ entry.user?.name }}</span>
              </td>
              <td v-if="authStore.isSuperAdmin || authStore?.permissions?.includes('delete journals')" class="px-8 py-5 text-center">
                <button @click="deleteEntry(entry)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-600 hover:text-white transition-colors shadow-xs" title="سڕینەوەی مامەڵە">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-8 border-t border-slate-200 flex justify-center gap-4 bg-slate-50/50">
        <button @click="fetchEntries(pagination.current_page - 1)" :disabled="pagination.current_page <= 1"
          class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-700 disabled:opacity-30 hover:bg-blue-600 hover:text-white transition-all shadow-xs">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div class="px-6 flex items-center text-xs font-black text-slate-600 uppercase tracking-widest">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </div>
        <button @click="fetchEntries(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page"
          class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-700 disabled:opacity-30 hover:bg-blue-600 hover:text-white transition-all shadow-xs">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
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

async function deleteEntry(entry) {
  const result = await Swal.fire({
    title: 'سڕینەوەی مامەڵە',
    html: `ئایا دڵنیایت لە سڕینەوەی ئەم جوڵەیە؟ <br><span class="text-xs text-rose-600 font-bold">ئەم کارە تەواوی مامەڵەکە لە هەموو سندوقەکان دەسڕێتەوە بەشێوەیەکی یەکجاری!</span>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'بەڵێ، بیسڕەوە',
    cancelButtonText: 'پەشیمان بوونەوە',
    background: '#ffffff',
    color: '#0f172a',
    customClass: {
      popup: 'rounded-[2.5rem] border border-slate-200 shadow-2xl',
      confirmButton: 'px-8 py-4 text-white rounded-2xl font-black mx-2',
      cancelButton: 'px-8 py-4 text-white rounded-2xl font-black mx-2'
    }
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/journals/${entry.id}`)
      
      Swal.fire({
        title: 'سڕایەوە!',
        text: 'مامەڵەکە بە سەرکەوتوویی سڕایەوە و حیسابات گەڕایەوە باری پێشوو.',
        icon: 'success',
        background: '#ffffff',
        color: '#0f172a',
        confirmButtonColor: '#2563eb',
        confirmButtonText: 'باشە، داخستن',
        customClass: {
          popup: 'rounded-[2.5rem] border border-slate-200 shadow-2xl',
          confirmButton: 'px-8 py-4 text-white rounded-2xl font-black'
        }
      })
      
      fetchEntries(pagination.value.current_page)
    } catch (e) {
      console.error('Error deleting entry:', e)
      Swal.fire({
        title: 'هەڵە!',
        text: e.response?.data?.message || 'کێشەیەک ڕوویدا لە کاتی سڕینەوە',
        icon: 'error',
        background: '#ffffff',
        color: '#0f172a',
        confirmButtonColor: '#2563eb',
        confirmButtonText: 'داخستن',
        customClass: {
          popup: 'rounded-[2.5rem] border border-slate-200 shadow-2xl',
          confirmButton: 'px-8 py-4 text-white rounded-2xl font-black'
        }
      })
    }
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
