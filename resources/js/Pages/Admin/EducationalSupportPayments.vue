<template>
    <AdminLayout :title="language.educational_support_payments">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">{{ language.educational_support_payments }}</h1>
            </div>

            <!-- Payments Table -->
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
                                    {{ language.amount }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.payment_status }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.payment_date }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.actions }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="payment in payments" :key="payment.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ payment.subscription?.student?.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ payment.subscription?.subject?.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                        {{ payment.subscription?.subject?.duration === 'monthly' ? language.monthly : language.yearly }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium">{{ formatPrice(payment.amount) }}</span>
                                            <button
                                                @click="openAmountModal(payment)"
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
                                            'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                            getPaymentStatusClass(payment.status)
                                        ]"
                                    >
                                        {{ getPaymentStatusText(payment.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ payment.payment_date ? formatDate(payment.payment_date) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <button
                                            v-if="payment.status === 'draft' || payment.status === 'partial'"
                                            @click="openPaymentModal(payment)"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                            title="Process Payment"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                        </button>
                                        <button
                                            v-if="payment.status === 'draft'"
                                            @click="markAsPaid(payment)"
                                            class="text-green-600 hover:text-green-900 transition-colors"
                                            title="Mark as Paid"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                        <button
                                            v-if="payment.status === 'draft'"
                                            @click="markAsCancelled(payment)"
                                            class="text-red-600 hover:text-red-900 transition-colors"
                                            title="Mark as Cancelled"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="payments.length === 0">
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No educational support payments found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Amount Update Modal -->
        <div v-if="showAmountModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Background overlay -->
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAmountModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        {{ language.update_amount }}
                                    </h3>
                                    <button @click="closeAmountModal" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Form -->
                                <form @submit.prevent="updateAmount" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.amount }} <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="amountForm.amount"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="0.00"
                                        />
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="updateAmount" type="button" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ language.save }}
                        </button>
                        <button @click="closeAmountModal" type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ language.cancel }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Processing Modal -->
        <div v-if="showPaymentModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Background overlay -->
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closePaymentModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        {{ language.payment_details }}
                                    </h3>
                                    <button @click="closePaymentModal" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Payment Details -->
                                <div class="mb-4 space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">{{ language.student_name }}:</span>
                                        <span class="text-sm text-gray-900">{{ selectedPayment?.subscription?.student?.name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">{{ language.subject }}:</span>
                                        <span class="text-sm text-gray-900">{{ selectedPayment?.subscription?.subject?.name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">{{ language.total_amount }}:</span>
                                        <span class="text-sm font-bold text-gray-900">{{ formatPrice(selectedPayment?.amount) }}</span>
                                    </div>
                                    <div v-if="selectedPayment?.paid_amount > 0" class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">{{ language.paid_amount }}:</span>
                                        <span class="text-sm text-green-600">{{ formatPrice(selectedPayment?.paid_amount) }}</span>
                                    </div>
                                    <div v-if="getRemainingBalance(selectedPayment) > 0" class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">{{ language.remaining_balance }}:</span>
                                        <span class="text-sm text-red-600">{{ formatPrice(getRemainingBalance(selectedPayment)) }}</span>
                                    </div>
                                </div>

                                <!-- Form -->
                                <form @submit.prevent="processPayment" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.paid_amount }} <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="paymentForm.paid_amount"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            :max="getRemainingBalance(selectedPayment)"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="0.00"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ language.max_amount }}: {{ formatPrice(getRemainingBalance(selectedPayment)) }}
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="processPayment" type="button" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ language.process_payment }}
                        </button>
                        <button @click="closePaymentModal" type="button" 
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
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useCurrency } from '@/Composables/useCurrency.js';

const props = defineProps({
    payments: Array,
    language: Object
});

const { formatPrice } = useCurrency();

const showAmountModal = ref(false);
const showPaymentModal = ref(false);
const selectedPayment = ref(null);

const amountForm = reactive({
    amount: ''
});

const paymentForm = reactive({
    paid_amount: ''
});

const openAmountModal = (payment) => {
    selectedPayment.value = payment;
    amountForm.amount = payment.amount;
    showAmountModal.value = true;
};

const openPaymentModal = (payment) => {
    selectedPayment.value = payment;
    // Set the input to 0 for additional payments, not the current paid amount
    paymentForm.paid_amount = 0;
    showPaymentModal.value = true;
};

const closeAmountModal = () => {
    showAmountModal.value = false;
    selectedPayment.value = null;
    amountForm.amount = '';
};

const closePaymentModal = () => {
    showPaymentModal.value = false;
    selectedPayment.value = null;
    paymentForm.paid_amount = '';
};

const updateAmount = () => {
    if (!selectedPayment.value) return;
    
    router.patch(`/admin/educational-support-payments/${selectedPayment.value.id}/update-amount`, {
        amount: amountForm.amount
    }, {
        onSuccess: () => {
            closeAmountModal();
        }
    });
};

const markAsPaid = (payment) => {
    if (confirm('Are you sure you want to mark this payment as paid?')) {
        router.patch(`/admin/educational-support-payments/${payment.id}/mark-as-paid`);
    }
};

const markAsCancelled = (payment) => {
    if (confirm('Are you sure you want to mark this payment as cancelled?')) {
        router.patch(`/admin/educational-support-payments/${payment.id}/mark-as-cancelled`);
    }
};

const processPayment = () => {
    if (!selectedPayment.value) return;
    
    router.patch(`/admin/educational-support-payments/${selectedPayment.value.id}/process-payment`, {
        paid_amount: paymentForm.paid_amount
    }, {
        onSuccess: () => {
            closePaymentModal();
        }
    });
};

const getRemainingBalance = (payment) => {
    if (!payment) return 0;
    return Math.max(0, payment.amount - (payment.paid_amount || 0));
};

const formatDate = (date) => {
    if (!date) return '-';
    const value = typeof date === 'string' ? date : String(date);
    // Normalize to the first 10 chars (YYYY-MM-DD) to avoid timezone shifts
    const isoPrefix = value.substring(0, 10); // e.g., 2025-09-02 from 2025-09-02T00:00:00.000000Z
    const ymd = /^\d{4}-\d{2}-\d{2}$/;
    if (ymd.test(isoPrefix)) {
        const [y, m, d] = isoPrefix.split('-');
        return `${Number(m)}/${Number(d)}/${y}`;
    }
    return new Date(value).toLocaleDateString();
};

const getPaymentStatusClass = (status) => {
    switch (status) {
        case 'draft':
            return 'bg-yellow-100 text-yellow-800';
        case 'paid':
            return 'bg-green-100 text-green-800';
        case 'partial':
            return 'bg-blue-100 text-blue-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getPaymentStatusText = (status) => {
    switch (status) {
        case 'draft':
            return props.language.draft;
        case 'paid':
            return props.language.paid;
        case 'partial':
            return props.language.partial;
        case 'cancelled':
            return props.language.cancelled;
        default:
            return status;
    }
};
</script>
