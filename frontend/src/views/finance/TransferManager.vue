<template>
  <div class="space-y-8 animate-fade-in max-w-5xl mx-auto pb-20">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 bg-slate-900/40 backdrop-blur-3xl p-8 rounded-[2.5rem] border border-white/5 shadow-2xl">
      <div dir="rtl" class="text-right">
        <h1 class="text-3xl font-black text-white tracking-tight italic">گواستنەوەی پارە</h1>
        <p class="text-slate-500 font-medium mt-1 uppercase text-xs tracking-[0.2em]">Money Transfer & Internal Movement</p>
      </div>
      <div class="p-3 bg-blue-500/10 rounded-2xl border border-blue-500/20">
        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
      </div>
    </div>

    <!-- Transfer Form Card -->
    <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-3xl relative group">
      <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl -mr-48 -mt-48 transition-all duration-700"></div>
      <div class="absolute bottom-0 left-0 w-96 h-96 bg-emerald-500/5 rounded-full blur-3xl -ml-48 -mb-48 transition-all duration-700"></div>
      
      <div class="p-8 md:p-12 relative">
        <form @submit.prevent="submitTransfer" class="space-y-12" dir="rtl">
          
          <!-- Accounts Selection Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- From Account -->
            <div class="space-y-4">
              <label class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] px-2 flex items-center gap-2">
                <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                لە کوێوە دەڕوات (سەرچاوە)
              </label>
              <div class="relative group/input">
                <select v-model="form.from_account_id" required 
                  class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-5 text-white font-black text-lg focus:border-rose-500 outline-none appearance-none cursor-pointer transition-all hover:border-white/10">
                  <option value="" disabled>هەڵبژاردنی حیساب...</option>
                  <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }} ({{ acc.code }})</option>
                </select>
                <div class="absolute left-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
              </div>
            </div>

            <!-- To Account -->
            <div class="space-y-4">
              <label class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] px-2 flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                بۆ کوێ دەچێت (وەرگر)
              </label>
              <div class="relative group/input">
                <select v-model="form.to_account_id" required 
                  class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-5 text-white font-black text-lg focus:border-emerald-500 outline-none appearance-none cursor-pointer transition-all hover:border-white/10">
                  <option value="" disabled>هەڵبژاردنی حیساب...</option>
                  <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }} ({{ acc.code }})</option>
                </select>
                <div class="absolute left-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Principal Transfer Amount -->
          <div class="p-8 rounded-[2rem] bg-blue-500/5 border border-blue-500/10 space-y-6">
             <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-white font-black text-xl">بڕی حەواڵە <span class="text-slate-500 text-sm font-medium">(پارەی ئەسڵی کە دەگوازرێتەوە)</span></h3>
             </div>
             
             <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
               <div class="space-y-4">
                  <label class="text-xs font-black text-blue-400 uppercase tracking-[0.2em] px-2">دراوی حەواڵە</label>
                  <div class="flex gap-2 p-1 bg-slate-950/80 rounded-2xl border border-white/5">
                    <button v-for="c in currencies" :key="c.id" type="button"
                      @click="form.currency_id = c.id"
                      class="flex-1 py-5 rounded-xl text-sm font-black uppercase transition-all"
                      :class="form.currency_id === c.id ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/20' : 'text-slate-500 hover:text-white'">
                      {{ c.code }}
                    </button>
                  </div>
               </div>
               
               <div class="space-y-4">
                  <label class="text-xs font-black text-blue-400 uppercase tracking-[0.2em] px-2">بڕی پارە</label>
                  <div class="relative">
                    <input :value="formText.amount" @input="e => updateAmount('amount', e.target.value)" type="text" required placeholder="0"
                      class="w-full bg-slate-950/80 border border-white/5 rounded-2xl px-6 py-5 text-white font-black text-3xl focus:border-blue-500 outline-none transition-all placeholder:text-slate-800" />
                  </div>
               </div>
             </div>
          </div>

          <!-- Commission / Fee Section -->
          <div class="p-8 rounded-[2rem] bg-emerald-500/5 border border-emerald-500/10 space-y-6">
             <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <h3 class="text-white font-black text-xl">عومولە / کرێ <span class="text-slate-500 text-sm font-medium">(ئەگەر هەیە)</span></h3>
             </div>
             
             <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
               <div class="space-y-4">
                  <label class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em] px-2">دراوی عومولە</label>
                  <div class="flex gap-2 p-1 bg-slate-950/80 rounded-2xl border border-white/5 overflow-x-auto custom-scrollbar">
                    <button v-for="c in currencies" :key="c.id" type="button"
                      @click="form.commission_currency_id = c.id"
                      class="px-6 py-5 rounded-xl text-sm font-black uppercase transition-all whitespace-nowrap"
                      :class="form.commission_currency_id === c.id ? 'bg-emerald-500 text-slate-950 shadow-xl shadow-emerald-500/20' : 'text-slate-500 hover:text-white'">
                      {{ c.code }}
                    </button>
                  </div>
               </div>
               
               <div class="space-y-4">
                  <label class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em] px-2">بڕی عومولە (داهات)</label>
                  <div class="relative">
                    <input :value="formText.commission_amount" @input="e => updateAmount('commission', e.target.value)" type="text" placeholder="0"
                      class="w-full bg-slate-950/80 border border-white/5 rounded-2xl px-6 py-5 text-emerald-400 font-black text-3xl focus:border-emerald-500 outline-none transition-all placeholder:text-slate-800" />
                  </div>
               </div>
             </div>
             
             <!-- Educational Helper -->
             <div v-if="form.commission_amount > 0" class="mt-4 p-4 rounded-xl bg-slate-950/50 border border-emerald-500/20 text-sm font-bold text-emerald-400/80 flex items-center gap-3">
               <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
               <p>کۆی گشتی بڕاو لە نێرەر: <span class="text-white mx-1">{{ formText.amount }} {{ selectedCurrencyCode }}</span> 
               <span v-if="form.currency_id !== form.commission_currency_id || form.commission_amount > 0"> + <span class="text-white mx-1">{{ formText.commission_amount }} {{ selectedCommissionCurrencyCode }}</span></span></p>
             </div>
          </div>

          <!-- Notes -->
          <div class="space-y-4">
            <label class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] px-2">تێبینی / هۆکاری حەواڵە</label>
            <textarea v-model="form.notes" rows="2" placeholder="بۆ نمونە: کرێی مانگانە، یان پێدانی قەرز..."
              class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-5 text-white font-bold focus:border-blue-500 outline-none transition-all placeholder:text-slate-800"></textarea>
          </div>

          <!-- Submit Button -->
          <div class="pt-4">
            <button type="submit" :disabled="loading"
              class="w-full py-6 bg-blue-600 hover:bg-blue-500 text-white font-black text-2xl rounded-2xl shadow-2xl shadow-blue-600/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center justify-center gap-4">
              <span v-if="loading" class="animate-spin rounded-full h-6 w-6 border-4 border-white/20 border-t-white"></span>
              {{ loading ? 'خەریکی جێبەجێکردنە...' : 'تەواوکردنی گواستنەوە' }}
            </button>
          </div>

        </form>
      </div>
    </div>

    <!-- Recent Transfers Table -->
    <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
      <div class="p-8 border-b border-white/5 flex items-center justify-between" dir="rtl">
        <h2 class="text-xl font-black text-white">دوایین گواستنەوە و حەواڵەکان</h2>
        <button @click="fetchData" class="p-2 text-slate-500 hover:text-blue-400 transition-colors bg-slate-950 rounded-xl border border-white/5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-right" dir="rtl">
          <thead>
            <tr class="bg-slate-950/40 text-slate-500 text-[10px] font-black uppercase tracking-widest">
              <th class="px-8 py-5">کات</th>
              <th class="px-8 py-5">لە کوێوە</th>
              <th class="px-8 py-5">بۆ کوێ</th>
              <th class="px-8 py-5">بڕی حەواڵە</th>
              <th class="px-8 py-5">عومولە</th>
              <th class="px-8 py-5">تێبینی</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="t in transfers" :key="t.id" class="hover:bg-white/[0.02] transition-colors group">
              <td class="px-8 py-6 text-slate-400 font-mono text-xs">{{ formatTime(t.created_at) }}</td>
              <td class="px-8 py-6 text-white font-bold">{{ t.from_account?.name }}</td>
              <td class="px-8 py-6 text-white font-bold">{{ t.to_account?.name }}</td>
              <td class="px-8 py-6">
                <span class="text-blue-400 font-black text-lg">{{ formatNumber(t.amount) }}</span>
                <span class="text-slate-500 text-[10px] mr-2 font-black uppercase">{{ t.currency?.code }}</span>
              </td>
              <td class="px-8 py-6">
                 <div v-if="t.commission_amount > 0" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                    <span class="text-emerald-400 font-black">{{ formatNumber(t.commission_amount) }}</span>
                    <span class="text-emerald-500/60 text-[9px] font-black uppercase">{{ getCurrencyCode(t.commission_currency_id) }}</span>
                 </div>
                 <span v-else class="text-slate-600 font-bold text-xs">—</span>
              </td>
              <td class="px-8 py-6 text-slate-500 text-xs font-medium">{{ t.notes || '---' }}</td>
            </tr>
            <tr v-if="transfers.length === 0">
              <td colspan="6" class="px-8 py-20 text-center text-slate-600 font-bold italic">هیچ گواستنەوەیەک نەکراوە</td>
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
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'

