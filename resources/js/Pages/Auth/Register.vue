<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <Head title="Register - EduConnect" />
    
    <!-- Navigation -->
    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
          </div>
          <span class="text-xl font-bold text-white">EduConnect</span>
        </div>
        
        <a href="/" class="text-gray-300 hover:text-white transition-colors">
          ← Back to Home
        </a>
      </div>
    </nav>

    <!-- Register Form -->
    <div class="flex flex-1 justify-center items-center px-6 py-20">
      <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold mb-2">Create Account</h1>
          <p class="text-gray-400">Join EduConnect to transform your school's management</p>
        </div>

        <!-- Register Card -->
        <div class="bg-gray-800 rounded-xl p-8 shadow-2xl border border-gray-700">
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                Full Name
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Enter your full name"
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                required
                autofocus
                autocomplete="name"
              />
              <span v-if="form.errors.name" class="mt-2 text-sm text-red-400 block">
                {{ form.errors.name }}
              </span>
            </div>

            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                Email Address
              </label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="Enter your email"
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                required
                autocomplete="username"
              />
              <span v-if="form.errors.email" class="mt-2 text-sm text-red-400 block">
                {{ form.errors.email }}
              </span>
            </div>

            <!-- Password -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                Password
              </label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                placeholder="Create a strong password"
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                required
                autocomplete="new-password"
              />
              <span v-if="form.errors.password" class="mt-2 text-sm text-red-400 block">
                {{ form.errors.password }}
              </span>
            </div>

            <!-- Confirm Password -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                Confirm Password
              </label>
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                placeholder="Confirm your password"
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                required
                autocomplete="new-password"
              />
              <span v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-400 block">
                {{ form.errors.password_confirmation }}
              </span>
            </div>

            <!-- Register Button -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-800 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
            >
              <span v-if="!form.processing">Create Account</span>
              <span v-else class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Creating account...
              </span>
            </button>
          </form>
        </div>

        <!-- Login Link -->
        <div class="text-center mt-6">
          <span class="text-gray-400">Already have an account? </span>
          <Link 
            :href="route('login')" 
            class="text-blue-400 hover:text-blue-300 transition-colors font-medium"
          >
            Sign in here
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
