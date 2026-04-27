<template>
  <div class="space-y-8 animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 bg-slate-900/40 backdrop-blur-3xl p-8 rounded-[2.5rem] border border-white/5">
      <div dir="rtl" class="text-right">
        <h1 class="text-3xl font-black text-white tracking-tight">بەڕێوەبردنی حیسابات</h1>
        <p class="text-slate-500 font-medium mt-1">لیستی هەموو قاسەکان، وەکیلەکان، و مەسروفاتەکان لە یەک خشتەدا</p>
      </div>
      <button @click="showCreateModal = true"
        class="px-8 py-4 bg-emerald-500 text-slate-950 font-black rounded-2xl shadow-xl shadow-emerald-500/10 hover:scale-105 transition-all flex items-center gap-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
        زیادکردنی حیسابی نوێ
      </button>
    </div>

    <!-- Stats Row (Mini) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
       <div v-for="(count, label) in typeCounts" :key="label" class="bg-slate-900/40 border border-white/5 p-4 rounded-2xl flex items-center justify-between">
          <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ label }}</span>
          <span class="text-lg font-black text-white">{{ count }}</span>
       </div>
    </div>

    <!-- Advanced Table Area -->
    <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
      <!-- Table Search & Filters -->
      <div class="p-6 border-b border-white/5 flex flex-col md:flex-row gap-4 justify-between items-center bg-slate-950/20">
        <div class="relative w-full md:w-96 group">
          <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input v-model="searchTerm" @input="onSearch" type="text" placeholder="بگەڕێ بۆ حساب..." class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl py-3 pr-12 pl-4 focus:outline-none focus:border-emerald-500/50 transition-all font-bold" dir="rtl">
        </div>
        <div class="flex gap-2">
           <button v-for="t in types" :key="t.key" @click="filterType = t.key" 
             class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-tighter transition-all"
             :class="filterType === t.key ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400 hover:bg-slate-700'">
             {{ t.label }}
           </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse" dir="rtl">
          <thead>
            <tr class="bg-slate-950/40 text-slate-500 uppercase text-[10px] font-black tracking-[0.2em] border-b border-white/5">
              <th class="px-8 py-5">کۆد</th>
              <th class="px-8 py-5">ناوی حیساب</th>
              <th class="px-8 py-5">جۆری حیساب</th>
              <th class="px-8 py-5">مۆبایل</th>
              <th class="px-8 py-5 text-left">باڵانس (بەکۆی گشتی)</th>
              <th class="px-8 py-5 text-center">کردارەکان</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="account in filteredAccounts" :key="account.id" class="group hover:bg-emerald-500/[0.02] transition-colors cursor-pointer">
              <td class="px-8 py-5">
                <span class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center font-mono font-black text-emerald-500 border border-white/5 group-hover:border-emerald-500/50 transition-all">
                  {{ account.code || '—' }}
                </span>
              </td>
              <td class="px-8 py-5">
                <div class="flex flex-col">
                  <span class="text-white font-black text-lg group-hover:text-emerald-400 transition-colors">{{ account.name }}</span>
                  <span class="text-[10px] text-slate-600 font-bold tracking-widest">{{ account.address || 'بێ ناونیشان' }}</span>
                </div>
              </td>
              <td class="px-8 py-5">
                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border"
                  :class="getTypeStyle(account.type)">
                  {{ typeLabels[account.type] }}
                </span>
              </td>
              <td class="px-8 py-5 text-slate-400 font-bold tracking-tighter">
                {{ account.mobile || '—' }}
              </td>
              <td class="px-8 py-5 text-left">
                <div class="flex flex-col items-start gap-1.5">
                  <div v-for="b in account.balances" :key="b.currency_code" 
                       class="flex items-center gap-3 bg-slate-950/60 px-3 py-1.5 rounded-xl border border-white/5 min-w-[150px] justify-between group-hover:border-emerald-500/30 transition-all">
                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">{{ b.currency_code }}</span>
                    <span class="font-black text-sm transition-colors font-mono" 
                          :class="b.amount >= 0 ? 'text-emerald-400' : 'text-rose-500'">
                      {{ formatNum(b.amount) }}
                    </span>
                  </div>
                  <span v-if="!account.balances || account.balances.length === 0" class="text-slate-800 font-bold text-xs px-4">0</span>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                  <router-link :to="{ name: 'admin-account-statement', query: { id: account.id } }" class="p-2.5 bg-emerald-500/10 text-emerald-400 rounded-xl hover:bg-emerald-500 hover:text-slate-950 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                  </router-link>
                  
                  <!-- Edit Button -->
                  <button v-if="can('MANAGE ACCOUNTS')" @click="openEditModal(account)" class="p-2.5 bg-blue-500/10 text-blue-500 rounded-xl hover:bg-blue-500 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </button>

                  <!-- Delete Button -->
                  <button v-if="can('DELETE RECORDS')" @click="deleteAccount(account.id)" class="p-2.5 bg-rose-500/10 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Account Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showCreateModal || editingAccount" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
          <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="closeModals"></div>
          
          <div class="relative bg-slate-900 border border-white/10 rounded-[2.5rem] shadow-3xl w-full max-w-xl p-10 overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-3xl -mr-16 -mt-16" :class="editingAccount ? 'bg-blue-500/5' : 'bg-emerald-500/5'"></div>
            
            <div dir="rtl" class="text-right mb-8">
              <h2 class="text-2xl font-black text-white">{{ editingAccount ? 'دەستکاری حیساب' : 'زیادکردنی حیسابی نوێ' }}</h2>
              <p class="text-slate-500 text-sm font-medium">زانیارییەکانی حیسابەکە بە وردی پڕ بکەرەوە</p>
            </div>

            <form @submit.prevent="editingAccount ? updateAccount() : submitAccount()" class="space-y-6" dir="rtl">
              <div class="grid grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="text-xs font-black text-slate-500 uppercase tracking-widest px-2">ناوی حیساب</label>
                  <input v-model="activeForm.name" required type="text" placeholder="بۆ نموونە: کۆمپانیای ناترۆن" 
                    class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:border-blue-500 outline-none transition-all" />
                </div>
                <div class="space-y-2">
                  <label class="text-xs font-black text-slate-500 uppercase tracking-widest px-2">کۆدی حیساب</label>
                  <input v-model="activeForm.code" type="text" placeholder="بۆ نموونە: 101" 
                    class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-white font-mono font-bold focus:border-blue-500 outline-none transition-all" />
                </div>
              </div>

              <div class="space-y-2">
                <label class="text-xs font-black text-slate-500 uppercase tracking-widest px-2">جۆری حیساب</label>
                <select v-model="activeForm.type" required class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:border-blue-500 outline-none appearance-none cursor-pointer">
                  <option value="vault">قاسە / سندوق (Vault)</option>
                  <option value="client">کڕیار / وەکیل (Client)</option>
                  <option value="revenue">داهات (Revenue)</option>
                  <option value="expense">مەسروفات (Expense)</option>
                  <option value="equity">سەرمایە (Equity)</option>
                </select>
              </div>

              <div class="space-y-2">
                <label class="text-xs font-black text-slate-500 uppercase tracking-widest px-2">تێبینی (ئارەزوومەندانە)</label>
                <textarea v-model="activeForm.notes" rows="3" class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-white font-medium focus:border-blue-500 outline-none transition-all"></textarea>
              </div>

              <div class="flex gap-4 pt-4">
                <button type="submit" :disabled="loading" 
                  class="flex-1 py-5 text-slate-950 font-black text-lg rounded-2xl transition-all shadow-xl active:scale-95 disabled:opacity-50"
                  :class="editingAccount ? 'bg-blue-500 shadow-blue-500/20' : 'bg-emerald-500 shadow-emerald-500/20'">
                  {{ loading ? 'خەریکی پاشەکەوتکردنە...' : (editingAccount ? 'نوێکردنەوەی حساب' : 'تۆمارکردنی حساب') }}
                </button>
                <button type="button" @click="closeModals" class="px-8 py-5 bg-slate-800 text-white font-black rounded-2xl">پاشگەزبوونەوە</button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
