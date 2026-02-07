<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const page = usePage();
const language = computed(() => page.props.language || {});

// Accept props from backend
const props = defineProps({
  categories: Array
});

// Use data from props
const categories = computed(() => props.categories || []);

// Modal state
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedCategory = ref(null);

// Predefined colors for categories
const colors = [
  '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
  '#EC4899', '#14B8A6', '#F97316', '#6366F1', '#84CC16'
];

// Form for creating new category
const form = useForm({
  name: '',
  name_fr: '',
  description: '',
  color: '#3B82F6',
  is_active: true
});

// Form for editing category
const editForm = useForm({
  name: '',
  name_fr: '',
  description: '',
  color: '',
  is_active: true
});

// Modal functions
const openCreateModal = () => {
  showCreateModal.value = true;
  form.reset();
  form.color = '#3B82F6';
  form.is_active = true;
};

const closeCreateModal = () => {
  showCreateModal.value = false;
  form.reset();
};

const openEditModal = (category) => {
  selectedCategory.value = category;
  editForm.name = category.name;
  editForm.name_fr = category.name_fr;
  editForm.description = category.description;
  editForm.color = category.color;
  editForm.is_active = category.is_active;
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  selectedCategory.value = null;
  editForm.reset();
};

const submitForm = () => {
  form.post(route('admin.expense-categories.store'), {
    preserveScroll: true,
    onSuccess: () => {
      closeCreateModal();
      form.reset();
    }
  });
};

const submitEditForm = () => {
  editForm.put(route('admin.expense-categories.update', selectedCategory.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      closeEditModal();
    }
  });
};

const deleteCategory = (category) => {
  if (category.expenses_count > 0) {
    alert('Impossible de supprimer une catégorie qui contient des dépenses.');
    return;
  }
  
  if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?')) {
    router.delete(route('admin.expense-categories.destroy', category.id), {
      preserveScroll: true
    });
  }
};

const toggleActive = (category) => {
  router.patch(route('admin.expense-categories.toggle-active', category.id), {}, {
    preserveScroll: true
  });
};
</script>

<template>
  <Head title="Catégories de dépenses" />
  
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Catégories de dépenses</h1>
          <p class="text-sm text-gray-600 mt-1">Gérer les catégories de dépenses</p>
        </div>
        <PrimaryButton @click="openCreateModal" class="whitespace-nowrap">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Ajouter une catégorie
        </PrimaryButton>
      </div>

      <!-- Categories Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div 
          v-for="category in categories" 
          :key="category.id"
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
        >
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div 
                class="w-12 h-12 rounded-lg flex items-center justify-center"
                :style="{ backgroundColor: category.color + '20' }"
              >
                <div 
                  class="w-6 h-6 rounded-full"
                  :style="{ backgroundColor: category.color }"
                ></div>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ category.name }}</h3>
                <p v-if="category.name_fr" class="text-sm text-gray-500">{{ category.name_fr }}</p>
              </div>
            </div>
            <button 
              @click="toggleActive(category)"
              :class="[
                'px-2 py-1 text-xs rounded-full',
                category.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
              ]"
            >
              {{ category.is_active ? 'Actif' : 'Inactif' }}
            </button>
          </div>

          <p v-if="category.description" class="text-sm text-gray-600 mb-4">
            {{ category.description }}
          </p>

          <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="text-sm text-gray-500">
              <span class="font-medium">{{ category.expenses_count }}</span> dépense(s)
            </div>
            <div class="flex gap-2">
              <button 
                @click="openEditModal(category)"
                class="text-blue-600 hover:text-blue-900"
                title="Modifier"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button 
                @click="deleteCategory(category)"
                class="text-red-600 hover:text-red-900"
                :class="{ 'opacity-50 cursor-not-allowed': category.expenses_count > 0 }"
                title="Supprimer"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div 
          v-if="categories.length === 0"
          class="col-span-full flex flex-col items-center justify-center py-12 text-gray-500"
        >
          <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
          </svg>
          <p class="text-lg font-medium">Aucune catégorie trouvée</p>
          <p class="text-sm">Commencez par ajouter votre première catégorie de dépense</p>
        </div>
      </div>
    </div>

    <!-- Create Category Modal -->
    <Modal :show="showCreateModal" @close="closeCreateModal">
      <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Ajouter une catégorie</h2>
        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <InputLabel for="name" value="Nom (EN) *" />
            <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
            <InputError :message="form.errors.name" class="mt-2" />
          </div>

          <div>
            <InputLabel for="name_fr" value="Nom (FR)" />
            <TextInput id="name_fr" v-model="form.name_fr" type="text" class="mt-1 block w-full" />
            <InputError :message="form.errors.name_fr" class="mt-2" />
          </div>

          <div>
            <InputLabel for="description" value="Description" />
            <textarea 
              id="description" 
              v-model="form.description" 
              rows="3" 
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
            ></textarea>
            <InputError :message="form.errors.description" class="mt-2" />
          </div>

          <div>
            <InputLabel for="color" value="Couleur" />
            <div class="mt-2 flex gap-2 flex-wrap">
              <button
                v-for="color in colors"
                :key="color"
                type="button"
                @click="form.color = color"
                class="w-10 h-10 rounded-lg border-2 transition-all"
                :class="form.color === color ? 'border-gray-900 scale-110' : 'border-gray-200'"
                :style="{ backgroundColor: color }"
              ></button>
            </div>
            <InputError :message="form.errors.color" class="mt-2" />
          </div>

          <div class="flex items-center">
            <input 
              id="is_active" 
              v-model="form.is_active" 
              type="checkbox" 
              class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
            <label for="is_active" class="ml-2 text-sm text-gray-700">Actif</label>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <SecondaryButton @click="closeCreateModal" type="button">Annuler</SecondaryButton>
            <PrimaryButton :disabled="form.processing">Ajouter</PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Edit Category Modal -->
    <Modal :show="showEditModal" @close="closeEditModal">
      <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Modifier la catégorie</h2>
        <form @submit.prevent="submitEditForm" class="space-y-4">
          <div>
            <InputLabel for="edit_name" value="Nom (EN) *" />
            <TextInput id="edit_name" v-model="editForm.name" type="text" class="mt-1 block w-full" required />
            <InputError :message="editForm.errors.name" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_name_fr" value="Nom (FR)" />
            <TextInput id="edit_name_fr" v-model="editForm.name_fr" type="text" class="mt-1 block w-full" />
            <InputError :message="editForm.errors.name_fr" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_description" value="Description" />
            <textarea 
              id="edit_description" 
              v-model="editForm.description" 
              rows="3" 
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
            ></textarea>
            <InputError :message="editForm.errors.description" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_color" value="Couleur" />
            <div class="mt-2 flex gap-2 flex-wrap">
              <button
                v-for="color in colors"
                :key="color"
                type="button"
                @click="editForm.color = color"
                class="w-10 h-10 rounded-lg border-2 transition-all"
                :class="editForm.color === color ? 'border-gray-900 scale-110' : 'border-gray-200'"
                :style="{ backgroundColor: color }"
              ></button>
            </div>
            <InputError :message="editForm.errors.color" class="mt-2" />
          </div>

          <div class="flex items-center">
            <input 
              id="edit_is_active" 
              v-model="editForm.is_active" 
              type="checkbox" 
              class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
            <label for="edit_is_active" class="ml-2 text-sm text-gray-700">Actif</label>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <SecondaryButton @click="closeEditModal" type="button">Annuler</SecondaryButton>
            <PrimaryButton :disabled="editForm.processing">Enregistrer</PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </AdminLayout>
</template>