const accounts = ref([])
const currencies = ref([])
const transfers = ref([])
const loading = ref(false)

// Display formatting forms
const formText = ref({
  amount: '',
  commission_amount: ''
})

// Submission form
const form = ref({
  from_account_id: '',
  to_account_id: '',
  currency_id: '',
  amount: 0,
  commission_amount: 0,
  commission_currency_id: '',
  notes: ''
})

const selectedCurrencyCode = computed(() => {
  const c = currencies.value.find(c => c.id === form.value.currency_id)
  return c ? c.code : ''
})

const selectedCommissionCurrencyCode = computed(() => {
  const c = currencies.value.find(c => c.id === form.value.commission_currency_id)
  return c ? c.code : ''
})

function formatWithCommas(str) {
  if (!str) return '';
  let clean = str.toString().replace(/[^\d.]/g, '');
  const parts = clean.split('.');
  if (parts.length > 2) clean = parts[0] + '.' + parts.slice(1).join('');
  const [whole, decimal] = clean.split('.');
  const formattedWhole = whole.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return decimal !== undefined ? `${formattedWhole}.${decimal}` : formattedWhole;
}

function updateAmount(field, value) {
  formText.value[field] = formatWithCommas(value);
  form.value[field] = parseFloat(value.replace(/,/g, '')) || 0;
}

