<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    formations: {
        type: Array,
        default: () => []
    },
    editingStudent: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'student-created', 'student-updated']);

const page = usePage();
const language = computed(() => page.props.language || {});

// Form data
const form = useForm({
    name: '',
    email: '',
    student_id: '', // Add student_id to form
    password: 'password123', // Default password
    password_confirmation: 'password123',
    phone: '',
    parent_phone: '',
    address: '',
    date_of_birth: '',
    formations: [], // Array of formation IDs
    notes: '',
    status: 'active'
});

// Generate student ID automatically
const generateStudentId = () => {
    const prefix = 'ST';
    const random = Math.random().toString(36).substring(2, 8).toUpperCase();
    return `#${prefix}${random}`;
};

// Auto-generate student ID when modal opens
const studentId = ref(generateStudentId());

// Initialize form student_id
form.student_id = studentId.value;

// Multi-select state for formations
const isFormationDropdownOpen = ref(false);
const formationSearchQuery = ref('');

// Filtered formations based on search
const filteredFormations = computed(() => {
    if (!formationSearchQuery.value) {
        return props.formations || [];
    }
    return (props.formations || []).filter(formation => 
        formation.title.toLowerCase().includes(formationSearchQuery.value.toLowerCase())
    );
});

// Selected formations (convert from IDs to full objects)
const selectedFormations = computed(() => {
    if (!props.formations || !form.formations) return [];
    return props.formations.filter(formation => 
        form.formations.includes(formation.id)
    );
});

// Toggle formation selection
const toggleFormation = (formation) => {
    const index = form.formations.findIndex(id => id === formation.id);
    if (index > -1) {
        form.formations.splice(index, 1);
    } else {
        form.formations.push(formation.id);
    }
};

// Check if formation is selected
const isFormationSelected = (formation) => {
    return form.formations.includes(formation.id);
};

// Remove formation from selection
const removeFormation = (formation) => {
    const index = form.formations.findIndex(id => id === formation.id);
    if (index > -1) {
        form.formations.splice(index, 1);
    }
};

