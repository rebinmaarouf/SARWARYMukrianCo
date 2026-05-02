<template>
  <div class="space-y-8 animate-fade-in">
    <!-- Header Section -->
    <div class="flex items-center justify-between bg-slate-900/50 backdrop-blur-xl p-8 rounded-[2.5rem] border border-white/5 shadow-2xl">
      <div dir="rtl">
        <h2 class="text-3xl font-black text-white mb-2">بەڕێوەبردنی کارمەندان</h2>
        <p class="text-slate-400 font-medium">کۆنتڕۆڵکردنی دەسەڵاتەکان و کارمەندانی سیستم</p>
      </div>
      <button @click="openModal()" class="group flex items-center gap-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 px-8 py-4 rounded-2xl font-black shadow-xl shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:-translate-y-1 transition-all active:scale-95">
        <span>زیادکردنی کارمەند</span>
        <svg class="w-6 h-6 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
      </button>
    </div>

    <!-- Users Grid -->
    <div v-if="isLoading" class="flex flex-col items-center justify-center py-20 gap-4">
      <div class="w-12 h-12 border-4 border-emerald-500/10 border-t-emerald-500 rounded-full animate-spin"></div>
      <p class="text-xs font-black text-slate-500 uppercase tracking-widest animate-pulse">Fetching staff records...</p>
    </div>

    <div v-else-if="users.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="user in users" :key="user.id" 
           v-show="user.email !== 'rebin.maaruf@gmail.com' || auth.user?.email === 'rebin.maaruf@gmail.com'"
           class="group bg-slate-900/40 backdrop-blur-xl border border-white/5 rounded-[2rem] p-6 hover:border-emerald-500/30 transition-all duration-500 shadow-xl">
        <div class="flex items-start justify-between mb-6">
          <div class="w-16 h-16 bg-slate-800 rounded-2xl flex items-center justify-center text-2xl font-black text-emerald-500 border border-white/5 group-hover:border-emerald-500/50 transition-all">
            {{ user.name.charAt(0) }}
          </div>
          <div class="flex gap-2" v-if="user.email !== 'rebin.maaruf@gmail.com' || auth.user?.email === 'rebin.maaruf@gmail.com'">
            <button @click="openModal(user)" class="p-3 bg-blue-500/10 text-blue-400 rounded-xl hover:bg-blue-500 hover:text-white transition-all shadow-lg shadow-blue-500/5">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <button v-if="user.email !== 'rebin.maaruf@gmail.com'" @click="deleteUser(user.id)" class="p-3 bg-red-500/10 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-lg shadow-red-500/5">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
        
        <div class="space-y-1" dir="rtl">
          <h3 class="text-xl font-black text-white">{{ user.name }}</h3>
          <p class="text-slate-500 text-sm font-medium">{{ user.email }}</p>
        </div>

        <div class="mt-6 flex flex-wrap gap-2" dir="rtl">
          <span v-for="role in user.roles" :key="role.id" class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded-lg border border-emerald-500/20">
            {{ role.name }}
          </span>
          <span v-if="user.permissions?.length" class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-lg border border-blue-500/20">
            + {{ user.permissions.length }} Custom
          </span>
        </div>
      </div>
    </div>

    <div v-else class="bg-slate-900/50 border border-slate-800 rounded-[3rem] p-20 text-center animate-fade-in">
       <div class="w-20 h-20 bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-600">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
       </div>
       <h3 class="text-xl font-black text-white mb-2">هیچ کارمەندێک نەدۆزرایەوە</h3>
       <p class="text-slate-500 font-medium max-w-xs mx-auto">وادیارە هێشتا هیچ کارمەندێک لە سیستمدا دروست نەکراوە یان داتاکان بار نەکراون.</p>
       <button @click="fetchUsers" class="mt-8 text-emerald-500 font-black uppercase text-xs tracking-widest hover:text-emerald-400 transition-colors">دووبارە هەوڵ بدەرەوە</button>
    </div>

    <!-- Enhanced Modal -->
    <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-950/80 backdrop-blur-md">
      <div class="bg-slate-900 border border-white/10 w-full max-w-2xl rounded-[3rem] shadow-3xl overflow-hidden animate-zoom-in">
        <div class="p-8 border-b border-white/5 flex items-center justify-between bg-slate-900/50">
          <button @click="showModal = false" class="text-slate-500 hover:text-white transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
          <div class="text-right">
             <h3 class="text-2xl font-black text-white" dir="rtl">{{ isEditing ? 'دەستکاریکردنی کارمەند' : 'زیادکردنی کارمەندی نوێ' }}</h3>
             <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Staff Access Control</p>
          </div>
        </div>

        <div class="p-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
          <div class="space-y-8">
            <!-- Basic Info Section -->
            <div class="grid grid-cols-1 gap-6">
              <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mr-2" dir="rtl">ناوی تەواوی کارمەند</label>
                <input v-model="form.name" type="text" class="w-full bg-slate-950 border-2 border-slate-800 text-white rounded-2xl px-6 py-4 focus:border-emerald-500/50 transition-all font-bold text-right" placeholder="ناوی سیانی بنووسە...">
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mr-2" dir="rtl">ئیمەیڵی کارمەند</label>
                  <input v-model="form.email" type="email" class="w-full bg-slate-950 border-2 border-slate-800 text-white rounded-2xl px-6 py-4 focus:border-emerald-500/50 transition-all font-bold" dir="ltr" placeholder="email@example.com">
                </div>
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mr-2" dir="rtl">وشەی نهێنی</label>
                  <input v-model="form.password" type="password" class="w-full bg-slate-950 border-2 border-slate-800 text-white rounded-2xl px-6 py-4 focus:border-emerald-500/50 transition-all font-bold" dir="ltr" :placeholder="isEditing ? 'بەتاڵ بێت ناگۆڕێت' : '********'">
                </div>
              </div>
            </div>

            <!-- Role Selector -->
            <div class="space-y-4 pt-8 border-t border-white/5">
              <div class="flex items-center justify-between" dir="rtl">
                 <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ڕۆڵ و گرووپی دەسەڵات</label>
                 <span class="text-[10px] text-emerald-500 font-black px-2 py-1 bg-emerald-500/5 rounded-lg border border-emerald-500/10">Required Field</span>
              </div>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <button 
                  v-for="role in allRoles" 
                  :key="role.id"
                  type="button"
                  @click="toggleRole(role.name)"
                  class="relative px-4 py-4 rounded-2xl border-2 font-black text-xs transition-all text-center overflow-hidden group/role"
                  :class="form.roles.includes(role.name) ? 'bg-emerald-500/10 border-emerald-500 text-emerald-400 shadow-xl shadow-emerald-500/10' : 'bg-slate-950 border-slate-800 text-slate-500 hover:border-slate-700'"
                >
                  <div v-if="form.roles.includes(role.name)" class="absolute top-1 right-1">
                     <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                  </div>
                  {{ role.name }}
                </button>
              </div>
            </div>

            <!-- Advanced Overrides (Collapsible for Cleanliness) -->
            <div class="space-y-4 pt-8 border-t border-white/5">
               <button @click="showAdvanced = !showAdvanced" class="flex items-center justify-between w-full p-4 bg-slate-950/50 rounded-2xl border border-white/5 hover:border-blue-500/30 transition-all group">
                  <svg class="w-5 h-5 text-slate-500 transition-transform" :class="{ 'rotate-180': showAdvanced }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                  <span class="text-xs font-black text-slate-400 uppercase tracking-widest" dir="rtl">دەسەڵاتی تایبەت (Advanced Overrides)</span>
               </button>
               
               <div v-if="showAdvanced" class="grid grid-cols-1 gap-3 p-4 bg-slate-950/30 rounded-3xl border border-white/5 animate-fade-in">
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[250px] overflow-y-auto pr-2 custom-scrollbar">
                     <label 
                       v-for="perm in allPermissions" 
                       :key="perm.id"
                       class="flex items-center justify-between p-4 bg-slate-900/50 border border-white/5 rounded-2xl cursor-pointer hover:border-blue-500/30 transition-all group"
                       dir="rtl"
                     >
                       <span class="text-[10px] font-black transition-colors" :class="form.permissions.includes(perm.name) ? 'text-blue-400' : 'text-slate-500'">{{ formatPerm(perm.name) }}</span>
                       <input type="checkbox" :value="perm.name" v-model="form.permissions" class="w-5 h-5 rounded-lg border-2 border-slate-700 bg-slate-950 text-blue-500 focus:ring-blue-500/20 transition-all">
                     </label>
                  </div>
                  <p class="text-[9px] text-slate-600 font-medium text-center mt-4" dir="rtl">
                     تێبینی: لێرەدا دەتوانیت دەسەڵاتی جیاواز بدەیت بە کارمەندەکە کە لە ڕۆڵەکەیدا نییە.
                  </p>
               </div>
            </div>
          </div>
        </div>

        <div class="p-8 bg-slate-950/30 border-t border-white/5 flex gap-4">
          <button @click="saveUser" class="flex-1 h-16 bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 font-black rounded-2xl shadow-xl shadow-emerald-500/10 hover:shadow-emerald-500/30 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-3">
             <span>{{ isEditing ? 'پاشەکەوتکردنی گۆڕانکارییەکان' : 'دروستکردنی ئەکاونتی کارمەند' }}</span>
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2/dist/sweetalert2.esm.all.js'

