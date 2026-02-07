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
  expenses: Object,
  filters: Object,
  statistics: Object,
  categories: Array
});

// Use data from props
const expenses = computed(() => props.expenses?.data || []);
const categories = computed(() => props.categories || []);
const searchQuery = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category_id || 'all');
const selectedStatus = ref(props.filters?.status || 'all');

// Pagination computed properties
const pagination = computed(() => props.expenses || {});
const totalExpenses = computed(() => pagination.value.total || 0);
const currentPage = computed(() => pagination.value.current_page || 1);
const totalPages = computed(() => pagination.value.last_page || 1);
const startIndex = computed(() => pagination.value.from || 0);
const endIndex = computed(() => pagination.value.to || 0);

// Statistics
const statistics = computed(() => props.statistics || {});

// Modal state
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const showPayModal = ref(false);
const selectedExpense = ref(null);

// Form for creating new expense
const form = useForm({
  title: '',
  category_id: '',
  description: '',
  amount: '',
  expense_date: new Date().toISOString().split('T')[0],
  payment_method: '',
  receipt_number: '',
  vendor_name: '',
  status: 'pending'
});

// Form for editing expense
const editForm = useForm({
  title: '',
  category_id: '',
  description: '',
  amount: '',
  expense_date: '',
  payment_method: '',
  receipt_number: '',
  vendor_name: '',
  status: ''
});

// Form for marking as paid
const payForm = useForm({
  payment_method: '',
  receipt_number: ''
});

// Modal functions
const openCreateModal = () => {
  showCreateModal.value = true;
  form.reset();
  form.expense_date = new Date().toISOString().split('T')[0];
  form.status = 'pending';
  form.category_id = categories.value.length > 0 ? categories.value[0].id : '';
};

const closeCreateModal = () => {
  showCreateModal.value = false;
  form.reset();
};

const openEditModal = (expense) => {
  selectedExpense.value = expense;
  editForm.title = expense.title;
  editForm.category_id = expense.category_id;
  editForm.description = expense.description;
  editForm.amount = expense.amount;
  editForm.expense_date = expense.expense_date;
  editForm.payment_method = expense.payment_method;
  editForm.receipt_number = expense.receipt_number;
  editForm.vendor_name = expense.vendor_name;
  editForm.status = expense.status;
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  selectedExpense.value = null;
  editForm.reset();
};

const openViewModal = (expense) => {
  selectedExpense.value = expense;
  showViewModal.value = true;
};

const closeViewModal = () => {
  showViewModal.value = false;
  selectedExpense.value = null;
};

const openPayModal = (expense) => {
  selectedExpense.value = expense;
  payForm.payment_method = expense.payment_method || '';
  payForm.receipt_number = expense.receipt_number || '';
  showPayModal.value = true;
};

const closePayModal = () => {
  showPayModal.value = false;
  selectedExpense.value = null;
  payForm.reset();
};

const submitForm = () => {
  form.post(route('admin.expenses.store'), {
    preserveScroll: true,
    onSuccess: () => {
      closeCreateModal();
      form.reset();
    }
  });
};

const submitEditForm = () => {
  editForm.put(route('admin.expenses.update', selectedExpense.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      closeEditModal();
    }
  });
};

const submitPayForm = () => {
  payForm.patch(route('admin.expenses.mark-as-paid', selectedExpense.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      closePayModal();
    }
  });
};

const deleteExpense = (expense) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense?')) {
    router.delete(route('admin.expenses.destroy', expense.id), {
      preserveScroll: true
    });
  }
};

const markAsCancelled = (expense) => {
  if (confirm('Êtes-vous sûr de vouloir annuler cette dépense?')) {
    router.patch(route('admin.expenses.mark-as-cancelled', expense.id), {}, {
      preserveScroll: true
    });
  }
};

// Search and filter
const performSearch = () => {
  router.get(route('admin.expenses.index'), {
    search: searchQuery.value,
    category_id: selectedCategory.value,
    status: selectedStatus.value
  }, {
    preserveScroll: true,
    preserveState: true
  });
};

// Format currency
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(amount);
};

// Get category label
const getCategoryLabel = (expense) => {
  if (!expense.expense_category) return 'N/A';
  const locale = language.value.locale || 'en';
  return locale === 'fr' && expense.expense_category.name_fr 
    ? expense.expense_category.name_fr 
    : expense.expense_category.name;
};