// Submit form
const submitForm = () => {
    // Ensure student_id is set (should already be set, but just in case)
    if (!form.student_id) {
        form.student_id = studentId.value;
    }
    
    // Debug: log form data
    console.log('Submitting form with data:', {
        name: form.name,
        email: form.email,
        student_id: form.student_id,
        phone: form.phone,
        parent_phone: form.parent_phone,
        address: form.address,
        date_of_birth: form.date_of_birth,
        formations: form.formations,
        notes: form.notes,
        status: form.status
    });
    
    if (props.editingStudent) {
        // Update existing student
        form.put(`/admin/students/${props.editingStudent.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                console.log('Student updated successfully!');
                closeModal();
                emit('student-updated');
            },
            onError: (errors) => {
                console.error('Form submission errors:', errors);
            }
        });
    } else {
        // Create new student
        form.post('/admin/students', {
            preserveScroll: true,
            onSuccess: () => {
                console.log('Student created successfully!');
                closeModal();
                emit('student-created');
            },
            onError: (errors) => {
                console.error('Form submission errors:', errors);
            }
        });
    }
};

// Close modal and reset form
const closeModal = () => {
    form.reset();
    form.clearErrors();
    // Generate new student ID for next time
    studentId.value = generateStudentId();
    form.student_id = studentId.value; // Update form student_id too
    // Reset dropdown state
    isFormationDropdownOpen.value = false;
    formationSearchQuery.value = '';
    emit('close');
};

// Watch for editing student changes
import { watch } from 'vue';

watch(() => props.editingStudent, (newStudent) => {
    if (newStudent) {
        // Fill form with student data for editing
        form.name = newStudent.name || '';
        form.email = newStudent.email || '';
        form.student_id = newStudent.student_id || '';
        form.phone = newStudent.phone || '';
        form.parent_phone = newStudent.parent_phone || '';
        form.address = newStudent.address || '';
        form.date_of_birth = newStudent.date_of_birth || '';
        form.notes = newStudent.notes || '';
        form.status = newStudent.status || 'active';
        form.formations = newStudent.formations || [];
        
        // Update student ID display
        studentId.value = newStudent.student_id || generateStudentId();
    } else {
        // Reset form for new student
        form.reset();
        studentId.value = generateStudentId();
        form.student_id = studentId.value;
    }
}, { immediate: true });

// Get today's date for max date of birth (should be in the past)
const today = new Date().toISOString().split('T')[0];
// Calculate max date (18 years ago for reasonable student age)
const maxDate = new Date();
maxDate.setFullYear(maxDate.getFullYear() - 5); // Minimum 5 years old
const maxDateString = maxDate.toISOString().split('T')[0];

// Calculate min date (reasonable max age for students)
const minDate = new Date();
minDate.setFullYear(minDate.getFullYear() - 25); // Maximum 25 years old
const minDateString = minDate.toISOString().split('T')[0];
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="2xl">
        <div class="p-4">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ editingStudent ? (language.edit || 'Edit') : (language.add_new || 'Add New') }} {{ language.student || 'Student' }}
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
            <form @submit.prevent="submitForm" class="space-y-4">
                <!-- Student ID Display -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 012-2h2a2 2 0 012 2v2m-4 0a2 2 0 01-2 2h-2a2 2 0 01-2-2m0 0V4a2 2 0 012-2h2a2 2 0 012 2v2"></path>
                        </svg>
                        <span class="text-sm font-medium text-blue-900">{{ language.student_id || 'Student ID' }}:</span>
                        <span class="text-sm font-mono text-blue-800 bg-white px-2 py-1 rounded border">{{ studentId }}</span>
                        <span v-if="editingStudent" class="text-xs text-blue-600">(Read-only)</span>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Full Name -->
                    <div>
                        <InputLabel for="name" value="Full Name" class="mb-2" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full"
                            placeholder="Ahmed Benali"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <InputLabel for="email" value="Email Address" class="mb-2" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="w-full"
                            placeholder="ahmed.student@academy.com"
                            required
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Phone -->
                    <div>
                        <InputLabel for="phone" value="Student Phone" class="mb-2" />
                        <TextInput
                            id="phone"
                            v-model="form.phone"
                            type="tel"
                            class="w-full"
                            placeholder="+212 6 12 34 56 78"
                        />
                        <InputError :message="form.errors.phone" class="mt-2" />
                    </div>

                    <!-- Parent Phone -->
                    <div>
                        <InputLabel for="parent_phone" value="Parent/Guardian Phone" class="mb-2" />
                        <TextInput
                            id="parent_phone"
                            v-model="form.parent_phone"
                            type="tel"
                            class="w-full"
                            placeholder="+212 6 12 34 56 78"
                            required
                        />
                        <InputError :message="form.errors.parent_phone" class="mt-2" />
                    </div>
                </div>

                <!-- Date of Birth -->
                <div>
                    <InputLabel for="date_of_birth" value="Date of Birth" class="mb-2" />
                    <TextInput
                        id="date_of_birth"
                        v-model="form.date_of_birth"
                        type="date"
                        class="w-full md:w-1/2"
                        :min="minDateString"
                        :max="maxDateString"
                        required
                    />
                    <InputError :message="form.errors.date_of_birth" class="mt-2" />
                </div>

                <!-- Formation Selection -->
                <div class="relative">
                    <InputLabel for="formations" value="Select Formations" class="mb-2" />
                    
                    <!-- Selected Formations Display -->
                    <div class="w-full min-h-[42px] px-3 py-2 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-purple-500 focus-within:border-transparent bg-white">
                        <div class="flex flex-wrap gap-1 mb-1" v-if="selectedFormations.length > 0">
                            <span v-for="formation in selectedFormations" :key="formation.id" 
                                  class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded">
                                {{ formation.title }}
                                <button @click="removeFormation(formation)" type="button" class="text-purple-600 hover:text-purple-800">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </span>
                        </div>
                        
                        <!-- Search Input -->
                        <input 
                            v-model="formationSearchQuery"
                            @focus="isFormationDropdownOpen = true"
                            @click="isFormationDropdownOpen = true"
                            type="text"
                            class="w-full border-none outline-none focus:ring-0 p-0"
                            :placeholder="selectedFormations.length === 0 ? 'Select Formations' : 'Add more formations...'"
                        />
                    </div>
                    
                    <!-- Dropdown List -->
                    <div v-if="isFormationDropdownOpen" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-auto">
                        <div v-if="filteredFormations.length === 0" class="px-3 py-2 text-gray-500 text-sm">
                            No formations found
                        </div>
                        <button 
                            v-for="formation in filteredFormations" 
                            :key="formation.id"
                            @click="toggleFormation(formation)"
                            type="button"
                            :class="[
                                'w-full text-left px-3 py-2 hover:bg-gray-50 flex items-center justify-between border-b border-gray-100 last:border-b-0',
                                isFormationSelected(formation) ? 'bg-purple-50 text-purple-700' : 'text-gray-700'
                            ]">
                            <div class="flex-1">
                                <div class="font-medium">{{ formation.title }}</div>
                                <div class="text-xs text-gray-500 flex items-center gap-2">
                                    <span>{{ formation.price }}</span>
                                    <span>•</span>
                                    <span>{{ formation.duration }}</span>
                                    <span>•</span>
                                    <span>{{ formation.level }}</span>
                                </div>
                            </div>
                            <svg v-if="isFormationSelected(formation)" class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Click outside to close dropdown -->
                    <div v-if="isFormationDropdownOpen" @click="isFormationDropdownOpen = false" class="fixed inset-0 z-0"></div>
                    
                    <p class="text-sm text-gray-500 mt-1">Select one or more formations for this student</p>
                    <InputError :message="form.errors.formations" class="mt-2" />
                </div>

                <!-- Address -->
                <div>
                    <InputLabel for="address" value="Address" class="mb-2" />
                    <textarea
                        id="address"
                        v-model="form.address"
                        rows="2"
                        class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm"
                        placeholder="Student's full address..."
                    ></textarea>
                    <InputError :message="form.errors.address" class="mt-2" />
                </div>

                                 <!-- Notes (Optional) -->
                 <div>
                     <InputLabel for="notes" value="Additional Notes (Optional)" class="mb-2" />
                     <textarea
                         id="notes"
                         v-model="form.notes"
                         rows="2"
                         class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm"
                         placeholder="Any additional information about the student..."
                     ></textarea>
                     <InputError :message="form.errors.notes" class="mt-2" />
                 </div>

                <!-- Status Selection -->
                <div v-if="editingStudent">
                    <InputLabel for="status" value="Status" class="mb-2" />
                    <select
                        id="status"
                        v-model="form.status"
                        class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>

                <!-- Password Notice -->
                <div v-if="!editingStudent" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-yellow-800">
                            <p class="font-medium">Default Login Credentials:</p>
                            <p>Password: <code class="bg-yellow-100 px-1 rounded">password123</code></p>
                            <p class="text-xs mt-1">The student can change this password after first login.</p>
                        </div>
                    </div>
                </div>

                                 <!-- Modal Footer -->
                 <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <SecondaryButton @click="closeModal" type="button">
                        {{ language.cancel || 'Cancel' }}
                    </SecondaryButton>
                    <PrimaryButton
                        type="submit"
                        :disabled="form.processing"
                        class="bg-purple-600 hover:bg-purple-700 focus:ring-purple-500"
                    >
                        {{ form.processing ? (editingStudent ? (language.updating || 'Updating...') : (language.creating || 'Creating...')) : (editingStudent ? (language.update_student || 'Update Student') : (language.create_student || 'Create Student')) }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<style scoped>
/* Custom styles for the modal */
</style>
