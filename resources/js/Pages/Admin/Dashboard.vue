<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

// Accept props from backend
const props = defineProps({
  stats: Object,
  recentActivity: Array
});

// Transform stats data for display
const statsCards = [
  {
    title: 'Total Students',
    value: props.stats?.totalStudents?.toLocaleString() || '1,250',
    icon: 'students',
    color: 'blue',
    bgColor: 'bg-blue-50',
    iconColor: 'text-blue-600'
  },
  {
    title: 'Total Teachers',
    value: props.stats?.totalTeachers?.toString() || '75',
    icon: 'teachers',
    color: 'green',
    bgColor: 'bg-green-50',
    iconColor: 'text-green-600'
  },
  {
    title: 'Total Classes',
    value: props.stats?.totalClasses?.toString() || '50',
    icon: 'classes',
    color: 'yellow',
    bgColor: 'bg-yellow-50',
    iconColor: 'text-yellow-600'
  },
  {
    title: 'Revenue',
    value: props.stats?.revenue || '$1.2M',
    icon: 'revenue',
    color: 'red',
    bgColor: 'bg-red-50',
    iconColor: 'text-red-600'
  }
];
</script>

<template>
  <AdminLayout title="Dashboard">
    <Head title="Dashboard" />

    <!-- Dashboard Header -->
    <div class="mb-8">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
      <p class="text-sm sm:text-base text-gray-600">Welcome to your school management dashboard.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
      <div v-for="stat in statsCards" :key="stat.title" 
           class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600 mb-2">{{ stat.title }}</p>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ stat.value }}</p>
          </div>
          <div :class="[stat.bgColor, 'w-12 h-12 rounded-lg flex items-center justify-center']">
            <!-- Students Icon -->
            <svg v-if="stat.icon === 'students'" :class="[stat.iconColor, 'w-6 h-6']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <!-- Teachers Icon -->
            <svg v-else-if="stat.icon === 'teachers'" :class="[stat.iconColor, 'w-6 h-6']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <!-- Classes Icon -->
            <svg v-else-if="stat.icon === 'classes'" :class="[stat.iconColor, 'w-6 h-6']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <!-- Revenue Icon -->
            <svg v-else-if="stat.icon === 'revenue'" :class="[stat.iconColor, 'w-6 h-6']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
      <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
      </div>

      <!-- Desktop Table View -->
      <div class="hidden sm:block">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100">
              <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
              <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="activity in props.recentActivity" :key="activity.id" class="hover:bg-gray-50 transition-colors duration-150">
              <td class="py-4 px-6">
                <div class="text-sm font-medium text-gray-900">{{ activity.user }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm text-gray-600">{{ activity.activity }}</div>
              </td>
              <td class="py-4 px-6">
                <div class="text-sm text-gray-500">{{ activity.date }}</div>
              </td>
              <td class="py-4 px-6">
                <span :class="[
                  'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                  activity.status === 'Completed' 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-yellow-100 text-yellow-800'
                ]">
                  {{ activity.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="sm:hidden divide-y divide-gray-100">
        <div v-for="activity in props.recentActivity" :key="activity.id" class="p-4">
          <div class="flex items-start justify-between mb-2">
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ activity.user }}</p>
              <p class="text-sm text-gray-600 mt-1">{{ activity.activity }}</p>
            </div>
            <span :class="[
              'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-3 flex-shrink-0',
              activity.status === 'Completed' 
                ? 'bg-green-100 text-green-800' 
                : 'bg-yellow-100 text-yellow-800'
            ]">
              {{ activity.status }}
            </span>
          </div>
          <p class="text-xs text-gray-500">{{ activity.date }}</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped></style>
