<template>
    <AdminLayout :title="language.manage_educational_support">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">{{ language.manage_educational_support }}</h1>
                <button
                    @click="showCreateModal = true"
                    class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-200 flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ language.add_new }}
                </button>
            </div>

            <!-- Subscriptions Table -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.student_name }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.subject }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.duration }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.start_date }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.end_date }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.status }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.payment_status }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.actions }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="subscription in subscriptions" :key="subscription.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ subscription.student?.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ subscription.subject?.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                        {{ subscription.subject?.duration === 'monthly' ? language.monthly : language.yearly }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(subscription.start_date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ subscription.end_date ? formatDate(subscription.end_date) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                            getStatusClass(subscription.status)
                                        ]"
                                    >
                                        {{ getStatusText(subscription.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                            getPaymentStatusClass(subscription.payment?.status)
                                        ]"
                                    >
                                        {{ getPaymentStatusText(subscription.payment?.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="editSubscription(subscription)"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteSubscription(subscription)"
                                            class="text-red-600 hover:text-red-900 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="subscriptions.length === 0">
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No educational support subscriptions found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal || showEditModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Background overlay -->
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        {{ showEditModal ? language.edit : language.add_new }} {{ language.manage_educational_support }}
                                    </h3>
                                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Form -->
                                <form @submit.prevent="showEditModal ? updateSubscription() : createSubscription()" class="space-y-4">
                                    <!-- Student (Required) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.student_name }} <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            v-model="form.student_id"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">{{ language.select_student || 'Select Student' }}</option>
                                            <option v-for="student in students" :key="student.id" :value="student.id">
                                                {{ student.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Subject (Required) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.subject }} <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            v-model="form.educational_subject_id"
                                            required
                                            @change="onSubjectChange"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">{{ language.select_subject || 'Select Subject' }}</option>
                                            <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                                                {{ subject.name }} ({{ subject.duration === 'monthly' ? language.monthly : language.yearly }})
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Calculated Dates Preview -->
                                    <div v-if="form.educational_subject_id && selectedSubject" class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                        <h4 class="text-sm font-medium text-blue-900 mb-2">{{ language.calculated_dates }}</h4>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-blue-700">{{ language.start_date }}:</span>
                                                <span class="text-blue-900 font-medium">{{ formatDate(calculatedStartDate) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-blue-700">{{ language.end_date }}:</span>
                                                <span class="text-blue-900 font-medium">{{ formatDate(calculatedEndDate) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-blue-700">{{ language.duration }}:</span>
                                                <span class="text-blue-900 font-medium">{{ selectedSubject.duration === 'monthly' ? language.monthly : language.yearly }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status (Required) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.status }} <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            v-model="form.status"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="active">{{ language.active }}</option>
                                            <option value="completed">{{ language.completed }}</option>
                                            <option value="cancelled">{{ language.cancelled }}</option>
                                        </select>
                                    </div>

                                    <!-- Notes (Optional) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.notes }} <span class="text-gray-400 text-xs">({{ language.optional || 'Optional' }})</span>
                                        </label>
                                        <textarea
                                            v-model="form.notes"
                                            rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            :placeholder="language.notes"
                                        ></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="showEditModal ? updateSubscription() : createSubscription()" type="button" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ language.save }}
                        </button>
                        <button @click="closeModal" type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ language.cancel }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    subscriptions: Array,
    students: Array,
    subjects: Array,
    language: Object
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingSubscription = ref(null);

const form = reactive({
    student_id: '',
    educational_subject_id: '',
    status: 'active',
    notes: ''
});

// Computed properties for date calculation
const selectedSubject = computed(() => {
    if (!form.educational_subject_id) return null;
    return props.subjects.find(subject => subject.id == form.educational_subject_id);
});

const calculatedStartDate = computed(() => {
    return new Date().toISOString().split('T')[0];
});

const calculatedEndDate = computed(() => {
    if (!selectedSubject.value) return null;
    
    const startDate = new Date();
    if (selectedSubject.value.duration === 'monthly') {
        startDate.setMonth(startDate.getMonth() + 1);
    } else {
        startDate.setFullYear(startDate.getFullYear() + 1);
    }
    
    return startDate.toISOString().split('T')[0];
});

const onSubjectChange = () => {
    // This will trigger the computed properties to recalculate
};

const editSubscription = (subscription) => {
    editingSubscription.value = subscription;
    form.student_id = subscription.student_id;
    form.educational_subject_id = subscription.educational_subject_id;
    form.status = subscription.status;
    form.notes = subscription.notes || '';
    showEditModal.value = true;
};

const createSubscription = () => {
    router.post('/admin/educational-support', form, {
        onSuccess: () => {
            closeModal();
        }
    });
};

const updateSubscription = () => {
    router.put(`/admin/educational-support/${editingSubscription.value.id}`, form, {
        onSuccess: () => {
            closeModal();
        }
    });
};

const deleteSubscription = (subscription) => {
    if (confirm('Are you sure you want to delete this educational support subscription?')) {
        router.delete(`/admin/educational-support/${subscription.id}`);
    }
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingSubscription.value = null;
    resetForm();
};

const resetForm = () => {
    form.student_id = '';
    form.educational_subject_id = '';
    form.status = 'active';
    form.notes = '';
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};

const getStatusClass = (status) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800';
        case 'completed':
            return 'bg-blue-100 text-blue-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getStatusText = (status) => {
    switch (status) {
        case 'active':
            return props.language.active;
        case 'completed':
            return props.language.completed;
        case 'cancelled':
            return props.language.cancelled;
        default:
            return status;
    }
};

const getPaymentStatusClass = (status) => {
    if (!status) return 'bg-gray-100 text-gray-800';
    
    switch (status) {
        case 'draft':
            return 'bg-yellow-100 text-yellow-800';
        case 'paid':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getPaymentStatusText = (status) => {
    if (!status) return 'No Payment';
    
    switch (status) {
        case 'draft':
            return 'Draft';
        case 'paid':
            return 'Paid';
        case 'cancelled':
            return 'Cancelled';
        default:
            return status;
    }
};
</script>
