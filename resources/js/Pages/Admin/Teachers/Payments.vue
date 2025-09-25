<template>
  <AdminLayout title="Teacher Payments">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Teacher Payments</h1>
          <p class="mt-2 text-sm text-gray-700">Generate monthly invoices for teachers and school</p>
        </div>
      </div>

      <!-- Database Status -->
      <div v-if="paymentCounts" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-blue-800 mb-2">Database Status</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
          <div>
            <span class="font-medium">Educational Payments:</span> {{ paymentCounts.educational_payments }} ({{ paymentCounts.paid_educational_payments }} paid)
          </div>
          <div>
            <span class="font-medium">Formation Payments:</span> {{ paymentCounts.formation_payments }} ({{ paymentCounts.paid_formation_payments }} paid)
          </div>
          <div>
            <span class="font-medium">Childhood Payments:</span> {{ paymentCounts.childhood_payments }} ({{ paymentCounts.paid_childhood_payments }} paid)
          </div>
        </div>
      </div>

      <!-- Generate Monthly Invoice Section -->
      <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Generate Monthly Invoice</h2>
        
        <div class="flex items-center space-x-4">
          <div>
            <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Select Month</label>
            <input
              v-model="selectedMonth"
              type="month"
              id="month"
              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <div class="flex items-end">
            <button
              @click="generateInvoices"
              :disabled="!selectedMonth || loading"
              class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-2 rounded-lg font-medium transition-colors"
            >
              <span v-if="loading">Generating...</span>
              <span v-else>Generate Monthly Invoice</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Debug Information -->
      <div v-if="invoices && invoices.debug" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Debug Information</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
          <div>
            <span class="font-medium">Educational Payments:</span> {{ invoices.debug.educational_payments_count }} ({{ invoices.debug.paid_educational_payments }} paid)
          </div>
          <div>
            <span class="font-medium">Formation Payments:</span> {{ invoices.debug.formation_payments_count }} ({{ invoices.debug.paid_formation_payments }} paid)
          </div>
          <div>
            <span class="font-medium">Childhood Payments:</span> {{ invoices.debug.childhood_payments_count }} ({{ invoices.debug.paid_childhood_payments }} paid)
          </div>
        </div>
      </div>

      <!-- Results Section -->
      <div v-if="invoices" class="space-y-6">
        <!-- Teacher Invoices -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Teacher Invoices - {{ invoices.month }}</h2>
          </div>
          
          <div v-if="invoices.teacher_invoices.length === 0" class="text-center py-8">
            <p class="text-gray-500">No teacher invoices for this month.</p>
            <p class="text-sm text-gray-400 mt-2">Try selecting a different month where payments were made.</p>
          </div>
          
          <div v-else class="space-y-4">
            <div
              v-for="invoice in invoices.teacher_invoices"
              :key="invoice.teacher.id"
              class="border border-gray-200 rounded-lg p-4"
            >
              <div class="flex justify-between items-start mb-3">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">{{ invoice.teacher.name }}</h3>
                  <p class="text-sm text-gray-600">{{ invoice.teacher.role || 'Teacher' }}</p>
                </div>
                <div class="text-right space-y-1">
                  <p class="text-2xl font-bold text-green-600">{{ formatMoney(invoice.total_amount) }}</p>
                  <p class="text-sm text-gray-500">Total Amount</p>
                  <button
                    class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800"
                    :title="`Download ${invoice.teacher.name} invoice`"
                    @click="downloadTeacher(invoice.teacher.id)"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                    </svg>
                    <span>Invoice</span>
                  </button>
                </div>
              </div>
              
              <div class="space-y-2">
                <h4 class="font-medium text-gray-900">Subject Details:</h4>
                <div class="space-y-1">
                  <div
                    v-for="subject in invoice.subjects"
                    :key="`${subject.subject_name}-${subject.student_name}`"
                    class="flex justify-between items-center text-sm bg-gray-50 px-3 py-2 rounded"
                  >
                    <div>
                      <span class="font-medium">{{ subject.subject_name }}</span>
                      <span class="text-gray-500"> - {{ subject.student_name }}</span>
                    </div>
                    <div class="text-right">
                      <span class="font-medium">{{ formatMoney(subject.amount) }}</span>
                      <span class="text-gray-500 text-xs block">{{ subject.payment_date }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- School Invoices -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">School Invoices - {{ invoices.month }}</h2>
          </div>
          
          <div v-if="invoices.school_invoices.length === 0" class="text-center py-8">
            <p class="text-gray-500">No school invoices for this month.</p>
            <p class="text-sm text-gray-400 mt-2">Try selecting a different month where payments were made.</p>
          </div>
          
          <div v-else class="space-y-4">
            <div
              v-for="invoice in invoices.school_invoices"
              :key="invoice.category"
              class="border border-gray-200 rounded-lg p-4"
            >
              <div class="flex justify-between items-start mb-3">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">{{ invoice.category }}</h3>
                </div>
                <div class="text-right space-y-1">
                  <p class="text-2xl font-bold text-blue-600">{{ formatMoney(invoice.total_amount) }}</p>
                  <p class="text-sm text-gray-500">Total Amount</p>
                  <button
                    class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800"
                    :title="`Download ${invoice.category} invoice`"
                    @click="downloadSchool(invoice.key)"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                    </svg>
                    <span>Invoice</span>
                  </button>
                </div>
              </div>
              
              <div class="space-y-2">
                <h4 class="font-medium text-gray-900">Subject Details:</h4>
                <div class="space-y-1">
                  <div
                    v-for="subject in invoice.subjects"
                    :key="`${subject.subject_name}-${subject.student_name}`"
                    class="flex justify-between items-center text-sm bg-gray-50 px-3 py-2 rounded"
                  >
                    <div>
                      <span class="font-medium">{{ subject.subject_name }}</span>
                      <span class="text-gray-500"> - {{ subject.student_name }}</span>
                    </div>
                    <div class="text-right">
                      <span class="font-medium">{{ formatMoney(subject.amount) }}</span>
                      <span class="text-gray-500 text-xs block">{{ subject.payment_date }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Summary -->
      <div v-if="invoices" class="bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary for {{ invoices.month }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="text-center">
            <p class="text-2xl font-bold text-green-600">
              {{ formatMoney(invoices.teacher_invoices.reduce((sum, inv) => sum + inv.total_amount, 0)) }}
            </p>
            <p class="text-sm text-gray-600">Total Teacher Payments</p>
          </div>
          <div class="text-center">
            <p class="text-2xl font-bold text-blue-600">
              {{ formatMoney(invoices.school_invoices.reduce((sum, inv) => sum + inv.total_amount, 0)) }}
            </p>
            <p class="text-sm text-gray-600">Total School Payments</p>
          </div>
          <div class="text-center">
            <p class="text-2xl font-bold text-gray-900">
              {{ formatMoney(invoices.teacher_invoices.reduce((sum, inv) => sum + inv.total_amount, 0) + 
                   invoices.school_invoices.reduce((sum, inv) => sum + inv.total_amount, 0)) }}
            </p>
            <p class="text-sm text-gray-600">Total Revenue</p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  teachers: Array,
  paymentCounts: Object,
  currency: String,
});

