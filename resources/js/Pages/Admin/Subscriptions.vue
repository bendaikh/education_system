<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const page = usePage();
const language = computed(() => page.props.language || {});

// Accept props from backend
const props = defineProps({
  subscriptions: Array,
  subscriptionTypes: Array,
  students: Array,
  pagination: Object,
  filters: Object
});

// Use data from props (reactive to changes)
const subscriptions = computed(() => props.subscriptions || []);
const searchQuery = ref(props.filters?.search || '');

// Pagination computed properties
const pagination = computed(() => props.pagination || {});
const totalSubscriptions = computed(() => pagination.value.total || 0);
const currentPage = computed(() => pagination.value.current_page || 1);
const totalPages = computed(() => pagination.value.last_page || 1);
const startIndex = computed(() => pagination.value.from || 0);
const endIndex = computed(() => pagination.value.to || 0);

// Toast notification state
const showToast = ref(false);
const toastMessage = ref('');

// Modal state
const showCreateModal = ref(false);

// Form for creating new subscription
const form = useForm({
  subscription_type_id: '',
  student_id: '',
});

// Modal functions
const openCreateModal = () => {
  showCreateModal.value = true;
  form.reset();
};

const closeCreateModal = () => {
  showCreateModal.value = false;
  form.reset();
};

const submitForm = () => {
  form.post(route('admin.subscriptions.store'), {
    onSuccess: () => {
      closeCreateModal();
      // Force a page refresh to get updated data
      router.visit(route('admin.subscriptions.index'), {
        preserveState: false,
        preserveScroll: true
      });
    }
  });
};

// Navigation functions that update URL
const goToPage = (pageNumber) => {
    if (pageNumber >= 1 && pageNumber <= totalPages.value) {
        router.get(route('admin.subscriptions.index'), {
            page: pageNumber,
            search: searchQuery.value,
            per_page: pagination.value.per_page
        }, {
            preserveState: true,
            preserveScroll: true
        });
    }
};

const previousPage = () => {
    if (currentPage.value > 1) {
        goToPage(currentPage.value - 1);
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        goToPage(currentPage.value + 1);
    }
};

// Search functionality
let searchTimeout = null;

