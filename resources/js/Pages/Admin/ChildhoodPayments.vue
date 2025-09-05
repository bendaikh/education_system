<template>
    <AdminLayout title="Childhood Payments">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Childhood Payments</h1>
                    <p class="mt-2 text-sm text-gray-700">Manage payments for childhood education subscriptions</p>
                </div>
            </div>

            <!-- Payments Table -->
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
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payment Date
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
                            <tr v-for="payment in payments" :key="payment.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-primary-800">
                                                    {{ payment.subscription?.student?.name?.charAt(0)?.toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ payment.subscription?.student?.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ payment.subscription?.student?.email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ payment.subscription?.childhood_subject?.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium">{{ formatPrice(payment.amount) }}</span>
                                            <button
                                                @click="openUpdateAmountModal(payment)"
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="Update Amount"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div v-if="payment.paid_amount > 0" class="text-xs">
                                            <span class="text-green-600">Paid: {{ formatPrice(payment.paid_amount) }}</span>
                                            <span v-if="getRemainingBalance(payment) > 0" class="text-red-600 ml-2">Remaining: {{ formatPrice(getRemainingBalance(payment)) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            getStatusClasses(payment.status)
                                        ]"
                                    >
                                        {{ payment.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ payment.payment_date ? formatDate(payment.payment_date) : 'Not set' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        {{ payment.notes || 'No notes' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <button
                                            v-if="payment.status !== 'Paid'"
                                            @click="markAsPaid(payment.id)"
                                            class="p-2 rounded hover:bg-green-50 text-green-600"
                                            title="Mark as Paid"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="openProcessModal(payment)"
                                            class="p-2 rounded hover:bg-blue-50 text-blue-600"
                                            title="Process Payment"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8"></path>
                                            </svg>
                                        </button>
                                        <button
                                            v-if="payment.status !== 'Cancelled'"
                                            @click="markAsCancelled(payment.id)"
                                            class="p-2 rounded hover:bg-gray-50 text-gray-600"
                                            title="Cancel Payment"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="payments.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No payments</h3>
                    <p class="mt-1 text-sm text-gray-500">Payments will appear here when subscriptions are created.</p>
                </div>
            </div>
        </div>

        <!-- Process Payment Modal -->
        <Modal :show="showProcessModal" @close="closeProcessModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Process Payment</h2>

                <form @submit.prevent="processPayment" class="space-y-4">
                    <div>
                        <label for="paid_amount" class="block text-sm font-medium text-gray-700">Paid Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">{{ currencyInfo.symbol }}&nbsp;</span>
                            </div>
                            <input
                                id="paid_amount"
                                v-model="processForm.paid_amount"
                                type="number"
                                step="0.01"
                                min="0"
                                :max="selectedPayment?.amount"
                                required
                                class="pl-8 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Maximum amount: {{ formatPrice(selectedPayment?.amount) }}
                        </p>
                    </div>

                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                        <input
                            id="payment_date"
                            v-model="processForm.payment_date"
                            type="date"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        />
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button
                            type="button"
                            @click="closeProcessModal"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            Process Payment
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Update Amount Modal -->
        <Modal :show="showUpdateAmountModal" @close="closeUpdateAmountModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Update Payment Amount</h2>

                <form @submit.prevent="updateAmount" class="space-y-4">
                    <div>
                        <label for="new_amount" class="block text-sm font-medium text-gray-700">New Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">{{ currencyInfo.symbol }}&nbsp;</span>
                            </div>
                            <input
                                id="new_amount"
                                v-model="updateAmountForm.amount"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                class="pl-8 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button
                            type="button"
                            @click="closeUpdateAmountModal"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            Update Amount
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import { useCurrency } from '@/Composables/useCurrency.js';

const props = defineProps({
    payments: Array
});

const { formatPrice, currencyInfo } = useCurrency();

const showProcessModal = ref(false);
const showUpdateAmountModal = ref(false);
const selectedPayment = ref(null);

const processForm = reactive({
    paid_amount: '',
    payment_date: ''
});

const updateAmountForm = reactive({
    amount: ''
});

const markAsPaid = (paymentId) => {
    if (confirm('Mark this payment as paid?')) {
        router.patch(`/admin/childhood-payments/${paymentId}/mark-as-paid`);
    }
};

const markAsCancelled = (paymentId) => {
    if (confirm('Cancel this payment?')) {
        router.patch(`/admin/childhood-payments/${paymentId}/mark-as-cancelled`);
    }
};

const openProcessModal = (payment) => {
    selectedPayment.value = payment;
    processForm.paid_amount = '';
    processForm.payment_date = new Date().toISOString().split('T')[0];
    showProcessModal.value = true;
};

const processPayment = () => {
    router.patch(`/admin/childhood-payments/${selectedPayment.value.id}/process-payment`, processForm, {
        onSuccess: () => {
            closeProcessModal();
        }
    });
};

const openUpdateAmountModal = (payment) => {
    selectedPayment.value = payment;
    updateAmountForm.amount = payment.amount;
    showUpdateAmountModal.value = true;
};

const updateAmount = () => {
    router.patch(`/admin/childhood-payments/${selectedPayment.value.id}/update-amount`, updateAmountForm, {
        onSuccess: () => {
            closeUpdateAmountModal();
        }
    });
};

const closeProcessModal = () => {
    showProcessModal.value = false;
    selectedPayment.value = null;
    processForm.paid_amount = '';
    processForm.payment_date = '';
};

const closeUpdateAmountModal = () => {
    showUpdateAmountModal.value = false;
    selectedPayment.value = null;
    updateAmountForm.amount = '';
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const getStatusClasses = (status) => {
    switch (status) {
        case 'Paid':
            return 'bg-green-100 text-green-800';
        case 'Partial':
            return 'bg-yellow-100 text-yellow-800';
        case 'Pending':
            return 'bg-blue-100 text-blue-800';
        case 'Cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getRemainingBalance = (payment) => {
    return Number(payment.amount) - Number(payment.paid_amount || 0);
};
</script>
