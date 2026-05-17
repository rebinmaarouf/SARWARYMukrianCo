<template>
  <div class="space-y-8 animate-fade-in text-slate-800 font-sans p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-center justify-between bg-white/90 backdrop-blur-md p-8 rounded-[2.5rem] border border-slate-200 shadow-lg">
      <div class="text-right order-2 md:order-1">
        <h2 class="text-3xl font-black text-slate-900 mb-2">حساب خیتامی (تەرازووی پێداچوونەوە)</h2>
        <p class="text-slate-600 font-medium">بینینی باڵانسی سەرجەم حیسابات بە شێوەیەکی گشتی</p>
      </div>
      
      <!-- Filters -->
      <div class="flex flex-wrap items-center gap-4 order-1 md:order-2 mb-4 md:mb-0">
        <!-- Currency Toggle -->
        <div class="flex bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
          <button 
            @click="activeCurrency = 'USD'" 
            :class="activeCurrency === 'USD' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:text-slate-900'"
            class="px-5 py-2.5 rounded-xl font-black text-xs transition-all"
          >
            USD
          </button>
          <button 
            @click="activeCurrency = 'IQD'" 
            :class="activeCurrency === 'IQD' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:text-slate-900'"
            class="px-5 py-2.5 rounded-xl font-black text-xs transition-all"
          >
            IQD
          </button>
        </div>

        <!-- Date Picker -->
        <div class="relative">
          <input 
            type="date" 
            v-model="selectedDate" 
            @change="fetchData"
            class="bg-slate-50 border border-slate-200 text-slate-900 rounded-2xl px-5 py-3 focus:border-emerald-600 transition-all font-bold outline-none shadow-xs"
          />
        </div>

        <!-- Branch Select (Optional for Super Admin) -->
        <div v-if="auth.user?.roles?.some(r => r.name === 'Super Admin')" class="relative">
          <select 
            v-model="selectedBranch" 
            @change="fetchData"
            class="bg-slate-50 border border-slate-200 text-slate-900 rounded-2xl px-5 py-3 focus:border-emerald-600 transition-all font-bold outline-none shadow-xs pr-10"
          >
            <option value="">گشت لقەکان</option>
            <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex flex-col items-center justify-center py-20 gap-4">
      <div class="w-12 h-12 border-4 border-slate-200 border-t-emerald-600 rounded-full animate-spin"></div>
      <p class="text-xs font-black text-slate-500 uppercase tracking-widest animate-pulse">شیکردنەوەی حیسابات...</p>
    </div>

    <!-- Content -->
    <div v-else-if="activeData" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      
      <!-- Right Side: Debits (قەرزدارەکان / مەوجودات) -->
      <div class="bg-white/80 backdrop-blur-md border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-lg">
        <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center" dir="rtl">
          <h3 class="text-xl font-black text-slate-900">قەرزدارەکان (Debit)</h3>
          <span class="text-emerald-600 font-black text-lg">{{ formatNumber(activeData.total_debits) }} {{ activeData.currency_symbol }}</span>
        </div>
        
        <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
          <table class="w-full text-right" dir="rtl">
            <thead>
              <tr class="text-slate-500 text-xs font-black uppercase tracking-widest border-b border-slate-100">
                <th class="pb-3 pr-4">کۆد</th>
                <th class="pb-3">ناوی حیساب</th>
                <th class="pb-3 pl-4 text-left">بڕ (Debit)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in activeData.debits" :key="item.account_id" class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                <td class="py-4 pr-4 font-bold text-slate-500">{{ item.account_code }}</td>
                <td class="py-4 font-bold text-slate-900">{{ item.account_name }}</td>
                <td class="py-4 pl-4 text-left font-black text-emerald-600">{{ formatNumber(item.balance) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Left Side: Credits (قەرزەکان / مطلوبات) -->
      <div class="bg-white/80 backdrop-blur-md border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-lg">
        <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center" dir="rtl">
          <h3 class="text-xl font-black text-slate-900">قەرزەکان (Credit)</h3>
          <span class="text-rose-600 font-black text-lg">{{ formatNumber(activeData.total_credits) }} {{ activeData.currency_symbol }}</span>
        </div>
        
        <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
          <table class="w-full text-right" dir="rtl">
            <thead>
              <tr class="text-slate-500 text-xs font-black uppercase tracking-widest border-b border-slate-100">
                <th class="pb-3 pr-4">کۆد</th>
                <th class="pb-3">ناوی حیساب</th>
                <th class="pb-3 pl-4 text-left">بڕ (Credit)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in activeData.credits" :key="item.account_id" class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                <td class="py-4 pr-4 font-bold text-slate-500">{{ item.account_code }}</td>
                <td class="py-4 font-bold text-slate-900">{{ item.account_name }}</td>
                <td class="py-4 pl-4 text-left font-black text-rose-600">{{ formatNumber(item.balance) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- Empty State -->
    <div v-else class="bg-white border border-slate-200 rounded-[3rem] p-20 text-center shadow-lg">
      <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-600 border border-slate-200 shadow-xs">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 2 0 01.707.293l5.414 5.414a1 2 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      </div>
      <h3 class="text-xl font-black text-slate-900 mb-2">هیچ داتایەک نییە</h3>
      <p class="text-slate-600 font-medium max-w-xs mx-auto">هیچ حیساباتێک نەدۆزرایەوە بۆ ئەم بەروارە یان ئەم دراوە.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '@/plugins/axios'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const isLoading = ref(false)
const selectedDate = ref(new Date().toISOString().substr(0, 10))
const selectedBranch = ref('')
const activeCurrency = ref('USD')
const rawData = ref([])
const branches = ref([])

const fetchData = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('/closing-account', {
      params: {
        date: selectedDate.value,
        branch_id: selectedBranch.value
      }
    })
    rawData.value = response.data
    
    // Set default active currency if available
    if (rawData.value.length > 0 && !rawData.value.find(c => c.currency_code === activeCurrency.value)) {
      activeCurrency.value = rawData.value[0].currency_code
    }
  } catch (error) {
    console.error('Failed to fetch closing account data:', error)
  } finally {
    isLoading.value = false
  }
}

const fetchBranches = async () => {
  try {
    const response = await axios.get('/branches')
    branches.value = response.data
  } catch (error) {
    console.error('Failed to fetch branches:', error)
  }
}

const activeData = computed(() => {
  return rawData.value.find(c => c.currency_code === activeCurrency.value)
})

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num)
}

onMounted(() => {
  fetchData()
  if (auth.user?.roles?.some(r => r.name === 'Super Admin')) {
    fetchBranches()
  }
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(241, 245, 249, 0.5);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(203, 213, 225, 0.5);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(148, 163, 184, 0.5);
}
</style>
