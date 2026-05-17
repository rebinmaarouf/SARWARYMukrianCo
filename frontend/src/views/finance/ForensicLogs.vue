<template>
  <div class="space-y-8 animate-fade-in max-w-[1700px] mx-auto pb-20 text-slate-800 font-sans">
    
    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row items-start xl:items-center justify-between gap-6 bg-white p-6 md:p-10 rounded-[2.5rem] border border-slate-200 relative overflow-hidden shadow-sm">
      <div class="absolute inset-0 bg-gradient-to-r from-rose-500/5 via-transparent to-blue-500/5 pointer-events-none"></div>
      <div class="relative z-10 w-full">
        <div class="flex items-center gap-3 mb-2">
           <span class="w-10 h-1 bg-gradient-to-r from-rose-500 to-blue-600 rounded-full"></span>
           <h2 class="text-[10px] font-black text-rose-600 uppercase tracking-[0.4em]">Forensic Audit Trail</h2>
        </div>
        <h1 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tighter">ناوەندی بەدواداچوونی ورد (Forensic)</h1>
        <p class="text-slate-600 text-xs md:text-base font-medium mt-3 max-w-2xl leading-relaxed">
          چاودێری چرکە بە چرکەی جوڵەکانی سیستەم. لێرەدا دەتوانیت بزانیت کێ، کەی، و چۆن داتاکانی گۆڕیوە.
        </p>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6" dir="rtl">
        <div class="space-y-2">
          <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">بەکارهێنەر</label>
          <select v-model="filters.user_id" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-900 focus:border-rose-600 focus:ring-1 focus:ring-rose-600 outline-none transition-all shadow-xs">
            <option :value="null">هەموو یوزەرەکان</option>
            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">جۆری کردار</label>
          <select v-model="filters.action" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-900 focus:border-rose-600 focus:ring-1 focus:ring-rose-600 outline-none transition-all shadow-xs">
            <option :value="null">هەموو کردارەکان</option>
            <option value="created">دروستکردن</option>
            <option value="updated">گۆڕانکاری (Update)</option>
            <option value="deleted">سڕینەوە (Soft Delete)</option>
            <option value="voided">پوچەڵکردنەوە (Void)</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-2">لە بەرواری</label>
          <input v-model="filters.date_from" type="date" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-900 focus:border-rose-600 focus:ring-1 focus:ring-rose-600 outline-none transition-all shadow-xs" />
        </div>
        <div class="flex items-end">
          <button @click="fetchLogs" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl transition-all shadow-md shadow-rose-600/10 active:scale-95">گەڕانی ورد</button>
        </div>
      </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-right" dir="rtl">
          <thead>
            <tr class="bg-slate-50 text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-slate-200">
              <th class="px-8 py-6">کات و بەروار</th>
              <th class="px-8 py-6">بەکارهێنەر</th>
              <th class="px-8 py-6">کردار</th>
              <th class="px-8 py-6">بەش (Model)</th>
              <th class="px-8 py-6 text-center">بینینی وردەکاری</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-semibold">
            <tr v-for="log in logs" :key="log.id" class="hover:bg-slate-50 transition-colors group">
              <td class="px-8 py-5">
                <span class="text-xs font-mono text-slate-600">{{ formatTime(log.created_at) }}</span>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-rose-50 flex items-center justify-center text-rose-600 text-[10px] font-black border border-rose-200 shadow-xs">
                    {{ log.user?.name?.charAt(0) }}
                  </div>
                  <span class="text-sm font-black text-slate-900">{{ log.user?.name || 'System' }}</span>
                </div>
              </td>
              <td class="px-8 py-5">
                <span :class="getActionClass(log.action)" class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-xs">
                  {{ log.action }}
                </span>
              </td>
              <td class="px-8 py-5 text-slate-600 text-xs font-mono">
                {{ formatModelName(log.model_type) }} #{{ log.model_id }}
              </td>
              <td class="px-8 py-5 text-center">
                <button @click="showLogDetail(log)" class="p-2 bg-white hover:bg-slate-100 rounded-xl transition-all border border-slate-200 shadow-xs">
                  <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Detail Modal -->
    <div v-if="selectedLog" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-10 backdrop-blur-sm bg-slate-900/60">
      <div class="bg-white border border-slate-200 rounded-[3rem] w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl animate-fade-in font-sans">
        <div class="p-8 border-b border-slate-200 flex items-center justify-between bg-slate-50">
          <h2 class="text-2xl font-black text-slate-900">بەدواداچوونی گۆڕانکاری</h2>
          <button @click="selectedLog = null" class="p-3 bg-white hover:bg-rose-50 hover:text-rose-600 rounded-2xl transition-all border border-slate-200 shadow-xs">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-8 space-y-8" dir="rtl">
          <!-- Comparison View -->
          <div v-if="selectedLog.action === 'updated'" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
              <h3 class="text-[10px] font-black text-rose-600 uppercase tracking-widest px-2">داتای پێشتر (Before)</h3>
              <div class="bg-slate-50 p-6 rounded-3xl border border-rose-200 text-xs font-mono space-y-2 max-h-96 overflow-auto shadow-inner">
                <div v-for="(val, key) in selectedLog.before" :key="key" class="flex flex-col border-b border-slate-200 pb-2">
                  <span class="text-slate-500 text-[10px] font-black">{{ translateKey(key) }}</span>
                  <span class="text-rose-600 font-bold break-all">{{ val }}</span>
                </div>
              </div>
            </div>
            <div class="space-y-4">
              <h3 class="text-[10px] font-black text-emerald-700 uppercase tracking-widest px-2">داتای ئێستا (After)</h3>
              <div class="bg-slate-50 p-6 rounded-3xl border border-emerald-200 text-xs font-mono space-y-2 max-h-96 overflow-auto shadow-inner">
                <div v-for="(val, key) in selectedLog.after" :key="key" class="flex flex-col border-b border-slate-200 pb-2">
                  <span class="text-slate-500 text-[10px] font-black">{{ translateKey(key) }}</span>
                  <span class="text-emerald-700 font-bold break-all">{{ val }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Single View for Created/Deleted -->
          <div v-else class="space-y-4">
            <h3 class="text-[10px] font-black text-blue-700 uppercase tracking-widest px-2">داتای پاشکەوتکراو</h3>
            <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-slate-200 text-xs font-mono grid grid-cols-2 gap-4 shadow-inner">
              <div v-for="(val, key) in (selectedLog.after || selectedLog.before)" :key="key" class="flex flex-col bg-white p-3 rounded-xl border border-slate-100 shadow-xs">
                <span class="text-slate-500 text-[10px] font-black">{{ translateKey(key) }}</span>
                <span class="text-slate-900 font-bold break-all">{{ val }}</span>
              </div>
            </div>
          </div>

          <!-- Metadata Section -->
          <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200 flex flex-wrap gap-10 shadow-xs">
            <div>
              <span class="text-[9px] font-black text-slate-500 uppercase block mb-1">ئامێر و براوسەر</span>
              <p class="text-xs text-slate-700 font-semibold">{{ selectedLog.user_agent }}</p>
            </div>
            <div>
              <span class="text-[9px] font-black text-slate-500 uppercase block mb-1">IP Address</span>
              <p class="text-xs text-rose-600 font-mono font-bold">{{ selectedLog.ip_address }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'

const logs = ref([])
const users = ref([])
const selectedLog = ref(null)
const filters = ref({ user_id: null, action: null, date_from: null, date_to: null })

function translateKey(key) {
  const translations = {
    'id': 'ناسنامە (ID)',
    'account_id': 'حیساب',
    'currency_id': 'دراو',
    'debit': 'قەرزدار (Debit)',
    'credit': 'قەرز (Credit)',
    'rate_at_time': 'نرخی کاتی مامەڵە',
    'base_amount': 'بڕ بە دراوی سەرەکی',
    'entryable_type': 'بەش (Model)',
    'entryable_id': 'ناسنامەی بەش',
    'branch_id': 'لق',
    'user_id': 'بەکارهێنەر',
    'description': 'ڕوونکردنەوە',
    'date': 'بەروار',
    'hash': 'هاشی ئەمنی (Hash)',
    'previous_hash': 'هاشی پێشوو',
    'created_at': 'کاتی دروستکردن',
    'updated_at': 'کاتی دەستکاریکردن'
  }
  return translations[key] || key
}

async function fetchLogs() {
  try {
    const { data } = await axios.get('/audit-logs', { params: filters.value })
    logs.value = data.data
  } catch (e) {
    console.error(e)
  }
}

async function fetchUsers() {
  try {
    const { data } = await axios.get('/admin/users')
    users.value = data.data || data
  } catch (e) { console.error(e) }
}

function showLogDetail(log) {
  selectedLog.value = log
}

function formatTime(dateStr) {
  return new Date(dateStr).toLocaleString('en-GB', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric' })
}

function formatModelName(model) {
  return model.split('\\').pop()
}

function getActionClass(action) {
  switch (action) {
    case 'created': return 'bg-emerald-50 text-emerald-700 border border-emerald-200'
    case 'updated': return 'bg-blue-50 text-blue-700 border border-blue-200'
    case 'deleted': return 'bg-rose-50 text-rose-700 border border-rose-200'
    case 'voided': return 'bg-purple-50 text-purple-700 border border-purple-200'
    default: return 'bg-slate-100 text-slate-600 border border-slate-200'
  }
}

onMounted(() => {
  fetchLogs()
  fetchUsers()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
