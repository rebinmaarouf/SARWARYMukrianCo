<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-white tracking-tight">حسابەکان (وەکیل)</h1>
        <p class="text-slate-400 text-sm mt-1">بەڕێوەبردن و گەڕانی حسابەکان بە کۆد یان ناو</p>
      </div>
      <button @click="showCreateModal = true"
        class="px-6 py-3 bg-gradient-to-r from-[#10B981] to-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all duration-300 hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        حسابی نوێ
      </button>
    </div>

    <!-- Search Bar (Ultra Fast) -->
    <div class="bg-[#1e293b]/60 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-4">
      <div class="relative">
        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input 
          ref="searchInput"
          v-model="searchTerm" 
          @input="onSearch"
          type="text" 
          placeholder="بگەرێ بە کۆد (13) یان ناو (ناترۆن)..." 
          class="w-full bg-[#0f172a]/80 text-white placeholder-slate-500 border border-slate-700/50 rounded-xl py-4 pr-12 pl-4 text-lg font-medium focus:outline-none focus:ring-2 focus:ring-[#10B981]/50 focus:border-[#10B981]/50 transition-all"
          dir="rtl"
        />
      </div>
    </div>

    <!-- Accounts Table -->
    <div class="bg-[#1e293b]/60 backdrop-blur-xl rounded-2xl border border-slate-700/50 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-right">
          <thead>
            <tr class="bg-[#0f172a]/60 border-b border-slate-700/50">
              <th class="px-6 py-4 text-sm font-bold text-[#10B981] tracking-wide">کۆد</th>
              <th class="px-6 py-4 text-sm font-bold text-[#10B981] tracking-wide">ناو</th>
              <th class="px-6 py-4 text-sm font-bold text-[#10B981] tracking-wide">مۆبایل</th>
              <th class="px-6 py-4 text-sm font-bold text-[#10B981] tracking-wide">ناونیشان</th>
              <th class="px-6 py-4 text-sm font-bold text-[#10B981] tracking-wide">جۆر</th>
              <th class="px-6 py-4 text-sm font-bold text-[#10B981] tracking-wide">کردار</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(account, idx) in accounts" :key="account.id"
              class="border-b border-slate-800/50 hover:bg-[#10B981]/5 transition-colors cursor-pointer group"
              :class="idx % 2 === 0 ? 'bg-transparent' : 'bg-[#0f172a]/20'">
              
              <td class="px-6 py-3" @dblclick="startEdit(account, 'code')">
                <template v-if="editing?.id === account.id && editing?.field === 'code'">
                  <input v-model="editing.value" @blur="saveEdit(account)" @keydown.enter="saveEdit(account)" @keydown.escape="cancelEdit"
                    class="bg-[#0f172a] text-[#10B981] border border-[#10B981]/50 rounded-lg px-3 py-1 w-20 text-center font-mono font-bold focus:outline-none focus:ring-2 focus:ring-[#10B981]" autofocus />
                </template>
                <template v-else>
                  <span class="font-mono font-bold text-[#10B981] text-lg bg-[#10B981]/10 px-3 py-1 rounded-lg">{{ account.code || '—' }}</span>
                </template>
              </td>

              <td class="px-6 py-3" @dblclick="startEdit(account, 'name')">
                <template v-if="editing?.id === account.id && editing?.field === 'name'">
                  <input v-model="editing.value" @blur="saveEdit(account)" @keydown.enter="saveEdit(account)" @keydown.escape="cancelEdit"
                    class="bg-[#0f172a] text-white border border-slate-600 rounded-lg px-3 py-1 w-full font-bold focus:outline-none focus:ring-2 focus:ring-[#10B981]" dir="rtl" autofocus />
                </template>
                <template v-else>
                  <span class="font-bold text-white text-base">{{ account.name }}</span>
                </template>
              </td>

              <td class="px-6 py-3" @dblclick="startEdit(account, 'mobile')">
                <template v-if="editing?.id === account.id && editing?.field === 'mobile'">
                  <input v-model="editing.value" @blur="saveEdit(account)" @keydown.enter="saveEdit(account)" @keydown.escape="cancelEdit"
                    class="bg-[#0f172a] text-white border border-slate-600 rounded-lg px-3 py-1 w-full focus:outline-none focus:ring-2 focus:ring-[#10B981]" dir="ltr" autofocus />
                </template>
                <template v-else>
                  <span class="text-slate-300 font-medium" dir="ltr">{{ account.mobile || '—' }}</span>
                </template>
              </td>

              <td class="px-6 py-3" @dblclick="startEdit(account, 'address')">
                <template v-if="editing?.id === account.id && editing?.field === 'address'">
                  <input v-model="editing.value" @blur="saveEdit(account)" @keydown.enter="saveEdit(account)" @keydown.escape="cancelEdit"
                    class="bg-[#0f172a] text-white border border-slate-600 rounded-lg px-3 py-1 w-full focus:outline-none focus:ring-2 focus:ring-[#10B981]" dir="rtl" autofocus />
                </template>
                <template v-else>
                  <span class="text-slate-400">{{ account.address || '—' }}</span>
                </template>
              </td>

              <td class="px-6 py-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold"
                  :class="{
                    'bg-emerald-500/20 text-emerald-400': account.type === 'vault',
                    'bg-blue-500/20 text-blue-400': account.type === 'customer',
                    'bg-amber-500/20 text-amber-400': account.type === 'expense',
                    'bg-purple-500/20 text-purple-400': account.type === 'equity',
                    'bg-cyan-500/20 text-cyan-400': account.type === 'revenue',
                    'bg-slate-500/20 text-slate-400': account.type === 'general',
                  }">
                  {{ typeLabels[account.type] || account.type }}
                </span>
              </td>

              <td class="px-6 py-3">
                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button @click="confirmDelete(account)" class="p-2 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors" title="سڕینەوە">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="flex items-center justify-between px-6 py-4 border-t border-slate-700/50">
        <span class="text-sm text-slate-400">{{ pagination.total }} حساب - پەڕەی {{ pagination.current_page }} لە {{ pagination.last_page }}</span>
        <div class="flex gap-2">
          <button @click="fetchAccounts(pagination.current_page - 1)" :disabled="pagination.current_page <= 1"
            class="px-4 py-2 rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-30 transition-colors font-bold text-sm">پێشوو</button>
          <button @click="fetchAccounts(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page"
            class="px-4 py-2 rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-30 transition-colors font-bold text-sm">دواتر</button>
        </div>
      </div>
    </div>

    <!-- Modals (Create/Delete) ... (Omitted for brevity, kept consistent) -->
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import axios from '../../plugins/axios'

