<template>
  <div class="max-w-[1400px] mx-auto">
    <!-- Header -->
    <div class="bg-white dark:bg-slate-800 rounded-t-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex justify-between items-center mb-4">
      <h2 class="text-2xl font-black text-[#0A5C36] dark:text-[#34D399] drop-shadow-sm">کڕین و فرۆشتن</h2>
      <div class="flex gap-4 items-center">
        <span class="font-bold">جۆری مامەڵە:</span>
        <select class="bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-4 py-2 font-bold text-slate-800 dark:text-slate-200 min-w-[200px]">
          <option>دینار - دۆلار</option>
          <option>تمەن + دۆلار</option>
          <option>دۆلار - یۆرۆ</option>
        </select>
        <button class="bg-[#10B981] text-white px-6 py-2 rounded-lg font-bold hover:bg-[#059669] shadow-md transition">نوێکردنەوە</button>
      </div>
    </div>

    <!-- Main Split Screen -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
      
      <!-- Right Side (لەلایەن ئێمەوە) -->
      <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md rounded-2xl p-6 border-t-4 border-[#0A5C36] shadow-lg flex flex-col gap-6">
        <h3 class="text-xl font-bold border-b border-slate-200 dark:border-slate-700 pb-2 text-[#0A5C36] dark:text-[#34D399]">لەلایەن ئێمەوە</h3>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-bold text-slate-500 mb-1">بڕی دراو</label>
            <input type="number" class="w-full bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-4 py-3 text-xl font-mono text-center font-bold" placeholder="0.00" />
          </div>
          <div>
            <label class="block text-sm font-bold text-slate-500 mb-1">جۆری دراو</label>
            <select class="w-full bg-[#D1FAE5] dark:bg-[#0A5C36] text-[#0A5C36] dark:text-[#A7F3D0] border-0 rounded-xl px-4 py-3 text-xl font-bold text-center">
              <option>دۆلار</option>
              <option>دینار</option>
            </select>
          </div>
        </div>

        <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
           <label class="block text-sm font-bold text-slate-500 mb-2">لە قاصەی</label>
           <select class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-4 py-2 font-bold text-lg">
             <option>سندوقی سەرەکی</option>
           </select>
        </div>
      </div>

      <!-- Center Exchange Icon -->
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#10B981] text-white w-14 h-14 rounded-full flex items-center justify-center shadow-2xl border-4 border-slate-100 dark:border-slate-900 z-10 hidden md:flex">
         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
      </div>

      <!-- Left Side (لەلایەن کڕیار) -->
      <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md rounded-2xl p-6 border-t-4 border-red-500 shadow-lg flex flex-col gap-6">
        <h3 class="text-xl font-bold border-b border-slate-200 dark:border-slate-700 pb-2 text-red-600 dark:text-red-400">لەلایەن کڕیار</h3>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-bold text-slate-500 mb-1">بڕی دراو</label>
            <input type="number" class="w-full bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-4 py-3 text-xl font-mono text-center font-bold" placeholder="0.00" />
          </div>
          <div>
            <label class="block text-sm font-bold text-slate-500 mb-1">جۆری دراو</label>
            <select class="w-full bg-rose-100 dark:bg-red-900 text-red-700 dark:text-rose-200 border-0 rounded-xl px-4 py-3 text-xl font-bold text-center">
              <option>دینار</option>
              <option>دۆلار</option>
            </select>
          </div>
        </div>

        <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
           <label class="block text-sm font-bold text-slate-500 mb-2">نرخی ئاڵوگۆڕ (Rate)</label>
           <input type="number" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-4 py-2 font-bold font-mono text-2xl text-center" value="152.000" />
        </div>
      </div>

    </div>

    <!-- Bottom Actions & Print -->
    <div class="mt-6 bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm">
       <div class="flex items-end justify-between">
         <div class="w-1/2">
            <label class="block text-sm font-bold text-slate-500 mb-1">تێبینی / ناو</label>
            <input type="text" class="w-full bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-4 py-3 font-bold" placeholder="ناوی کڕیار یان تێبینی بنووسە..." />
         </div>
         <div class="flex gap-4">
            <button class="bg-slate-800 dark:bg-black text-white px-8 py-4 rounded-xl font-black text-xl shadow-xl hover:bg-slate-700 hover:-translate-y-1 transition flex items-center gap-2">
               <span>چاپکردنی وەسڵ</span>
               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            </button>
            <button class="bg-[#10B981] text-white px-8 py-4 rounded-xl font-black text-xl shadow-xl hover:bg-[#059669] hover:-translate-y-1 transition">
               پەسەندکردن
            </button>
         </div>
       </div>
    </div>
  </div>
</template>

<script setup>
</script>
