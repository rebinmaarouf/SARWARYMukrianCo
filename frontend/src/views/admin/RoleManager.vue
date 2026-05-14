<template>
  <div class="p-8 space-y-10 max-w-[1400px] mx-auto text-slate-800 font-sans">
    <!-- Header Section -->
    <header class="flex flex-col md:flex-row justify-between items-center gap-6">
      <div class="text-right">
        <h2 class="text-xs font-black text-emerald-700 uppercase tracking-widest mb-2">Access Control</h2>
        <h1 class="text-4xl font-black text-slate-900 tracking-tighter">بەڕێوەبردنی ڕۆڵ و دەسەڵاتەکان</h1>
        <p class="text-slate-600 font-medium mt-2">لێرەدا دەتوانیت گرووپی کارمەندان و دەسەڵاتەکانیان دیاری بکەیت.</p>
      </div>
      <button @click="openModal()" class="group bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-[2rem] font-black transition-all shadow-md shadow-emerald-600/10 active:scale-95 flex items-center gap-3">
        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
        زیادکردنی ڕۆڵی نوێ
      </button>
    </header>

    <!-- Roles Grid -->
    <div v-if="isLoading" class="flex flex-col items-center justify-center py-20 gap-4">
      <div class="w-12 h-12 border-4 border-slate-200 border-t-emerald-600 rounded-full animate-spin"></div>
      <p class="text-xs font-black text-slate-500 uppercase tracking-widest animate-pulse">Loading access controls...</p>
    </div>

    <div v-else-if="roles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="role in roles" :key="role.id" class="bg-white border border-slate-200 p-8 rounded-[3rem] hover:border-emerald-300 transition-all relative overflow-hidden group shadow-sm">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-600/5 rounded-full blur-3xl group-hover:bg-emerald-600/10 transition-all"></div>
        
        <div class="flex justify-between items-start mb-6">
          <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-emerald-700 border border-slate-200 shadow-xs">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          </div>
          <div class="flex gap-2 font-semibold">
            <button @click="openModal(role)" class="p-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl transition-colors border border-slate-200 shadow-xs">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>
            <button v-if="role.name !== 'Super Admin'" @click="deleteRole(role.id)" class="p-3 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-colors border border-rose-200 shadow-xs">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>

        <h3 class="text-2xl font-black text-slate-900 mb-2">{{ role.name }}</h3>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6 font-mono">
          {{ getPermCount(role) }} دەسەڵاتی بۆ دیاریکراوە
        </p>

        <div class="flex flex-wrap gap-2 font-semibold">
          <span v-for="p in getPerms(role).slice(0, 4)" :key="p" class="px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 text-[10px] font-bold rounded-lg uppercase shadow-xs">
            {{ permissionLabels[p] || p.replace('manage_', '').replace('view_', '').replace(/_/g, ' ') }}
          </span>
          <span v-if="getPermCount(role) > 4" class="px-3 py-1 bg-slate-100 border border-slate-200 text-slate-700 text-[10px] font-bold rounded-lg shadow-xs font-mono">
            +{{ getPermCount(role) - 4 }}
          </span>
        </div>
      </div>
    </div>

    <div v-else class="bg-white border border-slate-200 rounded-[3rem] p-20 text-center animate-fade-in shadow-sm">
       <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-600 border border-slate-200 shadow-xs">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
       </div>
       <h3 class="text-xl font-black text-slate-900 mb-2">هیچ ڕۆڵێک نەدۆزرایەوە</h3>
       <p class="text-slate-600 font-medium max-w-xs mx-auto">وادیارە هێشتا هیچ ڕۆڵێک لە سیستمدا دروست نەکراوە یان داتاکان بار نەکراون.</p>
       <button @click="fetchData" class="mt-8 text-emerald-600 font-black uppercase text-xs tracking-widest hover:text-emerald-700 transition-colors">دووبارە هەوڵ بدەرەوە</button>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 backdrop-blur-md bg-slate-900/40">
      <div class="bg-white border border-slate-200 w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden">
        <div class="p-10">
          <div class="flex justify-between items-center mb-10">
            <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-2xl font-black text-slate-900">رێکخستنی ڕۆڵ</h3>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 font-semibold">
            <div class="space-y-8">
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-4">ناوی ڕۆڵ</label>
                <input v-model="form.name" type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-900 p-5 rounded-[1.5rem] outline-none font-bold focus:border-emerald-600 transition-all shadow-xs">
              </div>
            </div>

            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-6">دەسەڵاتە دیاریکراوەکان</label>
              <div class="grid grid-cols-1 gap-3 max-h-[400px] overflow-y-auto pr-4 custom-scrollbar font-semibold">
                <label v-for="p in allPermissions" :key="p.id" class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100 transition-colors shadow-xs">
                  <span class="text-xs font-black text-slate-800 uppercase tracking-tight">{{ permissionLabels[p.name] || p.name.replace(/_/g, ' ') }}</span>
                  <input type="checkbox" v-model="form.permissions" :value="p.name" class="w-5 h-5 rounded-lg text-emerald-600 bg-white border-slate-300 focus:ring-emerald-600 cursor-pointer">
                </label>
              </div>
            </div>
          </div>

          <div class="mt-12">
            <button @click="saveRole" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white p-6 rounded-[2rem] font-black transition-all shadow-md shadow-emerald-600/10 active:scale-95">
              پاشەکەوتکردنی گۆڕانکارییەکان
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2'

