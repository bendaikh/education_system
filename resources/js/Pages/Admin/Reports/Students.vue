<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link as InertiaLink } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend)

const props = defineProps({
  summary: Object,
  recent: Array,
  monthlyCounts: Array
})

const countsChart = computed(() => ({
  labels: props.monthlyCounts?.map(m => m.month) || [],
  datasets: [{
    label: 'Students Added',
    data: props.monthlyCounts?.map(m => m.count) || [],
    borderColor: '#EF4444',
    backgroundColor: '#EF4444',
    tension: 0.4,
    pointRadius: 4
  }]
}))
</script>

<template>
  <AdminLayout title="Students Reports">
    <Head title="Students Reports" />
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h1 class="text-xl font-semibold text-gray-900 mb-4">Students Reports</h1>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="border rounded-lg p-4">
          <div class="text-sm text-gray-600">Total Students</div>
          <div class="text-xl font-semibold">{{ props.summary?.total ?? 0 }}</div>
        </div>
        <div class="md:col-span-2 border rounded-lg p-4">
          <div class="text-sm font-semibold mb-3">Students Added (12 months)</div>
          <div class="h-48 sm:h-56"><Line :data="countsChart" :options="{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}" /></div>
        </div>
      </div>

      <h2 class="text-lg font-semibold text-gray-900 mb-2">Recent Students</h2>
      <!-- Desktop Table -->
      <div class="hidden sm:block overflow-x-auto mb-2">
        <table class="min-w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left py-2 px-3 text-xs text-gray-500">ID</th>
              <th class="text-left py-2 px-3 text-xs text-gray-500">Name</th>
              <th class="text-left py-2 px-3 text-xs text-gray-500">Created</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="s in (props.recent?.data || [])" :key="s?.id">
              <td class="py-2 px-3 text-sm">{{ s?.id }}</td>
              <td class="py-2 px-3 text-sm">{{ s?.name }}</td>
              <td class="py-2 px-3 text-sm">{{ s?.created }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Mobile Card List -->
      <div class="sm:hidden divide-y rounded-md border mb-2">
        <div v-for="s in (props.recent?.data || [])" :key="s?.id" class="p-3">
          <div class="flex items-center justify-between">
            <div class="text-sm font-medium text-gray-900">#{{ s?.id }}</div>
            <div class="text-sm text-gray-500">{{ s?.created }}</div>
          </div>
          <div class="mt-1 text-sm text-gray-600">Name: <span class="font-medium text-gray-900">{{ s?.name }}</span></div>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <InertiaLink v-for="link in props.recent?.links || []" :key="link.label" :href="link.url || '#'" :preserve-scroll="true"
          class="px-3 py-1 rounded border text-sm" :class="[link.active ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-50', !link.url ? 'opacity-50 cursor-not-allowed' : '']" v-html="link.label" />
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped></style>


