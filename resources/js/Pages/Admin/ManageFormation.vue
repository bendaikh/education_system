<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <AdminLayout :title="'Formation Management'">
    <Head title="Formation Management" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <Link :href="route('admin.formations.index')" class="block p-6 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow transition">
        <div class="flex items-center gap-3 mb-2">
          <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">FS</div>
          <h3 class="text-lg font-semibold text-gray-900">Formation Subjects</h3>
        </div>
        <p class="text-sm text-gray-600">Create and manage formation subjects.</p>
      </Link>

      <Link :href="route('admin.subscriptions.index')" class="block p-6 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow transition">
        <div class="flex items-center gap-3 mb-2">
          <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center">MS</div>
          <h3 class="text-lg font-semibold text-gray-900">Manage Formation Subscriptions</h3>
        </div>
        <p class="text-sm text-gray-600">Subscribe students to formation plans.</p>
      </Link>

      <Link :href="route('admin.payments.index')" class="block p-6 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow transition">
        <div class="flex items-center gap-3 mb-2">
          <div class="w-10 h-10 rounded-lg bg-green-100 text-green-700 flex items-center justify-center">PM</div>
          <h3 class="text-lg font-semibold text-gray-900">Payments</h3>
        </div>
        <p class="text-sm text-gray-600">Create and track formation payments.</p>
      </Link>
    </div>
  </AdminLayout>
</template>

<style scoped></style>


