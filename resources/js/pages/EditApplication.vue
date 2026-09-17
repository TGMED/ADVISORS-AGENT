<template>
    <div class="min-h-screen bg-background animate-fade-in">
        <main class="container mx-auto px-4 sm:px-6 py-6 sm:py-8">
            <div class="mb-8 animate-slide-up">
                <div class="flex items-center gap-4 mb-4">
                    <router-link :to="`/applications`"
                                 class="text-primary hover:underline flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Application
                    </router-link>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">Edit Application</h1>
                <p class="text-muted-foreground">Update application information and status</p>
            </div>

            <div v-if="loading" class="flex items-center justify-center py-12">
                <div class="flex flex-col items-center gap-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                    <p class="text-muted-foreground">Loading Application...</p>
                </div>
            </div>

            <div v-else-if="error"
                 class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-500 text-sm mb-6">
                {{ error }}
            </div>

            <div v-else class="glass-card rounded-xl p-4 sm:p-6 animate-slide-up">
                <form @submit.prevent="updateApplication" class="space-y-6">
                    <!-- Student Information (Read-only) -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Student Information
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-muted-foreground">Full Name</label>
                                <p class="font-medium">{{ form.studentName }}</p>
                            </div>

                            <div>
                                <label class="text-sm text-muted-foreground">Phone Number</label>
                                <p class="font-medium">{{ form.studentPhone }}</p>
                            </div>

                            <div>
                                <label class="text-sm text-muted-foreground">Email</label>
                                <p class="font-medium">{{ form.studentEmail }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Application Status -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Application Status *</label>

                        <!-- <CHANGE> Replace hardcoded options with dynamically fetched statuses -->
                        <select
                            v-model="form.status" required
                            class="w-full px-4 py-2.5 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                            <option value="">Select Status</option>
                            <option v-for="status in applicationStatuses" :key="status.id" :value="status.status_code">
                                {{ status.status_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Course (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Course</label>
                        <p class="font-medium text-muted-foreground">{{ form.courseName }}</p>
                    </div>

                    <!-- Class of Degree -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Class of Degree</label>
                        <input v-model="form.classOfDegree" type="text" placeholder="e.g., First Class, Upper Second"
                               disabled
                               class="w-full px-4 py-2.5 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"/>
                    </div>

                    <!-- Schools of Choice (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Schools of Choice</label>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="(school, idx) in form.schools" :key="idx"
                                  class="inline-block bg-secondary text-secondary-foreground px-3 py-2 rounded-lg">
                                {{ school }}
                            </span>
                            <span v-if="form.schools.length === 0"
                                  class="text-muted-foreground">No schools selected</span>
                        </div>
                    </div>

                    <!-- Countries of Preference (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Countries of Preference</label>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="(country, idx) in form.countries" :key="idx"
                                  class="inline-block bg-secondary text-secondary-foreground px-3 py-2 rounded-lg">
                                {{ country }}
                            </span>
                            <span v-if="form.countries.length === 0"
                                  class="text-muted-foreground">No countries selected</span>
                        </div>
                    </div>

                    <!-- Notes/Comments -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Notes/Comments</label>
                        <textarea v-model="form.notes" rows="4" placeholder="Add any additional notes or comments..."
                                  class="w-full px-4 py-2.5 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none"></textarea>
                    </div>

                    <!-- Success Message -->
                    <div v-if="successMessage"
                         class="p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-700 text-sm">
                        {{ successMessage }}
                    </div>

                    <!-- Error Message -->
                    <div v-if="updateError"
                         class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-500 text-sm">
                        {{ updateError }}
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col-reverse sm:flex-row gap-3 sm:gap-4 justify-end pt-4 border-t border-border">
                        <router-link :to="`/applications/${applicationId}`"
                                     class="px-6 py-2.5 bg-muted text-foreground rounded-lg font-medium hover:bg-muted/80 transition-all duration-200 text-center">
                            Cancel
                        </router-link>

                        <button type="submit" :disabled="submitting"
                                class="px-6 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <span v-if="submitting">Updating...</span>
                            <span v-else>Update Application</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import {useRoute, useRouter} from 'vue-router';
import {applicationsAPI} from '../services/api.js';

const route = useRoute();
const router = useRouter();
const applicationId = route.params.id;
const loading = ref(true);
const submitting = ref(false);
const error = ref(null);
const updateError = ref(null);
const successMessage = ref(null);
const applicationStatuses = ref([]);

const form = ref({
    studentName: '',
    studentEmail: '',
    courseName: '',
    status: '',
    classOfDegree: '',
    studentPhone: '',
    schools: [],
    countries: [],
    notes: ''
});

const fetchApplication = async () => {
    try {
        loading.value = true;
        error.value = null;

        const response = await applicationsAPI.getCounselorApplicationById(applicationId);
        const app = response.data.data || response.data;

        form.value = {
            studentName: `${app.student?.first_name || ''} ${app.student?.middle_name || ''} ${app.student?.last_name || ''}`.trim(),
            studentEmail: app.student?.email || 'N/A',
            studentPhone: app.student?.phone_number || 'N/A',
            courseName: app.course?.name || 'N/A',
            status: app.status,
            classOfDegree: app.class_of_degree || '',
            schools: app.schools_of_choice_details?.map(s => s.name) || [],
            countries: app.country_of_preference_details?.map(c => c.name) || [],
            notes: app.notes || ''
        };
    } catch (err) {
        console.error('Error fetching application:', err);
        error.value = 'Failed to load application. Please try again.';
    } finally {
        loading.value = false;
    }
};

const updateApplication = async () => {
    try {
        submitting.value = true;
        updateError.value = null;
        successMessage.value = null;

        const payload = {
            status: form.value.status,
            class_of_degree: form.value.classOfDegree,
            notes: form.value.notes
        };

        await applicationsAPI.updateCounselorApplicationStatus(applicationId, payload);
        successMessage.value = 'Application updated successfully!';

        setTimeout(() => {
            router.push(`/applications/${applicationId}`);
        }, 2000);
    } catch (err) {
        console.error('Error updating application:', err);
        updateError.value = err.response?.data?.message || 'Failed to update application. Please try again.';
    } finally {
        submitting.value = false;
    }
};

const fetchApplicationStatuses = async () => {
    try {
        const response = await applicationsAPI.getStatus();
        console.log(response);
        applicationStatuses.value = response.data.data || response.data;
    } catch (err) {
        console.error('Error fetching application statuses:', err);
        return [];
    }
};

onMounted(() => {
    fetchApplication();
    fetchApplicationStatuses();
});
</script>
