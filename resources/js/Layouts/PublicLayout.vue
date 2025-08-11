<script setup>
import { computed } from 'vue';
import { Link as InertiaLink, usePage, Head } from '@inertiajs/vue3';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);
</script>

<template>
  <Head>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700;900&family=Public+Sans:wght@400;500;700;900&display=swap" />
  </Head>
  <div class="flex min-h-screen flex-col bg-white" style="font-family: 'Public Sans','Noto Sans',sans-serif;">
    <!-- Utility bar -->
    <div class="hidden sm:flex items-center justify-between bg-primary-600 px-4 py-1 text-xs text-white">
      <span>{{ page.props.settings?.contact_email ?? '[email protected]' }}</span>
      <div class="flex gap-3">
        <InertiaLink href="#" class="hover:opacity-80">FB</InertiaLink>
        <InertiaLink href="#" class="hover:opacity-80">TW</InertiaLink>
        <InertiaLink href="#" class="hover:opacity-80">IG</InertiaLink>
      </div>
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-30 flex items-center justify-between border-b border-[#f0f1f5] bg-white px-6 py-3">
      <div class="flex items-center gap-3">
        <svg viewBox="0 0 48 48" class="h-6 w-6 text-primary-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M42.4379 44C42.4379 44 36.0744 33.9038 41.1692 24C46.8624 12.9336 42.2078 4 42.2078 4L7.01134 4C7.01134 4 11.6577 12.932 5.96912 23.9969C0.876273 33.9029 7.27094 44 7.27094 44L42.4379 44Z" />
        </svg>
        <h1 class="text-lg font-bold text-[#111218]">Evergreen Academy</h1>
      </div>

      <nav class="hidden lg:flex gap-8">
        <InertiaLink href="/" class="text-sm font-medium text-[#111218] hover:text-primary-600">Home</InertiaLink>
        <InertiaLink href="#" class="text-sm font-medium text-[#111218] hover:text-primary-600">About</InertiaLink>
        <InertiaLink href="#" class="text-sm font-medium text-[#111218] hover:text-primary-600">Courses</InertiaLink>
        <InertiaLink href="#" class="text-sm font-medium text-[#111218] hover:text-primary-600">Admissions</InertiaLink>
        <InertiaLink href="#" class="text-sm font-medium text-[#111218] hover:text-primary-600">Contact</InertiaLink>
      </nav>

      <div class="flex items-center gap-4">
        <button class="flex h-10 w-10 items-center justify-center rounded bg-[#f0f1f5] text-[#111218]">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 256 256">
            <path d="M229.66 218.34l-50.07-50.06a88.11 88.11 0 1 0-11.31 11.31l50.06 50.07a8 8 0 0 0 11.32-11.32ZM40 112a72 72 0 1 1 72 72 72.08 72.08 0 0 1-72-72Z" />
          </svg>
        </button>
        <InertiaLink
          :href="isAuthenticated ? '/dashboard' : '/login'"
          class="flex h-10 min-w-[84px] items-center justify-center rounded bg-primary-600 px-4 text-sm font-bold text-white"
        >
          {{ isAuthenticated ? 'Dashboard' : 'Login' }}
        </InertiaLink>
      </div>
    </header>

    <!-- Page content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-[#111218] text-[#bdbfc9] pt-10 pb-6 px-6">
      <div class="mx-auto max-w-6xl grid gap-8 sm:grid-cols-3">
        <div>
          <h3 class="mb-3 text-white font-semibold">Links</h3>
          <ul class="space-y-2 text-sm">
            <li><InertiaLink href="#">Home</InertiaLink></li>
            <li><InertiaLink href="#">Courses</InertiaLink></li>
            <li><InertiaLink href="#">Admissions</InertiaLink></li>
            <li><InertiaLink href="#">Contact</InertiaLink></li>
          </ul>
        </div>
        <div>
          <h3 class="mb-3 text-white font-semibold">Follow Us</h3>
          <div class="flex gap-4">
            <InertiaLink href="#">FB</InertiaLink>
            <InertiaLink href="#">TW</InertiaLink>
            <InertiaLink href="#">IG</InertiaLink>
          </div>
        </div>
        <div>
          <h3 class="mb-3 text-white font-semibold">Contact</h3>
          <p class="text-sm">Email: {{ page.props.settings?.contact_email ?? 'info@example.com' }}</p>
          <p class="text-sm">Address: 35 King Street, CA</p>
        </div>
      </div>
      <p class="mt-6 text-center text-xs text-[#bdbfc9]">© 2024 Evergreen Academy. All rights reserved.</p>
    </footer>
  </div>
</template>

<style scoped>
</style>
