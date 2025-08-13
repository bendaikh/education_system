<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const page = usePage();
const language = computed(() => page.props.language || {});

// Accept props from backend
const props = defineProps({
  formations: Array,
  total: Number,
  availableTeachers: Array
});

// Reactive formations data
const searchQuery = ref('');
const currentPage = ref(1);
const formationsPerPage = ref(4);

// Make formations computed from props so they update when server data changes
const formations = computed(() => props.formations || []);

// Modal state
const showModal = ref(false);

// Form data
const formData = ref({
  title: '',
  description: '',
  teachers: [],
  duration: '',
  level: '',
  price: '',
  status: 'Active'
});

// Multi-select state
const isTeacherDropdownOpen = ref(false);
const teacherSearchQuery = ref('');

// Filtered teachers based on search
const filteredTeachers = computed(() => {
  if (!teacherSearchQuery.value) {
    return props.availableTeachers || [];
  }
  return (props.availableTeachers || []).filter(teacher => 
    teacher.name.toLowerCase().includes(teacherSearchQuery.value.toLowerCase())
  );
});

// Toggle teacher selection
const toggleTeacher = (teacher) => {
  const index = formData.value.teachers.findIndex(t => t.id === teacher.id);
  if (index > -1) {
    formData.value.teachers.splice(index, 1);
  } else {
    formData.value.teachers.push(teacher);
  }
};

// Check if teacher is selected
const isTeacherSelected = (teacher) => {
  return formData.value.teachers.some(t => t.id === teacher.id);
};

// Remove teacher from selection
const removeTeacher = (teacher) => {
  const index = formData.value.teachers.findIndex(t => t.id === teacher.id);
  if (index > -1) {
    formData.value.teachers.splice(index, 1);
  }
};

// Form functions
const openModal = () => {
  showModal.value = true;
  resetForm();
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  formData.value = {
    title: '',
    description: '',
    teachers: [],
    duration: '',
    level: '',
    price: '',
    status: 'Active'
  };
  isTeacherDropdownOpen.value = false;
  teacherSearchQuery.value = '';
};

const submitForm = () => {
  // Validate required fields
  if (!formData.value.title || !formData.value.description || !formData.value.price || formData.value.teachers.length === 0) {
    alert('Please fill in all required fields');
    return;
  }

  // Submit form using Inertia
  router.post('/admin/formations', formData.value, {
    onSuccess: () => {
      closeModal();
      // The page will automatically refresh with the new formation
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors);
      alert('Error creating formation. Please check your input.');
    }
  });
};

// Pagination computed properties
const totalPages = computed(() => Math.ceil(totalFormations.value / formationsPerPage.value));
const startIndex = computed(() => (currentPage.value - 1) * formationsPerPage.value + 1);
const endIndex = computed(() => Math.min(currentPage.value * formationsPerPage.value, totalFormations.value));

// Filtered formations based on search
const filteredFormations = computed(() => {
  if (!searchQuery.value) {
    return formations.value;
  }
  
  const query = searchQuery.value.toLowerCase();
  return formations.value.filter(formation => 
    formation.title.toLowerCase().includes(query) ||
    formation.description.toLowerCase().includes(query) ||
    (Array.isArray(formation.teachers) ? 
      formation.teachers.some(teacher => teacher.toLowerCase().includes(query)) :
      formation.teachers.toLowerCase().includes(query)) ||
    (formation.level && formation.level.toLowerCase().includes(query)) ||
    formation.status.toLowerCase().includes(query)
  );
});

// Update total count to use filtered results
const totalFormations = computed(() => filteredFormations.value.length);

// Paginated formations for display
const paginatedFormations = computed(() => {
  const start = (currentPage.value - 1) * formationsPerPage.value;
  const end = start + formationsPerPage.value;
  return filteredFormations.value.slice(start, end);
});

// Reset to first page when search changes
watch(searchQuery, () => {
  currentPage.value = 1;
});

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