const roles = ref([])
const allPermissions = ref([])
const showModal = ref(false)
const isLoading = ref(false)
const route = useRoute()
const form = ref({ id: null, name: '', permissions: [] })

const permissionLabels = {
  view_dashboard: 'بینینی داشبۆرد',
  manage_exchange: 'بەڕێوەبردنی تێرمیناڵی ئاڵوگۆڕ',
  manage_hawala: 'بەڕێوەبردنی حەواڵەکان',
  manage_vaults: 'بەڕێوەبردنی خەزێنەکان',
  manage_users: 'بەڕێوەبردنی بەکارهێنەران',
  manage_accounts: 'بەڕێوەبردنی حیسابات',
  view_reports: 'بینینی ڕاپۆرتەکان',
  delete_records: 'سڕینەوەی تۆمارەکان',
  edit_past_records: 'دەستکاریکردنی تۆمارە کۆنەکان',
  verify_database_integrity: 'پشکنینی پاکی داتابەیس',
  manage_notifications: 'ڕێکخستنی نۆتیفیکەیشنی ڕاستەوخۆ (Pusher)'
}

async function fetchData() {
  isLoading.value = true
  try {
    const [rolesRes, permsRes] = await Promise.all([
      axios.get('/admin/roles'),
      axios.get('/admin/all-permissions')
    ])
    roles.value = rolesRes.data
    allPermissions.value = permsRes.data
  } catch (e) { 
    console.error(e) 
  } finally {
    isLoading.value = false
  }
}

function getPerms(role) {
  return role.permissions || []
}

function getPermCount(role) {
  return getPerms(role).length
}

function openModal(role = null) {
  if (role) {
    form.value.id = role.id
    form.value.name = role.name
    form.value.permissions = [...getPerms(role)]
  } else {
    form.value.id = null
    form.value.name = ''
    form.value.permissions = []
  }
  showModal.value = true
}

async function saveRole() {
  try {
    if (form.value.id) {
      await axios.put(`/admin/roles/${form.value.id}`, form.value)
    } else {
      await axios.post('/admin/roles', form.value)
    }
    showModal.value = false
    fetchData()
    Swal.fire({ icon: 'success', title: 'سەرکەوتوو بوو', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false, background: '#ffffff', color: '#0f172a' })
  } catch (e) { Swal.fire({ title: 'هەڵە', text: 'نەتوانرا پاشەکەوت بکرێت', icon: 'error', background: '#ffffff', color: '#0f172a' }) }
}

async function deleteRole(id) {
  const result = await Swal.fire({ title: 'دڵنیایت؟', icon: 'warning', showCancelButton: true, confirmButtonColor: '#10b981', cancelButtonColor: '#f43f5e', background: '#ffffff', color: '#0f172a', customClass: { popup: 'rounded-[2rem] border border-slate-200' } })
  if (result.isConfirmed) {
    try {
      await axios.delete(`/admin/roles/${id}`)
      fetchData()
    } catch (e) { Swal.fire({ title: 'هەڵە', text: 'نەتوانرا بسڕێتەوە', icon: 'error', background: '#ffffff', color: '#0f172a' }) }
  }
}

// Watch for route changes - MOVED TO BOTTOM
watch(() => route.path, (newPath) => {
  if (newPath === '/admin/roles') {
    fetchData()
  }
}, { immediate: true })
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
