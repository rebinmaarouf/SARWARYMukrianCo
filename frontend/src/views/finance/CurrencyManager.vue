<template>
  <div class="space-y-10 animate-fade-in p-2">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 bg-slate-900/40 backdrop-blur-3xl p-10 rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden">
      <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 rounded-full blur-[100px] -mr-32 -mt-32"></div>
      
      <div dir="rtl" class="relative z-10 text-right">
        <h2 class="text-xs font-black text-emerald-500 uppercase tracking-[0.3em] mb-3">Global Finance</h2>
        <h1 class="text-4xl font-black text-white tracking-tighter mb-2">ڕێکخستنی دراوەکان</h1>
        <p class="text-slate-500 font-medium text-lg">دیاریکردنی دراوی سەرەکی و نرخی ئاڵوگۆڕی ڕۆژ</p>
      </div>

      <div class="flex items-center gap-4 relative z-10">
        <!-- Current Rate Badge (NEW) -->
        <div class="px-8 py-6 bg-emerald-500/10 border-2 border-emerald-500/30 rounded-[2rem] flex flex-col items-center shadow-2xl shadow-emerald-500/10">
           <span class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] mb-2">Current Active Rate</span>
           <div class="flex items-center gap-3">
              <span class="text-4xl font-black text-white font-mono">{{ formatNum(currentRate) }}</span>
              <span class="text-sm font-black text-emerald-500 uppercase">IQD</span>
           </div>
        </div>
      </div>
    </div>

    <!-- Currency Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
      <!-- Exchange Rate Card -->
      <div class="bg-slate-900/60 backdrop-blur-3xl border border-white/10 rounded-[3.5rem] p-12 shadow-3xl relative group">
        <div class="absolute top-12 left-12 w-20 h-20 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform duration-500">
           <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>

        <div dir="rtl" class="mb-12 text-right">
           <h3 class="text-2xl font-black text-white mb-2">گۆڕینی نرخی ئاڵوگۆڕ</h3>
           <p class="text-slate-500 font-medium">بۆ گۆڕینی نرخ، بڕە نوێیەکە لێرە داخڵ بکە</p>
        </div>

        <div class="space-y-8">
           <div class="relative">
              <div class="absolute inset-y-0 left-8 flex items-center pointer-events-none">
                 <span class="text-3xl font-black text-slate-700 uppercase">IQD</span>
              </div>
              <input 
                v-model="rateInput"
                type="number" 
                placeholder="1515"
                class="w-full bg-slate-950/50 border-4 border-slate-800 text-emerald-400 text-6xl font-black rounded-[2.5rem] px-10 py-12 focus:outline-none focus:border-emerald-500/50 transition-all shadow-inner text-center"
              />
           </div>

           <button 
             @click="saveRate"
             :disabled="loading"
             class="w-full h-24 bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 text-2xl font-black rounded-[2rem] shadow-2xl shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:-translate-y-1 active:scale-95 transition-all flex items-center justify-center gap-4"
           >
             <span v-if="!loading">تۆمارکردنی نرخی نوێ</span>
             <svg v-else class="animate-spin h-8 w-8 text-slate-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
             <svg v-if="!loading" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
           </button>
        </div>
      </div>

      <!-- Currency Details -->
      <div class="grid grid-cols-1 gap-6">
        <div class="bg-blue-600/10 border border-blue-500/20 rounded-[2.5rem] p-10 flex items-center justify-between group">
           <div class="space-y-1 text-right">
              <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Global Market</span>
              <h4 class="text-2xl font-black text-white">United States Dollar</h4>
              <p class="text-blue-400/60 font-bold">دراوی بنەڕەتی سیستم ($)</p>
           </div>
           <div class="text-5xl font-black text-blue-500 opacity-20 group-hover:opacity-100 transition-opacity">$</div>
        </div>

        <div class="bg-amber-600/10 border border-amber-500/20 rounded-[2.5rem] p-10 flex items-center justify-between group">
           <div class="space-y-1 text-right">
              <span class="text-[10px] font-black text-amber-400 uppercase tracking-widest">Local Market</span>
              <h4 class="text-2xl font-black text-white">Iraqi Dinar</h4>
              <p class="text-amber-400/60 font-bold">دراوی لاوەکی (IQD)</p>
           </div>
           <div class="text-4xl font-black text-amber-500 opacity-20 group-hover:opacity-100 transition-opacity">ع.د</div>
        </div>

        <div class="bg-slate-900/40 border border-white/5 rounded-[2.5rem] p-8 flex items-center justify-between gap-6" dir="rtl">
           <div class="flex items-center gap-6">
              <div class="w-16 h-16 bg-slate-800 rounded-2xl flex items-center justify-center">
                 <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div class="text-right">
                 <p class="text-slate-500 text-sm font-bold uppercase tracking-widest">کۆتا نوێکردنەوە</p>
                 <p class="text-white font-black">{{ lastUpdated || 'ئێستا' }}</p>
              </div>
           </div>
           <div class="text-emerald-500 text-xs font-black bg-emerald-500/10 px-4 py-2 rounded-full border border-emerald-500/20">
              SECURE
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

const rateInput = ref(null)
const currentRate = ref(0)
const lastUpdated = ref('')
const loading = ref(false)

const fetchRate = async () => {
  try {
    const { data } = await axios.get('/currencies')
    const iqd = data.find(c => c.code === 'IQD')
    if (iqd) {
      currentRate.value = iqd.exchange_rate
      lastUpdated.value = new Date(iqd.updated_at).toLocaleString('en-US', { 
        hour: '2-digit', minute: '2-digit', second: '2-digit',
        day: 'numeric', month: 'short' 
      })
    }
  } catch (err) {
    console.error('Error fetching rate:', err)
  }
}

const saveRate = async () => {
  if (!rateInput.value || rateInput.value <= 0) {
    Swal.fire({ icon: 'warning', title: 'ئاگاداری', text: 'تکایە نرخێکی دروست بنووسە', background: '#0f172a', color: '#fff' })
    return
  }

  loading.value = true
  try {
    await axios.post('/currencies/update-rate', { 
      to: 'IQD', 
      rate: rateInput.value 
    })
    
    await fetchRate() // Refresh the current rate display
    rateInput.value = null // Clear input after success

    Swal.fire({
      icon: 'success',
      title: 'نرخ جێگیرکرا',
      text: `نرخی ١ دۆلار بە ${currentRate.value} دینار جێگیرکرا`,
      background: '#0f172a',
      color: '#fff',
      confirmButtonColor: '#10b981'
    })
  } catch (err) {
    console.error(err)
    Swal.fire({ icon: 'error', title: 'هەڵە', text: 'نەتوانرا نرخەکە جێگیر بکرێت', background: '#0f172a', color: '#fff' })
  } finally {
    loading.value = false
  }
}

function formatNum(val) {
  return new Intl.NumberFormat().format(val || 0)
}

onMounted(() => {
  fetchRate()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.8s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
