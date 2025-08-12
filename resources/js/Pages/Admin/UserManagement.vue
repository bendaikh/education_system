<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

// TODO: replace with backend-provided props later
const users = [
  { id: 1, name: 'Clara Bennett', role: 'Student', email: 'clara.bennett@example.com', status: 'Active' },
  { id: 2, name: 'Owen Carter', role: 'Teacher', email: 'owen.carter@example.com', status: 'Active' },
  { id: 3, name: 'Emma Foster', role: 'Admin', email: 'emma.foster@example.com', status: 'Active' },
  { id: 4, name: 'Lucas Hayes', role: 'Student', email: 'lucas.hayes@example.com', status: 'Inactive' },
];
</script>

<template>
  <AdminLayout title="User Management">
    <Head title="User Management" />

    <!-- Add New User Button -->
    <div class="flex justify-end mb-6">
      <button class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl min-h-[44px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Add New User
      </button>
    </div>

    <!-- Search -->
    <div class="px-3 sm:px-4 py-3">
      <label class="block w-full h-11 sm:h-12">
        <div class="flex h-full items-stretch rounded bg-gray-100">
          <div class="flex items-center justify-center pl-3 sm:pl-4 text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" sm:width="24" sm:height="24" fill="currentColor" viewBox="0 0 256 256">
              <path
                d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z" />
            </svg>
          </div>
          <input
            placeholder="Search users..."
            class="flex-1 rounded-r border-none bg-gray-100 px-3 sm:px-4 text-sm sm:text-base text-gray-900 placeholder-gray-500 focus:outline-none" />
        </div>
      </label>
    </div>

    <!-- Filters (placeholders) -->
    <div class="hidden sm:flex flex-wrap gap-3 p-3 pr-4">
      <button class="flex h-8 items-center gap-2 rounded bg-gray-100 px-4 text-sm font-medium text-gray-900 min-h-[44px] sm:min-h-[32px]">
        Role
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 256 256">
          <path d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z" />
        </svg>
      </button>
      <button class="flex h-8 items-center gap-2 rounded bg-gray-100 px-4 text-sm font-medium text-gray-900 min-h-[44px] sm:min-h-[32px]">
        Status
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 256 256">
          <path d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z" />
        </svg>
      </button>
      <button class="flex h-8 items-center gap-2 rounded bg-gray-100 px-4 text-sm font-medium text-gray-900 min-h-[44px] sm:min-h-[32px]">
        Date Joined
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 256 256">
          <path d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z" />
        </svg>
      </button>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden sm:block px-3 sm:px-4 py-3">
      <div class="overflow-hidden rounded border border-gray-300 bg-white">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-white text-left text-gray-900">
              <th class="px-4 py-3 w-1/4 font-medium">Name</th>
              <th class="px-4 py-3 w-1/4 font-medium">Role</th>
              <th class="px-4 py-3 w-1/3 font-medium">Email</th>
              <th class="px-4 py-3 w-28 font-medium">Status</th>
              <th class="px-4 py-3 w-28 font-medium text-gray-500">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in users" :key="u.id" class="border-t border-gray-300">
              <td class="px-4 py-2">{{ u.name }}</td>
              <td class="px-4 py-2 text-gray-500">{{ u.role }}</td>
              <td class="px-4 py-2 text-gray-500">{{ u.email }}</td>
              <td class="px-4 py-2">
                <span :class="[
                    'inline-flex h-8 items-center justify-center rounded w-full min-w-[84px] max-w-[120px] px-4',
                    u.status === 'Active' ? 'bg-primary-50 text-primary-700' : 'bg-gray-100 text-gray-900'
                  ]">
                  {{ u.status }}
                </span>
              </td>
              <td class="px-4 py-2 text-primary-600 font-bold cursor-pointer">Edit</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile Card View -->
    <div class="sm:hidden px-3 py-3 space-y-3">
      <div v-for="u in users" :key="u.id" class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm">
        <div class="flex items-start justify-between mb-3">
          <div class="flex-1 min-w-0">
            <h3 class="text-base font-medium text-gray-900 truncate">{{ u.name }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ u.role }}</p>
          </div>
          <span :class="[
              'inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-medium ml-3',
              u.status === 'Active' ? 'bg-primary-50 text-primary-700' : 'bg-gray-100 text-gray-900'
            ]">
            {{ u.status }}
          </span>
        </div>
        <div class="mb-3">
          <p class="text-sm text-gray-500 break-all">{{ u.email }}</p>
        </div>
        <div class="flex justify-end">
          <button class="text-primary-600 font-semibold text-sm min-h-[44px] px-4 py-2 rounded hover:bg-primary-50 transition-colors">
            Edit
          </button>
        </div>
      </div>
    </div>

    <!-- Footer actions -->
    <div class="flex justify-end px-3 sm:px-4 py-3">
      <button class="h-11 sm:h-10 rounded bg-primary-600 px-4 sm:px-4 text-sm font-bold text-white min-w-[120px] min-h-[44px] sm:min-h-[40px]">
        Add New User
      </button>
    </div>
  </AdminLayout>
</template>

<style scoped></style>
