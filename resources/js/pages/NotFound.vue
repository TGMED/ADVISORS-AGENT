<template>
    <div class="min-h-screen bg-background flex items-center justify-center px-4 sm:px-6 py-8 animate-fade-in">
        <div class="max-w-2xl w-full text-center">
            <!-- 404 Illustration -->
            <div class="mb-8 animate-bounce">
                <svg class="w-64 h-64 mx-auto text-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <!-- Error Code -->
            <h1 class="text-6xl sm:text-8xl font-bold text-primary mb-4 animate-slide-up">404</h1>

            <!-- Error Message -->
            <h2 class="text-2xl sm:text-3xl font-bold mb-4 animate-slide-up" style="animation-delay: 0.1s;">
                {{ errorTitle }}
            </h2>

            <p class="text-muted-foreground text-base sm:text-lg mb-8 animate-slide-up" style="animation-delay: 0.2s;">
                {{ errorMessage }}
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

            <!-- Additional Help -->
            <div class="mt-12 p-6 glass-card rounded-xl animate-slide-up" style="animation-delay: 0.4s;">
                <h3 class="text-lg font-semibold mb-3">Need Help?</h3>
                <p class="text-muted-foreground mb-4">
                    If you believe this is an error, please contact support or try one of the following:
                </p>
                <ul class="text-left text-muted-foreground space-y-2 max-w-md mx-auto">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Check the URL for any typos</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Verify you have the correct permissions</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Contact your administrator for access</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed} from 'vue';
import {useRouter, useRoute} from 'vue-router';

const router = useRouter();
const route = useRoute();

const errorType = computed(() => route.query.type || 'not-found');

const errorTitle = computed(() => {
    switch (errorType.value) {
        case 'unauthorized':
            return 'Access Denied';
        case 'forbidden':
            return 'Forbidden';
        default:
            return 'Page Not Found';
    }
});

const errorMessage = computed(() => {
    switch (errorType.value) {
        case 'unauthorized':
            return "You don't have permission to access this page. Please contact your administrator if you believe this is an error.";
        case 'forbidden':
            return "This page is restricted to certain user roles. You don't have the required permissions to view this content.";
        default:
            return "The page you're looking for doesn't exist or has been moved. Let's get you back on track.";
    }
});

const goBack = () => {
    if (window.history.length > 1) {
        router.go(-1);
    } else {
        router.push('/dashboard');
    }
};
</script>
