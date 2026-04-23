<template>
  <div class="flex items-center justify-center min-h-[60vh]">
    <div class="text-center space-y-4">
      <div class="w-24 h-24 mx-auto rounded-full bg-[#10B981]/10 flex items-center justify-center">
        <svg class="w-12 h-12 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
      </div>
      <h2 class="text-3xl font-black text-white">{{ title }}</h2>
      <p class="text-slate-400 text-lg">ئەم بەشە بەم زووانە دروست دەکرێت</p>
      <button @click="$router.push('/')" class="px-8 py-3 bg-gradient-to-r from-[#10B981] to-emerald-500 text-white font-bold rounded-xl shadow-lg hover:shadow-emerald-500/30 transition-all active:scale-95">
        گەڕانەوە بۆ داشبۆرد
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const title = computed(() => {
  if (route.path.includes('hawala')) return 'حەواڵەکان'
  if (route.path.includes('vaults')) return 'قاصەکان'
  return 'بەم زووانە'
})
</script>
