<template>
    <AdminLayout :title="language.educational_subjects">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">{{ language.educational_subjects }}</h1>
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

            <!-- Subjects Table -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.name }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.description }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.duration }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.price }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.teachers }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.status }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ language.actions }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="subject in subjects" :key="subject.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ subject.name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ subject.description || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ subject.duration === 'monthly' ? language.monthly : language.yearly }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatPrice(subject.price) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="teacher in subject.teachers" :key="teacher.id" 
                                              class="inline-flex px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                            {{ teacher.name }}
                                        </span>
                                        <span v-if="!subject.teachers || subject.teachers.length === 0" class="text-gray-400">
                                            -
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                            subject.status === 'active' 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-red-100 text-red-800'
                                        ]"
                                    >
                                        {{ subject.status === 'active' ? language.active : language.inactive }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="editSubject(subject)"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteSubject(subject)"
                                            class="text-red-600 hover:text-red-900 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="subjects.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No educational subjects found.
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
                                        {{ showEditModal ? language.edit : language.add_new }} {{ language.educational_subjects }}
                                    </h3>
                                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Form -->
                                <form @submit.prevent="showEditModal ? updateSubject() : createSubject()" class="space-y-4">
                                    <!-- Name (Required) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.name }} <span class="text-red-500">*</span>
                                        </label>
                                        <input v-model="form.name" type="text" required 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               :placeholder="language.name" />
                                    </div>

                                    <!-- Description (Optional) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.description }} <span class="text-gray-400 text-xs">({{ language.optional || 'Optional' }})</span>
                                        </label>
                                        <textarea v-model="form.description" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                  :placeholder="language.description"></textarea>
                                    </div>

                                    <!-- Duration (Required) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.duration }} <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            v-model="form.duration"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">{{ language.select_duration }}</option>
                                            <option value="monthly">{{ language.monthly }}</option>
                                            <option value="yearly">{{ language.yearly }}</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.price }} <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="form.price"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="0.00"
                                        />
                                    </div>

                                    <!-- Teachers (Optional) -->
                                    <div class="relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.teachers }} <span class="text-gray-400 text-xs">({{ language.optional || 'Optional' }})</span>
                                        </label>
                                        
                                        <!-- Selected Teachers Display -->
                                        <div class="w-full min-h-[42px] px-3 py-2 border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent bg-white">
                                            <div class="flex flex-wrap gap-1 mb-1" v-if="selectedTeachers.length > 0">
                                                <span v-for="teacher in selectedTeachers" :key="teacher.id" 
                                                      class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                                                    {{ teacher.name }}
                                                    <button @click="removeTeacher(teacher)" type="button" class="text-blue-600 hover:text-blue-800">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </span>
                                            </div>
                                            
                                            <!-- Search Input -->
                                            <input 
                                                v-model="teacherSearchQuery"
                                                @focus="isTeacherDropdownOpen = true"
                                                @click="isTeacherDropdownOpen = true"
                                                type="text"
                                                class="w-full border-none outline-none focus:ring-0 p-0"
                                                :placeholder="selectedTeachers.length === 0 ? (language.select_teachers || 'Select Teachers') : 'Add more teachers...'"
                                            />
                                        </div>
                                        
                                        <!-- Dropdown List -->
                                        <div v-if="isTeacherDropdownOpen" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                            <div v-if="filteredTeachers.length === 0" class="px-3 py-2 text-gray-500 text-sm">
                                                No teachers found
                                            </div>
                                            <button 
                                                v-for="teacher in filteredTeachers" 
                                                :key="teacher.id"
                                                @click="toggleTeacher(teacher)"
                                                type="button"
                                                :class="[
                                                    'w-full text-left px-3 py-2 hover:bg-gray-50 flex items-center justify-between',
                                                    isTeacherSelected(teacher) ? 'bg-blue-50 text-blue-700' : 'text-gray-700'
                                                ]">
                                                <div>
                                                    <div class="font-medium">{{ teacher.name }}</div>
                                                    <div class="text-xs text-gray-500">{{ teacher.department }}</div>
                                                </div>
                                                <svg v-if="isTeacherSelected(teacher)" class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Click outside to close dropdown -->
                                        <div v-if="isTeacherDropdownOpen" @click="isTeacherDropdownOpen = false" class="fixed inset-0 z-0"></div>
                                    </div>

                                    <!-- Status (Required) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ language.status }} <span class="text-red-500">*</span>
                                        </label>
                                        <select v-model="form.status" required 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="active">{{ language.active }}</option>
                                            <option value="inactive">{{ language.inactive }}</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="showEditModal ? updateSubject() : createSubject()" type="button" 
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
import { useCurrency } from '@/Composables/useCurrency.js';

const props = defineProps({
    subjects: Array,
    teachers: Array,
    language: Object
});

const { formatPrice } = useCurrency();

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingSubject = ref(null);

// Teacher selection state
const isTeacherDropdownOpen = ref(false);
const teacherSearchQuery = ref('');
const selectedTeachers = ref([]);

const form = reactive({
    name: '',
    description: '',
    duration: '',
    price: '',
    status: 'active',
    teacher_ids: []
});

// Filtered teachers based on search
const filteredTeachers = computed(() => {
    if (!teacherSearchQuery.value) {
        return props.teachers || [];
    }
    return (props.teachers || []).filter(teacher => 
        teacher.name.toLowerCase().includes(teacherSearchQuery.value.toLowerCase())
    );
});

// Toggle teacher selection
const toggleTeacher = (teacher) => {
    const index = selectedTeachers.value.findIndex(t => t.id === teacher.id);
    if (index > -1) {
        selectedTeachers.value.splice(index, 1);
    } else {
        selectedTeachers.value.push(teacher);
    }
};

// Check if teacher is selected
const isTeacherSelected = (teacher) => {
    return selectedTeachers.value.some(t => t.id === teacher.id);
};

// Remove teacher from selection
const removeTeacher = (teacher) => {
    const index = selectedTeachers.value.findIndex(t => t.id === teacher.id);
    if (index > -1) {
        selectedTeachers.value.splice(index, 1);
    }
};

const editSubject = (subject) => {
    editingSubject.value = subject;
    form.name = subject.name;
    form.description = subject.description || '';
    form.duration = subject.duration;
    form.price = subject.price; // Add price to form
    form.status = subject.status;
    
    // Set selected teachers
    selectedTeachers.value = subject.teachers ? [...subject.teachers] : [];
    
    showEditModal.value = true;
};

const createSubject = () => {
    const formData = {
        ...form,
        teacher_ids: selectedTeachers.value.map(teacher => teacher.id)
    };
    
    router.post('/admin/educational-subjects', formData, {
        onSuccess: () => {
            closeModal();
        }
    });
};

const updateSubject = () => {
    const formData = {
        ...form,
        teacher_ids: selectedTeachers.value.map(teacher => teacher.id)
    };
    
    router.put(`/admin/educational-subjects/${editingSubject.value.id}`, formData, {
        onSuccess: () => {
            closeModal();
        }
    });
};

const deleteSubject = (subject) => {
    if (confirm('Are you sure you want to delete this educational subject?')) {
        router.delete(`/admin/educational-subjects/${subject.id}`);
    }
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingSubject.value = null;
    resetForm();
};

const resetForm = () => {
    form.name = '';
    form.description = '';
    form.duration = '';
    form.price = ''; // Reset price
    form.status = 'active';
    selectedTeachers.value = [];
    isTeacherDropdownOpen.value = false;
    teacherSearchQuery.value = '';
};
</script>
