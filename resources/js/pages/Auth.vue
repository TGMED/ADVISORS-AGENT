<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
        <div class="max-w-6xl w-full grid md:grid-cols-2 gap-8 items-center">
            Left side - Welcome content
            <div class="text-center md:text-left space-y-6">
                <div class="space-y-4">
                    <h1 class="text-4xl sm:text-5xl font-bold text-gray-900">
                        Student Management System
                    </h1>
                    <p class="text-xl text-gray-600">
                        Streamline your student application process with our comprehensive management platform
                    </p>
                </div>

                <div class="space-y-4 pt-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Efficient Application Management</h3>
                            <p class="text-gray-600">Track and manage student applications in one place</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Real-time Dashboard</h3>
                            <p class="text-gray-600">Monitor application status and analytics instantly</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Secure & Reliable</h3>
                            <p class="text-gray-600">Your data is protected with enterprise-grade security</p>
                        </div>
                    </div>
                </div>
            </div>

            Right side - Auth options
            <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="text-3xl font-bold text-gray-900">Get Started</h2>
                    <p class="text-gray-600">Choose an option to continue</p>
                </div>

                <div class="space-y-4">
                    <button
                        @click="navigateTo('login')"
                        class="w-full py-4 px-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 group"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Sign In to Your Account
                    </button>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">or</span>
                        </div>
                    </div>

                    <button
                        @click="navigateTo('register')"
                        class="w-full py-4 px-6 bg-white hover:bg-gray-50 text-blue-600 font-semibold rounded-lg border-2 border-blue-600 transition-colors duration-200 flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Create New Account
                    </button>
                </div>

                <div class="pt-4 text-center text-sm text-gray-600">
                    <p>By continuing, you agree to our Terms of Service and Privacy Policy</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
</script>