const can = (permission) => {
  if (!auth.user) return false
  
  // 0. Master Override (The Owner/Super Admin)
  if (auth.user.email === 'rebin.maaruf@gmail.com') return true

  const roleName = (typeof auth.user.role === 'object' ? auth.user.role.name : auth.user.role)?.toLowerCase() || ''
  
  // 1. Admin Role check
  if (roleName.includes('admin')) return true
  
  // 2. Direct/Special Permissions check
  const hasDirect = auth.user.permissions?.some(p => p.name === permission)
  if (hasDirect) return true

  // 3. Inherited Role Permissions check
  if (typeof auth.user.role === 'object' && auth.user.role.permissions) {
    return auth.user.role.permissions.some(p => p.name === permission)
  }

  return false
}

const accounts = ref([])
const searchTerm = ref('')
const filterType = ref('all')
const showCreateModal = ref(false)
const loading = ref(false)
const form = ref({
  name: '',
  code: '',
  type: 'client',
  notes: ''
})

const typeLabels = {
  vault: 'قاسە/سندوق',
  client: 'کڕیار/وەکیل',
  expense: 'مەسروفات',
  equity: 'سەرمایە',
  revenue: 'داهات',
  general: 'گشتی',
}

const types = [
  { key: 'all', label: 'هەمووی' },
  { key: 'vault', label: 'قاسەکان' },
  { key: 'client', label: 'وەکیلەکان' },
  { key: 'expense', label: 'مەسروفات' },
]

