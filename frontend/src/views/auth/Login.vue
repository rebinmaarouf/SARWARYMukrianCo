<template>
  <div class="min-h-screen bg-[#070b14] flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Premium Entrance Background -->
    <div class="absolute inset-0 z-0">
      <div class="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] bg-emerald-600/10 blur-[180px] rounded-full animate-pulse"></div>
      <div class="absolute bottom-[-20%] right-[-10%] w-[60%] h-[60%] bg-blue-600/10 blur-[180px] rounded-full animate-pulse" style="animation-delay: 3s"></div>
    </div>

    <div class="w-full max-w-lg relative z-10">
      <!-- High-End Brand Section -->
      <div class="text-center mb-12">
        <div class="relative inline-block group">
          <div class="absolute -inset-4 bg-gradient-to-tr from-emerald-500 to-teal-400 rounded-[2.5rem] blur-2xl opacity-20 group-hover:opacity-50 transition duration-1000"></div>
          <div class="relative w-28 h-28 bg-slate-900 border border-white/10 rounded-[2.5rem] mx-auto mb-8 flex items-center justify-center shadow-3xl rotate-6 group-hover:rotate-0 transition-all duration-700">
             <span class="text-white text-4xl font-black tracking-tighter">SM</span>
          </div>
        </div>
        <h1 class="text-5xl font-black text-white tracking-tighter mb-4">سەروەر موکریان</h1>
        <p class="text-slate-500 text-lg font-medium" dir="rtl">دەروازەی ئەلیکترۆنی بۆ بەڕێوەبردنی دارایی</p>
      </div>

      <!-- Login Card -->
      <div class="bg-slate-900/60 backdrop-blur-3xl border border-white/10 rounded-[3rem] p-12 shadow-[0_32px_80px_-16px_rgba(0,0,0,0.8)] relative">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-1 bg-gradient-to-r from-transparent via-emerald-500 to-transparent rounded-full opacity-50"></div>
        
        <form @submit.prevent="handleLogin" class="space-y-8">
          <!-- Email Input -->
          <div class="space-y-3">
            <label class="text-xs font-black text-slate-500 mr-2 uppercase tracking-widest flex items-center gap-2" dir="rtl">
               ناونیشانی ئیمەیڵ
               <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
            </label>
            <div class="relative group">
              <input 
                v-model="form.email"
                type="email" 
                required
                dir="ltr"
                class="w-full bg-slate-950/50 border-2 border-slate-800 text-white rounded-2xl px-6 py-4.5 focus:outline-none focus:border-emerald-500/50 focus:ring-8 focus:ring-emerald-500/5 transition-all text-lg font-medium placeholder:text-slate-700"
                placeholder="rebinmaarouf@gmail.com"
              />
              <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-slate-600 group-focus-within:text-emerald-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
              </div>
            </div>
          </div>

          <!-- Password Input -->
          <div class="space-y-3">
            <label class="text-xs font-black text-slate-500 mr-2 uppercase tracking-widest flex items-center gap-2" dir="rtl">
               وشەی نهێنی
               <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
            </label>
            <div class="relative group">
              <input 
                v-model="form.password"
                type="password" 
                required
                dir="ltr"
                class="w-full bg-slate-950/50 border-2 border-slate-800 text-white rounded-2xl px-6 py-4.5 focus:outline-none focus:border-emerald-500/50 focus:ring-8 focus:ring-emerald-500/5 transition-all text-lg font-medium placeholder:text-slate-800"
                placeholder="••••••••••••"
              />
              <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-slate-600 group-focus-within:text-emerald-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
            </div>
          </div>

          <!-- Error Message -->
          <Transition name="fade">
            <div v-if="auth.error" class="bg-red-500/10 border border-red-500/30 text-red-400 p-5 rounded-2xl text-sm font-black text-center shadow-lg" dir="rtl">
              {{ auth.error }}
            </div>
          </Transition>

          <!-- Submit Button -->
          <button 
            type="submit"
            :disabled="auth.loading"
            class="w-full h-20 bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 text-xl font-black rounded-2xl shadow-2xl shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:-translate-y-1 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-4 group"
          >
            <span v-if="!auth.loading">چوونە ناو سیستم</span>
            <svg v-else class="animate-spin h-7 w-7 text-slate-950" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-if="!auth.loading" class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
          </button>
        </form>
      </div>

      <!-- Enterprise Footer -->
      <div class="mt-16 flex flex-col items-center gap-4">
         <div class="flex items-center gap-8 text-slate-600 text-xs font-black uppercase tracking-[0.2em]">
            <span>Secured by Sanctum</span>
            <div class="w-1 h-1 bg-slate-800 rounded-full"></div>
            <span>v2.4.0 Stable</span>
            <div class="w-1 h-1 bg-slate-800 rounded-full"></div>
            <span>Encrypted</span>
         </div>
         <p class="text-slate-700 text-[10px] font-bold">© 2026 Sarwary Mukrian Co. Enterprise Resources Planning System</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({
  email: '',
  password: ''
})

const handleLogin = async () => {
  try {
    const result = await auth.login(form)
    if (result?.twoFactor) {
      router.push('/verify-2fa')
    } else {
      router.push('/admin')
    }
  } catch (err) {
    console.error('Login failed', err)
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.4s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
