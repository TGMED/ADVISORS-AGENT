<template>
    <div class="min-h-screen flex animate-fade-in">
        <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-8 lg:p-12">
            <div class="w-full max-w-md space-y-8 animate-slide-up">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <img :src="logo()" alt="Advisors Academy" class="h-20 w-auto mb-4"/>
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Welcome back</h1>
                        <p class="text-muted-foreground">Sign in to your agent account</p>
                    </div>
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
                </div>

                <form @submit.prevent="handleLogin" class="space-y-6">
                    <div class="space-y-4">
                        <div class="space-y-2 group">
                            <label for="email"
                                   class="text-sm font-medium transition-colors group-focus-within:text-primary">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                :disabled="loading"
                                required
                                @input="clearError"
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                placeholder="agent@example.com"
                            />
                        </div>

                        <div class="space-y-2 group">
                            <label for="password"
                                   class="text-sm font-medium transition-colors group-focus-within:text-primary">Password</label>
                            <div class="relative">
                                <input
                                    id="password"
                                    :disabled="loading"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    @input="clearError"
                                    class="w-full px-4 py-3 pr-11 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-muted-foreground hover:text-foreground transition-colors"
                                    tabindex="-1"
                                >
                                    <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        <circle cx="12" cy="12" r="3" stroke-width="2"/>
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 3l18 18M10.584 10.587A2.999 2.999 0 0115 12c0 .513-.129.996-.356 1.417M6.71 6.707C4.934 7.92 3.59 9.74 2.958 12c1.274 4.057 5.065 7 9.542 7 1.53 0 2.983-.345 4.287-.965M9.88 4.6A10.66 10.66 0 0112 5c4.477 0 8.268 2.943 9.542 7a11.02 11.02 0 01-2.363 3.78"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Error message with dismiss button -->
                    <div v-if="error"
                         class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-500 text-sm flex items-start justify-between gap-3 animate-slide-up">
                        <div class="flex items-start gap-2 flex-1">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ error }}</span>
                        </div>
                        <button
                            @click="clearError"
                            type="button"
                            class="text-red-500 hover:text-red-700 transition-colors flex-shrink-0"
                            aria-label="Dismiss error"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-2 cursor-pointer group">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="w-4 h-4 rounded border-border bg-input text-primary focus:ring-primary transition-all"
                            />
                            <span class="text-sm text-muted-foreground group-hover:text-foreground transition-colors">Remember me</span>
                        </label>
                        <router-link to="/forgot-password" class="text-sm text-primary hover:underline transition-all">
                            Forgot password?
                        </router-link>
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ loading ? 'Signing in...' : 'Sign in' }}
                    </button>
                </form>

                <p class="text-center text-sm text-muted-foreground">
                    Don't have an account?
                    <router-link to="/register" class="text-primary hover:underline font-medium transition-all">
                        Create account
                    </router-link>
                </p>
            </div>
        </div>

        <div class="hidden lg:block lg:w-1/2 relative bg-gradient-to-br from-primary/20 to-primary/5">
            <div class="absolute inset-0 flex items-center justify-center p-12">
                <div class="max-w-lg space-y-6 animate-slide-up">
                    <div class="glass-card p-8 rounded-2xl space-y-4 hover:shadow-xl transition-all duration-300">
                        <div class="w-12 h-12 bg-primary/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold">Manage Student Applications</h2>
                        <p class="text-muted-foreground">
                            Access your dashboard to track applications, manage students, and collaborate with
                            educational institutions worldwide.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div
                            class="glass-card p-6 rounded-xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <div class="text-3xl font-bold text-primary">500+</div>
                            <div class="text-sm text-muted-foreground mt-1">Applications</div>
                        </div>
                        <div
                            class="glass-card p-6 rounded-xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <div class="text-3xl font-bold text-primary">50+</div>
                            <div class="text-sm text-muted-foreground mt-1">Universities</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {ref} from 'vue';
import {useAuth} from '../composables/useAuth';
import {useTheme} from '../composables/useTheme';
import logo from '@/assets/logo.png'

export default {
    name: 'Login',
    methods: {
        logo() {
            return logo
        }
    },
    setup() {
        const {login, loading, error, clearError} = useAuth();
        const {isDark, toggleTheme} = useTheme();

        const form = ref({
            email: '',
            password: '',
            remember: false
        });
        const showPassword = ref(false);

        const handleLogin = async () => {
            await login(form.value)
        }

        return {
            form,
            handleLogin,
            loading,
            isDark,
            toggleTheme,
            showPassword,
            error,
            clearError
        };
    }
};
</script>