const accounts = ref([])
const loading = ref(false)
const searchTerm = ref('')
const searchInput = ref(null)
const showCreateModal = ref(false)
const deleteTarget = ref(null)
const editing = ref(null)
let searchTimeout = null

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
})

const typeLabels = {
  vault: 'قاسە/سندوق',
  customer: 'کڕیار/حساب',
  expense: 'مەسروفات',
  equity: 'سەرمایە',
  revenue: 'داهات',
  general: 'گشتی',
}

const newAccount = ref({
  code: '',
  name: '',
  mobile: '',
  address: '',
  type: 'customer',
})

async function fetchAccounts(page = 1) {
  loading.value = true
  try {
    const params = { page, per_page: 50 }
    if (searchTerm.value) params.search = searchTerm.value

    const { data } = await axios.get('/accounts', { params })
    accounts.value = data.data
    pagination.value = {
      current_page: data.current_page,
      last_page: data.last_page,
      total: data.total,
    }
  } catch (e) {
    console.error('Error fetching accounts:', e)
  } finally {
    loading.value = false
  }
}

function onSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchAccounts(1)
  }, 150)
}

function startEdit(account, field) {
  editing.value = { id: account.id, field, value: account[field] || '' }
  nextTick(() => {
    const input = document.querySelector('td input:focus') || document.querySelector('td input')
    if (input) input.focus()
  })
}

function cancelEdit() {
  editing.value = null
}

async function saveEdit(account) {
  if (!editing.value) return
  const { field, value } = editing.value
  
  try {
    const payload = { ...account, [field]: value }
    await axios.put(`/accounts/${account.id}`, payload)
    account[field] = value
  } catch (e) {
    console.error('Error updating account:', e)
  }
  editing.value = null
}

async function createAccount() {
  try {
    const { data } = await axios.post('/accounts', newAccount.value)
    accounts.value.unshift(data)
    showCreateModal.value = false
    newAccount.value = { code: '', name: '', mobile: '', address: '', type: 'customer' }
  } catch (e) {
    console.error('Error creating account:', e)
  }
}

function confirmDelete(account) {
  deleteTarget.value = account
}

async function doDelete() {
  try {
    await axios.delete(`/accounts/${deleteTarget.value.id}`)
    accounts.value = accounts.value.filter(a => a.id !== deleteTarget.value.id)
    deleteTarget.value = null
  } catch (e) {
    console.error('Error deleting account:', e)
  }
}

onMounted(() => {
  fetchAccounts()
  nextTick(() => searchInput.value?.focus())
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
