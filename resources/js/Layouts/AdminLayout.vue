<script setup>
import { computed, ref, onMounted } from 'vue';
import { Link as InertiaLink, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    title: { type: String, default: '' },
});

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

// Language switching
const currentLocale = ref(page.props.locale || 'en');

const switchLanguage = (locale) => {
    currentLocale.value = locale;
    localStorage.setItem('locale', locale);
    
    // Use Inertia router to send POST request
    router.post(`/language/${locale}`, {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            // Language switched successfully
            console.log('Language switched to:', locale);
        },
        onError: (errors) => {
            console.error('Error switching language:', errors);
        }
    });
};

// Sidebar state - open by default on desktop, closed on mobile
const sidebarOpen = ref(false);

// Dropdown state for navigation items with subitems
const dropdownOpen = ref({});

// Set initial state based on screen size
onMounted(() => {
    // Check if we're on desktop (lg breakpoint = 1024px)
    const isDesktop = window.innerWidth >= 1024;
    sidebarOpen.value = isDesktop;
    
    // Listen for window resize to adjust sidebar state
    const handleResize = () => {
        const isNowDesktop = window.innerWidth >= 1024;
        // Only auto-open on desktop if it's currently closed
        // Don't auto-close when switching from desktop to mobile
        if (isNowDesktop && !sidebarOpen.value) {
            sidebarOpen.value = true;
        }
    };
    
    window.addEventListener('resize', handleResize);
    
    // Cleanup event listener
    return () => window.removeEventListener('resize', handleResize);
});

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

// Handle navigation clicks - only close sidebar on mobile
const handleNavClick = () => {
    if (window.innerWidth < 1024) {
        sidebarOpen.value = false;
    }
};

// Toggle dropdown for navigation items
const toggleDropdown = (itemKey) => {
    dropdownOpen.value[itemKey] = !dropdownOpen.value[itemKey];
};