function onSearch() {
  // Computed property handled automatically, but we can add debounce here if needed
}

const filteredAccounts = computed(() => {
  return accounts.value.filter(a => {
    const term = searchTerm.value.toLowerCase()
    const matchesSearch = a.name.toLowerCase().includes(term) || (a.code && a.code.toString().includes(term))
    const matchesType = filterType.value === 'all' || a.type === filterType.value
    return matchesSearch && matchesType
  })
})

const typeCounts = computed(() => {
  const counts = { 'قاسە': 0, 'وەکیل': 0, 'مەسروفات': 0, 'گشتی': 0 }
  accounts.value.forEach(a => {
    if (a.type === 'vault') counts['قاسە']++
    if (a.type === 'client') counts['وەکیل']++
    if (a.type === 'expense') counts['مەسروفات']++
    if (a.type === 'general') counts['گشتی']++
  })
  return counts
})

const getTypeStyle = (type) => {
  const styles = {
    vault: 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
    client: 'bg-blue-500/10 text-blue-500 border-blue-500/20',
    expense: 'bg-rose-500/10 text-rose-500 border-rose-500/20',
    equity: 'bg-purple-500/10 text-purple-500 border-purple-500/20',
    general: 'bg-slate-800 text-slate-400 border-white/5'
  }
  return styles[type] || styles.general
}

const editingAccount = ref(null)
const editForm = ref({ name: '', type: '', code: '', notes: '' })

const activeForm = computed(() => editingAccount.value ? editForm.value : form.value)

function closeModals() {
  showCreateModal.value = false
  editingAccount.value = null
  form.value = { name: '', type: 'client', code: '', notes: '' }
}

function openEditModal(acc) {
  editingAccount.value = acc
  editForm.value = { ...acc }
}

