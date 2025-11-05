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
  payments: Array,
  subscriptions: Array,
  summary: Object,
  monthlyPayments: Array,
  monthlySubscriptions: Array
});

const paymentsChart = computed(() => ({
  labels: props.monthlyPayments?.map(m => m.month) || [],
  datasets: [{
    label: 'Payments',
    data: props.monthlyPayments?.map(m => m.amount) || [],
    borderColor: '#F59E0B',
    backgroundColor: '#F59E0B',
    tension: 0.4,
    pointRadius: 4
  }]
}))
const subsChart = computed(() => ({
  labels: props.monthlySubscriptions?.map(m => m.month) || [],
  datasets: [{
    label: 'Subscriptions',
    data: props.monthlySubscriptions?.map(m => m.count) || [],
    borderColor: '#8B5CF6',
    backgroundColor: '#8B5CF6',
    tension: 0.4,
    pointRadius: 4
  }]
}))
</script>

<template>
  <AdminLayout title="Formations Reports">
    <Head title="Formations Reports" />
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h1 class="text-xl font-semibold text-gray-900 mb-2">Formations Reports</h1>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="border rounded-lg p-4">
          <div class="text-sm text-gray-600">Payments Total</div>
          <div class="text-xl font-semibold">{{ (props.summary?.paymentsTotal ?? 0).toLocaleString() }}</div>
        </div>
        <div class="border rounded-lg p-4">
          <div class="text-sm text-gray-600">Subscriptions</div>
          <div class="text-xl font-semibold">{{ props.summary?.subscriptionsCount ?? 0 }}</div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="border rounded-lg p-4">
          <div class="text-sm font-semibold mb-3">Payments (12 months)</div>
          <div class="h-56"><Line :data="paymentsChart" :options="{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}" /></div>
        </div>
        <div class="border rounded-lg p-4">
          <div class="text-sm font-semibold mb-3">Subscriptions (12 months)</div>
          <div class="h-56"><Line :data="subsChart" :options="{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}" /></div>
        </div>
      </div>

      <h2 class="text-lg font-semibold text-gray-900 mb-2">Recent Payments</h2>
      <div class="overflow-x-auto mb-2">
        <table class="min-w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left py-2 px-3 text-xs text-gray-500">ID</th>
              <th class="text-left py-2 px-3 text-xs text-gray-500">Amount</th>
              <th class="text-left py-2 px-3 text-xs text-gray-500">Status</th>
              <th class="text-left py-2 px-3 text-xs text-gray-500">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="p in (props.payments?.data || [])" :key="p?.id">
              <td class="py-2 px-3 text-sm">{{ p?.id }}</td>
              <td class="py-2 px-3 text-sm">{{ (p?.amount ?? 0).toLocaleString() }}</td>
              <td class="py-2 px-3 text-sm">{{ p?.status }}</td>
              <td class="py-2 px-3 text-sm">{{ p?.date }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex items-center gap-2 mb-6">
        <inertia-link v-for="link in props.payments?.links || []" :key="link.label" :href="link.url || '#'" :preserve-scroll="true"
          class="px-3 py-1 rounded border text-sm" :class="[link.active ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-50', !link.url ? 'opacity-50 cursor-not-allowed' : '']" v-html="link.label" />
      </div>

      <h2 class="text-lg font-semibold text-gray-900 mb-2">Recent Subscriptions</h2>
      <div class="overflow-x-auto mb-2">
        <table class="min-w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left py-2 px-3 text-xs text-gray-500">ID</th>
              <th class="text-left py-2 px-3 text-xs text-gray-500">Status</th>
              <th class="text-left py-2 px-3 text-xs text-gray-500">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="s in (props.subscriptions?.data || [])" :key="s?.id">
              <td class="py-2 px-3 text-sm">{{ s?.id }}</td>
              <td class="py-2 px-3 text-sm">{{ s?.status }}</td>
              <td class="py-2 px-3 text-sm">{{ s?.date }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex items-center gap-2">
        <inertia-link v-for="link in props.subscriptions?.links || []" :key="link.label" :href="link.url || '#'" :preserve-scroll="true"
          class="px-3 py-1 rounded border text-sm" :class="[link.active ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-50', !link.url ? 'opacity-50 cursor-not-allowed' : '']" v-html="link.label" />
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped></style>


