<template>
    <div class="min-h-screen bg-background animate-fade-in">
        <main class="container mx-auto px-4 sm:px-6 py-6 sm:py-8 max-w-4xl">

            <form @submit.prevent="handleSubmit" class="space-y-8 animate-fade-in">
                <div v-if="error" class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-500 text-sm">
                    {{ error }}
                </div>

                <div v-if="success" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                    <div class="bg-card rounded-xl p-6 sm:p-8 mx-4 text-center border border-border shadow-2xl animate-scale-in">
                        <svg class="w-16 h-16 text-emerald-500 mx-auto mb-4 animate-spin" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <h3 class="text-xl font-bold mb-2">{{ successMessage }}</h3>
                        <p class="text-muted-foreground text-sm">Redirecting to dashboard...</p>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="glass-card rounded-xl p-4 sm:p-6 animate-slide-up">
                    <h2 class="text-lg font-bold mb-6">Personal Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 group">
                            <label for="first_name" class="text-sm font-medium">First Name *</label>
                            <input
                                id="first_name"
                                v-model="form.first_name"
                                type="text"
                                required
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                placeholder="John"
                            />
                        </div>

                        <div class="space-y-2 group">
                            <label for="middle_name" class="text-sm font-medium">Middle Name</label>
                            <input
                                id="middle_name"
                                v-model="form.middle_name"
                                type="text"
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                placeholder="Optional"
                            />
                        </div>

                        <div class="space-y-2 group">
                            <label for="last_name" class="text-sm font-medium">Last Name *</label>
                            <input
                                id="last_name"
                                v-model="form.last_name"
                                type="text"
                                required
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                placeholder="Doe"
                            />
                        </div>

                        <div class="space-y-2 group">
                            <label for="gender" class="text-sm font-medium">Gender *</label>
                            <select
                                id="gender"
                                v-model="form.gender"
                                required
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                            >
                                <option value="">Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="space-y-2 group">
                            <label for="email" class="text-sm font-medium">Email *</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                placeholder="student@email.com"
                            />
                        </div>

                        <div class="space-y-2 group">
                            <label for="phone_number" class="text-sm font-medium">Phone Number *</label>
                            <input
                                id="phone_number"
                                v-model="form.phone_number"
                                type="tel"
                                required
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                placeholder="+1234567890"
                            />
                        </div>

                        <div class="space-y-2 group">
                            <label for="country" class="text-sm font-medium">Country *</label>
                            <input
                                id="country"
                                v-model="form.country"
                                type="text"
                                required
                                class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                placeholder="United States"
                            />
                        </div>

                        <DatePickerField
                            id="birth_date"
                            v-model="form.birth_date"
                            label="Date of Birth"
                            :required="true"
                            :max-date="maxBirthDate"
                        />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col-reverse sm:flex-row gap-3 sm:gap-4 pt-6">
                    <router-link to="/students"
                                 class="flex-1 px-6 py-3 border border-border rounded-lg font-medium hover:bg-muted transition-all text-center">
                        Cancel
                    </router-link>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="flex-1 px-6 py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <span v-if="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Submitting...
                        </span>
                        <span v-else>Create Student Profile</span>
                    </button>
                </div>
            </form>
        </main>
    </div>
</template>

<script>
import {ref, computed} from 'vue'
import {useRouter} from 'vue-router'
import {studentsAPI} from '../services/api'
import DatePickerField from '../components/DatePickerField.vue'

export default {
    name: 'CreateStudent',
    components: {DatePickerField},
    setup() {
        const router = useRouter()

        const error = ref('')
        const loading = ref(false)
        const success = ref(false)
        const successMessage = ref('Student profile created successfully!')

        const form = ref({
            first_name: '',
            middle_name: '',
            last_name: '',
            gender: '',
            email: '',
            phone_number: '',
            birth_date: '',
            country: '',
        })

        const maxBirthDate = computed(() => {
            const date = new Date()
            date.setFullYear(date.getFullYear() - 16)
            return date.toISOString().split('T')[0]
        })

        const handleSubmit = async () => {
            try {
                loading.value = true
                error.value = null
                success.value = false

                const response = await studentsAPI.create(form.value)

                successMessage.value = response.data?.message || 'Student profile created successfully!'
                success.value = true

                setTimeout(() => {
                    router.push('/students')
                }, 2000)
            } catch (err) {
                console.error('Error creating student:', err)
                error.value = err.response?.data?.message || 'Failed to create student profile'
                setTimeout(() => error.value = null, 5000)
            } finally {
                loading.value = false
            }
        }

        return {
            form,
            error,
            loading,
            success,
            successMessage,
            maxBirthDate,
            handleSubmit
        }
    }
}
</script>