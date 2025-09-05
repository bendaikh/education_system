<template>
    <AdminLayout title="Childhood Subscriptions">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Childhood Subscriptions</h1>
                    <p class="mt-2 text-sm text-gray-700">Manage childhood education subscriptions for students</p>
                </div>
                <button
                    @click="showCreateModal = true"
                    class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Subscription
                </button>
            </div>

            <!-- Subscriptions Table -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Period
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payment Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Notes
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="subscription in subscriptions" :key="subscription.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-primary-800">
                                                    {{ subscription.student?.name?.charAt(0)?.toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ subscription.student_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ subscription.student_email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ subscription.childhood_subject_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ formatPrice(subscription.childhood_subject_price) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ formatDate(subscription.start_date) }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        to {{ formatDate(subscription.end_date) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            getStatusClasses(subscription.status)
                                        ]"
                                    >
                                        {{ subscription.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            getPaymentStatusClasses(subscription.payment_status)
                                        ]"
                                    >
                                        {{ subscription.payment_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        {{ subscription.notes || 'No notes' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button
                                            @click="editSubscription(subscription)"
                                            class="text-primary-600 hover:text-primary-900"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="deleteSubscription(subscription.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="subscriptions.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No subscriptions</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new childhood subscription.</p>
                    <div class="mt-6">
                        <button
                            @click="showCreateModal = true"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Subscription
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="showCreateModal || showEditModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ showEditModal ? 'Edit Subscription' : 'Create New Subscription' }}
                </h2>

                <form @submit.prevent="showEditModal ? updateSubscription() : createSubscription()" class="space-y-4">
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700">Student</label>
                        <select
                            id="student_id"
                            v-model="form.student_id"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        >
                            <option value="">Select a student</option>
                            <option v-for="student in students" :key="student.id" :value="student.id">
                                {{ student.name }} ({{ student.email }})
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="childhood_subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                        <select
                            id="childhood_subject_id"
                            v-model="form.childhood_subject_id"
                            required
                            @change="onSubjectChange"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        >
                            <option value="">Select a subject</option>
                            <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                                {{ subject.name }} ({{ subject.duration === 'monthly' ? 'Monthly' : subject.duration === 'yearly' ? 'Yearly' : subject.duration }}) - {{ formatPrice(subject.price) }}
                            </option>
                        </select>
                    </div>

                    <!-- Start Date Picker and Calculated End Date -->
                    <div v-if="form.childhood_subject_id && selectedSubject" class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Calculated Dates</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <label class="block text-xs text-blue-700 mb-1">Start Date</label>
                                <input type="date" v-model="form.start_date" class="w-full border rounded-md px-3 py-2" />
                            </div>
                            <div>
                                <label class="block text-xs text-blue-700 mb-1">End Date</label>
                                <input type="date" :value="calculatedEndDate" class="w-full border rounded-md px-3 py-2 bg-gray-100" readonly />
                            </div>
                        </div>
                        <div class="mt-3 text-sm">
                            <span class="text-blue-700">Duration:</span>
                            <span class="text-blue-900 font-medium">{{ selectedSubject.duration === 'monthly' ? 'Monthly' : selectedSubject.duration === 'yearly' ? 'Yearly' : selectedSubject.duration }}</span>
                        </div>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select
                            id="status"
                            v-model="form.status"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        >
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                            placeholder="Optional notes about this subscription"
                        ></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button
                            type="button"
                            @click="closeModal"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            {{ showEditModal ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import { useCurrency } from '@/Composables/useCurrency.js';

const props = defineProps({
    subscriptions: Array,
    students: Array,
    subjects: Array
});

const { formatPrice } = useCurrency();

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingSubscription = ref(null);

const form = reactive({
    student_id: '',
    childhood_subject_id: '',
    start_date: '',
    end_date: '',
    status: 'Active',
    notes: ''
});

// Computed properties for automatic date calculation
const selectedSubject = computed(() => {
    if (!form.childhood_subject_id) return null;
    return props.subjects.find(subject => subject.id == form.childhood_subject_id);
});

const calculatedEndDate = computed(() => {
    if (!selectedSubject.value) return null;
    
    const startDate = form.start_date ? new Date(form.start_date) : new Date();
    if (selectedSubject.value.duration === 'monthly') {
        startDate.setMonth(startDate.getMonth() + 1);
    } else if (selectedSubject.value.duration === 'yearly') {
        startDate.setFullYear(startDate.getFullYear() + 1);
    }
    
    return startDate.toISOString().split('T')[0];
});

const onSubjectChange = () => {
    // This will trigger the computed properties to recalculate
    if (form.start_date) {
        // Auto-calculate end date when subject changes
        form.end_date = calculatedEndDate.value;
    }
};

const resetForm = () => {
    form.student_id = '';
    form.childhood_subject_id = '';
    form.start_date = '';
    form.end_date = '';
    form.status = 'Active';
    form.notes = '';
};

const createSubscription = () => {
    // Use calculated end date if available
    const subscriptionData = {
        ...form,
        end_date: calculatedEndDate.value || form.end_date
    };
    
    router.post('/admin/childhood-subscriptions', subscriptionData, {
        onSuccess: () => {
            closeModal();
            resetForm();
        }
    });
};

const editSubscription = (subscription) => {
    editingSubscription.value = subscription;
    form.student_id = subscription.student_id;
    form.childhood_subject_id = subscription.childhood_subject_id;
    form.start_date = subscription.start_date;
    form.end_date = subscription.end_date;
    form.status = subscription.status;
    form.notes = subscription.notes || '';
    showEditModal.value = true;
};

const updateSubscription = () => {
    // Use calculated end date if available
    const subscriptionData = {
        ...form,
        end_date: calculatedEndDate.value || form.end_date
    };
    
    router.put(`/admin/childhood-subscriptions/${editingSubscription.value.id}`, subscriptionData, {
        onSuccess: () => {
            closeModal();
            resetForm();
            editingSubscription.value = null;
        }
    });
};

const deleteSubscription = (id) => {
    if (confirm('Are you sure you want to delete this subscription?')) {
        router.delete(`/admin/childhood-subscriptions/${id}`);
    }
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingSubscription.value = null;
    resetForm();
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const getStatusClasses = (status) => {
    switch (status) {
        case 'Active':
            return 'bg-green-100 text-green-800';
        case 'Inactive':
            return 'bg-gray-100 text-gray-800';
        case 'Cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getPaymentStatusClasses = (status) => {
    switch (status) {
        case 'Paid':
            return 'bg-green-100 text-green-800';
        case 'Pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'Partial':
            return 'bg-blue-100 text-blue-800';
        case 'Cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>
