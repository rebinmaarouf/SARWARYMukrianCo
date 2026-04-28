<template>
  <div class="min-h-screen bg-[#020617] flex relative overflow-hidden text-white font-sans">
    
    <!-- Left Side: Dynamic Exchange Visuals (Hidden on small screens) -->
    <div class="hidden lg:flex w-1/2 relative bg-slate-900/50 items-center justify-center overflow-hidden border-r border-white/5">
      <!-- Glow Effects -->
      <div class="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[120px] animate-pulse"></div>
      <div class="absolute bottom-1/4 right-1/4 w-[400px] h-[400px] bg-blue-500/10 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>
      
      <!-- Abstract Trading Grid & Nodes -->
      <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#10b981 1px, transparent 1px); background-size: 40px 40px;"></div>
      
      <!-- Connecting Lines Graph -->
      <svg class="absolute inset-0 w-full h-full opacity-40" viewBox="0 0 100 100" preserveAspectRatio="none">
        <path d="M0,80 Q20,60 40,70 T80,30 L100,50" fill="none" stroke="url(#emerald-gradient)" stroke-width="0.5" class="animate-dash" />
        <path d="M0,90 Q30,40 50,60 T100,20" fill="none" stroke="url(#emerald-gradient)" stroke-width="0.2" class="animate-dash-slow" />
        <defs>
          <linearGradient id="emerald-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stop-color="#020617" />
            <stop offset="50%" stop-color="#10b981" />
            <stop offset="100%" stop-color="#020617" />
          </linearGradient>
        </defs>
      </svg>

      <!-- Center Branding Quote -->
      <div class="relative z-10 p-12 max-w-xl backdrop-blur-sm bg-slate-950/30 border border-white/5 rounded-3xl" dir="rtl">
        <div class="mb-6 flex gap-2">
           <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
           <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
        </div>
        <h2 class="text-4xl font-black tracking-tighter leading-tight mb-4 text-transparent bg-clip-text bg-gradient-to-l from-white to-slate-400">
          سەنتەری سەرەکی بۆ ئاڵوگۆڕی دراوە جیهانییەکان
        </h2>
        <p class="text-slate-500 font-medium text-lg">ئاسایش، خێرایی، و وردبینی لە هەموو مامەڵەیەکدا. سیستەمی زیرەکی کۆمپانیای سەروەر موکریان.</p>
        
        <div class="mt-8 flex items-center gap-4">
           <div class="px-4 py-2 rounded-full border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-xs font-black tracking-widest uppercase">Global Trading</div>
           <div class="px-4 py-2 rounded-full border border-blue-500/20 bg-blue-500/10 text-blue-400 text-xs font-black tracking-widest uppercase">Live Sync</div>
        </div>
      </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 relative z-10">
      
      <!-- Mobile Background Glow -->
      <div class="absolute top-0 right-0 w-[300px] h-[300px] bg-emerald-500/20 rounded-full blur-[150px] lg:hidden"></div>

      <div class="w-full max-w-md" dir="rtl">
        
        <!-- Logo Section -->
        <div class="text-center mb-12">
          <!-- The New Logo Image -->
          <div class="relative inline-block mb-6 group">
            <div class="absolute inset-0 bg-emerald-500/20 blur-3xl rounded-full scale-125 group-hover:bg-emerald-500/40 transition-all duration-500"></div>
            <div class="w-32 h-32 bg-white rounded-[2.5rem] flex items-center justify-center shadow-[0_20px_50px_rgba(0,0,0,0.5)] relative z-10 overflow-hidden border-4 border-white/10 group-hover:scale-105 transition-transform duration-500">
               <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent"></div>
               <img :src="logoUrl" alt="Company Logo" class="w-24 h-24 object-contain relative z-10" @error="logoError = true" v-if="!logoError" />
               <div v-else class="text-emerald-500 font-black text-4xl">S</div>
            </div>
          </div>
          
          <h1 class="text-3xl font-black text-white tracking-tight mb-2">بەخێربێیتەوە</h1>
          <p class="text-slate-500 text-sm font-bold">تکایە زانیارییەکانت داخڵ بکە بۆ چوونە ژوورەوە</p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="space-y-6">
          
          <!-- Email Field -->
          <div class="space-y-2">
            <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest px-1 flex items-center gap-2">
               ئیمەیڵ
               <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
            </label>
            <div class="relative group">
              <input 
                v-model="form.email"
                type="email" 
                required
                dir="ltr"
                class="w-full bg-slate-900/80 border border-white/5 text-white rounded-2xl px-5 py-4 focus:outline-none focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/10 transition-all font-medium placeholder:text-slate-700"
                placeholder="admin@example.com"
              />
              <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-slate-600 group-focus-within:text-emerald-500 transition-colors">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
            </div>
          </div>

          <!-- Password Field -->
          <div class="space-y-2">
            <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest px-1 flex items-center gap-2">
               وشەی نهێنی
               <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
            </label>
            <div class="relative group">
              <input 
                v-model="form.password"
                type="password" 
                required
                dir="ltr"
                class="w-full bg-slate-900/80 border border-white/5 text-white rounded-2xl px-5 py-4 focus:outline-none focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/10 transition-all font-medium placeholder:text-slate-700"
                placeholder="••••••••••••"
              />
              <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-slate-600 group-focus-within:text-emerald-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
            </div>
          </div>

          <!-- Error Alert -->
          <Transition name="fade">
            <div v-if="auth.error" class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-4 py-3 rounded-xl text-xs font-black text-center flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              {{ auth.error }}
            </div>
          </Transition>

          <!-- Submit Button -->
          <button 
            type="submit"
            :disabled="auth.loading"
            class="w-full py-4 mt-4 bg-emerald-500 text-slate-950 text-lg font-black rounded-2xl shadow-[0_0_40px_-10px_rgba(16,185,129,0.5)] hover:bg-emerald-400 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3"
          >
            <span v-if="!auth.loading">چوونە ژوورەوە</span>
            <svg v-else class="animate-spin h-6 w-6 text-slate-950" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-if="!auth.loading" class="w-5 h-5 -rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </button>
        </form>
        
        <!-- Footer Info -->
        <div class="mt-12 text-center text-[10px] font-black text-slate-600 uppercase tracking-widest flex items-center justify-center gap-4">
           <span>ERP System v2.4</span>
           <div class="w-1 h-1 bg-slate-700 rounded-full"></div>
           <span class="text-emerald-500/50">Secured</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const logoUrl = '/logo.png'
const logoError = ref(false)

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
.animate-dash { stroke-dasharray: 1000; stroke-dashoffset: 1000; animation: dash 10s linear infinite; }
.animate-dash-slow { stroke-dasharray: 1000; stroke-dashoffset: 1000; animation: dash 15s linear infinite reverse; }
@keyframes dash { to { stroke-dashoffset: 0; } }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
