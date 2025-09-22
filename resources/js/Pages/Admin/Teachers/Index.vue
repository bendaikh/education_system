<template>
  <AdminLayout title="All Teachers">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">All Teachers</h1>
          <p class="mt-2 text-sm text-gray-700">Manage all teachers in the system</p>
        </div>
        <Link
          :href="route('admin.teachers.create')"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
        >
          Add New Teacher
        </Link>
      </div>

      <!-- Teachers Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="teacher in teachers"
          :key="teacher.id"
          class="bg-white rounded-lg shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow"
        >
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900">{{ teacher.name }}</h3>
              <p class="text-sm text-gray-600">{{ teacher.role || 'Teacher' }}</p>
              <p class="text-sm text-gray-500">{{ teacher.department || 'No Department' }}</p>
            </div>
          </div>

          <div class="mt-4 space-y-2">
            <div v-if="teacher.contact_email" class="flex items-center text-sm text-gray-600">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              {{ teacher.contact_email }}
            </div>
            <div v-if="teacher.contact_phone" class="flex items-center text-sm text-gray-600">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
              </svg>
              {{ teacher.contact_phone }}
            </div>
          </div>

          <div class="mt-4 flex space-x-2">
            <Link
              :href="route('admin.teachers.show', teacher.id)"
              class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm font-medium transition-colors text-center"
            >
              View
            </Link>
            <Link
              :href="route('admin.teachers.edit', teacher.id)"
              class="flex-1 bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded text-sm font-medium transition-colors text-center"
            >
              Edit
            </Link>
            <button
              @click="deleteTeacher(teacher.id)"
              class="flex-1 bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded text-sm font-medium transition-colors"
            >
              Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="teachers.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No teachers</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new teacher.</p>
        <div class="mt-6">
          <Link
            :href="route('admin.teachers.create')"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
          >
            Add New Teacher
          </Link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  teachers: Array,
});

const deleteTeacher = (teacherId) => {
  if (confirm('Are you sure you want to delete this teacher?')) {
    router.delete(route('admin.teachers.destroy', teacherId), {
      onSuccess: () => {
        // Teacher deleted successfully
      },
    });
  }
};
</script>
