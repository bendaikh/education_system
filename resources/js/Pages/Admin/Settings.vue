<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const page = usePage();
const language = computed(() => page.props.language || {});

// Accept props from backend
const props = defineProps({
    settings: Object
});

// Form data for settings - load from backend or use defaults
const form = useForm({
    app_name: props.settings?.app_name || 'Admin Panel',
    country: props.settings?.country || 'United States',
    currency: props.settings?.currency || 'USD ($)',
    email_notifications: props.settings?.email_notifications ?? true,
    push_notifications: props.settings?.push_notifications ?? false,
    monthly_reports: props.settings?.monthly_reports ?? true,
    logo: null
});

// Toggle functions for notification switches
const toggleEmailNotifications = () => {
    form.email_notifications = !form.email_notifications;
};

const togglePushNotifications = () => {
    form.push_notifications = !form.push_notifications;
};

const toggleMonthlyReports = () => {
    form.monthly_reports = !form.monthly_reports;
};

// File upload handler
const handleLogoUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
    }
};

// Form submission
const saveSettings = () => {
    form.post('/admin/settings', {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        },
        onError: () => {
            // Handle errors
        }
    });
};

const cancelChanges = () => {
    form.reset();
};
</script>

<template>
    <AdminLayout :title="language.settings || 'Settings'">
        <Head :title="language.settings || 'Settings'" />

        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
            </div>

            <!-- Settings Content -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
                    <!-- General Settings -->
                    <div class="p-8">
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">General</h2>
                            <p class="text-gray-500">Manage your application's global settings.</p>
                        </div>

                        <div class="space-y-6">
                            <!-- App Name -->
                            <div>
                                <InputLabel for="app_name" value="App Name" class="mb-2" />
                                <TextInput
                                    id="app_name"
                                    v-model="form.app_name"
                                    type="text"
                                    class="w-full"
                                    placeholder="Admin Panel"
                                />
                            </div>

                            <!-- Logo Upload -->
                            <div>
                                <InputLabel for="logo" value="Logo" class="mb-2" />
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <label class="cursor-pointer">
                                        <input
                                            type="file"
                                            accept="image/*"
                                            class="sr-only"
                                            @change="handleLogoUpload"
                                        />
                                        <span class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                            Upload Logo
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Country -->
                            <div>
                                <InputLabel for="country" value="Country" class="mb-2" />
                                <select
                                    id="country"
                                    v-model="form.country"
                                    class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm"
                                >
                                    <option value="United States">United States</option>
                                    <option value="Canada">Canada</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="France">France</option>
                                    <option value="Germany">Germany</option>
                                    <option value="Spain">Spain</option>
                                    <option value="Italy">Italy</option>
                                    <option value="Morocco">Morocco</option>
                                    <option value="Australia">Australia</option>
                                </select>
                            </div>

                            <!-- Currency -->
                            <div>
                                <InputLabel for="currency" value="Currency" class="mb-2" />
                                <select
                                    id="currency"
                                    v-model="form.currency"
                                    class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm"
                                >
                                    <option value="USD ($)">USD ($)</option>
                                    <option value="EUR (€)">EUR (€)</option>
                                    <option value="GBP (£)">GBP (£)</option>
                                    <option value="CAD (C$)">CAD (C$)</option>
                                    <option value="AUD (A$)">AUD (A$)</option>
                                    <option value="MAD (DH)">MAD (DH)</option>
                                    <option value="JPY (¥)">JPY (¥)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Settings -->
                    <div class="p-8">
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">Notifications</h2>
                            <p class="text-gray-500">Manage how you receive notifications.</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Email Notifications -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Email Notifications</h3>
                                </div>
                                <button
                                    @click="toggleEmailNotifications"
                                    :class="[
                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2',
                                        form.email_notifications ? 'bg-purple-600' : 'bg-gray-200'
                                    ]"
                                    type="button"
                                    role="switch"
                                    :aria-checked="form.email_notifications"
                                >
                                    <span
                                        :class="[
                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                            form.email_notifications ? 'translate-x-5' : 'translate-x-0'
                                        ]"
                                    ></span>
                                </button>
                            </div>

                            <!-- Push Notifications -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Push Notifications</h3>
                                </div>
                                <button
                                    @click="togglePushNotifications"
                                    :class="[
                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2',
                                        form.push_notifications ? 'bg-purple-600' : 'bg-gray-200'
                                    ]"
                                    type="button"
                                    role="switch"
                                    :aria-checked="form.push_notifications"
                                >
                                    <span
                                        :class="[
                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                            form.push_notifications ? 'translate-x-5' : 'translate-x-0'
                                        ]"
                                    ></span>
                                </button>
                            </div>

                            <!-- Monthly Reports -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Monthly Reports</h3>
                                </div>
                                <button
                                    @click="toggleMonthlyReports"
                                    :class="[
                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2',
                                        form.monthly_reports ? 'bg-purple-600' : 'bg-gray-200'
                                    ]"
                                    type="button"
                                    role="switch"
                                    :aria-checked="form.monthly_reports"
                                >
                                    <span
                                        :class="[
                                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                            form.monthly_reports ? 'translate-x-5' : 'translate-x-0'
                                        ]"
                                    ></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-4">
                    <SecondaryButton @click="cancelChanges" type="button">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton
                        @click="saveSettings"
                        :disabled="form.processing"
                        class="bg-purple-600 hover:bg-purple-700 focus:ring-purple-500"
                    >
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Custom toggle switch animations */
.toggle-switch {
    transition: all 0.2s ease-in-out;
}
</style>
