<template>
    <div class="min-h-screen flex animate-fade-in">
        <div class="hidden lg:block lg:w-1/2 relative bg-linear-to-br from-primary/20 to-primary/5">
            <div class="absolute inset-0 flex items-center justify-center p-12">
                <div class="max-w-lg space-y-6 animate-slide-up">
                    <div class="glass-card p-8 rounded-2xl space-y-4 hover:shadow-xl transition-all duration-300">
                        <div class="w-12 h-12 bg-primary/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold">Join Our Network</h2>
                        <p class="text-muted-foreground">
                            Become a trusted education agent and help students achieve their dreams of studying abroad.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div v-for="(feature, index) in features" :key="index"
                             class="flex items-center space-x-3 text-sm animate-slide-up"
                             :style="{ animationDelay: `${index * 0.1}s` }">
                            <div
                                class="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-muted-foreground">{{ feature }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-8 lg:p-12">
            <div class="w-full max-w-md space-y-8 animate-slide-up">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Create your account</h1>
                        <p class="text-muted-foreground">Start managing student applications today</p>
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

                <form @submit.prevent="handleRegister" class="space-y-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2 group">
                                <label for="first_name"
                                       class="text-sm font-medium transition-colors group-focus-within:text-primary">First
                                    Name</label>
                                <input
                                    id="first_name"
                                    v-model="form.first_name"
                                    type="text"
                                    required
                                    :disabled="loading"
                                    class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    placeholder="John"
                                />
                            </div>
                            <div class="space-y-2 group">
                                <label for="last_name"
                                       class="text-sm font-medium transition-colors group-focus-within:text-primary">Last
                                    Name</label>
                                <input
                                    id="last_name"
                                    v-model="form.last_name"
                                    type="text"
                                    required
                                    :disabled="loading"
                                    class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    placeholder="Doe"
                                />
                            </div>
                        </div>

                        <div class="space-y-2 group">
                            <label for="agency_name"
                                   class="text-sm font-medium transition-colors group-focus-within:text-primary">Agency
                                Name</label>
                            <input
                                id="agency_name"
                                v-model="form.agency_name"
                                type="text"
                                required
                                :disabled="loading"
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                placeholder="Your Agency Ltd"
                            />
                        </div>

                        <div class="space-y-2 group">
                            <label for="business_registration_number"
                                   class="text-sm font-medium transition-colors group-focus-within:text-primary">Business
                                Registration Number</label>
                            <input
                                id="business_registration_number"
                                v-model="form.business_registration_number"
                                type="text"
                                required
                                :disabled="loading"
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                placeholder="BRN123456"
                            />
                        </div>

                        <div class="space-y-2 group">
                            <label for="email"
                                   class="text-sm font-medium transition-colors group-focus-within:text-primary">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                :disabled="loading"
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                placeholder="agent@example.com"
                            />
                        </div>

                        <div class="space-y-2 group">
                            <label for="phone"
                                   class="text-sm font-medium transition-colors group-focus-within:text-primary">Phone
                                Number</label>
                            <input
                                id="phone"
                                v-model="form.phone"
                                type="number"
                                required
                                :disabled="loading"
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                placeholder="09033226655"
                            />
                        </div>

                        <div class="space-y-2 group">
                            <label for="bdm_officer_id"
                                   class="text-sm font-medium transition-colors group-focus-within:text-primary">
                                Business Development Officer
                            </label>
                            <select
                                id="bdm_officer_id"
                                v-model="form.bdm_officer_id"
                                :disabled="loading"
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <option disabled value="">Select a BDM Officer</option>
                                <option v-for="officer in bdmOfficers" :key="officer.id" :value="officer.id">
                                    {{ officer.first_name }} {{ officer.last_name }}
                                </option>
                            </select>
                        </div>


                        <div class="space-y-2 group">
                            <label for="password"
                                   class="text-sm font-medium transition-colors group-focus-within:text-primary">Password</label>
                            <div class="relative">
                                <input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    :disabled="loading"
                                    class="w-full pr-12 px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    placeholder="••••••••"
                                />

                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    :disabled="loading"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-muted-foreground hover:text-foreground disabled:opacity-50 disabled:cursor-not-allowed"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                >
                                    <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.563-4.351M6.07 6.07A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.243 5.147M3 3l18 18"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9.88 9.88A3 3 0 0012 15a3 3 0 002.12-.88"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2 group">
                            <label for="password_confirmation"
                                   class="text-sm font-medium transition-colors group-focus-within:text-primary">Confirm
                                Password</label>
                            <div class="relative">
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    :type="showConfirm ? 'text' : 'password'"
                                    required
                                    :disabled="loading"
                                    class="w-full pr-12 px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    @click="showConfirm = !showConfirm"
                                    :disabled="loading"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-muted-foreground hover:text-foreground disabled:opacity-50 disabled:cursor-not-allowed"
                                    :aria-label="showConfirm ? 'Hide confirm password' : 'Show confirm password'"
                                >
                                    <svg v-if="!showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.563-4.351M6.07 6.07A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.243 5.147M3 3l18 18"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9.88 9.88A3 3 0 0012 15a3 3 0 002.12-.88"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <label class="flex items-start space-x-2 cursor-pointer group">
                        <input
                            v-model="form.is_terms_and_condition_accepted"
                            type="checkbox"
                            required
                            :disabled="loading"
                            class="w-4 h-4 mt-1 rounded border-border bg-input text-primary focus:ring-primary transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        />
                        <span class="text-sm text-muted-foreground group-hover:text-foreground transition-colors">
                            I agree to the <a href="#" class="text-primary hover:underline">Terms and Conditions</a> and <a
                            href="#" class="text-primary hover:underline">Privacy Policy</a>
                        </span>
                    </label>

                    <!-- Display error message as toast instead of reloading -->
                    <div v-if="error"
                         class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-500 text-sm">
                        {{ error }}
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <span v-if="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating account...
                        </span>
                        <span v-else>Create account</span>
                    </button>
                </form>

                <p class="text-center text-sm text-muted-foreground">
                    Already have an account?
                    <router-link to="/login" class="text-primary hover:underline font-medium transition-all">
                        Sign in
                    </router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>

<script>
import {onMounted, ref} from 'vue';
import {useAuth} from '../composables/useAuth';
import {useTheme} from '../composables/useTheme';
import {authAPI} from "../services/api.js";

export default {
    name: 'Register',
    setup() {
        const {register, error} = useAuth();
        const {isDark, toggleTheme} = useTheme();

        const form = ref({
            first_name: '',
            last_name: '',
            agency_name: '',
            business_registration_number: '',
            email: '',
            phone: '',
            bdm_officer_id: '',
            password: '',
            password_confirmation: '',
            role_name: 'agent',
            is_terms_and_condition_accepted: false
        });

        const showPassword = ref(false);
        const showConfirm = ref(false);
        const loading = ref(false);
        const bdmOfficers = ref([]);

        const features = [
            'Access to 50+ partner universities',
            'Real-time application tracking',
            'Dedicated support team'
        ];

        const handleRegister = async () => {
            loading.value = true;
            try {
                //await new Promise(resolve => setTimeout(resolve, 2000));
                await register(form.value);
            } finally {
                loading.value = false;
            }
        };

        const fetchBdmOfficers = async () => {
            try {
                const response = await authAPI.getBDMOfficers()
                console.log(response)
                bdmOfficers.value = response.data?.data || []
            } catch (err) {
                console.error('Failed to fetch BDM officers:', err);
            }
        };

        onMounted(() => {
            fetchBdmOfficers()
        });

        return {
            form,
            features,
            handleRegister,
            loading,
            error,
            isDark,
            toggleTheme,
            showPassword,
            showConfirm,
            bdmOfficers
        };
    }
};
</script>