const navItems = computed(() => {
    const language = page.props.language || {};
    return [
        { label: language.dashboard || 'Dashboard', href: '/admin/dashboard' },
        { label: language.classes || 'Classes', href: '/admin/classes' },
        { label: language.students || 'Students', href: '/admin/students' },
        { label: language.teachers || 'Teachers', href: '/admin/teachers' },
        { label: language.formations || 'Formations', href: '/admin/formations' },
        { label: language.payments || 'Payments', href: '/admin/payments' },
        { 
            label: language.subscriptions || 'Subscriptions', 
            key: 'subscriptions',
            hasSubmenu: true,
            subitems: [
                { 
                    label: language.manage_subscription_types || 'Manage Subscription Types', 
                    href: '/admin/subscriptions/types' 
                },
                { 
                    label: language.manage_subscriptions || 'Manage Subscriptions', 
                    href: '/admin/subscriptions' 
                }
            ]
        },
    ];
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-white">
        <!-- Header -->
        <header class="flex items-center justify-between border-b border-gray-100 px-4 sm:px-6 lg:px-10 py-3">
            <div class="flex items-center gap-2 sm:gap-4 text-gray-900">
                <!-- Hamburger Menu Button -->
                <button 
                    @click="toggleSidebar"
                    class="p-2 rounded-xl hover:bg-gradient-to-br hover:from-blue-50 hover:to-purple-50 transition-all duration-200 min-w-[44px] min-h-[44px] flex items-center justify-center border border-transparent hover:border-blue-200 hover:shadow-lg"
                    :class="{ 'bg-gradient-to-br from-blue-50 to-purple-50 border-blue-200 shadow-md': sidebarOpen }"
                >
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="transition-transform duration-200" :class="{ 'rotate-90': sidebarOpen }">
                        <path d="M3 12H21M3 6H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <svg viewBox="0 0 48 48" class="h-5 w-5 sm:h-6 sm:w-6 text-primary-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M42.4379 44C42.4379 44 36.0744 33.9038 41.1692 24C46.8624 12.9336 42.2078 4 42.2078 4L7.01134 4C7.01134 4 11.6577 12.932 5.96912 23.9969C0.876273 33.9029 7.27094 44 7.27094 44L42.4379 44Z" />
                </svg>
                <h2 class="text-base sm:text-lg font-bold tracking-tight hidden xs:block">School Admin</h2>
                <h2 class="text-base font-bold tracking-tight xs:hidden">Admin</h2>
            </div>
            <div class="flex items-center gap-1 sm:gap-3">
                <!-- Notification bell -->
                <button class="flex h-10 w-10 min-w-[44px] min-h-[44px] items-center justify-center rounded bg-gray-100 text-gray-900 sm:block hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 256 256">
                        <path
                            d="M221.8,175.94C216.25,166.38,208,139.33,208,104a80,80,0,1,0-160,0c0,35.34-8.26,62.38-13.81,71.94A16,16,0,0,0,48,200H88.81a40,40,0,0,0,78.38,0H208a16,16,0,0,0,13.8-24.06ZM128,216a24,24,0,0,1-22.62-16h45.24A24,24,0,0,1,128,216ZM48,184c7.7-13.24,16-43.92,16-80a64,64,0,1,1,128,0c0,36.05,8.28,66.73,16,80Z" />
                    </svg>
                </button>
                <!-- Language Switcher -->
                <div class="flex items-center gap-3 mr-4">
                    <button @click="switchLanguage('en')" :class="['min-w-[32px] min-h-[32px] rounded-lg flex items-center justify-center transition-colors', currentLocale === 'en' ? 'bg-blue-100 text-blue-600' : 'hover:bg-gray-100']">
                        <span class="text-sm font-medium">EN</span>
                    </button>
                    <button @click="switchLanguage('fr')" :class="['min-w-[32px] min-h-[32px] rounded-lg flex items-center justify-center transition-colors', currentLocale === 'fr' ? 'bg-blue-100 text-blue-600' : 'hover:bg-gray-100']">
                        <span class="text-sm font-medium">FR</span>
                    </button>
                </div>

                <!-- User Info and Avatar -->
                <div class="flex items-center gap-1 sm:gap-3">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-medium text-gray-900">{{ page.props.auth?.user?.name }}</div>
                        <div class="text-xs text-gray-500 capitalize">{{ page.props.auth?.user?.role }}</div>
                    </div>
                    <div class="size-8 sm:size-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold text-sm">
                        {{ page.props.auth?.user?.name?.charAt(0)?.toUpperCase() || 'A' }}
                    </div>
                    <InertiaLink href="/logout" method="post" as="button" 
                        class="ml-1 sm:ml-2 text-xs sm:text-sm text-gray-500 hover:text-gray-700 min-w-[44px] min-h-[44px] flex items-center justify-center">
                        <span class="hidden sm:inline">{{ (page.props.language && page.props.language.logout) || 'Logout' }}</span>
                        <svg class="sm:hidden w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </InertiaLink>
                </div>
            </div>
        </header>

        <div class="flex flex-1 relative">
            <!-- Mobile Overlay -->
            <div 
                v-if="sidebarOpen" 
                @click="toggleSidebar"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden transition-all duration-300 ease-in-out"
            ></div>
            
            <!-- Sidebar -->
            <aside 
                :class="[
                    'transition-all duration-300 ease-in-out z-50',
                    'sidebar-gradient',
                    'shadow-2xl shadow-slate-900/20 border-r border-slate-700/50',
                    // Mobile behavior - show/hide based on sidebarOpen state
                    sidebarOpen ? 'block' : 'hidden',
                    // Desktop behavior - always visible when open, hidden when closed
                    'lg:block',
                    // Positioning and sizing
                    sidebarOpen ? 'fixed lg:relative inset-y-0 left-0 w-72 sm:w-80' : '',
                    // Desktop width control
                    sidebarOpen ? 'lg:w-80' : 'lg:w-0 lg:overflow-hidden'
                ]"
            >
                <div class="flex h-full min-h-screen flex-col p-4 sm:p-6">


                    <!-- Navigation -->
                    <nav class="flex-1 space-y-2 pt-4">
                        <template v-for="item in navItems" :key="'side-'+(item.href || item.key)">
                            <!-- Regular navigation item -->
                            <InertiaLink v-if="!item.hasSubmenu" :href="item.href"
                                :class="[
                                    'nav-item-glow group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 min-h-[44px] relative overflow-hidden',
                                    $page.url.startsWith(item.href) 
                                        ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg shadow-blue-500/25' 
                                        : 'text-slate-300 hover:text-white hover:bg-slate-700/50 hover:shadow-lg hover:shadow-slate-900/20'
                                ]"
                                @click="handleNavClick">
                                <!-- Icon for each nav item -->
                                <div class="w-5 h-5 flex-shrink-0">
                                    <!-- Dashboard Icon -->
                                    <svg v-if="item.href === '/admin/dashboard'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6a2 2 0 01-2 2H10a2 2 0 01-2-2V5z"></path>
                                    </svg>
                                    <!-- Classes Icon -->
                                    <svg v-else-if="item.href === '/admin/classes'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <!-- Students Icon -->
                                    <svg v-else-if="item.href === '/admin/students'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <!-- Teachers Icon -->
                                    <svg v-else-if="item.href === '/admin/teachers'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <!-- Formations Icon -->
                                    <svg v-else-if="item.href === '/admin/formations'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                    </svg>
                                    <!-- Payments Icon -->
                                    <svg v-else-if="item.href === '/admin/payments'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ item.label }}</span>
                                <!-- Active indicator -->
                                <div v-if="$page.url.startsWith(item.href)" class="absolute inset-y-0 left-0 w-1 bg-white rounded-r-full"></div>
                            </InertiaLink>

                            <!-- Dropdown navigation item -->
                            <div v-else class="space-y-1">
                                <!-- Parent item -->
                                <button 
                                    @click="toggleDropdown(item.key)"
                                    :class="[
                                        'nav-item-glow group flex items-center justify-between w-full gap-3 px-4 py-3 rounded-xl transition-all duration-200 min-h-[44px] relative overflow-hidden',
                                        item.subitems.some(subitem => $page.url === subitem.href)
                                            ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg shadow-blue-500/25' 
                                            : 'text-slate-300 hover:text-white hover:bg-slate-700/50 hover:shadow-lg hover:shadow-slate-900/20'
                                    ]"
                                >
                                    <div class="flex items-center gap-3">
                                        <!-- Subscriptions Icon -->
                                        <div class="w-5 h-5 flex-shrink-0">
                                            <svg v-if="item.key === 'subscriptions'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium">{{ item.label }}</span>
                                    </div>
                                    <!-- Dropdown arrow -->
                                    <div class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen[item.key] }">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <!-- Active indicator -->
                                    <div v-if="item.subitems.some(subitem => $page.url === subitem.href)" class="absolute inset-y-0 left-0 w-1 bg-white rounded-r-full"></div>
                                </button>

                                <!-- Subitems -->
                                <transition name="dropdown">
                                    <div v-if="dropdownOpen[item.key]" class="ml-6 space-y-1">
                                        <InertiaLink 
                                            v-for="subitem in item.subitems" 
                                            :key="subitem.href" 
                                            :href="subitem.href"
                                            :class="[
                                                'group flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-200 min-h-[36px] relative',
                                                $page.url === subitem.href
                                                    ? 'bg-gradient-to-r from-blue-400 to-purple-500 text-white shadow-md' 
                                                    : 'text-slate-400 hover:text-white hover:bg-slate-700/30'
                                            ]"
                                            @click="handleNavClick"
                                        >
                                            <div class="w-2 h-2 rounded-full bg-current opacity-60"></div>
                                            <span class="text-sm font-medium">{{ subitem.label }}</span>
                                            <div v-if="$page.url === subitem.href" class="absolute inset-y-0 left-0 w-1 bg-white rounded-r-full"></div>
                                        </InertiaLink>
                                    </div>
                                </transition>
                            </div>
                        </template>
                        
                                           <!-- User Management -->
                   <InertiaLink href="/admin/user-management"
                       :class="[
                           'nav-item-glow group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 min-h-[44px] relative overflow-hidden',
                           $page.url.startsWith('/admin/user-management') 
                               ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg shadow-blue-500/25' 
                               : 'text-slate-300 hover:text-white hover:bg-slate-700/50 hover:shadow-lg hover:shadow-slate-900/20'
                       ]"
                       @click="handleNavClick">
                       <div class="w-5 h-5 flex-shrink-0">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                           </svg>
                       </div>
                       <span class="font-medium">{{ (page.props.language && page.props.language.user_management) || 'User Management' }}</span>
                       <div v-if="$page.url.startsWith('/admin/user-management')" class="absolute inset-y-0 left-0 w-1 bg-white rounded-r-full"></div>
                   </InertiaLink>
                        
                        <!-- Settings -->
                        <InertiaLink href="/admin/settings"
                            :class="[
                                'nav-item-glow group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 min-h-[44px] relative overflow-hidden',
                                $page.url.startsWith('/admin/settings') 
                                    ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg shadow-blue-500/25' 
                                    : 'text-slate-300 hover:text-white hover:bg-slate-700/50 hover:shadow-lg hover:shadow-slate-900/20'
                            ]"
                            @click="handleNavClick">
                            <div class="w-5 h-5 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span class="font-medium">{{ (page.props.language && page.props.language.settings) || 'Settings' }}</span>
                            <div v-if="$page.url.startsWith('/admin/settings')" class="absolute inset-y-0 left-0 w-1 bg-white rounded-r-full"></div>
                        </InertiaLink>
                    </nav>
                    
                    <!-- Sidebar Footer -->
                    <div class="mt-8 pt-6 border-t border-slate-700/50">
                        <InertiaLink href="/logout" method="post" as="button" 
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-red-500/20 transition-all duration-200 group">
                            <div class="w-5 h-5 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </div>
                            <span class="font-medium">Logout</span>
                        </InertiaLink>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main 
                :class="[
                    'flex-1 flex flex-col transition-all duration-300 ease-in-out p-4 sm:p-6',
                    sidebarOpen ? 'lg:max-w-[calc(100%-320px)]' : 'lg:max-w-full'
                ]"
            >
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

.dropdown-enter-to,
.dropdown-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>
