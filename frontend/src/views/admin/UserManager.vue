<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-black text-white">بەڕێوەبردنی کارمەندان</h1>
        <p class="text-slate-400 mt-1">زیادکردن و دیاریکردنی دەسەڵاتەکانی ستافی کۆمپانیا</p>
      </div>
      <button 
        @click="openCreateModal"
        class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        کارمەندی نوێ
      </button>
    </div>

    <!-- Users Table -->
    <div class="bg-slate-800/40 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden shadow-2xl">
      <table class="w-full text-right" dir="rtl">
        <thead>
          <tr class="bg-white/5 text-slate-400 text-sm font-bold">
            <th class="px-6 py-4">ناوی تەواو</th>
            <th class="px-6 py-4">ئیمەیڵ</th>
            <th class="px-6 py-4">ڕۆڵ / دەسەڵات</th>
            <th class="px-6 py-4">دۆخ</th>
            <th class="px-6 py-4 text-left">کردارەکان</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          <tr v-for="user in users" :key="user.id" class="hover:bg-white/5 transition-colors">
            <td class="px-6 py-5">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center text-emerald-500 font-bold border border-white/10">
                  {{ user.name.charAt(0) }}
                </div>
                <span class="text-white font-bold">{{ user.name }}</span>
              </div>
            </td>
            <td class="px-6 py-5 text-slate-300">{{ user.email }}</td>
            <td class="px-6 py-5">
              <div class="flex flex-wrap gap-2">
                <span 
                  v-for="role in user.roles" :key="role.id"
                  class="px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider"
                  :class="getRoleClass(role.name)"
                >
                  {{ role.name }}
                </span>
              </div>
            </td>
            <td class="px-6 py-5">
              <span class="flex items-center gap-2 text-emerald-500 text-sm font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Active
              </span>
            </td>
            <td class="px-6 py-5 text-left">
              <div class="flex items-center justify-end gap-2">
                <button @click="editUser(user)" class="p-2 text-slate-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button @click="deleteUser(user)" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-all">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- User Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm">
        <div class="bg-slate-900 border border-white/10 w-full max-w-xl rounded-3xl shadow-2xl overflow-hidden">
          <div class="p-8 border-b border-white/5 flex justify-between items-center" dir="rtl">
            <h2 class="text-2xl font-black text-white">{{ editingUser ? 'دەستکاری کارمەند' : 'زیادکردنی کارمەندی نوێ' }}</h2>
            <button @click="showModal = false" class="text-slate-500 hover:text-white">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          
          <form @submit.prevent="saveUser" class="p-8 space-y-6" dir="rtl">
            <div class="grid grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-slate-400 mr-1">ناوی تەواو</label>
                <input v-model="form.name" type="text" required class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all">
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-slate-400 mr-1">ئیمەیڵ</label>
                <input v-model="form.email" type="email" required class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all">
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-400 mr-1">وشەی نهێنی {{ editingUser ? '(بەتاڵ بێ کێشە نیە)' : '' }}</label>
              <input v-model="form.password" type="password" :required="!editingUser" class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all">
            </div>

            <div class="space-y-3">
              <label class="text-sm font-bold text-slate-400 mr-1">دیاریکردنی دەسەڵاتەکان</label>
              <div class="grid grid-cols-2 gap-3">
                <div 
                  v-for="role in allRoles" :key="role.id"
                  @click="toggleRole(role.name)"
                  class="flex items-center gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                  :class="form.roles.includes(role.name) ? 'bg-emerald-500/10 border-emerald-500/50 text-emerald-400' : 'bg-slate-800/50 border-slate-700 text-slate-400 hover:border-slate-500'"
                >
                  <div class="w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all" :class="form.roles.includes(role.name) ? 'bg-emerald-500 border-emerald-500' : 'border-slate-600'">
                    <svg v-if="form.roles.includes(role.name)" class="w-3 h-3 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <span class="font-bold">{{ role.name }}</span>
                </div>
              </div>
            </div>

            <div class="pt-4 flex gap-3">
              <button type="submit" class="flex-grow bg-emerald-500 text-slate-900 font-black py-4 rounded-xl hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20">
                پاشەکەوتکردن
              </button>
              <button type="button" @click="showModal = false" class="px-8 py-4 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-700 transition-all">
                داخستن
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import axios from '../../plugins/axios'
import Swal from 'sweetalert2'

const users = ref([])
const allRoles = ref([])
const showModal = ref(false)
const editingUser = ref(null)

const form = reactive({
  name: '',
  email: '',
  password: '',
  roles: []
})

const fetchUsers = async () => {
  try {
    const response = await axios.get('/users')
    users.value = response.data
  } catch (err) {
    console.error('Error fetching users', err)
  }
}

const fetchRoles = async () => {
  try {
    const response = await axios.get('/roles')
    allRoles.value = response.data
  } catch (err) {
    console.error('Error fetching roles', err)
  }
}

onMounted(() => {
  fetchUsers()
  fetchRoles()
})

const openCreateModal = () => {
  editingUser.value = null
  Object.assign(form, { name: '', email: '', password: '', roles: [] })
  showModal.value = true
}

const editUser = (user) => {
  editingUser.value = user
  Object.assign(form, {
    name: user.name,
    email: user.email,
    password: '',
    roles: user.roles.map(r => r.name)
  })
  showModal.value = true
}

const toggleRole = (roleName) => {
  const index = form.roles.indexOf(roleName)
  if (index === -1) {
    form.roles.push(roleName)
  } else {
    form.roles.splice(index, 1)
  }
}

const saveUser = async () => {
  try {
    if (editingUser.value) {
      await axios.put(`/users/${editingUser.value.id}`, form)
    } else {
      await axios.post('/users', form)
    }
    showModal.value = false
    fetchUsers()
    Swal.fire({
      icon: 'success',
      title: 'سەرکەوتوو بوو',
      text: 'زانیارییەکان بە سەرکەوتوویی پاشەکەوت کران',
      background: '#1e293b',
      color: '#fff',
      confirmButtonColor: '#10b981'
    })
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'هەڵە',
      text: err.response?.data?.message || 'کێشەیەک ڕوویدا',
      background: '#1e293b',
      color: '#fff'
    })
  }
}

const deleteUser = async (user) => {
  const result = await Swal.fire({
    title: 'دڵنیای؟',
    text: `دەتەوێت کارمەند "${user.name}" بسڕیتەوە؟`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#334155',
    confirmButtonText: 'بەڵێ، بسڕەوە',
    cancelButtonText: 'نەخێر',
    background: '#1e293b',
    color: '#fff'
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/users/${user.id}`)
      fetchUsers()
      Swal.fire({
        icon: 'success',
        title: 'سڕایەوە',
        background: '#1e293b',
        color: '#fff'
      })
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'هەڵە',
        text: err.response?.data?.message || 'کێشەیەک ڕوویدا',
        background: '#1e293b',
        color: '#fff'
      })
    }
  }
}

const getRoleClass = (name) => {
  switch (name) {
    case 'Super Admin': return 'bg-emerald-500/20 text-emerald-400'
    case 'Accountant': return 'bg-blue-500/20 text-blue-400'
    case 'Cashier': return 'bg-amber-500/20 text-amber-400'
    default: return 'bg-slate-500/20 text-slate-400'
  }
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>
