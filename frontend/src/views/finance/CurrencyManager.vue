<template>
  <div class="space-y-10 animate-fade-in p-2 text-slate-800 font-sans">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 bg-white p-10 rounded-[3rem] border border-slate-200 shadow-sm relative overflow-hidden">
      <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-600/5 rounded-full blur-[100px] -mr-32 -mt-32"></div>
      
      <div dir="rtl" class="relative z-10 text-right">
        <h2 class="text-xs font-black text-emerald-700 uppercase tracking-[0.3em] mb-3">Global Finance</h2>
        <h1 class="text-4xl font-black text-slate-900 tracking-tighter mb-2">ڕێکخستنی دراوەکان</h1>
        <p class="text-slate-600 font-medium text-lg">دیاریکردنی دراوی سەرەکی و نرخی ئاڵوگۆڕی ڕۆژ</p>
      </div>

      <div class="flex items-center gap-4 relative z-10">
        <!-- Base Currency Badge -->
        <div class="px-8 py-6 bg-emerald-50 border border-emerald-200 rounded-[2rem] flex flex-col items-center shadow-xs">
           <span class="text-[10px] font-black text-emerald-700 uppercase tracking-[0.3em] mb-2 font-mono">Base Currency</span>
           <div class="flex items-center gap-3">
              <span class="text-4xl font-black text-slate-900 font-mono">IQD</span>
              <span class="text-sm font-black text-emerald-700 uppercase">دیناری عێراقی</span>
           </div>
        </div>
      </div>
    </div>

    <!-- Currency Management Panel -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8" dir="rtl">
      <!-- Left side: Explanation & Guide -->
      <div class="bg-white border border-slate-200 rounded-[3.5rem] p-10 shadow-sm text-right flex flex-col justify-between">
        <div>
          <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-700 mb-6 border border-emerald-200 shadow-xs">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h3 class="text-2xl font-black text-slate-900 mb-4">ڕێبەری نرخەکان</h3>
          <p class="text-slate-600 leading-relaxed text-md mb-6 font-semibold">
            لێرەوە دەتوانیت نرخی فەرمی سیستەم (System Exchange Rate) بۆ هەموو دراوەکان نوێ بکەیتەوە. 
          </p>
          <div class="space-y-4 font-semibold">
            <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 shadow-xs">
              <div class="w-2 h-2 bg-emerald-600 rounded-full mt-2"></div>
              <p class="text-slate-700 text-sm">نرخەکان بە شێوەی دراوی تاک (Unit) خەزن دەبن بۆ پاراستنی باڵانسی حیسابات.</p>
            </div>
            <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 shadow-xs">
              <div class="w-2 h-2 bg-emerald-600 rounded-full mt-2"></div>
              <p class="text-slate-700 text-sm">ئەگەر نرخێک تۆمار نەکەیت، سیستمەکە بە شێوازی تێچووی کۆتا کڕین (Last Buy Rate) قازانجت بۆ حیساب دەکات.</p>
            </div>
          </div>
        </div>
        
        <div class="pt-8 border-t border-slate-100 mt-8 flex items-center justify-between text-slate-500 shadow-xs">
          <span class="text-xs font-bold uppercase tracking-wider">Secure Audit System</span>
          <span class="text-xs font-bold uppercase tracking-wider font-mono">v2.1</span>
        </div>
      </div>

      <!-- Right side: Dynamic Currency Rates List (2/3 width) -->
      <div class="xl:col-span-2 space-y-6">
        <div v-for="c in activeCurrencies" :key="c.id" class="bg-white border border-slate-200 rounded-[2.5rem] p-8 hover:border-emerald-300 transition-all duration-300 shadow-sm">
          <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            
            <!-- Currency Icon & Name -->
            <div class="flex items-center gap-5 text-right">
              <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-black shadow-xs border border-slate-100"
                   :class="c.code === 'USD' ? 'bg-blue-50 text-blue-700' : c.code === 'GBP' ? 'bg-purple-50 text-purple-700' : c.code === 'EUR' ? 'bg-indigo-50 text-indigo-700' : c.code === 'TRY' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700'">
                {{ c.symbol }}
              </div>
              <div>
                <div class="flex items-center gap-3">
                  <h4 class="text-2xl font-black text-slate-900 font-mono">{{ c.code }}</h4>
                  <span class="text-xs font-bold text-slate-500">({{ c.name }})</span>
                </div>
                <!-- Interactive Preview Labels -->
                <p class="text-slate-600 text-sm font-semibold mt-1">
                  نرخی فەرمی ئێستا: 
                  <span class="text-emerald-700 font-mono font-black text-base">{{ formatNum(c.current_rate) }} دینار</span>
                  <span class="text-slate-300 font-bold mx-2">|</span>
                  بۆ ١٠٠ {{ c.code }}: 
                  <span class="text-slate-800 font-mono font-bold">{{ formatNum(c.current_rate * (1/getMultiplier(c.code))) }} دینار</span>
                </p>
              </div>
            </div>

            <!-- Edit Rate Action Form -->
            <div class="flex items-center gap-3 w-full lg:w-auto font-semibold">
              <div class="relative flex-1 lg:flex-initial">
                <input 
                  v-model="rates[c.code]"
                  type="number" 
                  step="0.000001"
                  placeholder="نرخی نوێ"
                  class="w-full lg:w-48 bg-slate-50 border border-slate-200 text-emerald-800 font-mono font-black text-xl rounded-2xl px-5 py-4 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 transition-all text-center shadow-xs"
                />
              </div>
              <button 
                @click="updateCurrencyRate(c.code, rates[c.code])"
                :disabled="updatingId === c.code"
                class="px-6 py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-md font-black rounded-2xl shadow-md shadow-emerald-600/10 active:scale-95 flex items-center justify-center gap-2 transition-all"
              >
                <span v-if="updatingId !== c.code">نوێکردنەوە</span>
                <svg v-else class="animate-spin h-5 w-5 text-current" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'

