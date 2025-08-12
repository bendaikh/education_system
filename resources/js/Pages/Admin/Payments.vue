<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

// Accept props from backend
const props = defineProps({
  payments: Array,
  total: Number
});

// Use data from props or fallback to mock data
const payments = ref(props.payments || []);

const searchQuery = ref('');
const currentPage = ref(1);
const totalPayments = ref(props.total || 156);
const paymentsPerPage = ref(4);

// Pagination
const totalPages = Math.ceil(totalPayments.value / paymentsPerPage.value);
const startIndex = (currentPage.value - 1) * paymentsPerPage.value + 1;
const endIndex = Math.min(currentPage.value * paymentsPerPage.value, totalPayments.value);

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages) {
    currentPage.value = page;
  }
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages) {
    currentPage.value++;
  }
};

// Status badge styling
const getStatusClass = (status) => {
  switch (status) {
    case 'Paid':
      return 'bg-green-100 text-green-800';
    case 'Pending':
      return 'bg-yellow-100 text-yellow-800';
    case 'Overdue':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};
</script>

<template>
  <AdminLayout title="Payments">
    <Head title="Payments" />

    <!-- Add New Payment Button -->
    <div class="flex justify-end mb-6">
      <button class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl min-h-[44px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Add New Payment
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6">
      <div class="flex flex-col sm:flex-row gap-4">
        <!-- Search -->
        <div class="flex-1">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search payments..."
              class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            />
          </div>
        </div>
        
        <!-- Filter and Sort buttons -->
        <div class="flex gap-3">
          <button class="inline-flex items-center gap-2 px-4 py-3 border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
            </svg>
            Filter
          </button>
          <button class="inline-flex items-center gap-2 px-4 py-3 border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
            </svg>
            Sort
          </button>
        </div>
      </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Desktop Table View -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Type</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="payment in payments" :key="payment.id" class="hover:bg-gray-50 transition-colors duration-150">
              <td class="py-4 px-6">
                <div class="text-sm font-medium text-gray-900">{{ payment.student_name }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm text-gray-600">{{ payment.payment_type }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm font-semibold text-gray-900">{{ payment.amount }}</div>
              </td>
              <td class="py-4 px-6">
                <span :class="getStatusClass(payment.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                  {{ payment.status }}
                </span>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm text-gray-600">{{ payment.due_date }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <!-- View Button -->
                  <button class="text-green-600 hover:text-green-800 transition-colors duration-200" title="View Payment">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                  <!-- Edit Button -->
                  <button class="text-blue-600 hover:text-blue-800 transition-colors duration-200" title="Edit Payment">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <!-- Delete Button -->
                  <button class="text-red-600 hover:text-red-800 transition-colors duration-200" title="Delete Payment">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="md:hidden divide-y divide-gray-100">
        <div v-for="payment in payments" :key="payment.id" class="p-4 sm:p-6">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0">
              <h3 class="text-base font-medium text-gray-900 truncate">{{ payment.student_name }}</h3>
              <p class="text-sm text-gray-600 mt-1">{{ payment.payment_type }}</p>
            </div>
            <span :class="getStatusClass(payment.status)" class="px-2 py-1 text-xs font-medium rounded-full ml-3">
              {{ payment.status }}
            </span>
          </div>
          <div class="grid grid-cols-2 gap-2 text-sm mb-4">
            <div>
              <span class="text-gray-500">Amount:</span>
              <span class="ml-1 text-gray-900 font-semibold">{{ payment.amount }}</span>
            </div>
            <div>
              <span class="text-gray-500">Due Date:</span>
              <span class="ml-1 text-gray-900">{{ payment.due_date }}</span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <!-- View Button -->
            <button class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 min-h-[44px] min-w-[44px] flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
            </button>
            <!-- Edit Button -->
            <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 min-h-[44px] min-w-[44px] flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </button>
            <!-- Delete Button -->
            <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 min-h-[44px] min-w-[44px] flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ startIndex }}</span> to <span class="font-medium">{{ endIndex }}</span> of <span class="font-medium">{{ totalPayments }}</span> Payments
          </div>
          
          <div class="flex items-center justify-center sm:justify-end">
            <nav class="relative z-0 inline-flex rounded-lg shadow-sm -space-x-px" aria-label="Pagination">
              <!-- Previous Button -->
              <button 
                @click="previousPage"
                :disabled="currentPage === 1"
                :class="[
                  'relative inline-flex items-center px-3 py-2 rounded-l-lg border text-sm font-medium min-h-[44px]',
                  currentPage === 1 
                    ? 'border-gray-300 text-gray-300 cursor-not-allowed' 
                    : 'border-gray-300 text-gray-500 hover:bg-gray-50 cursor-pointer'
                ]">
                Previous
              </button>
              
              <!-- Page Numbers -->
              <button 
                v-for="page in Math.min(totalPages, 5)" 
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium min-h-[44px]',
                  page === currentPage 
                    ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                    : 'border-gray-300 text-gray-500 hover:bg-gray-50'
                ]">
                {{ page }}
              </button>
              
              <!-- Next Button -->
              <button 
                @click="nextPage"
                :disabled="currentPage === totalPages"
                :class="[
                  'relative inline-flex items-center px-3 py-2 rounded-r-lg border text-sm font-medium min-h-[44px]',
                  currentPage === totalPages 
                    ? 'border-gray-300 text-gray-300 cursor-not-allowed' 
                    : 'border-gray-300 text-gray-500 hover:bg-gray-50 cursor-pointer'
                ]">
                Next
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped></style>