const auth = useAuthStore()

// --- Advanced Alert Configuration ---
const PremiumAlert = Swal.mixin({
  background: '#0f172a',
  color: '#f1f5f9',
  confirmButtonColor: '#10b981',
  cancelButtonColor: '#1e293b',
  customClass: {
    popup: 'rounded-[2rem] border border-white/10 shadow-3xl backdrop-blur-xl',
    confirmButton: 'px-8 py-3 rounded-xl font-black uppercase tracking-widest text-sm transition-all hover:scale-105 active:scale-95',
    cancelButton: 'px-8 py-3 rounded-xl font-black uppercase tracking-widest text-sm transition-all hover:scale-105 active:scale-95'
  },
  buttonsStyling: true,
  showClass: {
    popup: 'animate__animated animate__fadeInUp animate__faster'
  },
  hideClass: {
    popup: 'animate__animated animate__fadeOutDown animate__faster'
  }
})

const users = ref([])
const allRoles = ref([])
const allPermissions = ref([])
const showModal = ref(false)
const isEditing = ref(false)
const isLoading = ref(false)
const showAdvanced = ref(false)
const route = useRoute()

const form = reactive({
  id: null,
  name: '',
  email: '',
  password: '',
  roles: [],
  permissions: []
})

const fetchUsers = async () => {
  isLoading.value = true
  try {
    const { data } = await axios.get('/admin/users')
    users.value = data
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    isLoading.value = false
  }
}

