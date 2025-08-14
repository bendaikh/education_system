<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    subscriptionTypes: Array,
    formations: Array,
    pagination: Object,
    filters: Object
});

const page = usePage();
const language = computed(() => page.props.language || {});

const showCreateModal = ref(false);
const newFeature = ref('');
const searchQuery = ref(props.filters?.search || '');

// Pagination computed properties
const pagination = computed(() => props.pagination || {});
const totalTypes = computed(() => pagination.value.total || 0);
const currentPage = computed(() => pagination.value.current_page || 1);
const totalPages = computed(() => pagination.value.last_page || 1);
const startIndex = computed(() => pagination.value.from || 0);
const endIndex = computed(() => pagination.value.to || 0);

// Navigation functions that update URL
const goToPage = (pageNumber) => {
    if (pageNumber >= 1 && pageNumber <= totalPages.value) {
        router.get(route('admin.subscriptions.types.index'), {
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
    router.get(route('admin.subscriptions.types.index'), {
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

const form = useForm({
    name: '',
    formation_id: '',
    description: '',
    price: '',
    duration: 'monthly',
    features: [],
    max_students: '',
    is_active: true
});

const openCreateModal = () => {
    showCreateModal.value = true;
    form.reset();
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    form.reset();
};

const addFeature = () => {
    if (newFeature.value.trim()) {
        form.features.push(newFeature.value.trim());
        newFeature.value = '';
    }
};

const removeFeature = (index) => {
    form.features.splice(index, 1);
};

const submitForm = () => {
    form.post(route('admin.subscriptions.types.store'), {
        onSuccess: () => {
            closeCreateModal();
        }
    });
};

const formatPrice = (price, duration) => {
    const numPrice = parseFloat(price) || 0;
    if (numPrice === 0) return 'Free';
    return `$${numPrice.toFixed(2)}/${duration}`;
};

const getStatusColor = (isActive) => {
    return isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
};
</script>

<template>
    <AdminLayout :title="language.manage_subscription_types || 'Manage Subscription Types'">
        <!-- Add New Subscription Type Button -->
        <div class="flex justify-end mb-6">
            <button @click="openCreateModal" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl min-h-[44px]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ language.add_new || 'Add New' }} Subscription Type
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
                            placeholder="Search subscription types..."
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

        <!-- Subscription Types Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Details</th>
                            <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Formation</th>
                            <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.price || 'Price' }}</th>
                            <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Student Limit</th>
                            <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.features || 'Features' }}</th>
                            <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.status || 'Status' }}</th>
                            <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.actions || 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="type in subscriptionTypes" :key="type.id" class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-4 px-6">
                                <div class="text-sm font-medium text-gray-900">{{ type.name }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ type.description }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-600">
                                    {{ type.formation_name || 'No Formation' }}
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm font-semibold text-gray-900">{{ formatPrice(type.price, type.duration) }}</div>
                                <div class="text-xs text-gray-500 capitalize">{{ type.duration }} billing</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-600">
                                    {{ type.max_students ? `${type.max_students} students` : 'Unlimited' }}
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-900">
                                    <div v-for="feature in type.features.slice(0, 2)" :key="feature" class="mb-1">
                                        • {{ feature }}
                                    </div>
                                    <div v-if="type.features.length > 2" class="text-gray-500 text-xs">
                                        +{{ type.features.length - 2 }} more
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span :class="getStatusColor(type.is_active)" class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ type.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <!-- Edit Button -->
                                    <button class="text-blue-600 hover:text-blue-800 transition-colors duration-200" title="Edit Type">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <!-- Delete Button -->
                                    <button class="text-red-600 hover:text-red-800 transition-colors duration-200" title="Delete Type">
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
            <div class="lg:hidden divide-y divide-gray-100">
                <div v-for="type in subscriptionTypes" :key="type.id" class="p-4 sm:p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-medium text-gray-900 truncate">{{ type.name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ type.description }}</p>
                        </div>
                        <span :class="getStatusColor(type.is_active)" class="px-2 py-1 text-xs font-medium rounded-full ml-3">
                            {{ type.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 gap-2 text-sm mb-4">
                        <div>
                            <span class="text-gray-500">{{ (language.price || 'Price') + ':' }}</span>
                            <span class="ml-1 text-gray-900 font-semibold">{{ formatPrice(type.price, type.duration) }}</span>
                            <span class="ml-1 text-gray-500 text-xs capitalize">({{ type.duration }})</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Formation:</span>
                            <span class="ml-1 text-gray-900">{{ type.formation_name || 'No Formation' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Student Limit:</span>
                            <span class="ml-1 text-gray-900">{{ type.max_students ? `${type.max_students} students` : 'Unlimited' }}</span>
                        </div>
                        <div v-if="type.features && type.features.length > 0">
                            <span class="text-gray-500">{{ (language.features || 'Features') + ':' }}</span>
                            <div class="mt-1">
                                <div v-for="feature in type.features.slice(0, 2)" :key="feature" class="text-gray-900 text-sm">
                                    • {{ feature }}
                                </div>
                                <div v-if="type.features.length > 2" class="text-gray-500 text-xs">
                                    +{{ type.features.length - 2 }} more features
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
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

            <!-- Empty State -->
            <div v-if="!subscriptionTypes || subscriptionTypes.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No subscription types</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first subscription plan.</p>
                <div class="mt-6">
                    <button @click="openCreateModal" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg">
                        Create Subscription Type
                    </button>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="totalTypes > 0" class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ startIndex }}</span> to <span class="font-medium">{{ endIndex }}</span> of <span class="font-medium">{{ totalTypes }}</span> subscription types
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

        <!-- Create Subscription Type Modal -->
        <Modal :show="showCreateModal" @close="closeCreateModal" max-width="3xl">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Create Subscription Type</h2>
                    <button @click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Plan Name -->
                        <div>
                            <InputLabel for="modal-name" value="Plan Name" />
                            <TextInput
                                id="modal-name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., Premium Monthly"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Duration -->
                        <div>
                            <InputLabel for="modal-duration" value="Billing Duration" />
                            <select
                                id="modal-duration"
                                v-model="form.duration"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.duration" />
                        </div>

                        <!-- Price -->
                        <div>
                            <InputLabel for="modal-price" value="Price (USD)" />
                            <TextInput
                                id="modal-price"
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full"
                                placeholder="99.00"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.price" />
                        </div>

                        <!-- Max Students -->
                        <div>
                            <InputLabel for="modal-max-students" value="Maximum Students" />
                            <TextInput
                                id="modal-max-students"
                                v-model="form.max_students"
                                type="number"
                                min="1"
                                class="mt-1 block w-full"
                                placeholder="Leave empty for unlimited"
                            />
                            <InputError class="mt-2" :message="form.errors.max_students" />
                        </div>
                    </div>

                    <!-- Formation Selection -->
                    <div>
                        <InputLabel for="modal-formation" value="Formation (Optional)" />
                        <select
                            id="modal-formation"
                            v-model="form.formation_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">Select a formation (optional)...</option>
                            <option v-for="formation in formations" :key="formation.id" :value="formation.id">
                                {{ formation.title }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.formation_id" />
                    </div>

                    <!-- Description -->
                    <div>
                        <InputLabel for="modal-description" value="Description" />
                        <textarea
                            id="modal-description"
                            v-model="form.description"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            placeholder="Describe this subscription plan..."
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <!-- Features Section -->
                    <div>
                        <InputLabel value="Plan Features" />
                        
                        <!-- Add Feature -->
                        <div class="flex gap-3 mt-2 mb-4">
                            <TextInput
                                v-model="newFeature"
                                type="text"
                                class="flex-1"
                                placeholder="Add a feature (e.g., Advanced Analytics)"
                                @keyup.enter="addFeature"
                            />
                            <SecondaryButton type="button" @click="addFeature">
                                Add
                            </SecondaryButton>
                        </div>

                        <!-- Features List -->
                        <div v-if="form.features.length > 0" class="space-y-2 max-h-32 overflow-y-auto">
                            <div
                                v-for="(feature, index) in form.features"
                                :key="index"
                                class="flex items-center justify-between bg-gray-50 p-2 rounded"
                            >
                                <span class="text-sm text-gray-900">{{ feature }}</span>
                                <button
                                    type="button"
                                    @click="removeFeature(index)"
                                    class="text-red-600 hover:text-red-800 p-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <p v-else class="text-gray-500 text-sm italic mt-2">No features added yet</p>
                        <InputError class="mt-2" :message="form.errors.features" />
                    </div>

                    <!-- Active Status -->
                    <div>
                        <label class="flex items-center">
                            <Checkbox v-model:checked="form.is_active" />
                            <span class="ml-2 text-sm text-gray-600">Make this plan active and available for selection</span>
                        </label>
                        <InputError class="mt-2" :message="form.errors.is_active" />
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <SecondaryButton type="button" @click="closeCreateModal">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Create Subscription Type
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AdminLayout>
</template>