// Get status color
const getStatusColor = (status) => {
  switch (status) {
    case 'paid':
      return 'bg-green-100 text-green-800';
    case 'pending':
      return 'bg-yellow-100 text-yellow-800';
    case 'cancelled':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

// Get status label
const getStatusLabel = (status) => {
  const labels = {
    paid: { fr: 'Payé', en: 'Paid' },
    pending: { fr: 'En attente', en: 'Pending' },
    cancelled: { fr: 'Annulé', en: 'Cancelled' }
  };
  return labels[status]?.[language.value.locale] || status;
};

// Pagination
const goToPage = (page) => {
  router.get(route('admin.expenses.index'), {
    page,
    search: searchQuery.value,
    category_id: selectedCategory.value,
    status: selectedStatus.value
  }, {
    preserveScroll: true,
    preserveState: true
  });
};
</script>

<template>
  <Head title="Les dépenses" />
  
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Les dépenses</h1>
          <p class="text-sm text-gray-600 mt-1">Gérer les dépenses de l'école</p>
        </div>
        <PrimaryButton @click="openCreateModal" class="whitespace-nowrap">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Ajouter une dépense
        </PrimaryButton>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-blue-600 font-medium">Total des dépenses</p>
              <p class="text-2xl font-bold text-blue-900 mt-1">{{ formatCurrency(statistics.total) }}</p>
            </div>
            <div class="p-3 bg-blue-200 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-green-600 font-medium">Payé</p>
              <p class="text-2xl font-bold text-green-900 mt-1">{{ formatCurrency(statistics.paid) }}</p>
            </div>
            <div class="p-3 bg-green-200 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-6 border border-yellow-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-yellow-600 font-medium">En attente</p>
              <p class="text-2xl font-bold text-yellow-900 mt-1">{{ formatCurrency(statistics.pending) }}</p>
            </div>
            <div class="p-3 bg-yellow-200 rounded-lg">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="md:col-span-2">
            <TextInput
              v-model="searchQuery"
              type="text"
              placeholder="Rechercher..."
              @keyup.enter="performSearch"
              class="w-full"
            />
          </div>
          <div>
            <select v-model="selectedCategory" @change="performSearch" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
              <option value="all">Toutes les catégories</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ language.locale === 'fr' && cat.name_fr ? cat.name_fr : cat.name }}
              </option>
            </select>
          </div>
          <div>
            <select v-model="selectedStatus" @change="performSearch" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
              <option value="all">Tous les statuts</option>
              <option value="pending">En attente</option>
              <option value="paid">Payé</option>
              <option value="cancelled">Annulé</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Expenses Table -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="expense in expenses" :key="expense.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ expense.title }}</div>
                  <div v-if="expense.vendor_name" class="text-xs text-gray-500">{{ expense.vendor_name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                    {{ getCategoryLabel(expense) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                  {{ formatCurrency(expense.amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ new Date(expense.expense_date).toLocaleDateString('fr-FR') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="['px-2 py-1 text-xs rounded-full', getStatusColor(expense.status)]">
                    {{ getStatusLabel(expense.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end gap-2">
                    <button @click="openViewModal(expense)" class="text-blue-600 hover:text-blue-900" title="Voir">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    <button v-if="expense.status === 'pending'" @click="openPayModal(expense)" class="text-green-600 hover:text-green-900" title="Marquer comme payé">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </button>
                    <button @click="openEditModal(expense)" class="text-indigo-600 hover:text-indigo-900" title="Modifier">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button v-if="expense.status === 'pending'" @click="markAsCancelled(expense)" class="text-yellow-600 hover:text-yellow-900" title="Annuler">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                    <button @click="deleteExpense(expense)" class="text-red-600 hover:text-red-900" title="Supprimer">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="expenses.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                  Aucune dépense trouvée
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="bg-gray-50 px-6 py-3 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Affichage de {{ startIndex }} à {{ endIndex }} sur {{ totalExpenses }} dépenses
            </div>
            <div class="flex gap-2">
              <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="px-3 py-1 rounded border disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100"
              >
                Précédent
              </button>
              <button
                v-for="page in totalPages"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'px-3 py-1 rounded border',
                  currentPage === page ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'
                ]"
              >
                {{ page }}
              </button>
              <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="px-3 py-1 rounded border disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100"
              >
                Suivant
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Expense Modal -->
    <Modal :show="showCreateModal" @close="closeCreateModal">
      <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Ajouter une dépense</h2>
        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <InputLabel for="title" value="Titre *" />
            <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full" required />
            <InputError :message="form.errors.title" class="mt-2" />
          </div>

          <div>
            <InputLabel for="category" value="Catégorie *" />
            <select id="category" v-model="form.category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
              <option value="">Sélectionner une catégorie</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ language.locale === 'fr' && cat.name_fr ? cat.name_fr : cat.name }}
              </option>
            </select>
            <InputError :message="form.errors.category_id" class="mt-2" />
          </div>

          <div>
            <InputLabel for="amount" value="Montant (MAD) *" />
            <TextInput id="amount" v-model="form.amount" type="number" step="0.01" class="mt-1 block w-full" required />
            <InputError :message="form.errors.amount" class="mt-2" />
          </div>

          <div>
            <InputLabel for="expense_date" value="Date *" />
            <TextInput id="expense_date" v-model="form.expense_date" type="date" class="mt-1 block w-full" required />
            <InputError :message="form.errors.expense_date" class="mt-2" />
          </div>

          <div>
            <InputLabel for="vendor_name" value="Fournisseur / Bénéficiaire" />
            <TextInput id="vendor_name" v-model="form.vendor_name" type="text" class="mt-1 block w-full" />
            <InputError :message="form.errors.vendor_name" class="mt-2" />
          </div>

          <div>
            <InputLabel for="description" value="Description" />
            <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            <InputError :message="form.errors.description" class="mt-2" />
          </div>

          <div>
            <InputLabel for="payment_method" value="Méthode de paiement" />
            <TextInput id="payment_method" v-model="form.payment_method" type="text" class="mt-1 block w-full" placeholder="Espèces, Virement, Chèque..." />
            <InputError :message="form.errors.payment_method" class="mt-2" />
          </div>

          <div>
            <InputLabel for="receipt_number" value="Numéro de reçu" />
            <TextInput id="receipt_number" v-model="form.receipt_number" type="text" class="mt-1 block w-full" />
            <InputError :message="form.errors.receipt_number" class="mt-2" />
          </div>

          <div>
            <InputLabel for="status" value="Statut *" />
            <select id="status" v-model="form.status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
              <option value="pending">En attente</option>
              <option value="paid">Payé</option>
            </select>
            <InputError :message="form.errors.status" class="mt-2" />
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <SecondaryButton @click="closeCreateModal" type="button">Annuler</SecondaryButton>
            <PrimaryButton :disabled="form.processing">Ajouter</PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Edit Expense Modal -->
    <Modal :show="showEditModal" @close="closeEditModal">
      <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Modifier la dépense</h2>
        <form @submit.prevent="submitEditForm" class="space-y-4">
          <div>
            <InputLabel for="edit_title" value="Titre *" />
            <TextInput id="edit_title" v-model="editForm.title" type="text" class="mt-1 block w-full" required />
            <InputError :message="editForm.errors.title" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_category" value="Catégorie *" />
            <select id="edit_category" v-model="editForm.category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
              <option value="">Sélectionner une catégorie</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ language.locale === 'fr' && cat.name_fr ? cat.name_fr : cat.name }}
              </option>
            </select>
            <InputError :message="editForm.errors.category_id" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_amount" value="Montant (MAD) *" />
            <TextInput id="edit_amount" v-model="editForm.amount" type="number" step="0.01" class="mt-1 block w-full" required />
            <InputError :message="editForm.errors.amount" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_expense_date" value="Date *" />
            <TextInput id="edit_expense_date" v-model="editForm.expense_date" type="date" class="mt-1 block w-full" required />
            <InputError :message="editForm.errors.expense_date" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_vendor_name" value="Fournisseur / Bénéficiaire" />
            <TextInput id="edit_vendor_name" v-model="editForm.vendor_name" type="text" class="mt-1 block w-full" />
            <InputError :message="editForm.errors.vendor_name" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_description" value="Description" />
            <textarea id="edit_description" v-model="editForm.description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            <InputError :message="editForm.errors.description" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_payment_method" value="Méthode de paiement" />
            <TextInput id="edit_payment_method" v-model="editForm.payment_method" type="text" class="mt-1 block w-full" />
            <InputError :message="editForm.errors.payment_method" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_receipt_number" value="Numéro de reçu" />
            <TextInput id="edit_receipt_number" v-model="editForm.receipt_number" type="text" class="mt-1 block w-full" />
            <InputError :message="editForm.errors.receipt_number" class="mt-2" />
          </div>

          <div>
            <InputLabel for="edit_status" value="Statut *" />
            <select id="edit_status" v-model="editForm.status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
              <option value="pending">En attente</option>
              <option value="paid">Payé</option>
              <option value="cancelled">Annulé</option>
            </select>
            <InputError :message="editForm.errors.status" class="mt-2" />
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <SecondaryButton @click="closeEditModal" type="button">Annuler</SecondaryButton>
            <PrimaryButton :disabled="editForm.processing">Enregistrer</PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- View Expense Modal -->
    <Modal :show="showViewModal" @close="closeViewModal">
      <div class="p-6" v-if="selectedExpense">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Détails de la dépense</h2>
        <div class="space-y-3">
          <div class="grid grid-cols-2 gap-2">
            <div class="text-sm text-gray-500">Titre:</div>
            <div class="text-sm font-medium">{{ selectedExpense.title }}</div>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div class="text-sm text-gray-500">Catégorie:</div>
            <div class="text-sm font-medium">{{ getCategoryLabel(selectedExpense) }}</div>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div class="text-sm text-gray-500">Montant:</div>
            <div class="text-sm font-semibold">{{ formatCurrency(selectedExpense.amount) }}</div>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div class="text-sm text-gray-500">Date:</div>
            <div class="text-sm">{{ new Date(selectedExpense.expense_date).toLocaleDateString('fr-FR') }}</div>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div class="text-sm text-gray-500">Statut:</div>
            <div><span :class="['px-2 py-1 text-xs rounded-full', getStatusColor(selectedExpense.status)]">{{ getStatusLabel(selectedExpense.status) }}</span></div>
          </div>
          <div v-if="selectedExpense.vendor_name" class="grid grid-cols-2 gap-2">
            <div class="text-sm text-gray-500">Fournisseur:</div>
            <div class="text-sm">{{ selectedExpense.vendor_name }}</div>
          </div>
          <div v-if="selectedExpense.payment_method" class="grid grid-cols-2 gap-2">
            <div class="text-sm text-gray-500">Méthode de paiement:</div>
            <div class="text-sm">{{ selectedExpense.payment_method }}</div>
          </div>
          <div v-if="selectedExpense.receipt_number" class="grid grid-cols-2 gap-2">
            <div class="text-sm text-gray-500">Numéro de reçu:</div>
            <div class="text-sm">{{ selectedExpense.receipt_number }}</div>
          </div>
          <div v-if="selectedExpense.description" class="pt-2 border-t">
            <div class="text-sm text-gray-500 mb-1">Description:</div>
            <div class="text-sm">{{ selectedExpense.description }}</div>
          </div>
        </div>
        <div class="flex justify-end mt-6">
          <SecondaryButton @click="closeViewModal">Fermer</SecondaryButton>
        </div>
      </div>
    </Modal>

    <!-- Pay Expense Modal -->
    <Modal :show="showPayModal" @close="closePayModal">
      <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Marquer comme payé</h2>
        <form @submit.prevent="submitPayForm" class="space-y-4">
          <div>
            <InputLabel for="pay_payment_method" value="Méthode de paiement" />
            <TextInput id="pay_payment_method" v-model="payForm.payment_method" type="text" class="mt-1 block w-full" placeholder="Espèces, Virement, Chèque..." />
            <InputError :message="payForm.errors.payment_method" class="mt-2" />
          </div>

          <div>
            <InputLabel for="pay_receipt_number" value="Numéro de reçu" />
            <TextInput id="pay_receipt_number" v-model="payForm.receipt_number" type="text" class="mt-1 block w-full" />
            <InputError :message="payForm.errors.receipt_number" class="mt-2" />
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <SecondaryButton @click="closePayModal" type="button">Annuler</SecondaryButton>
            <PrimaryButton :disabled="payForm.processing">Marquer comme payé</PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </AdminLayout>
</template>
