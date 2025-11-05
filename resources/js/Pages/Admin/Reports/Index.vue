<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
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
  totals: Object,
  monthlyPaymentsByCategory: Object,
  monthlySubscriptionsByCategory: Object
});

const paymentsChart = computed(() => ({
  labels: props.monthlyPaymentsByCategory?.labels || [],
  datasets: [
    { label: 'Educational Support', data: props.monthlyPaymentsByCategory?.educational_support || [], borderColor: '#3B82F6', backgroundColor: '#3B82F6', tension: 0.4, pointRadius: 3 },
    { label: 'Formations', data: props.monthlyPaymentsByCategory?.formations || [], borderColor: '#F59E0B', backgroundColor: '#F59E0B', tension: 0.4, pointRadius: 3 },
    { label: 'Childhood', data: props.monthlyPaymentsByCategory?.childhood || [], borderColor: '#10B981', backgroundColor: '#10B981', tension: 0.4, pointRadius: 3 },
  ]
}))

const subsChart = computed(() => ({
  labels: props.monthlySubscriptionsByCategory?.labels || [],
  datasets: [
    { label: 'Educational Support', data: props.monthlySubscriptionsByCategory?.educational_support || [], borderColor: '#6366F1', backgroundColor: '#6366F1', tension: 0.4, pointRadius: 3 },
    { label: 'Formations', data: props.monthlySubscriptionsByCategory?.formations || [], borderColor: '#8B5CF6', backgroundColor: '#8B5CF6', tension: 0.4, pointRadius: 3 },
    { label: 'Childhood', data: props.monthlySubscriptionsByCategory?.childhood || [], borderColor: '#EC4899', backgroundColor: '#EC4899', tension: 0.4, pointRadius: 3 },
  ]
}))
</script>

<template>
  <AdminLayout title="Reports">
    <Head title="Reports" />

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h1 class="text-xl font-semibold text-gray-900 mb-2">Reports Overview</h1>
      <p class="text-gray-600 mb-6">Key totals across categories.</p>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="border rounded-lg p-4">
          <div class="text-sm text-gray-600 mb-2">Educational Support</div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500">Payments</span>
            <span class="font-semibold">{{ (props.totals?.educational_support?.payments ?? 0).toLocaleString() }}</span>
          </div>
          <div class="flex items-center justify-between text-sm mt-1">
            <span class="text-gray-500">Subscriptions</span>
            <span class="font-semibold">{{ props.totals?.educational_support?.subscriptions ?? 0 }}</span>
          </div>
        </div>

        <div class="border rounded-lg p-4">
          <div class="text-sm text-gray-600 mb-2">Formations</div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500">Payments</span>
            <span class="font-semibold">{{ (props.totals?.formations?.payments ?? 0).toLocaleString() }}</span>
          </div>
          <div class="flex items-center justify-between text-sm mt-1">
            <span class="text-gray-500">Subscriptions</span>
            <span class="font-semibold">{{ props.totals?.formations?.subscriptions ?? 0 }}</span>
          </div>
        </div>

        <div class="border rounded-lg p-4">
          <div class="text-sm text-gray-600 mb-2">Childhood Education</div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500">Payments</span>
            <span class="font-semibold">{{ (props.totals?.childhood?.payments ?? 0).toLocaleString() }}</span>
          </div>
          <div class="flex items-center justify-between text-sm mt-1">
            <span class="text-gray-500">Subscriptions</span>
            <span class="font-semibold">{{ props.totals?.childhood?.subscriptions ?? 0 }}</span>
          </div>
        </div>
      </div>

      <!-- Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="border rounded-lg p-4">
          <div class="text-sm font-semibold mb-3">Payments by Category (12 months)</div>
          <div class="h-64">
            <Line :data="paymentsChart" :options="{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'top'}}}" />
          </div>
        </div>
        <div class="border rounded-lg p-4">
          <div class="text-sm font-semibold mb-3">Subscriptions by Category (12 months)</div>
          <div class="h-64">
            <Line :data="subsChart" :options="{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'top'}}}" />
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <inertia-link href="/admin/reports/educational-support" class="block p-4 border rounded-lg hover:bg-gray-50">Educational Support Reports</inertia-link>
        <inertia-link href="/admin/reports/formations" class="block p-4 border rounded-lg hover:bg-gray-50">Formations Reports</inertia-link>
        <inertia-link href="/admin/reports/childhood" class="block p-4 border rounded-lg hover:bg-gray-50">Childhood Education Reports</inertia-link>
        <inertia-link href="/admin/reports/teachers" class="block p-4 border rounded-lg hover:bg-gray-50">Teachers Reports</inertia-link>
        <inertia-link href="/admin/reports/students" class="block p-4 border rounded-lg hover:bg-gray-50">Students Reports</inertia-link>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped></style>


