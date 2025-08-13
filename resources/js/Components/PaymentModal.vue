<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { useCurrency } from '@/Composables/useCurrency.js';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    formations: {
        type: Array,
        default: () => []
    },
    students: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'payment-created']);

const page = usePage();
const language = computed(() => page.props.language || {});
const { formatPrice } = useCurrency();

// Form data
const form = useForm({
    student_id: '',
    formation_id: '',
    amount: '',
    payment_type: 'Cash',
    due_date: '',
    notes: ''
});

// Payment type options
const paymentTypes = [
    { value: 'Cash', label: 'Cash' },
    { value: 'Bank Transfer', label: 'Bank Transfer' },
    { value: 'Credit Card', label: 'Credit Card' },
    { value: 'Check', label: 'Check' },
    { value: 'Online', label: 'Online Payment' },
    { value: 'Other', label: 'Other' }
];

// Get selected formation details
const selectedFormation = computed(() => {
    if (!form.formation_id) return null;
    return props.formations.find(f => f.id == form.formation_id);
});

// Auto-fill amount when formation is selected
const onFormationChange = () => {
    if (selectedFormation.value && selectedFormation.value.price) {
        // Extract numeric value from price string
        const numericPrice = selectedFormation.value.price.replace(/[^\d.-]/g, '');
        form.amount = numericPrice;
    }
};

// Submit form
const submitForm = () => {
    form.post('/admin/payments', {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            // Emit event to parent component to refresh data
            emit('payment-created');
        },
        onError: () => {
            // Errors will be handled by form validation
        }
    });
};

// Close modal and reset form
const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

// Get today's date for min date
const today = new Date().toISOString().split('T')[0];
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ language.add_new || 'Add New' }} {{ language.payment || 'Payment' }}
                </h2>
                <button
                    @click="closeModal"
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form @submit.prevent="submitForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Selection -->
                    <div>
                        <InputLabel for="student_id" value="Student" class="mb-2" />
                        <select
                            id="student_id"
                            v-model="form.student_id"
                            class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm"
                            required
                        >
                            <option value="">{{ language.select_student || 'Select Student' }}</option>
                            <option v-for="student in students" :key="student.id" :value="student.id">
                                {{ student.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.student_id" class="mt-2" />
                    </div>

                    <!-- Formation Selection -->
                    <div>
                        <InputLabel for="formation_id" value="Formation/Course" class="mb-2" />
                        <select
                            id="formation_id"
                            v-model="form.formation_id"
                            @change="onFormationChange"
                            class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm"
                            required
                        >
                            <option value="">{{ language.select_formation || 'Select Formation' }}</option>
                            <option v-for="formation in formations" :key="formation.id" :value="formation.id">
                                {{ formation.title }} - {{ formatPrice(formation.price) }}
                            </option>
                        </select>
                        <InputError :message="form.errors.formation_id" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Amount -->
                    <div>
                        <InputLabel for="amount" value="Amount" class="mb-2" />
                        <TextInput
                            id="amount"
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            min="0"
                            class="w-full"
                            :placeholder="formatPrice(0)"
                            required
                        />
                        <InputError :message="form.errors.amount" class="mt-2" />
                        <p v-if="selectedFormation" class="text-sm text-gray-500 mt-1">
                            {{ language.formation_price || 'Formation Price' }}: {{ formatPrice(selectedFormation.price) }}
                        </p>
                    </div>

                    <!-- Payment Type -->
                    <div>
                        <InputLabel for="payment_type" value="Payment Type" class="mb-2" />
                        <select
                            id="payment_type"
                            v-model="form.payment_type"
                            class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm"
                            required
                        >
                            <option v-for="type in paymentTypes" :key="type.value" :value="type.value">
                                {{ type.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.payment_type" class="mt-2" />
                    </div>
                </div>

                <!-- Due Date -->
                <div>
                    <InputLabel for="due_date" value="Due Date" class="mb-2" />
                    <TextInput
                        id="due_date"
                        v-model="form.due_date"
                        type="date"
                        class="w-full"
                        :min="today"
                        required
                    />
                    <InputError :message="form.errors.due_date" class="mt-2" />
                </div>

                <!-- Notes (Optional) -->
                <div>
                    <InputLabel for="notes" value="Notes (Optional)" class="mb-2" />
                    <textarea
                        id="notes"
                        v-model="form.notes"
                        rows="3"
                        class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm"
                        :placeholder="language.add_notes || 'Add any additional notes...'"
                    ></textarea>
                    <InputError :message="form.errors.notes" class="mt-2" />
                </div>

                <!-- Selected Formation Info -->
                <div v-if="selectedFormation" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 mb-2">{{ language.selected_formation || 'Selected Formation' }}:</h4>
                    <div class="text-sm text-blue-800 space-y-1">
                        <p><strong>{{ language.title || 'Title' }}:</strong> {{ selectedFormation.title }}</p>
                        <p><strong>{{ language.price || 'Price' }}:</strong> {{ formatPrice(selectedFormation.price) }}</p>
                        <p v-if="selectedFormation.duration"><strong>{{ language.duration || 'Duration' }}:</strong> {{ selectedFormation.duration }}</p>
                        <p v-if="selectedFormation.level"><strong>{{ language.level || 'Level' }}:</strong> {{ selectedFormation.level }}</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                    <SecondaryButton @click="closeModal" type="button">
                        {{ language.cancel || 'Cancel' }}
                    </SecondaryButton>
                    <PrimaryButton
                        type="submit"
                        :disabled="form.processing"
                        class="bg-purple-600 hover:bg-purple-700 focus:ring-purple-500"
                    >
                        {{ form.processing ? (language.creating || 'Creating...') : (language.create_payment || 'Create Payment') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<style scoped>
/* Custom styles for the modal */
</style>
