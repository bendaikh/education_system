<template>
  <Modal :show="show" @close="closeModal">
    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-900">
          {{ language.import_students || 'Import Students' }}
        </h2>
        <button
          @click="closeModal"
          class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Instructions -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
          <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div class="text-sm text-blue-800">
            <p class="font-medium mb-1">{{ language.import_instructions || 'Import Instructions' }}</p>
            <ul class="list-disc list-inside space-y-1">
              <li>{{ language.supported_formats || 'Supported formats' }}: Excel (.xlsx, .xls) {{ language.and || 'and' }} CSV</li>
              <li>{{ language.max_file_size || 'Maximum file size' }}: 10MB</li>
              <li>{{ language.first_row_headers || 'First row should contain column headers' }}</li>
              <li>{{ language.required_fields || 'Required fields' }}: {{ language.name || 'Name' }}</li>
              <li>{{ language.optional_fields || 'Optional fields' }}: {{ language.email || 'Email' }}, {{ language.student_id || 'Student ID' }}, {{ language.phone || 'Phone' }}, {{ language.parent_phone || 'Parent Phone' }}, {{ language.address || 'Address' }}, {{ language.date_of_birth || 'Date of Birth' }}, {{ language.notes || 'Notes' }}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Download Template -->
      <div class="mb-6">
        <a
          :href="route('admin.students.template')"
          class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-colors duration-200"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          {{ language.download_template || 'Download Template' }}
        </a>
      </div>

      <!-- File Upload -->
      <form @submit.prevent="handleImport" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ language.select_file || 'Select File' }}
          </label>
          <div
            @drop.prevent="handleDrop"
            @dragover.prevent
            @dragenter.prevent
            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200"
            :class="{ 'border-blue-500 bg-blue-50': isDragOver }"
          >
            <input
              ref="fileInput"
              type="file"
              @change="handleFileSelect"
              accept=".xlsx,.xls,.csv"
              class="hidden"
            />
            
            <div v-if="!selectedFile" class="space-y-4">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div class="text-gray-600">
                <p class="font-medium">{{ language.drag_drop || 'Drag and drop your file here' }}</p>
                <p class="text-sm">{{ language.or || 'or' }}</p>
                <button
                  type="button"
                  @click="$refs.fileInput.click()"
                  class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                  {{ language.browse_files || 'Browse Files' }}
                </button>
              </div>
            </div>
            
            <div v-else class="space-y-4">
              <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <div class="text-gray-600">
                <p class="font-medium text-green-600">{{ selectedFile.name }}</p>
                <p class="text-sm">{{ formatFileSize(selectedFile.size) }}</p>
                <button
                  type="button"
                  @click="removeFile"
                  class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                >
                  {{ language.remove || 'Remove' }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Import Options -->
        <div class="bg-gray-50 rounded-lg p-4">
          <h3 class="text-sm font-medium text-gray-700 mb-3">{{ language.import_options || 'Import Options' }}</h3>
          <div class="space-y-3">
            <label class="flex items-center">
              <input
                v-model="importOptions.skipErrors"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
              />
              <span class="ml-2 text-sm text-gray-700">
                {{ language.skip_errors || 'Skip rows with errors and continue importing' }}
              </span>
            </label>
            <label class="flex items-center">
              <input
                v-model="importOptions.generateMissingFields"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
              />
              <span class="ml-2 text-sm text-gray-700">
                {{ language.generate_missing_fields || 'Generate missing Student IDs and emails automatically' }}
              </span>
            </label>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
          >
            {{ language.cancel || 'Cancel' }}
          </button>
          <button
            type="submit"
            :disabled="!selectedFile || isImporting"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
          >
            <svg v-if="isImporting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isImporting ? (language.importing || 'Importing...') : (language.import || 'Import') }}
          </button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const language = computed(() => page.props.language || {});

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close', 'imported']);

const fileInput = ref(null);
const selectedFile = ref(null);
const isDragOver = ref(false);
const isImporting = ref(false);

const importOptions = ref({
  skipErrors: true,
  generateMissingFields: true
});

const closeModal = () => {
  selectedFile.value = null;
  isDragOver.value = false;
  emit('close');
};

const handleDrop = (event) => {
  isDragOver.value = false;
  const files = event.dataTransfer.files;
  if (files.length > 0) {
    selectedFile.value = files[0];
  }
};

const handleFileSelect = (event) => {
  const files = event.target.files;
  if (files.length > 0) {
    selectedFile.value = files[0];
  }
};

const removeFile = () => {
  selectedFile.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const handleImport = async () => {
  if (!selectedFile.value) return;

  isImporting.value = true;

  try {
    const formData = new FormData();
    formData.append('file', selectedFile.value);
    formData.append('skip_errors', importOptions.value.skipErrors);
    formData.append('generate_missing_fields', importOptions.value.generateMissingFields);

    await router.post(route('admin.students.import'), formData, {
      onSuccess: () => {
        closeModal();
        emit('imported');
      },
      onError: (errors) => {
        console.error('Import failed:', errors);
      },
      onFinish: () => {
        isImporting.value = false;
      }
    });
  } catch (error) {
    console.error('Import error:', error);
    isImporting.value = false;
  }
};
</script>
