<template>
  <div class="min-h-screen bg-[#0f172a] flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Premium Ambient Background (Animated) -->
    <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-emerald-500/10 blur-[150px] rounded-full animate-pulse"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-blue-500/10 blur-[150px] rounded-full animate-pulse" style="animation-delay: 2s"></div>
    
    <!-- Starfield/Meteor effect (Subtle) -->
    <div class="absolute inset-0 opacity-20 pointer-events-none">
       <div v-for="i in 10" :key="i" class="meteor" :style="meteorStyle()"></div>
    </div>

    <div class="w-full max-w-md relative z-10 transition-all duration-700" :class="{ 'scale-95 opacity-0': success }">
      <div class="text-center mb-10">
        <div class="w-24 h-24 bg-slate-800/60 backdrop-blur-2xl border border-emerald-500/20 rounded-3xl mx-auto mb-8 flex items-center justify-center shadow-3xl relative group">
          <div class="absolute -inset-2 bg-emerald-500/20 blur-xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
          <svg class="w-12 h-12 text-emerald-400 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h1 class="text-3xl font-black text-white tracking-tight mb-3">دڵنیایی چوونەژوورەوە</h1>
        <div class="px-8" dir="rtl">
          <p class="text-slate-400 font-medium leading-relaxed">
            کۆدێکی <span class="text-emerald-400 font-black">٦ ڕەقەمی</span> بۆ ئیمەیڵەکەت نێردرا
          </p>
          <p class="text-emerald-500/80 text-sm font-bold mt-1 tracking-wide" dir="ltr">{{ auth.twoFactorEmail }}</p>
        </div>
      </div>

      <div class="bg-slate-800/40 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] p-10 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.6)]">
        <form @submit.prevent="handleVerify" class="space-y-10">
          <!-- 6 Digit Inputs (Fixed RTL/LTR) -->
          <div class="flex justify-between gap-3" dir="ltr" @paste="handlePaste">
            <input 
              v-for="(digit, index) in 6" 
              :key="index"
              ref="inputs"
              v-model="digits[index]"
              type="text"
              inputmode="numeric"
              maxlength="1"
              class="w-12 h-16 sm:w-14 sm:h-18 bg-slate-900/80 border-2 border-slate-700/50 text-white text-3xl font-black rounded-2xl text-center focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-300 shadow-inner"
              @input="handleInput(index, $event)"
              @keydown.backspace="handleBackspace(index, $event)"
              @focus="$event.target.select()"
            />
          </div>

          <!-- Error Message with Animation -->
          <Transition name="slide-up">
            <div v-if="auth.error" class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-2xl text-sm font-bold text-center flex items-center justify-center gap-2" dir="rtl">
              <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              {{ auth.error }}
            </div>
          </Transition>

          <div class="space-y-4">
            <button 
              type="submit"
              :disabled="auth.loading || !isComplete"
              class="w-full h-16 bg-gradient-to-r from-emerald-500 to-teal-600 text-slate-950 text-lg font-black rounded-2xl shadow-xl shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:-translate-y-1 active:scale-95 transition-all duration-300 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center gap-3 group"
            >
              <span v-if="!auth.loading">پشتڕاستکردنەوە</span>
              <svg v-else class="animate-spin h-6 w-6 text-slate-950" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-if="!auth.loading" class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
            
            <div class="flex items-center justify-center gap-6 mt-4">
               <button type="button" @click="$router.push('/login')" class="text-slate-500 text-sm font-bold hover:text-white transition-colors" dir="rtl">گۆڕینی ئیمەیڵ</button>
               <div class="h-4 w-px bg-slate-700"></div>
               <button type="button" class="text-emerald-500 text-sm font-black hover:text-emerald-400 transition-colors" dir="rtl">ناردنەوەی کۆد</button>
            </div>
          </div>
        </form>
      </div>

      <p class="text-center text-slate-600 text-xs mt-12 font-bold tracking-widest uppercase">
        Advanced Enterprise Security — SAP/Odoo Standard
      </p>
    </div>

    <!-- Success Overlay -->
    <div v-if="success" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-[#0f172a]">
       <div class="w-24 h-24 bg-emerald-500 rounded-full flex items-center justify-center animate-bounce shadow-3xl shadow-emerald-500/40">
          <svg class="w-14 h-14 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
       </div>
       <h2 class="text-3xl font-black text-white mt-8 animate-pulse">بەخێربێیتەوە</h2>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const auth = useAuthStore()
const inputs = ref([])
const digits = reactive(['', '', '', '', '', ''])
const success = ref(false)

const isComplete = computed(() => digits.every(d => d !== ''))

onMounted(() => {
  if (!auth.twoFactorEmail) {
    router.push('/login')
  } else {
    setTimeout(() => inputs.value[0]?.focus(), 500)
  }
})

const handleInput = (index, event) => {
  const val = event.target.value
  // Ensure only numbers
  if (!/^\d*$/.test(val)) {
    digits[index] = ''
    return
  }
  
  if (val && index < 5) {
    inputs.value[index + 1].focus()
  }
}

const handleBackspace = (index, event) => {
  if (!digits[index] && index > 0) {
    inputs.value[index - 1].focus()
  }
}

const handlePaste = (event) => {
  event.preventDefault()
  const data = event.clipboardData.getData('text').trim()
  if (!/^\d{6}$/.test(data)) return

  data.split('').forEach((char, i) => {
    if (i < 6) digits[i] = char
  })
  
  inputs.value[5].focus()
  handleVerify()
}

const handleVerify = async () => {
  try {
    const code = digits.join('')
    await auth.verify2FA(code)
    success.value = true
    setTimeout(() => {
      router.push('/')
    }, 1500)
  } catch (err) {
    // Reset inputs on error for better UX
    digits.fill('')
    inputs.value[0].focus()
  }
}

const meteorStyle = () => {
  const top = Math.random() * 100
  const left = Math.random() * 100
  const delay = Math.random() * 5
  const duration = 2 + Math.random() * 3
  return {
    top: `${top}%`,
    left: `${left}%`,
    animationDelay: `${delay}s`,
    animationDuration: `${duration}s`
  }
}
</script>

<style scoped>
.meteor {
  position: absolute;
  width: 2px;
  height: 2px;
  background: white;
  border-radius: 50%;
  box-shadow: 0 0 10px white, 0 0 20px #10b981;
  animation: meteor-flow linear infinite;
}

@keyframes meteor-flow {
  0% { transform: translateY(0) translateX(0) scale(1); opacity: 0; }
  10% { opacity: 1; }
  90% { opacity: 1; }
  100% { transform: translateY(100vh) translateX(100px) scale(0); opacity: 0; }
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.4s ease;
}
.slide-up-enter-from { opacity: 0; transform: translateY(10px); }
.slide-up-leave-to { opacity: 0; transform: translateY(-10px); }
</style>
