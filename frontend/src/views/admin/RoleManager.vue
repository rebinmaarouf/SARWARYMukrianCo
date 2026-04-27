<template>
  <div class="p-8 space-y-10 max-w-[1400px] mx-auto">
    <!-- Header Section -->
    <header class="flex flex-col md:flex-row justify-between items-center gap-6">
      <div class="text-right">
        <h2 class="text-xs font-black text-emerald-500 uppercase tracking-widest mb-2">Access Control</h2>
        <h1 class="text-4xl font-black text-white tracking-tighter">بەڕێوەبردنی ڕۆڵ و دەسەڵاتەکان</h1>
        <p class="text-slate-500 font-medium mt-2">لێرەدا دەتوانیت گرووپی کارمەندان و دەسەڵاتەکانیان دیاری بکەیت.</p>
      </div>
      <button @click="openModal()" class="group bg-emerald-500 hover:bg-emerald-400 text-white px-8 py-4 rounded-[2rem] font-black transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-3">
        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
        زیادکردنی ڕۆڵی نوێ
      </button>
    </header>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="role in roles" :key="role.id" class="bg-slate-900/30 border border-slate-800 p-8 rounded-[3rem] hover:border-emerald-500/30 transition-all relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all"></div>
        
        <div class="flex justify-between items-start mb-6">
          <div class="w-12 h-12 bg-slate-800 rounded-2xl flex items-center justify-center text-emerald-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          </div>
          <div class="flex gap-2">
            <button @click="openModal(role)" class="p-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>
            <button v-if="role.name !== 'Super Admin'" @click="deleteRole(role.id)" class="p-3 bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 rounded-xl transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>

        <h3 class="text-2xl font-black text-white mb-2">{{ role.name }}</h3>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6">
          {{ getPermCount(role) }} دەسەڵاتی بۆ دیاریکراوە
        </p>

        <div class="flex flex-wrap gap-2">
          <span v-for="p in getPerms(role).slice(0, 4)" :key="p" class="px-3 py-1 bg-emerald-500/5 border border-emerald-500/10 text-emerald-500 text-[10px] font-bold rounded-lg uppercase">
            {{ p.replace('manage_', '').replace('view_', '').replace(/_/g, ' ') }}
          </span>
          <span v-if="getPermCount(role) > 4" class="px-3 py-1 bg-slate-800 text-slate-400 text-[10px] font-bold rounded-lg">
            +{{ getPermCount(role) - 4 }}
          </span>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 backdrop-blur-2xl bg-black/60">
      <div class="bg-[#0f172a] border border-slate-800 w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden">
        <div class="p-10">
          <div class="flex justify-between items-center mb-10">
            <button @click="showModal = false" class="text-slate-500 hover:text-white transition-colors">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-2xl font-black text-white">رێکخستنی ڕۆڵ</h3>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div class="space-y-8">
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-4">ناوی ڕۆڵ</label>
                <input v-model="form.name" type="text" class="w-full bg-slate-900 border border-slate-800 text-white p-5 rounded-[1.5rem] outline-none font-bold">
              </div>
            </div>

            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-6">دەسەڵاتە دیاریکراوەکان</label>
              <div class="grid grid-cols-1 gap-3 max-h-[400px] overflow-y-auto pr-4 custom-scrollbar">
                <label v-for="p in allPermissions" :key="p.id" class="flex items-center justify-between p-4 bg-slate-900 border border-slate-800 rounded-2xl cursor-pointer">
                  <span class="text-xs font-black text-slate-300 uppercase tracking-tight">{{ p.name.replace(/_/g, ' ') }}</span>
                  <input type="checkbox" v-model="form.permissions" :value="p.name" class="w-5 h-5 rounded-lg text-emerald-500 bg-slate-800 border-slate-700">
                </label>
              </div>
            </div>
          </div>

          <div class="mt-12">
            <button @click="saveRole" class="w-full bg-emerald-500 text-white p-6 rounded-[2rem] font-black transition-all">
              پاشەکەوتکردنی گۆڕانکارییەکان
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2'

const roles = ref([])
const allPermissions = ref([])
const showModal = ref(false)
const form = ref({ id: null, name: '', permissions: [] })

async function fetchData() {
  try {
    const [rolesRes, permsRes] = await Promise.all([
      axios.get('/admin/roles'),
      axios.get('/admin/all-permissions')
    ])
    roles.value = rolesRes.data
    allPermissions.value = permsRes.data
  } catch (e) { console.error(e) }
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
    Swal.fire({ icon: 'success', title: 'سەرکەوتوو بوو', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false })
  } catch (e) { Swal.fire('هەڵە', 'نەتوانرا پاشەکەوت بکرێت', 'error') }
}

async function deleteRole(id) {
  const result = await Swal.fire({ title: 'دڵنیایت؟', icon: 'warning', showCancelButton: true, confirmButtonColor: '#10b981', cancelButtonColor: '#f43f5e' })
  if (result.isConfirmed) {
    try {
      await axios.delete(`/admin/roles/${id}`)
      fetchData()
    } catch (e) { Swal.fire('هەڵە', 'نەتوانرا بسڕێتەوە', 'error') }
  }
}

onMounted(fetchData)
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
</style>
