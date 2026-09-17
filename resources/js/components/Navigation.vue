<template>
    <nav v-if="!isHidden" class="border-b border-primary bg-card/50 backdrop-blur-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4 xl:space-x-8 min-w-0">
                    <router-link to="/dashboard"
                                 class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                        <img :src="logo()" alt="Advisors Academy" class="h-8 w-auto"/>
                    </router-link>

                    <div class="hidden lg:flex items-center space-x-1">
                        <!-- Filter navigation items based on user role -->
                        <router-link
                            v-for="item in filteredNavItems"
                            :key="item.path"
                            :to="item.path"
                            class="px-3 xl:px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-all duration-200 hover:bg-muted relative"
                            :class="isActive(item.path) ? 'bg-primary/10 text-primary' : 'text-foreground/70 hover:text-foreground'"
                        >
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          :d="item.icon"/>
                                </svg>
                                <span>{{ item.label }}</span>
                            </div>
                            <div v-if="isActive(item.path)"
                                 class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"></div>
                        </router-link>
                    </div>
                </div>

                <div class="flex items-center space-x-1 sm:space-x-4">
                    <button
                        @click="toggleTheme"
                        class="p-2 hover:bg-muted rounded-lg transition-all duration-200 hover:scale-110"
                        aria-label="Toggle theme"
                    >
                        <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    <button class="p-2 hover:bg-muted rounded-lg transition-all duration-200 hover:scale-110 relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-primary rounded-full"></span>
                    </button>

                    <div class="flex items-center space-x-3 pl-2 sm:pl-4 border-l border-border">
                        <!-- Display user info with role -->
                        <div class="hidden xl:block text-right max-w-[150px]">
                            <div class="text-sm font-medium truncate"
                                 :title="`${currentUser?.first_name} ${currentUser?.last_name}`">
                                {{ currentUser?.first_name }} {{ currentUser?.last_name }}
                            </div>
                            <div class="text-xs text-muted-foreground truncate capitalize"
                                 :title="userRole">
                                {{ userRole || 'User' }}
                            </div>
                        </div>
                        <div class="relative">
                            <button
                                @click="showUserMenu = !showUserMenu"
                                class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center cursor-pointer hover:bg-primary/30 transition-colors"
                            >
                                <span class="text-primary font-medium text-sm">{{ userInitials }}</span>
                            </button>
                            <div v-if="showUserMenu"
                                 class="absolute right-0 mt-2 w-48 bg-card border border-border rounded-lg shadow-lg py-2 z-50">
                                <div class="px-4 py-2 border-b border-border xl:hidden">
                                    <div class="text-sm font-medium truncate"
                                         :title="`${currentUser?.first_name} ${currentUser?.last_name}`">
                                        {{ currentUser?.first_name }} {{ currentUser?.last_name }}
                                    </div>
                                    <div class="text-xs text-muted-foreground truncate capitalize"
                                         :title="userRole">
                                        {{ userRole || 'User' }}
                                    </div>
                                </div>
                                <button
                                    @click="handleLogout"
                                    class="w-full px-4 py-2 text-left text-sm hover:bg-muted transition-colors flex items-center space-x-2 text-red-500"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Logout</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button @click="toggleMobileMenu"
                            class="lg:hidden p-2 hover:bg-muted rounded-lg transition-all duration-200" aria-label="Toggle menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu with logout button -->
            <!-- Filter mobile menu items based on user role -->
            <div v-if="mobileMenuOpen" class="lg:hidden py-4 border-t border-border animate-slide-down">
                <router-link
                    v-for="item in filteredNavItems"
                    :key="item.path"
                    :to="item.path"
                    @click="mobileMenuOpen = false"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-muted"
                    :class="isActive(item.path) ? 'bg-primary/10 text-primary' : 'text-foreground/70'"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"/>
                    </svg>
                    <span>{{ item.label }}</span>
                </router-link>
                <button
                    @click="handleLogout"
                    class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-muted text-red-500 mt-2 border-t border-border pt-4"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Logout</span>
                </button>
            </div>
        </div>
    </nav>
</template>

<script>
import {ref, computed, onMounted} from 'vue';
import {useRoute} from 'vue-router';
import {useTheme} from '../composables/useTheme';
import {useAuth} from '../composables/useAuth';
import logo from '@/assets/logo.png'

export default {
    name: 'Navigation',
    methods: {
        logo() {
            return logo
        }
    },
    setup() {
        const route = useRoute();
        const {isDark, toggleTheme} = useTheme();
        const {user: currentUser, logout, initAuth, userRole} = useAuth();
        const mobileMenuOpen = ref(false);
        const showUserMenu = ref(false);

        const isHidden = computed(() => {
            return route.path === '/forgot-password';
        });

        const userInitials = computed(() => {
            if (currentUser.value) {
                return `${currentUser.value.first_name?.[0] || ''}${currentUser.value.last_name?.[0] || ''}`;
            }
            return 'JD';
        });

        const allNavItems = [
            {
                label: 'Dashboard',
                path: '/dashboard',
                roles: ["agent", "counselor"],
                icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 00-5.356-1.857M17 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
            },
            {
                label: 'Students',
                path: '/students',
                roles: ["agent", "counselor"],
                icon: 'M12 4v16m8-8H4v-2a3 3 0 00-5.356-1.857M17 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
            },

            {
                label: 'Applications',
                path: '/applications',
                roles: ["agent", "counselor"],
                icon: 'M9 17v-6a2 2 0 012-2h6M9 17H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.343M9 17l6 6m0 0l6-6m-6 6V3'
            },
            {
                label: 'New Application',
                path: '/applications/create',
                roles: ["agent"],
                icon: 'M12 4v16m8-8H4'
            },
            {
                label: 'New Student',
                path: '/students/create',
                roles: ["agent"],
                icon: 'M12 4v16m8-8H4'
            },
            {
                label: 'Agents',
                path: '/agents',
                roles: ["counselor"],
                icon: 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m9-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm7 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-1 10v-2a4 4 0 0 0-3-3.87'
            },
        ];

        const filteredNavItems = computed(() => {
            return allNavItems.filter(item => {
                return item.roles.includes(userRole.value)
            })
        })

        const isActive = (path) => {
            return route.path === path;
        };

        const toggleMobileMenu = () => {
            mobileMenuOpen.value = !mobileMenuOpen.value;
        };

        const handleLogout = async () => {
            showUserMenu.value = false;
            await logout();
        };

        onMounted(() => {
            initAuth();
        });

        return {
            currentUser,
            userRole,
            userInitials,
            allNavItems,
            filteredNavItems,
            isActive,
            isDark,
            toggleTheme,
            mobileMenuOpen,
            toggleMobileMenu,
            showUserMenu,
            handleLogout,
            isHidden
        };
    }
};
</script>