// Status badge styling
const getStatusClass = (status) => {
  switch (status) {
    case 'Active':
      return 'bg-green-100 text-green-800';
    case 'Coming Soon':
      return 'bg-blue-100 text-blue-800';
    case 'Completed':
      return 'bg-gray-100 text-gray-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

// Level badge styling
const getLevelClass = (level) => {
  switch (level) {
    case 'Beginner':
      return 'bg-green-100 text-green-800';
    case 'Intermediate':
      return 'bg-yellow-100 text-yellow-800';
    case 'Advanced':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};
</script>

<template>
  <AdminLayout :title="language.formations || 'Formations'">
    <Head :title="language.formations || 'Formations'" />

    <!-- Add New Formation Button -->
    <div class="flex justify-end mb-6">
      <button @click="openModal" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl min-h-[44px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        {{ language.add_new || 'Add New' }} {{ language.formations || 'Formation' }}
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
              :placeholder="(language.search || 'Search') + ' ' + (language.formations || 'formations') + '...'"
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

    <!-- Formations Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Desktop Table View -->
      <div class="hidden lg:block overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.formation_title || 'Formation Title' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.formation_teachers || 'Teachers' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.duration || 'Duration' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.level || 'Level' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.price || 'Price' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.status || 'Status' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.actions || 'Actions' }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="formation in paginatedFormations" :key="formation.id" class="hover:bg-gray-50 transition-colors duration-150">
              <td class="py-4 px-6">
                <div class="text-sm font-medium text-gray-900">{{ formation.title }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ formation.description }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm text-gray-600">
                  <span v-if="Array.isArray(formation.teachers)">
                    {{ formation.teachers.join(', ') }}
                  </span>
                  <span v-else>{{ formation.teachers }}</span>
                </div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm text-gray-600">{{ formation.duration }}</div>
              </td>
              <td class="py-4 px-6">
                <span :class="getLevelClass(formation.level)" class="px-2 py-1 text-xs font-medium rounded-full">
                  {{ formation.level === 'Beginner' ? (language.beginner || 'Beginner') : 
                     formation.level === 'Intermediate' ? (language.intermediate || 'Intermediate') : 
                     formation.level === 'Advanced' ? (language.advanced || 'Advanced') : formation.level }}
                </span>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm font-semibold text-gray-900">{{ formation.price }}</div>
              </td>
              <td class="py-4 px-6">
                <span :class="getStatusClass(formation.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                  {{ formation.status }}
                </span>
              </td>
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <!-- View Button -->
                  <button class="text-green-600 hover:text-green-800 transition-colors duration-200" title="View Formation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                  <!-- Edit Button -->
                  <button class="text-blue-600 hover:text-blue-800 transition-colors duration-200" title="Edit Formation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <!-- Delete Button -->
                  <button class="text-red-600 hover:text-red-800 transition-colors duration-200" title="Delete Formation">
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
        <div v-for="formation in paginatedFormations" :key="formation.id" class="p-4 sm:p-6">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0">
              <h3 class="text-base font-medium text-gray-900 truncate">{{ formation.title }}</h3>
              <p class="text-sm text-gray-600 mt-1">
                <span v-if="Array.isArray(formation.teachers)">
                  {{ formation.teachers.join(', ') }}
                </span>
                <span v-else>{{ formation.teachers }}</span>
              </p>
            </div>
            <span :class="getStatusClass(formation.status)" class="px-2 py-1 text-xs font-medium rounded-full ml-3">
              {{ formation.status }}
            </span>
          </div>
          <div class="grid grid-cols-2 gap-2 text-sm mb-4">
            <div>
              <span class="text-gray-500">{{ (language.duration || 'Duration') + ':' }}</span>
              <span class="ml-1 text-gray-900">{{ formation.duration }}</span>
            </div>
            <div>
              <span class="text-gray-500">{{ (language.level || 'Level') + ':' }}</span>
              <span :class="getLevelClass(formation.level)" class="ml-1 px-2 py-0.5 text-xs font-medium rounded-full">
                {{ formation.level === 'Beginner' ? (language.beginner || 'Beginner') : 
                   formation.level === 'Intermediate' ? (language.intermediate || 'Intermediate') : 
                   formation.level === 'Advanced' ? (language.advanced || 'Advanced') : formation.level }}
              </span>
            </div>
            <div>
              <span class="text-gray-500">{{ (language.price || 'Price') + ':' }}</span>
              <span class="ml-1 text-gray-900 font-semibold">{{ formation.price }}</span>
            </div>
            <div>
              <span class="text-gray-500">{{ (language.enrolled_students || 'Students') + ':' }}</span>
              <span class="ml-1 text-gray-900">{{ formation.enrolled_students }}</span>
            </div>
          </div>
          <div class="mb-3">
            <span class="text-gray-500">{{ (language.description || 'Description') + ':' }}</span>
            <p class="text-sm text-gray-600 mt-1">{{ formation.description }}</p>
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
            Showing <span class="font-medium">{{ startIndex }}</span> to <span class="font-medium">{{ endIndex }}</span> of <span class="font-medium">{{ totalFormations }}</span> {{ language.formations || 'Formations' }}
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
              
              <!-- Ellipsis if there are more pages -->
              <span v-if="totalPages > 5" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm text-gray-700 min-h-[44px]">
                ...
              </span>
              
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

    <!-- Add Formation Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
                    {{ language.add_new || 'Add New' }} {{ language.formations || 'Formation' }}
                  </h3>
                  <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitForm" class="space-y-4">
                  <!-- Title (Required) -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      {{ language.formation_title || 'Formation Title' }} <span class="text-red-500">*</span>
                    </label>
                    <input v-model="formData.title" type="text" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           :placeholder="language.formation_title || 'Formation Title'" />
                  </div>

                  <!-- Description (Required) -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      {{ language.description || 'Description' }} <span class="text-red-500">*</span>
                    </label>
                    <textarea v-model="formData.description" required rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              :placeholder="language.description || 'Description'"></textarea>
                  </div>

                  <!-- Teachers (Required) -->
                  <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      {{ language.formation_teachers || 'Teachers' }} <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Selected Teachers Display -->
                    <div class="w-full min-h-[42px] px-3 py-2 border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent bg-white">
                      <div class="flex flex-wrap gap-1 mb-1" v-if="formData.teachers.length > 0">
                        <span v-for="teacher in formData.teachers" :key="teacher.id" 
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
                        :placeholder="formData.teachers.length === 0 ? (language.select_teachers || 'Select Teachers') : 'Add more teachers...'"
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

                  <!-- Price (Required) -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      {{ language.price || 'Price' }} <span class="text-red-500">*</span>
                    </label>
                    <input v-model="formData.price" type="text" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="$299" />
                  </div>

                  <!-- Duration (Optional) -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      {{ language.duration || 'Duration' }} <span class="text-gray-400 text-xs">({{ language.optional || 'Optional' }})</span>
                    </label>
                    <input v-model="formData.duration" type="text" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="12 weeks" />
                  </div>

                  <!-- Level (Optional) -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      {{ language.level || 'Level' }} <span class="text-gray-400 text-xs">({{ language.optional || 'Optional' }})</span>
                    </label>
                    <select v-model="formData.level" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <option value="">{{ language.select_level || 'Select Level' }}</option>
                      <option value="Beginner">{{ language.beginner || 'Beginner' }}</option>
                      <option value="Intermediate">{{ language.intermediate || 'Intermediate' }}</option>
                      <option value="Advanced">{{ language.advanced || 'Advanced' }}</option>
                    </select>
                  </div>

                  <!-- Status -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      {{ language.status || 'Status' }} <span class="text-red-500">*</span>
                    </label>
                    <select v-model="formData.status" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <option value="Active">Active</option>
                      <option value="Coming Soon">Coming Soon</option>
                      <option value="Completed">Completed</option>
                    </select>
                  </div>


                </form>
              </div>
            </div>
          </div>
          
          <!-- Modal Footer -->
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="submitForm" type="button" 
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
              {{ language.save || 'Save' }}
            </button>
            <button @click="closeModal" type="button" 
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              {{ language.cancel || 'Cancel' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped></style>
