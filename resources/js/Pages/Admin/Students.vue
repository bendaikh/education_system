<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import StudentModal from '@/Components/StudentModal.vue';
import ImportModal from '@/Components/ImportModal.vue';

const page = usePage();
const language = computed(() => page.props.language || {});

// Accept props from backend
const props = defineProps({
  students: Array,
  total: Number,
  formations: {
    type: Array,
    default: () => []
  }
});

// Use data from props or fallback to mock data
const students = ref(props.students || []);

const searchQuery = ref('');
const currentPage = ref(1);
const studentsPerPage = ref(10); // Show more students per page

// Calculate pagination based on actual students
const totalStudents = computed(() => students.value.length);
const filteredStudents = computed(() => {
  if (!searchQuery.value) return students.value;
  
  return students.value.filter(student => 
    student.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    student.student_id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (student.email && student.email.toLowerCase().includes(searchQuery.value.toLowerCase()))
  );
});

const paginatedStudents = computed(() => {
  const start = (currentPage.value - 1) * studentsPerPage.value;
  const end = start + studentsPerPage.value;
  return filteredStudents.value.slice(start, end);
});

// Modal state
const showStudentModal = ref(false);
const showImportModal = ref(false);
const editingStudent = ref(null);

// Functions to handle modal
const openStudentModal = () => {
  editingStudent.value = null;
  showStudentModal.value = true;
};

const closeStudentModal = () => {
  showStudentModal.value = false;
  editingStudent.value = null;
};

const openImportModal = () => {
  showImportModal.value = true;
};

const closeImportModal = () => {
  showImportModal.value = false;
};

// Edit student
const editStudent = async (student) => {
  try {
    const response = await fetch(`/admin/students/${student.id}/edit`);
    const data = await response.json();
    editingStudent.value = data.student;
    showStudentModal.value = true;
  } catch (error) {
    console.error('Error fetching student data:', error);
  }
};

// Delete student
const deleteStudent = (student) => {
  if (confirm(`Are you sure you want to delete ${student.name}? This action cannot be undone.`)) {
    router.delete(`/admin/students/${student.id}`, {
      onSuccess: () => {
        // Refresh the page to show updated data
        router.visit('/admin/students', {
          preserveState: false,
          preserveScroll: false,
          replace: true
        });
      },
      onError: (errors) => {
        console.error('Error deleting student:', errors);
      }
    });
  }
};

// Handle student creation success
const handleStudentCreated = () => {
  showStudentModal.value = false;
  // Reset pagination to first page
  currentPage.value = 1;
  // Use Inertia router to reload the page with fresh data
  router.visit('/admin/students', {
    preserveState: false,
    preserveScroll: false,
    replace: true
  });
};

// Handle import success
const handleImportSuccess = () => {
  showImportModal.value = false;
  // Reset pagination to first page
  currentPage.value = 1;
  // Use Inertia router to reload the page with fresh data
  router.visit('/admin/students', {
    preserveState: false,
    preserveScroll: false,
    replace: true
  });
};

// Handle student update success
const handleStudentUpdated = () => {
  showStudentModal.value = false;
  editingStudent.value = null;
  // Reset pagination to first page
  currentPage.value = 1;
  // Use Inertia router to reload the page with fresh data
  router.visit('/admin/students', {
    preserveState: false,
    preserveScroll: false,
    replace: true
  });
};

// Pagination calculations
const totalPages = computed(() => Math.ceil(filteredStudents.value.length / studentsPerPage.value));
const startIndex = computed(() => (currentPage.value - 1) * studentsPerPage.value + 1);
const endIndex = computed(() => Math.min(currentPage.value * studentsPerPage.value, filteredStudents.value.length));

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
</script>

<template>
  <AdminLayout :title="language.students || 'Students'">
    <Head :title="language.students || 'Students'" />

    <!-- Add New Student Button -->
    <div class="flex justify-end gap-3 mb-6">
      <!-- Import Button -->
      <button 
        @click="openImportModal"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-xl font-medium hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl min-h-[44px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
        </svg>
        {{ language.import || 'Import' }} {{ language.students || 'Students' }}
      </button>
      
      <!-- Add New Student Button -->
      <button 
        @click="openStudentModal"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl min-h-[44px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        {{ language.add_new || 'Add New' }} {{ language.student || 'Student' }}
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
              :placeholder="(language.search || 'Search') + ' ' + (language.students || 'students') + '...'"
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

    <!-- Students Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Desktop Table View -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.student_name || 'Student Name' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.student_id || 'Student ID' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.grade || 'Grade' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.contact_parent || 'Contact Parent' }}</th>
              <th class="text-left py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">{{ language.actions || 'Actions' }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="student in paginatedStudents" :key="student.id" class="hover:bg-gray-50 transition-colors duration-150">
              <td class="py-4 px-6">
                <div class="text-sm font-medium text-gray-900">{{ student.name }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm text-gray-600 font-mono">{{ student.student_id }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm text-gray-600">{{ student.grade }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm text-gray-600 font-mono">{{ student.contact_parent }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <!-- Edit Button -->
                  <button 
                    @click="editStudent(student)"
                    class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                    :title="language.edit || 'Edit'"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <!-- Delete Button -->
                  <button 
                    @click="deleteStudent(student)"
                    class="text-red-600 hover:text-red-800 transition-colors duration-200"
                    :title="language.delete || 'Delete'"
                  >
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
      <div class="md:hidden divide-y divide-gray-100">
        <div v-for="student in paginatedStudents" :key="student.id" class="p-4 sm:p-6">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0">
              <h3 class="text-base font-medium text-gray-900 truncate">{{ student.name }}</h3>
              <p class="text-sm text-gray-600 mt-1 font-mono">{{ student.student_id }}</p>
            </div>
            <div class="flex items-center gap-2 ml-3">
              <!-- Edit Button -->
              <button 
                @click="editStudent(student)"
                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 min-h-[44px] min-w-[44px] flex items-center justify-center"
                :title="language.edit || 'Edit'"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <!-- Delete Button -->
              <button 
                @click="deleteStudent(student)"
                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 min-h-[44px] min-w-[44px] flex items-center justify-center"
                :title="language.delete || 'Delete'"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
          <div class="grid grid-cols-1 gap-2 text-sm">
            <div>
              <span class="text-gray-500">{{ (language.grade || 'Grade') + ':' }}</span>
              <span class="ml-1 text-gray-900">{{ student.grade }}</span>
            </div>
            <div>
              <span class="text-gray-500">{{ (language.contact_parent || 'Parent Contact') + ':' }}</span>
              <span class="ml-1 text-gray-900 font-mono">{{ student.contact_parent }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ startIndex }}</span> to <span class="font-medium">{{ endIndex }}</span> of <span class="font-medium">{{ filteredStudents.length }}</span> {{ language.students || 'Students' }}
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

    <!-- Student Modal -->
    <StudentModal 
      :show="showStudentModal"
      :formations="formations"
      :editing-student="editingStudent"
      @close="closeStudentModal"
      @student-created="handleStudentCreated"
      @student-updated="handleStudentUpdated"
    />

    <!-- Import Modal -->
    <ImportModal 
      :show="showImportModal"
      @close="closeImportModal"
      @imported="handleImportSuccess"
    />
  </AdminLayout>
</template>

<style scoped></style>