const fetchMetadata = async () => {
  try {
    const [rolesRes, permsRes] = await Promise.all([
      axios.get('/admin/roles'),
      axios.get('/admin/all-permissions')
    ])
    allRoles.value = rolesRes.data
    allPermissions.value = permsRes.data
  } catch (err) {
    console.error('Error fetching metadata:', err)
  }
}

// Watch for route changes - MOVED TO BOTTOM
watch(() => route.path, (newPath) => {
  if (newPath === '/admin/users') {
    fetchUsers()
    fetchMetadata()
  }
}, { immediate: true })

const openModal = (user = null) => {
  if (user) {
    isEditing.value = true
    form.id = user.id
    form.name = user.name
    form.email = user.email
    form.password = ''
    form.roles = user.roles?.map(r => r.name) || []
    form.permissions = user.permissions?.map(p => p.name) || []
  } else {
    isEditing.value = false
    form.id = null
    form.name = ''
    form.email = ''
    form.password = ''
    form.roles = []
    form.permissions = []
  }
  showModal.value = true
}

const toggleRole = (roleName) => {
  const index = form.roles.indexOf(roleName)
  if (index > -1) {
    form.roles.splice(index, 1)
  } else {
    form.roles.push(roleName)
  }
}

const formatPerm = (perm) => {
  return perm.replace(/_/g, ' ').toUpperCase()
}

const saveUser = async () => {
  try {
    if (isEditing.value) {
      await axios.put(`/admin/users/${form.id}`, form)
      PremiumAlert.fire({ 
        title: 'سەرکەوتوو بوو', 
        text: 'زانیارییەکانی کارمەند بە سەرکەوتوویی نوێکرایەوە', 
        icon: 'success' 
      })
    } else {
      await axios.post('/admin/users', form)
      PremiumAlert.fire({ 
        title: 'سەرکەوتوو بوو', 
        text: 'کارمەندی نوێ بۆ ناو سیستم زیادکرا', 
        icon: 'success' 
      })
    }
    showModal.value = false
    fetchUsers()
  } catch (err) {
    PremiumAlert.fire({ 
      title: 'هەڵە ڕوویدا', 
      text: err.response?.data?.message || 'کێشەیەک لە سێرڤەر ڕوویدا، تکایە دووبارە هەوڵ بدەرەوە', 
      icon: 'error' 
    })
  }
}

const deleteUser = async (id) => {
  const result = await PremiumAlert.fire({
    title: 'ئایا دڵنیای؟',
    text: "ئەم کارمەندە بە یەکجاری لە سیستم دەسڕێتەوە و ناگەڕێتەوە!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'بەڵێ، بیسڕەوە',
    cancelButtonText: 'پەشیمانم',
    reverseButtons: true
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/admin/users/${id}`)
      PremiumAlert.fire({ title: 'سڕایەوە', text: 'کارمەندەکە بە سەرکەوتوویی سڕایەوە', icon: 'success' })
      fetchUsers()
    } catch (err) {
      PremiumAlert.fire({ title: 'هەڵە', text: 'نەتوانرا کارمەندەکە بسڕێتەوە', icon: 'error' })
    }
  }
}

onMounted(() => {
  fetchUsers()
  fetchMetadata()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.02);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(16, 185, 129, 0.2);
  border-radius: 10px;
}
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
.animate-zoom-in { animation: zoomIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes zoomIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
</style>
