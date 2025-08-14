<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { useCurrency } from '@/Composables/useCurrency.js';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js'
import { Line, Doughnut } from 'vue-chartjs'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  ArcElement
)

const page = usePage();
const language = computed(() => page.props.language || {});
const { formatLargeAmount } = useCurrency();

// Accept props from backend
const props = defineProps({
  stats: Object,
  totalRevenue: Number,
  monthlyPayments: Array,
  subscriptionStats: Array,
  totalActiveSubscriptions: Number,
  recentActivity: Array
});

// Transform stats data for display
const statsCards = computed(() => [
  {
    title: language.value.students || 'Students',
    value: props.stats?.totalStudents?.toLocaleString() || '0',
    icon: 'students',
    color: 'blue',
    bgColor: 'bg-blue-50',
    iconColor: 'text-blue-600'
  },
  {
    title: language.value.teachers || 'Teachers',
    value: props.stats?.totalTeachers?.toString() || '0',
    icon: 'teachers',
    color: 'green',
    bgColor: 'bg-green-50',
    iconColor: 'text-green-600'
  },
  {
    title: language.value.formations || 'Formations',
    value: props.stats?.totalFormations?.toString() || '0',
    icon: 'formations',
    color: 'yellow',
    bgColor: 'bg-yellow-50',
    iconColor: 'text-yellow-600'
  },
  {
    title: language.value.users || 'Users',
    value: props.stats?.totalUsers?.toString() || '0',
    icon: 'users',
    color: 'purple',
    bgColor: 'bg-purple-50',
    iconColor: 'text-purple-600'
  }
]);

// Chart configurations
const paymentChartData = computed(() => ({
  labels: props.monthlyPayments?.map(p => p.month) || [],
  datasets: [
    {
      label: 'Payments',
      data: props.monthlyPayments?.map(p => p.amount) || [],
      borderColor: '#3B82F6',
      backgroundColor: '#3B82F6',
      tension: 0.4,
      pointBackgroundColor: '#3B82F6',
      pointBorderColor: '#3B82F6',
      pointRadius: 6,
      pointHoverRadius: 8
    }
  ]
}));

const paymentChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          return formatLargeAmount(context.parsed.y);
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        callback: function(value) {
          return formatLargeAmount(value);
        }
      },
      grid: {
        color: '#F3F4F6'
      }
    },
    x: {
      grid: {
        display: false
      }
    }
  },
  elements: {
    point: {
      hoverBackgroundColor: '#3B82F6'
    }
  }
};

const subscriptionChartData = computed(() => ({
  labels: props.subscriptionStats?.map(s => s.name) || [],
  datasets: [
    {
      data: props.subscriptionStats?.map(s => s.count) || [],
      backgroundColor: [
        '#E5E7EB', // Light gray for Basic
        '#C7D2FE', // Light purple for Premium  
        '#A855F7'  // Purple for Pro
      ],
      borderWidth: 0,
      cutout: '60%'
    }
  ]
}));

const subscriptionChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          const stats = props.subscriptionStats?.[context.dataIndex];
          return `${stats?.name}: ${stats?.count} (${stats?.percentage}%)`;
        }
      }
    }
  }
};

// Calculate payment trend
const paymentTrend = computed(() => {
  if (!props.monthlyPayments || props.monthlyPayments.length < 2) return { value: 0, isPositive: true };
  
  const lastMonth = props.monthlyPayments[props.monthlyPayments.length - 1]?.amount || 0;
  const previousMonth = props.monthlyPayments[props.monthlyPayments.length - 2]?.amount || 0;
  
  if (previousMonth === 0) return { value: 0, isPositive: true };
  
  const percentage = ((lastMonth - previousMonth) / previousMonth * 100);
  return {
    value: Math.abs(percentage).toFixed(1),
    isPositive: percentage >= 0
  };
});

const todo_write = (merge, todos) => {
  // Mark dashboard update tasks as completed
};