function formatNumber(val) {
  return new Intl.NumberFormat().format(val || 0)
}

function formatTime(dateStr) {
  return new Date(dateStr).toLocaleString('en-GB', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit' })
}

function getCurrencyCode(id) {
  const c = currencies.value.find(c => c.id === id)
  return c ? c.code : ''
}

async function fetchData() {
  try {
    const [accRes, curRes, transRes] = await Promise.all([
      axios.get('/accounts?per_page=1000'),
      axios.get('/currencies'),
      axios.get('/transfers')
    ])
    accounts.value = accRes.data.data || accRes.data
    currencies.value = curRes.data
    transfers.value = transRes.data.data || transRes.data
    
    if (currencies.value.length > 0 && !form.value.currency_id) {
      form.value.currency_id = currencies.value[0].id
      form.value.commission_currency_id = currencies.value[0].id
    }
  } catch (e) {
    console.error(e)
  }
}

async function submitTransfer() {
  if (form.value.from_account_id === form.value.to_account_id) {
    return Swal.fire({ icon: 'error', title: 'هەڵە', text: 'ناتوانیت پارە بۆ هەمان حیساب بگوازیتەوە', background: '#0f172a', color: '#fff' })
  }
  
  if (!form.value.amount || form.value.amount <= 0) {
    return Swal.fire({ icon: 'error', title: 'هەڵە', text: 'تکایە بڕی حەواڵە بنووسە', background: '#0f172a', color: '#fff' })
  }

  loading.value = true
  try {
    const { data } = await axios.post('/transfers', form.value)
    
    Swal.fire({
      icon: 'success',
      title: 'سەرکەوتوو بوو',
      text: 'حەواڵەکە و عومولەکەی بە سەرکەوتوویی تۆمارکران',
      background: '#0f172a',
      color: '#fff',
      timer: 2500,
      showConfirmButton: false,
    })

    // Reset Form
    formText.value = { amount: '', commission_amount: '' }
    form.value = {
      from_account_id: '',
      to_account_id: '',
      currency_id: form.value.currency_id,
      amount: 0,
      commission_amount: 0,
      commission_currency_id: form.value.currency_id,
      notes: ''
    }
    
    // Add to top of list for instant feedback
    if (data.transfer) {
       transfers.value.unshift(data.transfer)
    }
    
  } catch (e) {
    Swal.fire({
      icon: 'error',
      title: 'هەڵە لە حەواڵەکردن',
      text: e.response?.data?.error || e.response?.data?.message || 'سێرڤەر وەڵامی نییە',
      background: '#0f172a',
      color: '#fff'
    })
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.6s cubic-bezier(0.22, 1, 0.36, 1); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

.custom-scrollbar::-webkit-scrollbar { height: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.2); border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.4); }
</style>
