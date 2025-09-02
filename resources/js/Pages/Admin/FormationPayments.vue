<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  payments: Array,
});

const showModal = ref(false);
const showAmountModal = ref(false);
const activePayment = ref(null);
const form = ref({ paid_amount: '' });
const amountForm = ref({ amount: '' });

const openProcessModal = (payment) => {
  activePayment.value = payment;
  form.value = { paid_amount: '' };
  showModal.value = true;
};
const closeModal = () => { showModal.value = false; activePayment.value = null; };
const processPayment = () => {
  if (!activePayment.value) return;
  router.patch(`/admin/formation-payments/${activePayment.value.id}/process-payment`, form.value, { onSuccess: closeModal });
};
const markAsPaid = (payment) => router.patch(`/admin/formation-payments/${payment.id}/mark-as-paid`);
const markAsCancelled = (payment) => router.patch(`/admin/formation-payments/${payment.id}/mark-as-cancelled`);
const openUpdateAmount = (payment) => {
  activePayment.value = payment;
  amountForm.value = { amount: payment.amount };
  showAmountModal.value = true;
};
const closeAmountModal = () => { showAmountModal.value = false; activePayment.value = null; };
const updateAmount = () => {
  if (!activePayment.value) return;
  router.patch(`/admin/formation-payments/${activePayment.value.id}/update-amount`, amountForm.value, { onSuccess: closeAmountModal });
};
</script>

<template>
  <AdminLayout title="Manage Formation Payments">
    <Head title="Manage Formation Payments" />
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Manage Formation Payments</h1>
    </div>
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formation</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="p in props.payments" :key="p.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ p.student_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ p.formation_title }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
              <div class="space-y-1">
                <div class="flex items-center gap-2">
                  <span class="font-medium">{{ Number(p.amount).toFixed(2) }}</span>
                  <button @click="openUpdateAmount(p)" class="p-1 rounded hover:bg-indigo-50 text-indigo-600" title="Update Amount">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </button>
                </div>
                <div v-if="p.paid_amount > 0" class="text-xs">
                  <span class="text-green-600">Paid: {{ Number(p.paid_amount).toFixed(2) }}</span>
                  <span v-if="Number(p.amount) - Number(p.paid_amount) > 0" class="text-red-600 ml-2">Remaining: {{ (Number(p.amount) - Number(p.paid_amount)).toFixed(2) }}</span>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 py-1 text-xs rounded-full" :class="{
                'bg-green-100 text-green-800': p.status === 'Paid',
                'bg-yellow-100 text-yellow-800': p.status === 'Draft' || p.status === 'Partial',
                'bg-red-100 text-red-800': p.status === 'Cancelled'
              }">{{ p.status }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ p.payment_date || '—' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <div class="flex items-center gap-2">
                <button @click="markAsPaid(p)" class="p-2 rounded hover:bg-green-50 text-green-600" title="Mark as Paid">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
                <button @click="openProcessModal(p)" class="p-2 rounded hover:bg-blue-50 text-blue-600" title="Process Payment">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8"/></svg>
                </button>
                <button @click="markAsCancelled(p)" class="p-2 rounded hover:bg-gray-50 text-gray-600" title="Cancel Payment">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      </div>
    </div>

    <!-- Process Payment Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="bg-white rounded-xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold mb-1">Process Payment</h3>
        <p class="text-sm text-gray-600 mb-4">Total: {{ Number(activePayment?.amount || 0).toFixed(2) }}</p>
        <div>
          <label class="block text-sm font-medium mb-1">Paid Amount</label>
          <input v-model.number="form.paid_amount" type="number" min="0" step="0.01" class="w-full border rounded-lg px-3 py-2" />
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <button @click="closeModal" class="px-4 py-2 rounded-lg border">Close</button>
          <button @click="processPayment" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Save</button>
        </div>
      </div>
    </div>

    <!-- Update Amount Modal -->
    <div v-if="showAmountModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="bg-white rounded-xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold mb-1">Update Amount</h3>
        <div>
          <label class="block text-sm font-medium mb-1">Amount</label>
          <input v-model.number="amountForm.amount" type="number" min="0" step="0.01" class="w-full border rounded-lg px-3 py-2" />
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <button @click="closeAmountModal" class="px-4 py-2 rounded-lg border">Close</button>
          <button @click="updateAmount" class="px-4 py-2 rounded-lg bg-indigo-600 text-white">Save</button>
        </div>
      </div>
    </div>
  </AdminLayout>
  </template>

<style scoped></style>