const performSearch = () => {
    router.get(route('admin.subscriptions.index'), {
        search: searchQuery.value,
        page: 1 // Reset to first page when searching
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 500); // Wait 500ms after user stops typing
};

// Generate visible page numbers for pagination
const getVisiblePages = () => {
    const total = totalPages.value;
    const current = currentPage.value;
    const pages = [];
    
    if (total <= 7) {
        // Show all pages if 7 or fewer
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {
        // Show smart pagination
        if (current <= 4) {
            // Near the beginning
            for (let i = 1; i <= 5; i++) {
                pages.push(i);
            }
            pages.push('...');
            pages.push(total);
        } else if (current >= total - 3) {
            // Near the end
            pages.push(1);
            pages.push('...');
            for (let i = total - 4; i <= total; i++) {
                pages.push(i);
            }
        } else {
            // In the middle
            pages.push(1);
            pages.push('...');
            for (let i = current - 1; i <= current + 1; i++) {
                pages.push(i);
            }
            pages.push('...');
            pages.push(total);
        }
    }
    
    return pages.filter(page => page !== '...' || pages.indexOf(page) === pages.lastIndexOf(page));
};

// Toast notification functions
const showSuccessToast = (message) => {
  toastMessage.value = message;
  showToast.value = true;
  setTimeout(() => {
    showToast.value = false;
  }, 3000); // Hide after 3 seconds
};

// Show flash message if available on page load
if (page.props.flash?.success) {
  showSuccessToast(page.props.flash.success);
}

// Status badge styling
const getStatusClass = (status) => {
  switch (status) {
    case 'Active':
      return 'bg-green-100 text-green-800';
    case 'Trial':
      return 'bg-blue-100 text-blue-800';
    case 'Expired':
      return 'bg-red-100 text-red-800';
    case 'Cancelled':
      return 'bg-gray-100 text-gray-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};
</script>

<template>
  <AdminLayout :title="language.subscriptions || 'Subscriptions'">
    <Head :title="language.subscriptions || 'Subscriptions'" />

    <!-- Add New Subscription Button -->
    <div class="flex justify-end mb-6">
      <button @click="openCreateModal" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl min-h-[44px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        {{ language.add_new || 'Add New' }} {{ language.subscriptions || 'Subscription' }}
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
              @keyup.enter="performSearch"
              @input="debounceSearch"
              type="text"
              placeholder="Search subscriptions..."
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
            {{ language.filter || 'Filter' }}
          </button>
          <button class="inline-flex items-center gap-2 px-4 py-3 border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
            </svg>
            {{ language.sort || 'Sort' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Desktop Table View -->
      <div class="hidden lg:block overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.plan_name || 'Plan Name' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.student || 'Student' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.price || 'Price' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.duration || 'Duration' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.status || 'Status' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.next_billing || 'Next Billing' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.expires || 'Expires' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.actions || 'Actions' }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="subscription in subscriptions" :key="subscription.id" class="hover:bg-gray-50 transition-colors duration-150">
              <td class="py-4 px-6">
                <div class="text-sm font-medium text-gray-900">{{ subscription.plan_name }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm font-medium text-gray-900">{{ subscription.subscriber_name }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ subscription.student_email }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm font-semibold text-gray-900">{{ subscription.price }}</div>
              </td>
              <td class="py-4 px-6">
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                  {{ subscription.duration }}
                </span>
              </td>
              <td class="py-4 px-6">
                <span :class="getStatusClass(subscription.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                  {{ subscription.status }}
                </span>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm text-gray-600">{{ subscription.next_billing }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm text-gray-600">{{ subscription.expires_at }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <!-- View Button -->
                  <button class="text-green-600 hover:text-green-800 transition-colors duration-200" title="View Subscription">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                  <!-- Edit Button -->
                  <button class="text-blue-600 hover:text-blue-800 transition-colors duration-200" title="Edit Subscription">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <!-- Cancel Button -->
                  <button class="text-orange-600 hover:text-orange-800 transition-colors duration-200" title="Cancel Subscription">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="lg:hidden divide-y divide-gray-100">
        <div v-for="subscription in subscriptions" :key="subscription.id" class="p-4 sm:p-6">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0">
              <h3 class="text-base font-medium text-gray-900 truncate">{{ subscription.plan_name }}</h3>
              <p class="text-sm text-gray-600 mt-1">{{ subscription.subscriber_name }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ subscription.student_email }}</p>
            </div>
            <div class="flex flex-col gap-1 ml-3">
              <span :class="getStatusClass(subscription.status)" class="px-2 py-1 text-xs font-medium rounded-full text-center">
                {{ subscription.status }}
              </span>
              <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 text-center">
                {{ subscription.duration }}
              </span>
            </div>
          </div>
          <div class="grid grid-cols-1 gap-2 text-sm mb-4">
            <div>
              <span class="text-gray-500">{{ (language.price || 'Price') + ':' }}</span>
              <span class="ml-1 text-gray-900 font-semibold">{{ subscription.price }}</span>
            </div>
            <div>
              <span class="text-gray-500">{{ (language.next_billing || 'Next Billing') + ':' }}</span>
              <span class="ml-1 text-gray-900">{{ subscription.next_billing }}</span>
            </div>
            <div>
              <span class="text-gray-500">{{ (language.expires || 'Expires') + ':' }}</span>
              <span class="ml-1 text-gray-900">{{ subscription.expires_at }}</span>
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
            <!-- Cancel Button -->
            <button class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors duration-200 min-h-[44px] min-w-[44px] flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!subscriptions || subscriptions.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No subscriptions</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating your first subscription.</p>
        <div class="mt-6">
          <button @click="openCreateModal" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg">
            Create Subscription
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalSubscriptions > 0" class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ startIndex }}</span> to <span class="font-medium">{{ endIndex }}</span> of <span class="font-medium">{{ totalSubscriptions }}</span> subscriptions
          </div>
          
          <div class="flex items-center justify-center sm:justify-end" v-if="totalPages > 1">
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
                v-for="pageNum in getVisiblePages()" 
                :key="pageNum"
                @click="goToPage(pageNum)"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium min-h-[44px]',
                  pageNum === currentPage 
                    ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                    : 'border-gray-300 text-gray-500 hover:bg-gray-50'
                ]">
                {{ pageNum }}
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

    <!-- Create Subscription Modal -->
    <Modal :show="showCreateModal" @close="closeCreateModal" max-width="3xl">
      <div class="p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Create New Subscription</h2>
          <button @click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Subscription Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Student Selection -->
            <div class="md:col-span-2">
              <InputLabel for="student_id" value="Select Student" />
              <select
                id="student_id"
                v-model="form.student_id"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required
              >
                <option value="">Choose a student...</option>
                <option v-for="student in students" :key="student.id" :value="student.id">
                  {{ student.name }} ({{ student.student_id }}) - {{ student.email }}
                </option>
              </select>
              <InputError class="mt-2" :message="form.errors.student_id" />
              <p class="mt-1 text-sm text-gray-500">The subscription will be assigned to this student</p>
            </div>

            <!-- Subscription Plan -->
            <div class="md:col-span-2">
              <InputLabel for="subscription_type_id" value="Subscription Plan" />
              <select
                id="subscription_type_id"
                v-model="form.subscription_type_id"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required
              >
                <option value="">Select a plan...</option>
                <option v-for="type in subscriptionTypes" :key="type.id" :value="type.id">
                  {{ type.name }} - {{ type.formatted_price }} ({{ type.duration }})
                </option>
              </select>
              <InputError class="mt-2" :message="form.errors.subscription_type_id" />
              <p class="mt-1 text-sm text-gray-500">
                The subscription will start immediately and auto-calculate expiry date based on the plan period.
              </p>
            </div>
          </div>

          <!-- Auto-managed Information Display -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-900 mb-2">Automatic Management</h4>
            <ul class="text-sm text-blue-800 space-y-1">
              <li>• Start Date: Will be set to today's date automatically</li>
              <li>• End Date: Will be calculated based on the selected plan (monthly/yearly)</li>
              <li>• Status: Will be set to 'Active' automatically</li>
              <li>• Next Billing: Will be calculated based on plan duration</li>
            </ul>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
            <SecondaryButton type="button" @click="closeCreateModal">
              Cancel
            </SecondaryButton>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
              Create Subscription
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Success Toast Notification -->
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
  </AdminLayout>
</template>

<style scoped></style>
