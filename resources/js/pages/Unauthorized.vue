<template>
    <div class="min-h-screen bg-background flex items-center justify-center px-4 sm:px-6 py-8 animate-fade-in">
        <div class="max-w-2xl w-full text-center">
            <!-- 403 Illustration -->
            <div class="mb-8 animate-bounce">
                <svg class="w-64 h-64 mx-auto text-red-500/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>

            <!-- Error Code -->
            <h1 class="text-6xl sm:text-8xl font-bold text-red-500 mb-4 animate-slide-up">403</h1>

            <!-- Error Message -->
            <h2 class="text-2xl sm:text-3xl font-bold mb-4 animate-slide-up" style="animation-delay: 0.1s;">
                Access Denied
            </h2>

            <p class="text-muted-foreground text-base sm:text-lg mb-8 animate-slide-up" style="animation-delay: 0.2s;">
                You don't have permission to access this page. This area is restricted to specific user roles.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-slide-up"
                 style="animation-delay: 0.3s;">
                <button
                    @click="goBack"
                    class="px-6 py-3 bg-muted text-foreground rounded-lg font-medium hover:bg-muted/80 transition-all duration-200 hover:shadow-lg hover:scale-105 flex items-center justify-center gap-2 w-full sm:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Go Back
                </button>

                <router-link
                    to="/dashboard"
                    class="px-6 py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-105 flex items-center justify-center gap-2 w-full sm:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Go to Dashboard
                </router-link>
            </div>

            <!-- Role Information -->
            <div class="mt-12 p-6 glass-card rounded-xl animate-slide-up" style="animation-delay: 0.4s;">
                <h3 class="text-lg font-semibold mb-3">Current Role: {{ userRole }}</h3>
                <p class="text-muted-foreground mb-4">
                    If you need access to this page, please contact your administrator.
                </p>
                <div class="text-left text-muted-foreground space-y-2 max-w-md mx-auto">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Different user roles have access to different features</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Contact support to request additional permissions</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed} from 'vue';
import {useRouter} from 'vue-router';
import {useAuth} from '../composables/useAuth.js';

const router = useRouter();
const {user, role} = useAuth();

const userRole = computed(() => {
    if (role.value) return role.value;
    if (user.value?.role) return user.value.role;
    return 'Unknown';
});

const goBack = () => {
    if (window.history.length > 1) {
        router.go(-1);
    } else {
        router.push('/dashboard');
    }
};
</script>
