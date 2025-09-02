<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useCurrency } from '@/Composables/useCurrency.js';

const props = defineProps({
  subscriptions: Array,
  students: Array,
  formations: Array,
});

const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);
const showDelete = ref(false);
const deleteId = ref(null);
const form = ref({ student_id: '', formation_id: '', start_date: '', status: 'active', notes: '' });

// Toast
const showToast = ref(false);
const toastMessage = ref('');
const showSuccessToast = (message) => {
  toastMessage.value = message;
  showToast.value = true;
  setTimeout(() => { showToast.value = false; }, 3000);
};
const { formatPrice } = useCurrency();

const openModal = () => { isEditing.value = false; currentId.value = null; showModal.value = true; };
const openEdit = (row) => {
  isEditing.value = true;
  currentId.value = row.id;
  form.value = {
    student_id: row.student_id || '',
    formation_id: row.formation_id || '',
    start_date: row.start_date || '',
    status: (row.status || 'active').toLowerCase(),
    notes: row.notes || ''
  };
  showModal.value = true;
};
const closeModal = () => { showModal.value = false; form.value = { student_id: '', formation_id: '', status: 'active', notes: '' }; };
const submit = () => {
  if (isEditing.value && currentId.value) {
    router.put(`/admin/formation-subscriptions/${currentId.value}`, form.value, {
      onSuccess: () => { closeModal(); showSuccessToast('Subscription updated successfully'); }
    });
  } else {
    router.post('/admin/formation-subscriptions', form.value, {
      onSuccess: () => { closeModal(); showSuccessToast('Subscription created successfully'); }
    });
  }
};
const openDelete = (row) => { deleteId.value = row.id; showDelete.value = true; };
const confirmDelete = () => { if (!deleteId.value) return; router.delete(`/admin/formation-subscriptions/${deleteId.value}`, { onSuccess: () => { showDelete.value = false; deleteId.value = null; showSuccessToast('Subscription deleted successfully'); } }); };
</script>

<template>
  <AdminLayout title="Manage Formation Subscriptions">
    <Head title="Manage Formation Subscriptions" />

    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Manage Formation Subscriptions</h1>
      <button @click="openModal" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl min-h-[44px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Add New
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Formation</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="s in props.subscriptions" :key="s.id" class="hover:bg-gray-50 transition-colors duration-150">
              <td class="py-4 px-6">
                <div class="text-sm font-medium text-gray-900">{{ s.student_name }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ s.student_email }}</div>
              </td>
              <td class="py-4 px-6"><div class="text-sm text-gray-700">{{ s.formation_title }}</div></td>
              <td class="py-4 px-6"><div class="text-sm font-semibold text-gray-900">{{ formatPrice(s.price) }}</div></td>
              <td class="py-4 px-6">
                <span :class="s.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 text-xs font-medium rounded-full">{{ s.status }}</span>
              </td>
              <td class="py-4 px-6">
                <span :class="{
                  'bg-yellow-100 text-yellow-800': s.payment_status === 'Draft',
                  'bg-green-100 text-green-800': s.payment_status === 'Paid',
                  'bg-blue-100 text-blue-800': s.payment_status === 'Partial',
                  'bg-red-100 text-red-800': s.payment_status === 'Cancelled',
                }" class="px-2 py-1 text-xs font-medium rounded-full">{{ s.payment_status }}</span>
              </td>
              <td class="py-4 px-6"><div class="text-sm text-gray-700">{{ s.start_date }}</div></td>
              <td class="py-4 px-6"><div class="text-sm text-gray-700">{{ s.end_date }}</div></td>
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <button @click="openEdit(s)" class="text-blue-600 hover:text-blue-800" title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </button>
                  <button @click="openDelete(s)" class="text-red-600 hover:text-red-800" title="Delete">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="bg-white rounded-xl w-full max-w-lg p-6">
        <h3 class="text-lg font-semibold mb-4">{{ isEditing ? 'Edit Formation Subscription' : 'New Formation Subscription' }}</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Student</label>
            <select v-model="form.student_id" class="w-full border rounded-lg px-3 py-2">
              <option value="">Select student</option>
              <option v-for="st in props.students" :key="st.id" :value="st.id">{{ st.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Formation</label>
            <select v-model="form.formation_id" class="w-full border rounded-lg px-3 py-2">
              <option value="">Select formation</option>
              <option v-for="f in props.formations" :key="f.id" :value="f.id">{{ f.title }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select v-model="form.status" class="w-full border rounded-lg px-3 py-2">
              <option value="active">Active</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Start Date</label>
            <input v-model="form.start_date" type="date" class="w-full border rounded-lg px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Notes</label>
            <textarea v-model="form.notes" rows="3" class="w-full border rounded-lg px-3 py-2"></textarea>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <button @click="closeModal" class="px-4 py-2 rounded-lg border">Cancel</button>
          <button @click="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Save</button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showToast" class="fixed bottom-4 right-4 z-50">
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 max-w-md">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          <span class="text-sm font-medium">{{ toastMessage }}</span>
          <button @click="showToast = false" class="ml-2 text-green-200 hover:text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </Transition>

    <!-- Delete Modal -->
    <div v-if="showDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="bg-white rounded-xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold mb-2">Delete Subscription</h3>
        <p class="text-sm text-gray-600">Are you sure you want to delete this formation subscription?</p>
        <div class="mt-6 flex justify-end gap-3">
          <button @click="showDelete = false" class="px-4 py-2 rounded-lg border">Cancel</button>
          <button @click="confirmDelete" class="px-4 py-2 rounded-lg bg-red-600 text-white">Delete</button>
        </div>
      </div>
    </div>
  </AdminLayout>
  </template>

<style scoped></style>