async function updateAccount() {
  loading.value = true
  try {
    const { data } = await axios.put(`/accounts/${editingAccount.value.id}`, editForm.value)
    const index = accounts.value.findIndex(a => a.id === data.id)
    accounts.value[index] = data
    editingAccount.value = null
    
    Swal.fire({
      icon: 'success',
      title: 'سەرکەوتوو بوو',
      text: 'گۆڕانکارییەکان بە سەرکەوتوویی پاشەکەوت کران',
      background: 'rgba(15, 23, 42, 0.9)',
      color: '#fff',
      backdrop: 'rgba(0,0,0,0.4) blur(10px)',
      showConfirmButton: false,
      timer: 2000,
      customClass: {
        popup: 'rounded-[2.5rem] border border-white/10 shadow-3xl',
        title: 'font-black text-2xl',
        htmlContainer: 'font-bold text-slate-400'
      }
    })
  } catch (e) {
    Swal.fire({
      icon: 'error',
      title: 'هەڵە ڕوویدا',
      text: e.response?.data?.error || 'نەتوانرا گۆڕانکارییەکان پاشەکەوت بکرێت',
      background: 'rgba(15, 23, 42, 0.9)',
      color: '#fff',
      confirmButtonColor: '#3b82f6',
      customClass: {
        popup: 'rounded-[2.5rem] border border-white/10 shadow-3xl',
        confirmButton: 'px-10 py-4 rounded-2xl font-black'
      }
    })
  } finally {
    loading.value = false
  }
}

async function deleteAccount(id) {
  const result = await Swal.fire({
    title: 'دڵنیایت لە سڕینەوە؟',
    text: "ئەم کارە ناگەڕێتەوە و هەموو داتاکانی ئەم حیسابە لەدەست دەچێت",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'بەڵێ، بیسرەوە',
    cancelButtonText: 'پەشیمانم',
    background: 'rgba(15, 23, 42, 0.9)',
    color: '#fff',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#1e293b',
    customClass: {
      popup: 'rounded-[2.5rem] border border-white/10 shadow-3xl',
      confirmButton: 'px-8 py-4 rounded-2xl font-black mx-2',
      cancelButton: 'px-8 py-4 rounded-2xl font-black mx-2'
    }
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/accounts/${id}`)
      accounts.value = accounts.value.filter(a => a.id !== id)
      Swal.fire({
        icon: 'success',
        title: 'سڕایەوە',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        background: '#10b981',
        color: '#fff'
      })
    } catch (e) {
      Swal.fire({
        icon: 'error',
        title: 'ناتوانیت بیسرێیتەوە',
        text: e.response?.data?.error || 'ئەم حیسابە مێژووی هەیە',
        background: 'rgba(15, 23, 42, 0.9)',
        color: '#fff',
        confirmButtonColor: '#ef4444',
        customClass: {
          popup: 'rounded-[2.5rem] border border-white/10 shadow-3xl'
        }
      })
    }
  }
}

async function fetchAccounts() {
  try {
    const { data } = await axios.get('/accounts')
    accounts.value = data.data || data
  } catch (e) { console.error(e) }
}

async function submitAccount() {
  loading.value = true
  try {
    await axios.post('/accounts', form.value)
    await fetchAccounts()
    showCreateModal.value = false
    form.value = { name: '', code: '', type: 'client', notes: '' }
    
    Swal.fire({
      icon: 'success',
      title: 'سەرکەوتوو بوو',
      text: 'حیسابەکە بە سەرکەوتوویی دروستکرا',
      background: '#0f172a',
      color: '#fff',
      confirmButtonColor: '#10b981'
    })
  } catch (e) {
    let msg = 'نەتوانرا حیسابەکە دروست بکرێت'
    if (e.response?.data?.errors) msg = Object.values(e.response.data.errors).flat().join('<br>')
    
    Swal.fire({
      icon: 'error',
      title: 'هەڵە',
      html: `<div dir="rtl" class="text-right text-sm font-bold">${msg}</div>`,
      background: '#0f172a',
      color: '#fff',
      confirmButtonColor: '#ef4444'
    })
  } finally {
    loading.value = false
  }
}

function formatNum(val) {
  return new Intl.NumberFormat().format(val || 0)
}

onMounted(fetchAccounts)
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
