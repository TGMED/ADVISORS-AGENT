<template>
    <div class="min-h-screen bg-background animate-fade-in">
        <main class="container mx-auto px-4 sm:px-6 py-6 sm:py-8">
            <div class="mb-8 animate-slide-up">
                <div class="flex items-center gap-4 mb-4">
                    <router-link to="/applications" class="text-primary hover:underline flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Applications
                    </router-link>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">Application Details</h1>
                <p class="text-muted-foreground">View application information</p>
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
                <!-- Student Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Student Information
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="text-sm text-muted-foreground">Full Name</label>
                            <p class="font-medium">{{ application.studentName }}</p>
                        </div>

                        <div>
                            <label class="text-sm text-muted-foreground">Phone Number</label>
                            <p class="font-medium">{{ application.studentPhone }}</p>
                        </div>

                        <div>
                            <label class="text-sm text-muted-foreground">Email</label>
                            <p class="font-medium">{{ application.studentEmail }}</p>
                        </div>
                    </div>
                </div>

                <!-- Application Details -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Application Details
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-muted-foreground">Course</label>
                            <p class="font-medium">{{ application.courseName }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-muted-foreground">Class of Degree</label>
                            <p class="font-medium">{{ application.classOfDegree }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-muted-foreground">Status</label>
                            <div>
                                <span :class="getStatusClass(application.status)">
                                    {{ application.status }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-muted-foreground">Applied Date</label>
                            <p class="font-medium">{{ application.submittedAt }}</p>
                        </div>
                    </div>
                </div>

                <!-- Schools of Choice -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Schools of Choice
                    </h2>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="(school, idx) in application.schools" :key="idx"
                              class="inline-block bg-secondary text-secondary-foreground px-3 py-2 rounded-lg">
                            {{ school }}
                        </span>
                        <span v-if="application.schools.length === 0"
                              class="text-muted-foreground">No schools selected</span>
                    </div>
                </div>

                <!-- Countries of Preference -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Countries of Preference
                    </h2>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="(country, idx) in application.countries" :key="idx"
                              class="inline-block bg-secondary text-secondary-foreground px-3 py-2 rounded-lg">
                            {{ country }}
                        </span>
                        <span v-if="application.countries.length === 0" class="text-muted-foreground">No countries selected</span>
                    </div>
                </div>

                <!-- Application Documents -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Application Documents

                        <button
                            v-if="!isCounselor"
                            @click="showDocumentModal = true"
                            class="text-blue-600 hover:underline flex items-center gap-1"
                            title="Manage Documents">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    </h2>
                    <div v-if="application.documents && application.documents.length > 0" class="space-y-3">
                        <div v-for="(doc, idx) in application.documents" :key="idx"
                             class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-secondary/50 rounded-lg hover:bg-secondary/70 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-8 h-8 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <div class="min-w-0">
                                    <p class="font-medium break-all">{{ getDocumentName(doc) }}</p>
                                    <p class="text-sm text-muted-foreground">Document {{ idx + 1 }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a :href="getDocumentUrl(doc)"
                                   target="_blank"
                                   class="px-3 py-1.5 text-sm bg-primary/20 text-primary rounded hover:bg-primary/30 transition-colors font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4.5c-7 0-10 7.5-10 7.5s3 7.5 10 7.5 10-7.5 10-7.5-3-7.5-10-7.5z"/>
                                        <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"/>
                                    </svg>
                                    View
                                </a>

                                <a :href="getDocumentUrl(doc)"
                                   target="_blank"
                                   download
                                   class="px-3 py-1.5 text-sm bg-primary/20 text-primary rounded hover:bg-primary/30 transition-colors font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-muted-foreground p-4 bg-secondary/30 rounded-lg">
                        No documents uploaded
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <router-link
                        v-if="isCounselor"
                        :to="`/applications/${applicationId}/edit`"
                        class="px-6 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-105 text-center">
                        Edit Application
                    </router-link>
                </div>
            </div>
        </main>

        <!-- Document Management Modal -->
        <div v-if="showDocumentModal"
             class="fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4 overflow-y-auto">
            <div
                class="bg-card rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-border shadow-2xl">
                <!-- Modal Header -->
                <div
                    class="sticky top-0 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-border p-4 sm:p-6 flex items-center justify-between backdrop-blur-2xl">
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold">Manage Documents</h2>
                        <p class="text-sm text-muted-foreground mt-1">Add, view, or remove application documents</p>
                    </div>
                    <button type="button" @click="closeModal"
                            class="text-muted-foreground hover:text-foreground transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4 sm:p-6 space-y-6">
                    <!-- Upload Section -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-primary uppercase tracking-wide">Add New Documents</h3>
                        <div
                            class="border-2 border-dashed border-border rounded-lg p-6 text-center hover:border-primary transition-colors bg-muted/30">
                            <input
                                type="file"
                                ref="fileInput"
                                @change="handleFileSelect"
                                multiple
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                class="hidden"
                            />
                            <button
                                type="button"
                                @click="$refs.fileInput.click()"
                                class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v16m8-8H4"/>
                                </svg>
                                Choose Files
                            </button>
                            <p class="text-sm text-muted-foreground mt-2">PDF, DOC, DOCX, JPG, PNG (Max 10MB each)</p>
                        </div>

                        <!-- New Files Preview -->
                        <div v-if="newFiles.length > 0" class="space-y-2">
                            <p class="text-xs text-muted-foreground font-medium mb-2">New files to upload:</p>
                            <div v-for="(file, idx) in newFiles" :key="idx"
                                 class="p-3 bg-muted/30 rounded-lg border border-border/50 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="min-w-0">
                                        <p class="font-medium text-sm break-all">{{ file.name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ formatFileSize(file.size) }}</p>
                                    </div>
                                </div>
                                <button type="button" @click="removeNewFile(idx)"
                                        class="text-red-500 hover:text-red-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Documents -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-primary uppercase tracking-wide">Existing Documents</h3>
                        <div v-if="application.documents && application.documents.length > 0" class="space-y-2">
                            <div v-for="(doc, idx) in application.documents" :key="idx"
                                 class="p-3 bg-muted/30 rounded-lg border border-border/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <div class="min-w-0">
                                        <p class="font-medium text-sm break-all">{{ getDocumentName(doc) }}</p>
                                        <p class="text-xs text-muted-foreground">Document {{ idx + 1 }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a :href="getDocumentUrl(doc)"
                                       target="_blank"
                                       class="px-3 py-1.5 text-sm bg-primary/20 text-primary rounded hover:bg-primary/30 transition-colors font-medium">
                                        View
                                    </a>
                                    <button
                                        type="button"
                                        @click="deleteDocument(doc, idx)"
                                        :disabled="deletingDoc === idx"
                                        class="px-3 py-1.5 text-sm bg-red-500/20 text-red-500 rounded hover:bg-red-500/30 transition-colors disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                                        {{ deletingDoc === idx ? 'Deleting...' : 'Delete' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="p-4 bg-muted/30 rounded-lg border border-border/50 text-center">
                            <p class="text-sm text-muted-foreground">No existing documents</p>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div v-if="successMessage"
                         class="p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-700 text-sm">
                        {{ successMessage }}
                    </div>

                    <!-- Error Message -->
                    <div v-if="documentError"
                         class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-500 text-sm">
                        {{ documentError }}
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3 p-4 sm:p-6 border-t border-border">
                    <button
                        type="button"
                        @click="closeModal"
                        class="px-6 py-3 border border-border rounded-lg font-medium hover:bg-muted transition-all duration-200">
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="uploadNewDocuments"
                        :disabled="newFiles.length === 0 || uploading"
                        class="px-6 py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <svg v-if="uploading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>
                            {{
                                uploading ? 'Uploading...' : `Upload ${newFiles.length} Document${newFiles.length !== 1 ? 's' : ''}`
                            }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import {useRoute} from 'vue-router';
import {applicationsAPI} from '../services/api.js';
import {useAuth} from "../composables/useAuth.js";

const route = useRoute();
const applicationId = route.params.id;
const loading = ref(true);
const error = ref(null);
const application = ref({});
const {isCounselor} = useAuth();

// Document modal states
const showDocumentModal = ref(false);
const newFiles = ref([]);
const fileInput = ref(null);
const uploading = ref(false);
const deletingDoc = ref(null);
const documentError = ref(null);
const successMessage = ref(null);

const getDocumentName = (doc) => doc?.name || 'Document';
const getDocumentUrl = (doc) => doc?.url || '#';

const statusColorMap = {
    document_submission: 'bg-blue-500/20 text-blue-700',
    document_in_check: 'bg-yellow-500/20 text-yellow-700',
    document_check_complete: 'bg-green-500/20 text-green-700',
    application_in_process: 'bg-blue-500/20 text-blue-700',
    application_done_successfully: 'bg-green-500/20 text-green-700',
    conditional_offers_received: 'bg-purple-500/20 text-purple-700',
    conditional_offers_accepted: 'bg-purple-700/20 text-purple-900',
    unconditional_offers_received: 'bg-indigo-500/20 text-indigo-700',
    unconditional_offers_accepted: 'bg-indigo-700/20 text-indigo-900',
    school_fees_payment_stage: 'bg-orange-500/20 text-orange-700',
    cas_ceo_loa_i20_stage: 'bg-teal-500/20 text-teal-700',
    visa_application: 'bg-blue-500/20 text-blue-700',
    visa_application_granted: 'bg-green-500/20 text-green-700',
    application_rejected: 'bg-red-500/20 text-red-700',
    pending: 'bg-gray-500/20 text-gray-700',
};

const getStatusClass = (statusCode) => {
    const baseClass = 'px-3 py-1 rounded-full text-xs font-medium';
    return `${baseClass} ${statusColorMap[statusCode] || 'bg-gray-500/20 text-gray-700'}`;
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const handleFileSelect = (event) => {
    const files = Array.from(event.target.files);
    const maxSize = 10 * 1024 * 1024; // 10MB

    const validFiles = files.filter(file => {
        if (file.size > maxSize) {
            documentError.value = `File ${file.name} is too large. Max size is 10MB.`;
            return false;
        }
        return true;
    });

    newFiles.value.push(...validFiles);
    event.target.value = '';
};

const removeNewFile = (index) => {
    newFiles.value.splice(index, 1);
};

const uploadNewDocuments = async () => {
    if (newFiles.value.length === 0) return;

    try {
        uploading.value = true;
        documentError.value = null;
        successMessage.value = null;

        const formData = new FormData();
        formData.append("update_type", "update");
        newFiles.value.forEach((file, index) => {
            formData.append(`application_documents[${index}]`, file);
        });

        await applicationsAPI.updateDocument(applicationId, formData);
        await fetchApplication();

        successMessage.value = 'Document updated successfully!';
        newFiles.value = [];
    } catch (err) {
        console.error("Error uploading documents:", err);
        documentError.value = "Failed to upload documents. Please try again.";
    } finally {
        uploading.value = false;
    }
};

const deleteDocument = async (doc, index) => {
    if (!confirm('Are you sure you want to delete this document?')) return;

    try {
        deletingDoc.value = index;
        documentError.value = null;
        successMessage.value = null;

        await applicationsAPI.updateDocument(applicationId, {update_type: 'delete', document_name: doc.name});
        await fetchApplication();

        successMessage.value = 'Document deleted successfully!';
    } catch (err) {
        console.error('Error deleting document:', err);
        documentError.value = 'Failed to delete document. Please try again.';
    } finally {
        deletingDoc.value = null;
    }
};

const closeModal = () => {
    showDocumentModal.value = false;
    newFiles.value = [];
    documentError.value = null;
};

const fetchApplication = async () => {
    try {
        loading.value = true;
        error.value = null;

        const viewApplicationPromise = isCounselor.value
            ? applicationsAPI.getCounselorApplicationById(applicationId)
            : applicationsAPI.getOne(applicationId);

        const [viewApplicationRes] = await Promise.all([
            viewApplicationPromise.catch(() => ({data: []})),
        ]);

        const app = viewApplicationRes.data.data || viewApplicationRes.data;

        application.value = {
            id: app.id,
            courseName: app.course_name || 'N/A',
            studentName: `${app.student?.first_name || ''} ${app.student?.middle_name || ''} ${app.student?.last_name || ''}`.trim(),
            studentEmail: app.student?.email || 'N/A',
            studentPhone: app.student?.phone_number || 'N/A',
            status: app.status,
            submittedAt: app.created_at ? new Date(app.created_at).toLocaleDateString() : 'N/A',
            classOfDegree: app.class_of_degree || 'N/A',
            schools: app.schools_of_choice_details?.map(s => s.name) || [],
            countries: app.country_of_preference_details?.map(c => c.name) || [],

            documents: app.application_documents?.map(path => ({
                name: path.split('/').pop(),
                url: `/storage/${path}`
            })) || [],
        };
    } catch (err) {
        console.error('Error fetching application:', err);
        error.value = 'Failed to load application. Please try again.';
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchApplication();
});
</script>