const currencies = ref([])
const loading = ref(false)
const updatingId = ref(null)
const rates = ref({})

// Get list of non-base active currencies to display for rating updates
const activeCurrencies = computed(() => {
  return currencies.value.filter(c => !c.is_base)
})

const getMultiplier = (code) => {
  if (code === 'IQD') return 1.0
  if (code === 'IRR') return 0.0000001
  return 0.01
}

const fetchCurrencies = async () => {
  try {
    const { data } = await axios.get('/currencies')
    currencies.value = data.data || data || []
    
    // Initialize rates input
    currencies.value.forEach(c => {
      rates.value[c.code] = c.current_rate
    })
  } catch (err) {
    console.error('Error fetching currencies:', err)
  }
}

const updateCurrencyRate = async (code, rate) => {
  if (!rate || rate <= 0) {
    Swal.fire({ 
      icon: 'warning', 
      title: 'ئاگاداری', 
      text: 'تکایە نرخێکی دروست بنووسە', 
      background: '#ffffff', 
      color: '#0f172a',
      customClass: { popup: 'rounded-[2.5rem] border border-slate-200 shadow-2xl' }
    })
    return
  }

  updatingId.value = code
  try {
    await axios.post('/currencies/update-rate', { 
      to: code, 
      rate: parseFloat(rate)
    })
    
    await fetchCurrencies() // Refresh display

    Swal.fire({
      icon: 'success',
      title: 'نرخ جێگیرکرا',
      text: `نرخی فەرمی دراوی ${code} بە سەرکەوتوویی جێگیرکرا`,
      background: '#ffffff',
      color: '#0f172a',
      confirmButtonColor: '#10b981',
      customClass: { popup: 'rounded-[2.5rem] border border-slate-200 shadow-2xl' }
    })
  } catch (err) {
    console.error(err)
    Swal.fire({ 
      icon: 'error', 
      title: 'هەڵە', 
      text: 'نەتوانرا نرخەکە جێگیر بکرێت', 
      background: '#ffffff', 
      color: '#0f172a',
      customClass: { popup: 'rounded-[2.5rem] border border-slate-200 shadow-2xl' }
    })
  } finally {
    updatingId.value = null
  }
}

function formatNum(val) {
  return new Intl.NumberFormat().format(val || 0)
}

onMounted(() => {
  fetchCurrencies()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.8s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
