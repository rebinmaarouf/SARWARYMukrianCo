<template>
  <div class="space-y-8 animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 bg-slate-900/40 backdrop-blur-3xl p-8 rounded-[2.5rem] border border-white/5">
      <div dir="rtl" class="text-right">
        <h1 class="text-3xl font-black text-white tracking-tight italic">گواستنەوەی پارە</h1>
        <p class="text-slate-500 font-medium mt-1 uppercase text-xs tracking-[0.2em]">Money Transfer & Internal Movement</p>
      </div>
      <div class="p-3 bg-blue-500/10 rounded-2xl border border-blue-500/20">
        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
      </div>
    </div>

    <!-- Transfer Form Card -->
    <div class="max-w-4xl mx-auto">
      <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl relative group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
        
        <div class="p-10 relative">
          <form @submit.prevent="submitTransfer" class="space-y-10" dir="rtl">
            
            <!-- Accounts Selection Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
              <!-- From Account -->
              <div class="space-y-4">
                <label class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] px-2 flex items-center gap-2">
                  <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                  لە کوێوە (Source)
                </label>
                <div class="relative group/input">
                  <select v-model="form.from_account_id" required 
                    class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-5 text-white font-black text-lg focus:border-blue-500 outline-none appearance-none cursor-pointer transition-all">
                    <option value="" disabled>هەڵبژاردنی حیساب</option>
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
                  بۆ کوێ (Destination)
                </label>
                <div class="relative group/input">
                  <select v-model="form.to_account_id" required 
                    class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-5 text-white font-black text-lg focus:border-emerald-500 outline-none appearance-none cursor-pointer transition-all">
                    <option value="" disabled>هەڵبژاردنی حیساب</option>
                    <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }} ({{ acc.code }})</option>
                  </select>
                  <div class="absolute left-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Amount & Currency Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
               <!-- Currency -->
               <div class="space-y-4">
                <label class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] px-2">دراو (Currency)</label>
                <div class="flex gap-2 p-1 bg-slate-950 rounded-2xl border border-white/5">
                  <button v-for="c in currencies" :key="c.id" type="button"
                    @click="form.currency_id = c.id"
                    class="flex-1 py-4 rounded-xl text-xs font-black uppercase transition-all"
                    :class="form.currency_id === c.id ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-500 hover:text-white'">
                    {{ c.code }}
                  </button>
                </div>
              </div>

              <!-- Amount -->
              <div class="space-y-4">
                <label class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] px-2">بڕی پارە (Amount)</label>
                <div class="relative group/input">
                   <input v-model="form.amount" type="number" step="0.01" required placeholder="0.00"
                    class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-5 text-white font-mono font-black text-3xl focus:border-blue-500 outline-none transition-all placeholder:text-slate-800" />
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div class="space-y-4">
              <label class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] px-2">تێبینی یان مێژوو (Notes)</label>
              <textarea v-model="form.notes" rows="3" placeholder="هۆکاری گواستنەوە بنووسە..."
                class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-5 text-white font-bold focus:border-blue-500 outline-none transition-all placeholder:text-slate-800"></textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-6">
              <button type="submit" :disabled="loading"
                class="w-full py-6 bg-blue-600 hover:bg-blue-500 text-white font-black text-xl rounded-2xl shadow-2xl shadow-blue-600/20 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-4">
                <svg v-if="!loading" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                <span v-if="loading" class="animate-spin rounded-full h-6 w-6 border-4 border-white/20 border-t-white"></span>
                {{ loading ? 'خەریکی گواستنەوەیە...' : 'تەواوکردنی گواستنەوە' }}
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>

    <!-- Recent Transfers Table -->
    <div class="bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
      <div class="p-8 border-b border-white/5 flex items-center justify-between" dir="rtl">
        <h2 class="text-xl font-black text-white">دوایین گواستنەوەکان</h2>
        <button @click="fetchData" class="p-2 text-slate-500 hover:text-blue-400 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-right" dir="rtl">
          <thead>
            <tr class="bg-white/5 text-slate-500 text-[10px] font-black uppercase tracking-widest">
              <th class="px-8 py-4">ڕێکەوت</th>
              <th class="px-8 py-4">لە کوێوە</th>
              <th class="px-8 py-4">بۆ کوێ</th>
              <th class="px-8 py-4">بڕی پارە</th>
              <th class="px-8 py-4">تێبینی</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="t in transfers" :key="t.id" class="hover:bg-white/[0.02] transition-colors group">
              <td class="px-8 py-5 text-slate-400 font-mono text-xs italic">{{ new Date(t.created_at).toLocaleString('en-GB') }}</td>
              <td class="px-8 py-5 text-white font-bold">{{ t.from_account?.name }}</td>
              <td class="px-8 py-5 text-white font-bold">{{ t.to_account?.name }}</td>
              <td class="px-8 py-5">
                <span class="text-emerald-400 font-black text-lg">{{ Number(t.amount).toLocaleString() }}</span>
                <span class="text-slate-500 text-[10px] mr-2 font-black uppercase">{{ t.currency?.code }}</span>
              </td>
              <td class="px-8 py-5 text-slate-500 text-sm italic">{{ t.notes || '---' }}</td>
            </tr>
            <tr v-if="transfers.length === 0">
              <td colspan="5" class="px-8 py-20 text-center text-slate-600 font-bold italic">هیچ گواستنەوەیەک نەکراوە</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'

const accounts = ref([])
const currencies = ref([])
const transfers = ref([])
const loading = ref(false)

const form = ref({
  from_account_id: '',
  to_account_id: '',
  currency_id: '',
  amount: '',
  notes: ''
})

async function fetchData() {
  try {
    const [accRes, curRes, transRes] = await Promise.all([
      axios.get('/accounts'),
      axios.get('/currencies'),
      axios.get('/transfers')
    ])
    accounts.value = accRes.data.data || accRes.data
    currencies.value = curRes.data
    transfers.value = transRes.data.data || transRes.data
    if (currencies.value.length > 0 && !form.value.currency_id) form.value.currency_id = currencies.value[0].id
  } catch (e) {
    console.error(e)
  }
}

async function submitTransfer() {
  if (form.value.from_account_id === form.value.to_account_id) {
    return Swal.fire({ icon: 'error', title: 'هەڵە', text: 'ناتوانیت پارە بۆ هەمان حیساب بگوازیتەوە', background: 'rgba(15, 23, 42, 0.9)', color: '#fff' })
  }

  loading.value = true
  try {
    await axios.post('/transfers', form.value)
    
    Swal.fire({
      icon: 'success',
      title: 'سەرکەوتوو بوو',
      text: 'پارەکە بە سەرکەوتوویی گواسترایەوە',
      background: 'rgba(15, 23, 42, 0.9)',
      color: '#fff',
      timer: 2000,
      showConfirmButton: false,
      customClass: { popup: 'rounded-[2.5rem] border border-white/10 shadow-3xl' }
    })

    form.value = {
      from_account_id: '',
      to_account_id: '',
      currency_id: currencies.value[0]?.id || '',
      amount: '',
      notes: ''
    }
    
    // Refresh list and dashboard
    await fetchData()
  } catch (e) {
    Swal.fire({
      icon: 'error',
      title: 'هەڵە لە گواستنەوە',
      text: e.response?.data?.error || 'سێرڤەر وەڵامی نییە',
      background: 'rgba(15, 23, 42, 0.9)',
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
</style>