const selectedMonth = ref('');
const loading = ref(false);
const invoices = ref(null);

const formatMoney = (amount) => {
  const value = Number(amount || 0).toFixed(2);
  if (invoices.value?.currency) return `${value} ${invoices.value.currency}`;
  if (props.currency) return `${value} ${props.currency}`;
  return `$${value}`;
};

const generateInvoices = async () => {
  if (!selectedMonth.value) return;
  
  loading.value = true;
  
  try {
    const response = await fetch(route('admin.teacher-payments.generate-monthly-invoices'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        month: selectedMonth.value,
      }),
    });
    
    if (response.ok) {
      invoices.value = await response.json();
    } else {
      alert('Error generating invoices');
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error generating invoices');
  } finally {
    loading.value = false;
  }
};

const downloadTeacher = async (teacherId) => {
  if (!selectedMonth.value) return;
  const url = route('admin.teacher-payments.download-teacher-invoice') + `?month=${encodeURIComponent(selectedMonth.value)}&teacher_id=${teacherId}`;
  window.open(url, '_blank');
};

const downloadSchool = async (categoryKey) => {
  if (!selectedMonth.value) return;
  const url = route('admin.teacher-payments.download-school-invoice') + `?month=${encodeURIComponent(selectedMonth.value)}&category=${encodeURIComponent(categoryKey)}`;
  window.open(url, '_blank');
};
</script>
