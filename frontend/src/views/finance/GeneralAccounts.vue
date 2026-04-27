<template>
  <div class="p-6 md:p-10 space-y-8 max-w-[1600px] mx-auto" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-10">
       <div>
          <h1 class="text-4xl font-black text-white tracking-tighter">ڕاپۆرتی ووردبینی و قازانج</h1>
          <p class="text-slate-400 font-medium mt-2 flex items-center gap-2">
             <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
             بەدواداچوونی دارایی و قازانج بە شێوازی موەحەد
          </p>
       </div>
       
       <div class="flex flex-wrap items-center gap-3 bg-slate-900/60 backdrop-blur-xl p-3 rounded-[2rem] border border-slate-800 shadow-2xl">
          <div class="flex flex-col px-4 border-l border-slate-800/50">
             <span class="text-[9px] font-black text-slate-500 uppercase">لە بەرواری</span>
             <input v-model="filters.start_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-white focus:ring-0 cursor-pointer" />
          </div>
          <div class="flex flex-col px-4 border-l border-slate-800/50">
             <span class="text-[9px] font-black text-slate-500 uppercase">بۆ بەرواری</span>
             <input v-model="filters.end_date" type="date" class="bg-transparent border-none p-0 text-sm font-black text-white focus:ring-0 cursor-pointer" />
          </div>
          <button @click="fetchReport" :disabled="loading" class="bg-emerald-600 hover:bg-emerald-500 text-white w-12 h-12 rounded-2xl flex items-center justify-center transition-all shadow-lg">
             <svg v-if="!loading" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
             <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
          </button>
       </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left: Asset Balances (Vaults) -->
      <div class="lg:col-span-2 space-y-6">
        <h3 class="text-xl font-black text-white">باڵانسی سندوقەکان (Assets)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="asset in reportData.assets" :key="asset.name" 
               class="bg-slate-900/40 border border-slate-800 p-8 rounded-[2.5rem] relative overflow-hidden group">
            <h4 class="text-xs font-black text-slate-500 uppercase mb-4 tracking-widest">{{ asset.name }}</h4>
            <div class="space-y-4">
              <div v-for="bal in asset.balances" :key="bal.currency" class="flex justify-between items-center">
                <span class="text-sm font-black text-emerald-500">{{ bal.currency }}</span>
                <span class="text-2xl font-black text-white">{{ formatNum(bal.balance) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Profit Summary -->
      <div class="space-y-6">
        <h3 class="text-xl font-black text-white">قازانجی ئەم ماوەیە (Net Profit)</h3>
        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 p-8 rounded-[3rem] shadow-2xl relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-2xl"></div>
          <div v-if="reportData.profits && reportData.profits.length" class="space-y-6">
            <div v-for="p in reportData.profits" :key="p.currency_id" class="border-b border-white/10 pb-4 last:border-0">
              <p class="text-[10px] font-black text-white/70 uppercase mb-1">قازانج بە {{ p.currency?.code }}</p>
              <p class="text-3xl font-black text-white">{{ formatNum(p.total_profit) }} <span class="text-sm">{{ p.currency?.code }}</span></p>
            </div>
          </div>
          <div v-else class="text-white/60 font-bold">هیچ قازانجێک تۆمار نەکراوە بۆ ئەم بەروارە</div>
        </div>

        <div class="bg-slate-900/40 border border-slate-800 p-8 rounded-[2.5rem]">
           <h4 class="text-xs font-black text-slate-400 uppercase mb-6">زانیاری زیاتر</h4>
           <p class="text-[11px] text-slate-500 leading-relaxed font-medium">
             ئەم ڕاپۆرتە تەنها داتاکانی حیسابی داهات (Revenue Accounts) وەردەگرێت بۆ ئەوەی دڵنیابین قازانجی ڕاستەقینە پیشان دەدەین.
           </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'

const loading = ref(false)
const filters = ref({
  start_date: new Date().toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0]
})

const reportData = ref({
  profits: [],
  assets: []
})

async function fetchReport() {
  loading.value = true
  try {
    const { data } = await axios.get('/reports/profit', { params: filters.value })
    reportData.value = data
  } catch (e) {
    console.error('Error fetching report:', e)
  } finally {
    loading.value = false
  }
}

function formatNum(val) {
  return new Intl.NumberFormat().format(parseFloat(val || 0))
}

onMounted(() => {
  fetchReport()
})
</script>