onMounted(() => {
  // Mark dashboard controller update as completed
  todo_write(true, [
    {"id": "update_dashboard_controller", "status": "completed"},
    {"id": "create_stats_cards", "status": "completed"},
    {"id": "implement_payments_chart", "status": "completed"}, 
    {"id": "create_subscriptions_chart", "status": "completed"},
    {"id": "update_dashboard_layout", "status": "completed"}
  ]);
});
</script>

<template>
  <AdminLayout :title="language.dashboard || 'Dashboard'">
    <Head :title="language.dashboard || 'Dashboard'" />

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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
            </svg>
            <!-- Formations Icon -->
            <svg v-else-if="stat.icon === 'formations'" :class="[stat.iconColor, 'w-6 h-6']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <!-- Users Icon -->
            <svg v-else-if="stat.icon === 'users'" :class="[stat.iconColor, 'w-6 h-6']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Payments Overview Chart -->
      <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">Payments Overview</h2>
            <div class="flex items-center gap-2 mt-1">
              <span :class="[
                'inline-flex items-center gap-1 text-sm font-medium',
                paymentTrend.isPositive ? 'text-green-600' : 'text-red-600'
              ]">
                <svg v-if="paymentTrend.isPositive" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7m0 10H7"></path>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10m0-10h10"></path>
                </svg>
                {{ paymentTrend.isPositive ? '+' : '-' }}{{ paymentTrend.value }}%
              </span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button class="p-1 hover:bg-gray-100 rounded-lg">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
              </svg>
            </button>
          </div>
        </div>
        <div class="h-64">
          <Line :data="paymentChartData" :options="paymentChartOptions" />
        </div>
      </div>

      <!-- Active Subscriptions Chart -->
      <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl shadow-sm p-6 text-white">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold">Active Subscriptions</h2>
          <button class="p-1 hover:bg-white/10 rounded-lg">
            <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
            </svg>
          </button>
        </div>
        
        <div class="flex items-center justify-center mb-6">
          <div class="relative w-32 h-32">
            <Doughnut :data="subscriptionChartData" :options="subscriptionChartOptions" />
          </div>
        </div>

        <!-- Legend -->
        <div class="space-y-3">
          <div v-for="(subscription, index) in subscriptionStats" :key="subscription.name" class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div :class="[
                'w-3 h-3 rounded-full',
                index === 0 ? 'bg-gray-300' : index === 1 ? 'bg-purple-300' : 'bg-white'
              ]"></div>
              <span class="text-sm font-medium">{{ subscription.name }}</span>
            </div>
            <span class="text-sm font-medium">{{ subscription.count }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
      <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-lg font-semibold text-gray-900">{{ language.recent_activity || 'Recent Activity' }}</h2>
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
            <tr v-for="activity in recentActivity" :key="activity.id" class="hover:bg-gray-50 transition-colors duration-150">
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
                  activity.status === 'Completed' || activity.status === 'Active'
                    ? 'bg-green-100 text-green-800' 
                    : activity.status === 'Pending'
                    ? 'bg-yellow-100 text-yellow-800'
                    : 'bg-gray-100 text-gray-800'
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
        <div v-for="activity in recentActivity" :key="activity.id" class="p-4">
          <div class="flex items-start justify-between mb-2">
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ activity.user }}</p>
              <p class="text-sm text-gray-600 mt-1">{{ activity.activity }}</p>
            </div>
            <span :class="[
              'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-3 flex-shrink-0',
              activity.status === 'Completed' || activity.status === 'Active'
                ? 'bg-green-100 text-green-800' 
                : activity.status === 'Pending'
                ? 'bg-yellow-100 text-yellow-800'
                : 'bg-gray-100 text-gray-800'
            ]">
              {{ activity.status }}
            </span>
          </div>
          <p class="text-xs text-gray-500">{{ activity.date }}</p>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="!recentActivity || recentActivity.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No recent activity</h3>
        <p class="mt-1 text-sm text-gray-500">Activity will appear here once you start using the system.</p>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped></style>