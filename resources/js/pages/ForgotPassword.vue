<template>
    <div class="min-h-screen flex animate-fade-in">
        <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-8 lg:p-12">
            <div class="w-full max-w-md space-y-8 animate-slide-up">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <img :src="logo()" alt="Advisors Academy" class="h-20 w-auto mb-4"/>
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Reset Password</h1>
                        <p class="text-muted-foreground">We'll help you get back into your account</p>
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

                <!-- Step 1: Enter Email -->
                <form v-if="currentStep === 1" @submit.prevent="handleEmailSubmit" class="space-y-6">
                    <div v-if="error"
                         class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-500 text-sm flex items-start space-x-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ error }}</span>
                    </div>

                    <div class="space-y-2 group">
                        <label for="email"
                               class="text-sm font-medium transition-colors group-focus-within:text-primary">Email
                            Address</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                            placeholder="agent@example.com"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ loading ? 'Sending code...' : 'Send Reset Code' }}
                    </button>
                </form>

                <!-- Step 2: Enter Code and New Password -->
                <form v-if="currentStep === 2" @submit.prevent="handleResetSubmit" class="space-y-6">
                    <div v-if="error"
                         class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-500 text-sm flex items-start space-x-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ error }}</span>
                    </div>

                    <div class="space-y-2 group">
                        <label for="reset-code"
                               class="text-sm font-medium transition-colors group-focus-within:text-primary">Reset
                            Code</label>
                        <input
                            id="reset-code"
                            v-model="form.code"
                            type="text"
                            required
                            placeholder="Enter the code sent to your email"
                            class="w-full px-4 py-3 pr-11 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                        />
                        <p class="text-xs text-muted-foreground">Check your email for the reset code</p>
                    </div>

                    <div class="space-y-2 group">
                        <label for="new-password"
                               class="text-sm font-medium transition-colors group-focus-within:text-primary">New
                            Password</label>
                        <div class="relative">
                            <input
                                id="new-password"
                                v-model="form.new_password"
                                :type="showNewPassword ? 'text' : 'password'"
                                required
                                placeholder="••••••••"
                                class="w-full px-4 py-3 pr-11 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                            />
                            <button
                                type="button"
                                @click="showNewPassword = !showNewPassword"
                                :aria-label="showNewPassword ? 'Hide password' : 'Show password'"
                                class="absolute inset-y-0 right-0 px-3 flex items-center text-muted-foreground hover:text-foreground transition-colors"
                                tabindex="-1"
                            >
                                <svg v-if="!showNewPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
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

                    <div class="space-y-2 group">
                        <label for="confirm-password"
                               class="text-sm font-medium transition-colors group-focus-within:text-primary">Confirm
                            Password</label>
                        <div class="relative">
                            <input
                                id="confirm-password"
                                v-model="form.password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                required
                                placeholder="••••••••"
                                class="w-full px-4 py-3 pr-11 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                            />
                            <button
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
                                class="absolute inset-y-0 right-0 px-3 flex items-center text-muted-foreground hover:text-foreground transition-colors"
                                tabindex="-1"
                            >
                                <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
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

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ loading ? 'Resetting password...' : 'Reset Password' }}
                    </button>

                    <button
                        type="button"
                        @click="currentStep = 1; error = null; form.code = ''; form.new_password = ''; form.password_confirmation = '';"
                        class="w-full py-2 text-sm text-muted-foreground hover:text-foreground transition-all"
                    >
                        Back to email entry
                    </button>
                </form>

                <!-- Step 3: Success Message -->
                <div v-if="currentStep === 3" class="space-y-6 text-center">
                    <div class="flex justify-center">
                        <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold mb-2">Password Reset Successful</h2>
                        <p class="text-muted-foreground">Your password has been successfully reset. You can now log in
                            with your new password.</p>
                    </div>
                    <router-link to="/login"
                                 class="inline-block px-6 py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200">
                        Back to Login
                    </router-link>
                </div>

                <p class="text-center text-sm text-muted-foreground">
                    Remember your password?
                    <router-link to="/login" class="text-primary hover:underline font-medium transition-all">
                        Sign in
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
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold">Secure Password Reset</h2>
                        <p class="text-muted-foreground">
                            Your account security is our priority. Follow the simple steps to reset your password and
                            regain access to your agent dashboard.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div
                            class="glass-card p-6 rounded-xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-primary font-bold">1</span>
                                </div>
                                <div>
                                    <div class="font-semibold">Enter Email</div>
                                    <div class="text-sm text-muted-foreground mt-1">Provide your registered email
                                        address
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="glass-card p-6 rounded-xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-primary font-bold">2</span>
                                </div>
                                <div>
                                    <div class="font-semibold">Verify Code</div>
                                    <div class="text-sm text-muted-foreground mt-1">Enter the code sent to your email
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="glass-card p-6 rounded-xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-primary font-bold">3</span>
                                </div>
                                <div>
                                    <div class="font-semibold">Create Password</div>
                                    <div class="text-sm text-muted-foreground mt-1">Set your new password securely</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {ref} from 'vue';
import {useTheme} from '../composables/useTheme';
import {authAPI} from '../services/api';
import logo from '@/assets/logo.png'

export default {
    name: 'ForgotPassword',
    methods: {
        logo() {
            return logo
        }
    },
    setup() {
        const {isDark, toggleTheme} = useTheme();
        const currentStep = ref(1);
        const loading = ref(false);
        const error = ref(null);
        const showNewPassword = ref(false);
        const showConfirmPassword = ref(false);

        const form = ref({
            destination: 'email',
            email: '',
            code: '',
            new_password: '',
            password_confirmation: ''
        });

        const handleEmailSubmit = async () => {
            try {
                error.value = null;

                if (!form.value.email && form.value.destination === 'email') {
                    error.value = 'Please enter a valid email address.';
                    return;
                }

                loading.value = true;
                await authAPI.getOtp({
                    destination: form.value.destination,
                    email: form.value.email
                });
                currentStep.value = 2;
            } catch (err) {
                error.value = err.response?.data?.message || err.response?.data?.error.message || 'Failed to send reset code. Please try again.';
            } finally {
                loading.value = false;
            }
        };

        const handleResetSubmit = async () => {
            try {
                error.value = null;

                if (form.value.new_password !== form.value.password_confirmation) {
                    error.value = 'Passwords do not match';
                    return;
                }

                if (form.value.new_password.length < 8) {
                    error.value = 'Password must be at least 8 characters long';
                    return;
                }

                loading.value = true;
                await authAPI.resetPassword({
                    destination: 'email',
                    email: form.value.email,
                    token: form.value.code,
                    password: form.value.new_password,
                    password_confirmation: form.value.password_confirmation
                });
                currentStep.value = 3;
            } catch (err) {
                error.value = err.response?.data?.message || err.response?.data?.error.message || 'Failed to reset password. Please try again.';
            } finally {
                loading.value = false;
            }
        };

        return {
            currentStep,
            loading,
            error,
            form,
            isDark,
            toggleTheme,
            showNewPassword,
            showConfirmPassword,
            handleEmailSubmit,
            handleResetSubmit
        };
    }
};
</script>
