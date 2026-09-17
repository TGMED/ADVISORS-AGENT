<template>
    <!-- add overflow-hidden when modals are open to prevent background scrolling -->
    <div class="min-h-screen bg-background animate-fade-in"
         :class="{ 'overflow-hidden': showDetailsModal || showEditModal || showApplicationsModal }">
        <main class="container mx-auto px-4 sm:px-6 py-6 sm:py-8">
            <div class="mb-8 animate-slide-up">
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">Student List & Applications</h1>
                <p class="text-muted-foreground">Manage and track all students & applications</p>
            </div>

            <div v-if="loading" class="flex items-center justify-center py-12">
                <!-- Improved loading spinner styling -->
                <div class="flex flex-col items-center gap-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                    <p class="text-muted-foreground">Loading Students...</p>
                </div>
            </div>

            <div v-else-if="error"
                 class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-500 text-sm mb-6">
                {{ error }}
                <button @click="fetchStudents" class="ml-4 underline hover:no-underline">Retry</button>
            </div>

            <div v-else class="glass-card rounded-xl p-4 sm:p-6 animate-slide-up" style="animation-delay: 0.1s;">
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 mb-6">
                    <div
                        class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-row lg:items-center gap-3">
                        <div class="relative sm:col-span-2 lg:flex-1 lg:min-w-48">
                            <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search by name, email, or course..."
                                class="w-full pl-10 pr-4 py-2.5 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                            />
                        </div>
                        <select v-model="countryFilter"
                                class="px-4 py-2.5 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200">
                            <option value="">All Countries</option>
                            <option value="United Kingdom">United Kingdom</option>
                            <option value="Canada">Canada</option>
                            <option value="Australia">Australia</option>
                            <option value="United States">United States</option>
                        </select>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-muted-foreground whitespace-nowrap">From:</label>
                            <input
                                v-model="startDate"
                                type="date"
                                class="px-3 py-2.5 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-sm w-full min-w-0 lg:w-auto"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-muted-foreground whitespace-nowrap">To:</label>
                            <input
                                v-model="endDate"
                                type="date"
                                class="px-3 py-2.5 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-sm w-full min-w-0 lg:w-auto"
                            />
                        </div>
                        <button
                            @click="applyFilter"
                            class="px-6 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-105 flex items-center space-x-2 justify-center"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <span>Filter</span>
                        </button>
                    </div>

                    <div v-if="!isCounselor" class="grid grid-cols-1 sm:grid-cols-2 gap-3 xl:flex">
                        <router-link
                            to="/students/create"
                            class="px-6 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-105 flex items-center space-x-2 justify-center whitespace-nowrap"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Add Student</span>
                        </router-link>

                        <router-link
                            to="/applications/create"
                            class="px-6 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-105 flex items-center space-x-2 justify-center whitespace-nowrap"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>New Application</span>
                        </router-link>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 mb-6">
                    <div v-for="(stat, index) in stats" :key="index"
                         class="p-4 bg-muted/50 rounded-lg border border-border hover:border-primary/50 hover:shadow-lg hover:-translate-y-1 transition-all duration-200 cursor-pointer">
                        <div class="text-xl sm:text-2xl font-bold mb-1">{{ stat.value }}</div>
                        <div class="text-sm text-muted-foreground">{{ stat.label }}</div>
                    </div>
                </div>

                <!-- Mobile/tablet card list -->
                <div class="lg:hidden space-y-3">
                    <div v-for="student in students" :key="student.id"
                         class="p-4 bg-muted/30 rounded-lg border border-border space-y-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-primary text-sm font-medium">{{ student.initials }}</span>
                            </div>
                            <div class="min-w-0">
                                <div class="font-medium break-words">{{ student.name }}</div>
                                <div class="text-sm text-muted-foreground break-all">{{ student.email }}</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-muted-foreground mb-1">Gender</p>
                                <p>{{ student.gender || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground mb-1">Phone</p>
                                <p class="break-all">{{ student.phone || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground mb-1">Country</p>
                                <p>{{ student.country || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground mb-1">Enrolled Date</p>
                                <p>{{ student.date }}</p>
                            </div>
                        </div>
                        <button
                            @click="viewApplications(student.id)"
                            class="w-full sm:w-auto px-4 py-2 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>View Applications</span>
                        </button>
                    </div>
                </div>

                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                        <tr class="border-b border-border">
                            <th class="text-left py-4 px-4 text-sm font-semibold">Student Full Name</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold">Email</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold">Gender</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold">Phone</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold">Country</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold">Enrolled Date</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold">Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr
                            v-for="student in students"
                            :key="student.id"
                            class="border-b border-border hover:bg-muted/50 transition-all duration-200"
                        >
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-primary text-sm font-medium">{{ student.initials }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ student.name }}</div>
                                        <!--<div class="text-sm text-muted-foreground">{{ student.email }}</div>-->
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-sm text-muted-foreground">{{ student.email }}</td>
                            <td class="py-4 px-4 text-sm text-muted-foreground">{{ student.gender }}</td>
                            <td class="py-4 px-4 text-sm text-muted-foreground">{{ student.phone }}</td>
                            <td class="py-4 px-4 text-sm text-muted-foreground">{{ student.country }}</td>
                            <td class="py-4 px-4 text-sm text-muted-foreground">{{ student.date }}</td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-2">
                                    <!-- Replaced View Details and Edit buttons with View Applications button -->
                                    <button
                                        @click="viewApplications(student.id)"
                                        class="px-4 py-2 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-105 animate-fade-in whitespace-nowrap"
                                        title="View Applications">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>View Applications</span>
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="students.length > 0" class="mt-6 space-y-4">
                    <!-- Items per page selector -->
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <label for="itemsPerPage" class="text-sm text-muted-foreground">Items per page:</label>
                            <select v-model.number="itemsPerPage" id="itemsPerPage"
                                    class="px-3 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="text-sm text-muted-foreground">
                            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
                        </div>

                        <!-- Pagination buttons -->
                        <div class="flex items-center justify-center gap-2">
                            <button
                                @click="updatePage(Math.max(1, currentPage - 1))"
                                :disabled="currentPage === 1"
                                class="px-3 py-2 text-sm border border-border rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-muted transition-all"
                            >
                                Previous
                            </button>
                            <div class="flex items-center gap-1">
                                <button
                                    v-for="page in paginationButtons"
                                    :key="page"
                                    @click="updatePage(page)"
                                    :class="currentPage === page ? 'bg-primary text-primary-foreground' : 'border border-border hover:bg-muted'"
                                    class="min-w-9 px-3 py-2 text-sm rounded-lg transition-all"
                                >
                                    {{ page }}
                                </button>
                            </div>
                            <button
                                @click="updatePage(Math.min(pagination.lastPage, currentPage + 1))"
                                :disabled="currentPage === pagination.lastPage"
                                class="px-3 py-2 text-sm border border-border rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-muted transition-all"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="students.length === 0" class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-muted-foreground mb-4" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 00-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-muted-foreground">No students found matching your filters</p>
                </div>
            </div>

            <!-- Improved view details modal with modern design -->
            <div v-if="showDetailsModal"
                 class="fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4 overflow-y-auto">
                <div
                    class="bg-card rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-border shadow-2xl">
                    <div
                        class="sticky top-0 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-border p-4 md:p-6 flex items-center justify-between backdrop-blur-2xl">
                        <div>
                            <h2 class="text-lg md:text-xl font-bold">Application Details</h2>
                            <p class="text-xs md:text-sm text-muted-foreground mt-1">Complete student information</p>
                        </div>
                        <button @click="showDetailsModal = false"
                                class="text-muted-foreground hover:text-foreground transition-colors flex-shrink-0">
                            <svg class="w-5 md:w-6 h-5 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div v-if="selectedStudent" class="p-4 md:p-6 space-y-6">
                        <!-- Personal Info Section -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-primary uppercase tracking-wide">Personal
                                Information</h3>
                            <!-- responsive grid: 1 column on mobile, 2 on md and above -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                                <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                    <p class="text-xs text-muted-foreground font-medium mb-1">First Name</p>
                                    <p class="font-medium text-sm">{{ selectedStudent.first_name }}</p>
                                </div>
                                <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                    <p class="text-xs text-muted-foreground font-medium mb-1">Last Name</p>
                                    <p class="font-medium text-sm">{{ selectedStudent.last_name }}</p>
                                </div>
                                <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                    <p class="text-xs text-muted-foreground font-medium mb-1">Email</p>
                                    <p class="font-medium break-all text-sm">{{ selectedStudent.email }}</p>
                                </div>
                                <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                    <p class="text-xs text-muted-foreground font-medium mb-1">Phone</p>
                                    <p class="font-medium text-sm">{{ selectedStudent.phone_number }}</p>
                                </div>
                                <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                    <p class="text-xs text-muted-foreground font-medium mb-1">Country</p>
                                    <p class="font-medium text-sm">{{ selectedStudent.country }}</p>
                                </div>
                                <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                    <p class="text-xs text-muted-foreground font-medium mb-1">Gender</p>
                                    <p class="font-medium text-sm">{{ selectedStudent.gender || 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Info Section -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-primary uppercase tracking-wide">Academic
                                Information</h3>
                            <!-- responsive grid for academic info -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                                <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                    <p class="text-xs text-muted-foreground font-medium mb-1">Class of Degree</p>
                                    <p class="font-medium text-sm">{{ selectedStudent.class_of_degree }}</p>
                                </div>
                                <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                    <p class="text-xs text-muted-foreground font-medium mb-1">Course</p>
                                    <p class="font-medium text-sm">{{ selectedStudent.course_name || 'N/A' }}</p>
                                </div>
                            </div>

                            <!-- Universities section -->
                            <div v-if="selectedStudent.universities && selectedStudent.universities.length > 0"
                                 class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                <p class="text-xs text-muted-foreground font-medium mb-3">Universities of Choice</p>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="university in selectedStudent.universities" :key="university.id"
                                          class="px-2 md:px-3 py-1 bg-primary/20 text-primary rounded text-xs font-medium">
                                        {{ university.name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Countries section -->
                            <div v-if="selectedStudent.countries && selectedStudent.countries.length > 0"
                                 class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                <p class="text-xs text-muted-foreground font-medium mb-3">Countries of Preference</p>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="country in selectedStudent.countries" :key="country.id"
                                          class="px-2 md:px-3 py-1 bg-primary/20 text-primary rounded text-xs font-medium">
                                        {{ country.name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Section -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-primary uppercase tracking-wide">Status</h3>
                            <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                <span :class="getStatusClass(selectedStudent.status)"
                                      class="px-3 md:px-4 py-2 rounded-full text-xs md:text-sm font-medium inline-block">
                                    {{ selectedStudent.status }}
                                </span>
                            </div>
                        </div>

                        <!-- Application Documents Section -->
                        <div
                            v-if="selectedStudent.application_documents && selectedStudent.application_documents.length > 0"
                            class="space-y-4">
                            <h3 class="text-sm font-semibold text-primary uppercase tracking-wide">Application
                                Documents</h3>
                            <!-- document links now open in modal instead of new tab -->
                            <div class="grid grid-cols-1 gap-3">
                                <div v-for="(doc, index) in getValidDocuments()"
                                     :key="index"
                                     @click.stop="viewingDocument = doc"
                                     class="p-3 md:p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg hover:bg-blue-500/20 transition-all duration-200 flex items-center space-x-3 group text-left cursor-pointer">
                                    <svg
                                        class="w-5 h-5 text-blue-500 group-hover:scale-110 transition-transform flex-shrink-0"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs md:text-sm font-medium text-blue-500 group-hover:underline truncate">
                                            {{ getDocumentName(doc) }}</p>
                                        <p class="text-xs text-muted-foreground">Click to view</p>
                                    </div>
                                    <svg
                                        class="w-4 h-4 ml-auto text-muted-foreground group-hover:text-blue-500 transition-colors flex-shrink-0"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div v-else
                             class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50 text-xs md:text-sm text-muted-foreground">
                            No application documents uploaded
                        </div>

                        <!-- Additional Info Section -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-primary uppercase tracking-wide">Additional
                                Information</h3>
                            <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                <p class="text-xs md:text-sm text-muted-foreground whitespace-pre-wrap">
                                    {{ selectedStudent.additional_notes || 'No additional notes' }}
                                </p>
                            </div>
                        </div>

                        <!-- Agent Info Section -->
                        <div v-if="selectedStudent.agent" class="space-y-4">
                            <h3 class="text-sm font-semibold text-primary uppercase tracking-wide">Agent
                                Information</h3>
                            <div class="p-3 md:p-4 bg-primary/5 rounded-lg border border-primary/20">
                                <p class="font-medium text-sm">
                                    <b>Agency Name: </b>
                                    {{ selectedStudent.agent.agency_name }}
                                </p>
                                <p class="text-xs md:text-sm text-muted-foreground"><b>Agent
                                    Name:
                                </b>
                                    {{ selectedStudent.agent.first_name }} {{ selectedStudent.agent.last_name }}
                                </p>
                                <p class="text-xs md:text-sm text-muted-foreground"><b>Agent
                                    Email: </b>{{ selectedStudent.agent.email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Added document viewer modal -->
            <div v-if="viewingDocument" @click.stop="viewingDocument = null"
                 class="fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4">
                <div
                    class="bg-card rounded-xl max-w-4xl w-full max-h-[90vh] border border-border shadow-2xl flex flex-col">
                    <div
                        class="sticky top-0 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-border p-4 sm:p-4 sm:p-6 flex items-center justify-between gap-3 backdrop-blur-2xl">
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg md:text-xl font-bold truncate">Document Viewer</h2>
                            <p class="text-xs md:text-sm text-muted-foreground mt-1 truncate">
                                {{ getDocumentName(viewingDocument) }}
                            </p>
                        </div>
                        <button @click="viewingDocument = null"
                                class="text-muted-foreground hover:text-foreground transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto bg-black/5">
                        <!-- iframe for document preview instead of opening in new tab -->
                        <iframe :src="getDocumentUrl(viewingDocument)" class="w-full h-[60vh] sm:h-[70vh]"/>
                    </div>
                    <div class="border-t border-border p-4 bg-muted/30 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3">
                        <a @click.stop.prevent="downloadFile(viewingDocument)"
                           class="px-3 md:px-4 py-2 bg-primary/20 text-primary rounded-lg hover:bg-primary/30 transition-all text-xs md:text-sm font-medium flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download
                        </a>

                        <a :href="getDocumentUrl(viewingDocument)" target="_blank" rel="noopener noreferrer"
                           class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-all text-sm font-medium text-center">
                            Open in New Tab
                        </a>
                    </div>
                </div>
            </div>

            <!-- Replaced basic edit modal with full application form that mirrors CreateApplication -->
            <div v-if="showEditModal"
                 class="fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4 overflow-y-auto">
                <div class="bg-card rounded-lg w-full max-w-4xl border border-border my-4 sm:my-8">
                    <div
                        class="sticky top-0 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-border p-4 sm:p-6 flex items-center justify-between backdrop-blur-2xl">
                        <h2 class="text-lg sm:text-xl font-bold">Edit Student Application</h2>
                        <button @click="showEditModal = false" class="text-muted-foreground hover:text-foreground">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4 sm:p-6 space-y-6 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <!-- Display validation errors -->
                        <div v-if="editErrors && Object.keys(editErrors).length > 0"
                             class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg">
                            <p class="text-sm text-red-500 font-medium mb-2">Please fix the following errors:</p>
                            <ul class="space-y-1">
                                <li v-for="(messages, field) in editErrors" :key="field" class="text-sm text-red-500">
                                    <strong>{{ field }}:</strong> {{ messages.join(', ') }}
                                </li>
                            </ul>
                        </div>

                        <!-- Personal Information Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">First Name</label>
                                    <input v-model="editForm.first_name" type="text"
                                           class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Middle Name</label>
                                    <input v-model="editForm.middle_name" type="text"
                                           class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Last Name</label>
                                    <input v-model="editForm.last_name" type="text"
                                           class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Gender</label>
                                    <select v-model="editForm.gender"
                                            class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                        <option value="">Select gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <!-- Disabled email and phone fields in edit form -->
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Email (Read-only)</label>
                                    <input v-model="editForm.email" type="email" disabled
                                           class="w-full px-4 py-3 bg-muted border border-border rounded-lg cursor-not-allowed opacity-60"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Phone Number (Read-only)</label>
                                    <input v-model="editForm.phone_number" type="tel" disabled
                                           class="w-full px-4 py-3 bg-muted border border-border rounded-lg cursor-not-allowed opacity-60"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Country of Origin</label>
                                    <input v-model="editForm.country" type="text"
                                           class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Class of Degree</label>
                                    <select v-model="editForm.class_of_degree"
                                            class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                        <option value="">Select class</option>
                                        <option value="First Class">First Class</option>
                                        <option value="Second Class Upper">Second Class Upper</option>
                                        <option value="Second Class Lower">Second Class Lower</option>
                                        <option value="Third Class">Third Class</option>
                                        <option value="Pass">Pass</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Preferences Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold">Academic Preferences</h3>
                            <div class="space-y-6 relative">
                                <!-- Desired Course with searchable dropdown -->
                                <div class="space-y-2 group">
                                    <label class="text-sm font-medium">Desired Course</label>
                                    <div class="relative">
                                        <input
                                            v-model="courseSearch"
                                            type="text"
                                            placeholder="Search and select a course..."
                                            class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            @focus="handleEditCourseFocus"
                                            @blur="handleEditCourseBlur"
                                        />
                                        <!-- Course dropdown -->
                                        <div v-if="showEditCourseDropdown"
                                             class="absolute z-50 w-full mt-1 bg-input border border-border rounded-lg shadow-lg max-h-64 overflow-y-auto">
                                            <div v-if="filteredEditCourses.length === 0"
                                                 class="p-3 text-sm text-muted-foreground">
                                                No courses found
                                            </div>
                                            <div
                                                v-for="course in filteredEditCourses"
                                                :key="course.id"
                                                @click="editForm.course_id = course.id; courseSearch = course.name; showEditCourseDropdown = false"
                                                class="px-4 py-2 cursor-pointer hover:bg-muted transition-colors"
                                            >
                                                {{ course.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="editForm.course_id" class="mt-2 text-sm text-muted-foreground">
                                        Selected: {{ getEditCourseName(editForm.course_id) }}
                                    </div>
                                </div>

                                <!-- Schools of Choice - multiple selection -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm font-medium">Schools of Choice <span
                                            class="text-xs text-muted-foreground">(Max 4 selections)</span></label>
                                        <!-- Tooltip showing selected schools count -->
                                        <div v-if="editForm.schools_of_choice && editForm.schools_of_choice.length > 0"
                                             class="group relative">
                                            <div
                                                class="w-5 h-5 bg-primary text-primary-foreground rounded-full flex items-center justify-center text-xs font-bold cursor-help">
                                                {{ editForm.schools_of_choice.length }}
                                            </div>
                                            <!-- Tooltip displaying selected schools (up to 4) -->
                                            <div
                                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-50">
                                                <div
                                                    class="bg-card border border-border rounded-lg shadow-lg p-3 w-max max-w-[16rem] text-sm">
                                                    <p class="font-semibold text-primary mb-2">Selected Schools:</p>
                                                    <ul class="space-y-1 text-muted-foreground">
                                                        <li v-for="(schoolId, idx) in editForm.schools_of_choice.slice(0, 4)"
                                                            :key="schoolId" class="text-xs">
                                                            {{ idx + 1 }}. {{ getEditSchoolName(schoolId) }}
                                                        </li>
                                                        <li v-if="editForm.schools_of_choice.length > 4"
                                                            class="text-xs italic">
                                                            +{{ editForm.schools_of_choice.length - 4 }} more
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <input
                                            v-model="schoolSearch"
                                            type="text"
                                            placeholder="Search and select a school..."
                                            class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            @focus="handleEditSchoolFocus"
                                            @blur="handleEditSchoolBlur"
                                        />
                                        <!-- Schools dropdown -->
                                        <div v-if="showEditSchoolDropdown"
                                             class="absolute z-50 bg-input border border-border rounded-lg shadow-lg max-h-64 overflow-y-auto"
                                             :style="{
                                               top: editSchoolInputTop + 'px',
                                               left: editSchoolInputLeft + 'px',
                                               width: editSchoolInputWidth + 'px'
                                             }">
                                            <div v-if="filteredEditSchools.length === 0"
                                                 class="p-3 text-sm text-muted-foreground">
                                                No schools found
                                            </div>
                                            <div
                                                v-for="school in filteredEditSchools"
                                                :key="school.id"
                                                @mousedown.prevent="toggleEditSchool(school.id)"
                                                class="px-4 py-2 cursor-pointer hover:bg-muted transition-colors flex items-center"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :checked="editForm.schools_of_choice && editForm.schools_of_choice.includes(String(school.id))"
                                                    class="mr-2"
                                                    @mousedown.prevent
                                                />
                                                {{ school.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Display selected schools as tags -->
                                    <div v-if="editForm.schools_of_choice && editForm.schools_of_choice.length > 0"
                                         class="mt-3 flex flex-wrap gap-2">
                                        <div
                                            v-for="schoolId in editForm.schools_of_choice"
                                            :key="schoolId"
                                            class="px-3 py-1 bg-primary/10 border border-primary/30 rounded-full text-sm flex items-center gap-2"
                                        >
                                            {{ getEditSchoolName(schoolId) }}
                                            <button
                                                type="button"
                                                @click="removeEditSchool(schoolId)"
                                                class="text-primary hover:text-primary/70"
                                            >
                                                ✕
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Country Preferences - multiple selection -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm font-medium">Country Preferences <span
                                            class="text-xs text-muted-foreground">(Max 4 selections)</span></label>
                                        <!-- Tooltip showing selected countries count -->
                                        <div
                                            v-if="editForm.country_of_preference && editForm.country_of_preference.length > 0"
                                            class="group relative">
                                            <div
                                                class="w-5 h-5 bg-primary text-primary-foreground rounded-full flex items-center justify-center text-xs font-bold cursor-help">
                                                {{ editForm.country_of_preference.length }}
                                            </div>
                                            <!-- Tooltip displaying selected countries (up to 4) -->
                                            <div
                                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-50">
                                                <div
                                                    class="bg-card border border-border rounded-lg shadow-lg p-3 w-max max-w-[16rem] text-sm">
                                                    <p class="font-semibold text-primary mb-2">Selected Countries:</p>
                                                    <ul class="space-y-1 text-muted-foreground">
                                                        <li v-for="(countryId, idx) in editForm.country_of_preference.slice(0, 4)"
                                                            :key="countryId" class="text-xs">
                                                            {{ idx + 1 }}. {{ getEditCountryName(countryId) }}
                                                        </li>
                                                        <li v-if="editForm.country_of_preference.length > 4"
                                                            class="text-xs italic">
                                                            +{{ editForm.country_of_preference.length - 4 }} more
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <input
                                            v-model="countrySearch"
                                            type="text"
                                            placeholder="Search and select countries..."
                                            class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            @focus="handleEditCountryFocus"
                                            @blur="handleEditCountryBlur"
                                        />
                                        <!-- Countries dropdown -->
                                        <div v-if="showEditCountryDropdown"
                                             class="absolute z-50 bg-input border border-border rounded-lg shadow-lg max-h-64 overflow-y-auto"
                                             :style="{
                                               top: editCountryInputTop + 'px',
                                               left: editCountryInputLeft + 'px',
                                               width: editCountryInputWidth + 'px'
                                             }">
                                            <div v-if="filteredEditCountries.length === 0"
                                                 class="p-3 text-sm text-muted-foreground">
                                                No countries found
                                            </div>
                                            <div
                                                v-for="country in filteredEditCountries"
                                                :key="country.id"
                                                @mousedown.prevent="toggleEditCountry(country.id)"
                                                class="px-4 py-2 cursor-pointer hover:bg-muted transition-colors flex items-center"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :checked="editForm.country_of_preference && editForm.country_of_preference.includes(String(country.id))"
                                                    class="mr-2"
                                                    @mousedown.prevent
                                                />
                                                {{ country.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Display selected countries as tags -->
                                    <div
                                        v-if="editForm.country_of_preference && editForm.country_of_preference.length > 0"
                                        class="mt-3 flex flex-wrap gap-2">
                                        <div
                                            v-for="countryId in editForm.country_of_preference"
                                            :key="countryId"
                                            class="px-3 py-1 bg-primary/10 border border-primary/30 rounded-full text-sm flex items-center gap-2"
                                        >
                                            {{ getEditCountryName(countryId) }}
                                            <button
                                                type="button"
                                                @click="removeEditCountry(countryId)"
                                                class="text-primary hover:text-primary/70"
                                            >
                                                ✕
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold">Additional Information</h3>
                            <div class="space-y-2">
                                <label for="additionalNotes" class="text-sm font-medium">Notes</label>
                                <textarea
                                    v-model="editForm.additional_notes"
                                    id="additionalNotes"
                                    placeholder="Add any additional notes..."
                                    rows="4"
                                    class="w-full px-4 py-3 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Document upload section to edit form -->
                        <div class="space-y-2">
                            <label for="editDocuments" class="text-sm font-medium">Application Documents</label>
                            <div
                                class="border-2 border-dashed border-border rounded-lg p-6 sm:p-8 text-center hover:border-primary transition-all duration-300 cursor-pointer"
                                :class="{ 'bg-primary/5 border-primary': editIsDragOver }"
                                @click="$refs.editDocumentInput.click()"
                                @drop.prevent="handleEditDrop"
                                @dragover.prevent="editIsDragOver = true"
                                @dragleave.prevent="editIsDragOver = false"
                            >
                                <svg class="w-12 h-12 mx-auto text-muted-foreground mb-4" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm text-muted-foreground mb-2">Click to upload or drag and drop</p>
                                <p class="text-xs text-muted-foreground">PDF, DOCX (max. 10MB each)</p>
                            </div>
                            <div v-if="editForm.application_documents && editForm.application_documents.length > 0"
                                 class="mt-4">
                                <p class="text-sm font-medium mb-2">Uploaded Files:</p>
                                <ul class="space-y-2">
                                    <li v-for="(file, index) in editForm.application_documents" :key="index"
                                        class="text-sm text-muted-foreground flex items-center justify-between bg-muted/50 p-3 rounded border border-border">
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div class="min-w-0">
                                                <div class="font-medium text-foreground break-all">
                                                    {{ typeof file === 'object' ? file.name : file }}
                                                </div>
                                                <div class="text-xs" v-if="typeof file === 'object'">
                                                    {{ (file.size / 1024).toFixed(2) }} KB
                                                </div>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeEditFile(index)"
                                            class="text-destructive hover:text-destructive/70 text-xs font-medium ml-2"
                                        >
                                            Remove
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <input
                                ref="editDocumentInput"
                                type="file"
                                multiple
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="hidden"
                                @change="handleEditFileUpload"
                            />
                        </div>

                        <!-- Added loading overlay with theme-colored spinner after successful submission -->
                        <div v-if="editSuccess" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                            <div
                                class="bg-card rounded-xl p-6 sm:p-8 mx-4 text-center border border-border shadow-2xl animate-scale-in">
                                <svg class="w-16 h-16 text-primary mx-auto mb-4 animate-spin" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <h3 class="text-xl font-bold mb-2">Application Updated</h3>
                                <p class="text-muted-foreground text-sm">Redirecting to student dashboard...</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3 pt-4 border-t border-border">
                            <button @click="showEditModal = false"
                                    class="px-6 py-3 border border-border rounded-lg hover:bg-muted transition-all">
                                Cancel
                            </button>
                            <button @click="saveEdit" :disabled="editLoading"
                                    class="px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 disabled:opacity-50 transition-all">
                                {{ editLoading ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Applications Modal with animation -->
            <div v-if="showApplicationsModal"
                 class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div
                    class="bg-card rounded-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto border border-border shadow-2xl animate-fade-in">
                    <div
                        class="sticky top-0 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-border p-4 md:p-6 flex items-center justify-between backdrop-blur-2xl">
                        <div>
                            <h2 class="text-lg md:text-xl font-bold">Student Applications</h2>
                            <p class="text-xs md:text-sm text-muted-foreground mt-1" v-if="selectedStudentDetails">
                                {{ selectedStudentDetails.first_name }}
                                {{ selectedStudentDetails.middle_name }}
                                {{ selectedStudentDetails.last_name }}
                            </p>
                        </div>
                        <button @click="showApplicationsModal = false"
                                class="text-muted-foreground hover:text-foreground transition-colors flex-shrink-0">
                            <svg class="w-5 md:w-6 h-5 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-4 md:p-6 space-y-6">
                        <!-- Loading State -->
                        <div v-if="applicationLoadingModal" class="flex items-center justify-center py-12">
                            <div class="flex flex-col items-center gap-4">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                                <p class="text-muted-foreground">Loading applications...</p>
                            </div>
                        </div>

                        <template v-else>
                            <!-- Student Details Section -->
                            <div v-if="selectedStudentDetails" class="space-y-4">
                                <h3 class="text-sm font-semibold text-primary uppercase tracking-wide">Student
                                    Information</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                                    <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                        <p class="text-xs text-muted-foreground font-medium mb-1">Full Name</p>
                                        <p class="font-medium text-sm">
                                            {{ selectedStudentDetails.first_name }}
                                            {{ selectedStudentDetails.middle_name }}
                                            {{ selectedStudentDetails.last_name }}
                                        </p>
                                    </div>
                                    <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                        <p class="text-xs text-muted-foreground font-medium mb-1">Email</p>
                                        <p class="font-medium text-sm break-all">{{ selectedStudentDetails.email }}</p>
                                    </div>
                                    <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                        <p class="text-xs text-muted-foreground font-medium mb-1">Phone</p>
                                        <p class="font-medium text-sm">{{ selectedStudentDetails.phone_number }}</p>
                                    </div>
                                    <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                        <p class="text-xs text-muted-foreground font-medium mb-1">Gender</p>
                                        <p class="font-medium text-sm">{{ selectedStudentDetails.gender || 'N/A' }}</p>
                                    </div>
                                    <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                        <p class="text-xs text-muted-foreground font-medium mb-1">Date of Birth</p>
                                        <p class="font-medium text-sm">
                                            {{
                                                selectedStudentDetails.birth_date ? new Date(selectedStudentDetails.birth_date).toLocaleDateString() : 'N/A'
                                            }}
                                        </p>
                                    </div>
                                    <div class="p-3 md:p-4 bg-muted/30 rounded-lg border border-border/50">
                                        <p class="text-xs text-muted-foreground font-medium mb-1">Country</p>
                                        <p class="font-medium text-sm">{{ selectedStudentDetails.country || 'N/A' }}</p>
                                    </div>
                                </div>

                                <!-- Agent Info -->
                                <div v-if="selectedStudentDetails.agent"
                                     class="p-3 md:p-4 bg-primary/5 rounded-lg border border-primary/20">
                                    <p class="text-xs text-muted-foreground font-medium mb-2">Agent Details</p>

                                    <p class="font-medium text-sm">
                                        <span class="text-muted-foreground">Full Name - </span>
                                        {{ selectedStudentDetails.agent.first_name }}
                                        {{ selectedStudentDetails.agent.last_name }}
                                        {{ selectedStudentDetails.agent.agency_name }}
                                    </p>

                                    <p class="font-medium text-sm">
                                        <span class="text-muted-foreground">Email - </span>
                                        {{ selectedStudentDetails.agent.email }}
                                    </p>

                                    <p class="font-medium text-sm">
                                        <span class="text-muted-foreground">Agency Name - </span>
                                        {{ selectedStudentDetails.agent.agency_name }}
                                    </p>

                                    <p class="font-medium text-sm">
                                        <span class="text-muted-foreground">Phone Number - </span>
                                        {{ selectedStudentDetails.agent.phone }}
                                    </p>
                                </div>
                            </div>

                            <!-- Applications Section -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-semibold text-primary uppercase tracking-wide">
                                    Applications ({{ studentApplications.length }})
                                </h3>

                                <div v-if="studentApplications.length === 0" class="text-center py-12">
                                    <svg class="w-16 h-16 mx-auto text-muted-foreground mb-4" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 00-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-muted-foreground">No applications found for this student</p>
                                </div>

                                <div v-else class="space-y-4">
                                    <div v-for="application in studentApplications" :key="application.id"
                                         class="p-4 border border-border rounded-lg hover:border-primary/50 transition-all duration-200 animate-slide-up">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-xs text-muted-foreground font-medium mb-1">Course</p>
                                                <p class="font-medium text-sm">
                                                    {{ application.course_name || 'N/A' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted-foreground font-medium mb-1">Status</p>
                                                <span :class="getStatusClass(application.status)"
                                                      class="inline-block px-3 py-1 rounded text-xs font-medium">
                                        {{ application.status }}
                                    </span>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted-foreground font-medium mb-1">
                                                    Universities</p>
                                                <div class="flex flex-wrap gap-2">
                                        <span v-for="university in application.schools_of_choice_details"
                                              :key="university.id"
                                              class="px-2 py-1 bg-primary/20 text-primary rounded text-xs font-medium">
                                            {{ university.name }}
                                        </span>
                                                    <span v-if="!application.schools_of_choice_details?.length"
                                                          class="text-xs text-muted-foreground">N/A</span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted-foreground font-medium mb-1">Applied
                                                    Date</p>
                                                <p class="font-medium text-sm">
                                                    {{ new Date(application.created_at).toLocaleDateString() }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted-foreground font-medium mb-1">Countries of
                                                    Preference</p>
                                                <div class="flex flex-wrap gap-2">
                                        <span v-for="country in application.country_of_preference_details"
                                              :key="country.id"
                                              class="px-2 py-1 bg-blue-500/20 text-blue-500 rounded text-xs font-medium">
                                            {{ country.name }}
                                        </span>
                                                    <span v-if="!application.country_of_preference_details?.length"
                                                          class="text-xs text-muted-foreground">N/A</span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted-foreground font-medium mb-1">Class of
                                                    Degree</p>
                                                <p class="font-medium text-sm">
                                                    {{ application.class_of_degree || 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="application.additional_notes"
                                             class="mt-4 pt-4 border-t border-border/50">
                                            <p class="text-xs text-muted-foreground font-medium mb-2">Additional
                                                Notes</p>
                                            <p class="text-sm text-foreground leading-relaxed">
                                                {{ application.additional_notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import {ref, computed, onMounted, watch} from 'vue';
import {studentsAPI, applicationsAPI, dashboardAPI, coursesAPI, schoolsAPI, countriesAPI} from '../services/api';
import {useAuth} from "../composables/useAuth.js";

export default {
    name: 'Students',
    components: {},
    setup() {
        const {userRole, isCounselor, isAgent} = useAuth()

        const searchQuery = ref('');
        const statusFilter = ref('');
        const countryFilter = ref('');
        const startDate = ref('');
        const endDate = ref('');
        const currentPage = ref(1);
        const itemsPerPage = ref(10);
        const loading = ref(true);
        const error = ref(null);
        const students = ref([]);
        const selectedStudent = ref(null);
        const showDetailsModal = ref(false);
        const showEditModal = ref(false);
        const editLoading = ref(false);
        const editErrors = ref({});
        const editIsDragOver = ref(false);
        const editSuccess = ref(false);
        const viewingDocument = ref(null);
        const showApplicationsModal = ref(false);
        const studentApplications = ref([]);
        const applicationLoadingModal = ref(false);
        const selectedStudentIdForApps = ref(null);
        const editCourses = ref([]);
        const editSchools = ref([]);
        const editCountries = ref([]);
        const courseSearch = ref('');
        const schoolSearch = ref('');
        const countrySearch = ref('');
        const showEditCourseDropdown = ref(false);
        const showEditSchoolDropdown = ref(false);
        const showEditCountryDropdown = ref(false);
        const editSchoolInputTop = ref(0);
        const editSchoolInputLeft = ref(0);
        const editSchoolInputWidth = ref(0);
        const editCountryInputTop = ref(0);
        const editCountryInputLeft = ref(0);
        const editCountryInputWidth = ref(0);
        const selectedStudentDetails = ref(null);

        // Edit form fields
        const editForm = ref({
            id: null, // Added id for update
            first_name: '',
            middle_name: '',
            last_name: '',
            gender: '',
            email: '',
            phone_number: '',
            country: '',
            class_of_degree: '',
            course_id: '',
            schools_of_choice: [],
            country_of_preference: [],
            status: '',
            additional_notes: '',
            application_documents: []
        });

        const stats = ref([
            {value: 0, label: 'Total Students'},
            {value: 0, label: 'Total Applications'},
            {value: 0, label: 'Visa Granted'},
            {value: 0, label: 'CAS/CEO/LOA/I20 Stage'},
            {value: 0, label: 'Unconditional Offers'},
        ]);


        const pagination = ref({
            total: 0,
            from: 0,
            to: 0,
            currentPage: 1,
            perPage: 10,
            lastPage: 1
        });

        const fetchStats = async () => {
            try {
                const statsPromise = isCounselor.value
                    ? dashboardAPI.getCounselorStats()
                    : dashboardAPI.getStats()

                const [statsRes] = await Promise.all([
                    statsPromise.catch(() => ({data: {data: {}}})),
                ])

                // Correctly access data, checking for 'data' and then 'data.data' or just 'data'
                const statsData = statsRes.data?.data || statsRes.data || {};


                stats.value = [
                    {value: statsData.total_students || 0, label: 'Total Students'},
                    {value: statsData.total_applications || 0, label: 'Total Applications'},
                    {value: statsData.visa_application_granted || 0, label: 'Visa Granted'},
                    {value: statsData.cas_ceo_loa_i20_stage || 0, label: 'CAS/CEO/LOA/I20 Stage'},
                    {value: statsData.unconditional_offers_received || 0, label: 'Unconditional Offers'},
                    {value: statsData.application_rejected || 0, label: 'Applications Rejected'},
                ];

            } catch (err) {
                console.error('[v0] Error fetching stats:', err);
            }
        };

        const fetchStudents = async () => {
            try {
                loading.value = true;
                error.value = null;

                const studentsPromise = isCounselor.value
                    ? studentsAPI.getCounselorStudents(currentPage.value, itemsPerPage.value, searchQuery.value, startDate.value, endDate.value)
                    : studentsAPI.getAll(currentPage.value, itemsPerPage.value, searchQuery.value, startDate.value, endDate.value)

                const response = await studentsPromise;
                const paginationData = response.data;
                const studentsData = paginationData.data || [];

                students.value = studentsData.map(student => ({
                    id: student.id,
                    name: `${student.first_name} ${student.middle_name} ${student.last_name}`,
                    email: student.email,
                    initials: `${student.first_name ? student.first_name[0] : ''}${student.last_name ? student.last_name[0] : ''}`,
                    course: student.course_name || 'N/A',
                    degree: student.class_of_degree || 'N/A',
                    country: student.country,
                    phone: student.phone_number,
                    gender: student.gender || 'N/A',
                    status: student.status || 'Pending',
                    statusClass: getStatusClass(student.status || 'Pending'),
                    date: student.created_at ? new Date(student.created_at).toLocaleDateString() : 'N/A',
                    fullData: student
                }));

                if (paginationData.meta) {
                    pagination.value = {
                        total: paginationData.meta.total,
                        from: paginationData.meta.from,
                        to: paginationData.meta.to,
                        currentPage: paginationData.meta.current_page,
                        perPage: paginationData.meta.per_page,
                        lastPage: paginationData.meta.last_page
                    };
                }

            } catch (err) {
                console.error('Error fetching students:', err);
                error.value = 'Failed to load student data. Please try again.';
            } finally {
                loading.value = false;
            }
        };

        const fetchStudentApplications = async (studentId) => {
            applicationLoadingModal.value = true;
            try {
                const studentPromise = isCounselor.value
                    ? studentsAPI.getCounselorStudentById(studentId)
                    : studentsAPI.getById(studentId)

                const studentRes = await studentPromise;

                const responseData = studentRes.data;

                if (responseData.data) {
                    selectedStudentDetails.value = {
                        first_name: responseData.data.first_name,
                        middle_name: responseData.data.middle_name,
                        last_name: responseData.data.last_name,
                        phone_number: responseData.data.phone_number,
                        email: responseData.data.email,
                        gender: responseData.data.gender,
                        birth_date: responseData.data.birth_date,
                        country: responseData.data.country,
                        agent: responseData.data.agent
                    };

                    studentApplications.value = responseData.data.applications || [];
                } else {
                    selectedStudentDetails.value = null;
                    studentApplications.value = [];
                }

                console.log('[v0] Student details:', selectedStudentDetails.value);
                console.log('[v0] Applications loaded:', studentApplications.value);
            } catch (err) {
                console.error(`[v0] Error fetching applications for student ${studentId}:`, err);
                selectedStudentDetails.value = null;
                studentApplications.value = [];
            } finally {
                applicationLoadingModal.value = false;
            }
        };

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
        };

        const getStatusClass = (status) => {
            const baseClass = 'px-3 py-1 rounded-full text-xs font-medium';
            return `${baseClass} ${statusColorMap[status] || 'bg-gray-500/20 text-gray-700'}`;
        };

        const viewApplications = async (studentId) => {
            selectedStudentIdForApps.value = studentId;
            studentApplications.value = [];
            selectedStudentDetails.value = null;
            showApplicationsModal.value = true;
            await fetchStudentApplications(studentId);
        };

        const viewDetails = (studentId) => {
            const student = students.value.find(s => s.id === studentId);
            if (student) {
                selectedStudent.value = student.fullData;
                showDetailsModal.value = true;
            }
        };

        const editStudent = (studentId) => {
            const student = students.value.find(s => s.id === studentId);
            if (student) {
                selectedStudent.value = student.fullData;

                let schoolIds = student.fullData.schools_of_choice || [];
                let countryIds = student.fullData.country_of_preference || [];

                // Convert to string IDs for consistency with API
                schoolIds = schoolIds.map(id => String(id));
                countryIds = countryIds.map(id => String(id));

                editForm.value = {
                    id: student.fullData.id,
                    first_name: student.fullData.first_name || '',
                    middle_name: student.fullData.middle_name || '',
                    last_name: student.fullData.last_name || '',
                    gender: student.fullData.gender || '',
                    email: student.fullData.email || '',
                    phone_number: student.fullData.phone_number || '',
                    country: student.fullData.country || '',
                    class_of_degree: student.fullData.class_of_degree || '',
                    course_id: student.fullData.course_id || '',
                    schools_of_choice: schoolIds,
                    country_of_preference: countryIds,
                    status: student.fullData.status || 'Pending',
                    additional_notes: student.fullData.additional_notes || '',
                    application_documents: student.fullData.application_documents || []
                };

                editErrors.value = {};
                courseSearch.value = student.fullData.course_name || '';
                schoolSearch.value = '';
                countrySearch.value = '';

                // Pre-populate dropdown data if available directly from student data
                editCourses.value = student.fullData.course ? [student.fullData.course] : [];
                editSchools.value = student.fullData.universities || [];
                editCountries.value = student.fullData.countries || [];

                showEditModal.value = true;
                editSuccess.value = false; // Reset success overlay state
            }
        };

        const saveEdit = async () => {
            try {
                editLoading.value = true;
                editErrors.value = {};

                const dataToSend = {
                    first_name: editForm.value.first_name || '',
                    middle_name: editForm.value.middle_name || '',
                    last_name: editForm.value.last_name || '',
                    gender: editForm.value.gender || '',
                    country: editForm.value.country || '',
                    class_of_degree: editForm.value.class_of_degree || '',
                    course_id: editForm.value.course_id || '',
                    status: editForm.value.status || 'Pending',
                    additional_notes: editForm.value.additional_notes || '',
                    schools_of_choice: editForm.value.schools_of_choice && editForm.value.schools_of_choice.length > 0 ? editForm.value.schools_of_choice.map(id => parseInt(id)) : [],
                    country_of_preference: editForm.value.country_of_preference && editForm.value.country_of_preference.length > 0 ? editForm.value.country_of_preference.map(id => parseInt(id)) : [],
                    application_documents: []
                };

                // Separate existing documents from new files
                const newFiles = [];
                editForm.value.application_documents.forEach((doc) => {
                    if (doc instanceof File) {
                        newFiles.push(doc);
                    }
                });

                dataToSend.application_documents = newFiles;

                console.log("[v0] Sending update request with data:", dataToSend);
                await applicationsAPI.patch(selectedStudent.value.id, dataToSend);

                console.log("[v0] Update successful");
                editSuccess.value = true;
                setTimeout(() => {
                    showEditModal.value = false;
                    editSuccess.value = false;
                    fetchStudents();
                    fetchStats();
                }, 2000);

            } catch (err) {
                console.error('[v0] Error updating student:', err);
                if (err.response?.data?.errors) {
                    editErrors.value = err.response.data.errors || {};
                } else if (err.response?.data?.message) {
                    error.value = err.response.data.message;
                } else {
                    error.value = 'An unexpected error occurred. Please try again.';
                }
            } finally {
                editLoading.value = false;
            }
        };

        const handleEditFileUpload = (event) => {
            const files = Array.from(event.target.files);
            const maxSize = 10 * 1024 * 1024; // 10MB

            files.forEach(file => {
                if (file.size > maxSize) {
                    // Handle error: file too large
                    console.warn(`File ${file.name} is too large.`);
                    return;
                }
                const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    // Handle error: invalid file type
                    console.warn(`File ${file.name} has an invalid type.`);
                    return;
                }
                editForm.value.application_documents.push(file);
            });
            event.target.value = ''; // Clear the input
        };

        const handleEditDrop = (event) => {
            editIsDragOver.value = false;
            handleEditFileUpload({target: {files: event.dataTransfer.files}});
        };

        const removeEditFile = (index) => {
            editForm.value.application_documents.splice(index, 1);
        };

        const getValidDocuments = () => {
            return selectedStudent.value?.application_documents.filter(doc => typeof doc === 'string') || [];
        };

        const getDocumentName = (docUrl) => {
            try {
                const url = new URL(docUrl);
                const pathname = url.pathname;
                // Extract filename from the end of the pathname
                const filename = pathname.substring(pathname.lastIndexOf('/') + 1);
                return filename || 'Document';
            } catch (e) {
                // If URL parsing fails, it's likely a relative path from Laravel
                const filename = docUrl.substring(docUrl.lastIndexOf('/') + 1);
                return filename || 'Document';
            }
        };

        const getDocumentUrl = (docPath) => {
            if (docPath && !docPath.startsWith('http')) {
                // Return full URL to bypass Vue Router - Laravel serves uploads from public/uploads
                const cleanPath = docPath.replace(/\\/g, '/').replace(/^\/+/, '');
                return `${window.location.origin}/${cleanPath}`;
            }
            return docPath;
        };

        const fetchEditCourses = async () => {
            try {
                const response = await coursesAPI.getAll();
                // Assuming the structure is consistent, adjust path if needed
                const data = response.data?.data || [];
                editCourses.value = Array.isArray(data) ? data : [];
            } catch (err) {
                console.error('[v0] Error fetching courses:', err);
                editCourses.value = [];
            }
        };

        const fetchEditSchools = async () => {
            try {
                const response = await schoolsAPI.getAll();
                // Assuming the structure is consistent, adjust path if needed
                const data = response.data?.data || [];
                editSchools.value = Array.isArray(data) ? data : [];
            } catch (err) {
                console.error('[v0] Error fetching schools:', err);
                editSchools.value = [];
            }
        };

        const fetchEditCountries = async () => {
            try {
                const response = await countriesAPI.getAll();
                // Assuming the structure is consistent, adjust path if needed
                const data = response.data?.data || [];
                editCountries.value = Array.isArray(data) ? data : [];
            } catch (err) {
                console.error('[v0] Error fetching countries:', err);
                editCountries.value = [];
            }
        };

        const filteredEditCourses = computed(() => {
            const courseList = Array.isArray(editCourses.value) ? editCourses.value : [];
            if (!courseSearch.value) return courseList;
            return courseList.filter(course =>
                course.name.toLowerCase().includes(courseSearch.value.toLowerCase())
            );
        });

        const filteredEditSchools = computed(() => {
            const schoolList = Array.isArray(editSchools.value) ? editSchools.value : [];
            if (!schoolSearch.value) return schoolList;
            return schoolList.filter(school =>
                school.name.toLowerCase().includes(schoolSearch.value.toLowerCase())
            );
        });

        const filteredEditCountries = computed(() => {
            const countryList = Array.isArray(editCountries.value) ? editCountries.value : [];
            if (!countrySearch.value) return countryList;
            return countryList.filter(country =>
                country.name.toLowerCase().includes(countrySearch.value.toLowerCase())
            );
        });

        const handleEditCourseFocus = () => {
            showEditCourseDropdown.value = true;
            if (editCourses.value.length === 0) {
                fetchEditCourses();
            }
        };

        const handleEditCourseBlur = () => {
            // Use nextTick to allow click event on dropdown items to fire before closing
            setTimeout(() => {
                showEditCourseDropdown.value = false;
            }, 150);
        };

        const handleEditSchoolFocus = () => {
            showEditSchoolDropdown.value = true;
            const input = document.querySelector('input[placeholder*="Search and select a school"]');
            if (input) {
                const rect = input.getBoundingClientRect();
                editSchoolInputTop.value = rect.bottom + window.scrollY;
                editSchoolInputLeft.value = rect.left + window.scrollX;
                editSchoolInputWidth.value = rect.width;
            }
            if (editSchools.value.length === 0) {
                fetchEditSchools();
            }
        };

        const handleEditSchoolBlur = () => {
            setTimeout(() => {
                showEditSchoolDropdown.value = false;
            }, 150);
        };

        const handleEditCountryFocus = () => {
            showEditCountryDropdown.value = true;
            // Optionally clear search or pre-fill if already selected
            // countrySearch.value = '';
            const input = document.querySelector('input[placeholder*="Search and select countries"]');
            if (input) {
                const rect = input.getBoundingClientRect();
                editCountryInputTop.value = rect.bottom + window.scrollY;
                editCountryInputLeft.value = rect.left + window.scrollX;
                editCountryInputWidth.value = rect.width;
            }
            if (editCountries.value.length === 0) {
                fetchEditCountries();
            }
        };

        const handleEditCountryBlur = () => {
            setTimeout(() => {
                showEditCountryDropdown.value = false;
            }, 150);
        };

        const toggleEditSchool = (schoolId) => {
            if (!editForm.value.schools_of_choice) {
                editForm.value.schools_of_choice = [];
            }
            const schoolIdStr = String(schoolId);
            const index = editForm.value.schools_of_choice.findIndex(id => String(id) === schoolIdStr);
            if (index !== -1) {
                editForm.value.schools_of_choice.splice(index, 1);
            } else {
                if (editForm.value.schools_of_choice.length < 4) {
                    editForm.value.schools_of_choice.push(schoolIdStr);
                } else {
                    console.warn('Max 4 schools of choice allowed.');
                }
            }
        };

        const removeEditSchool = (schoolId) => {
            if (editForm.value.schools_of_choice) {
                const index = editForm.value.schools_of_choice.findIndex(id => String(id) === String(schoolId));
                if (index !== -1) {
                    editForm.value.schools_of_choice.splice(index, 1);
                }
            }
        };

        const toggleEditCountry = (countryId) => {
            if (!editForm.value.country_of_preference) {
                editForm.value.country_of_preference = [];
            }
            const countryIdStr = String(countryId);
            const index = editForm.value.country_of_preference.findIndex(id => String(id) === countryIdStr);
            if (index !== -1) {
                editForm.value.country_of_preference.splice(index, 1);
            } else {
                if (editForm.value.country_of_preference.length < 4) {
                    editForm.value.country_of_preference.push(countryIdStr);
                } else {
                    console.warn('Max 4 country preferences allowed.');
                }
            }
        };

        const removeEditCountry = (countryId) => {
            if (editForm.value.country_of_preference) {
                const index = editForm.value.country_of_preference.findIndex(id => String(id) === String(countryId));
                if (index !== -1) {
                    editForm.value.country_of_preference.splice(index, 1);
                }
            }
        };

        const getEditCourseName = (courseId) => {
            // Handle case where a course is pre-selected from student data but not fetched globally yet
            if (editCourses.value.length === 1 && editCourses.value[0].id === courseId) {
                return editCourses.value[0].name;
            }
            const course = editCourses.value.find(c => c.id === courseId);
            return course ? course.name : (courseId || ''); // Fallback to ID if name not found
        };

        const getEditSchoolName = (schoolId) => {
            const school = editSchools.value.find(s => String(s.id) === String(schoolId));
            return school ? school.name : '';
        };

        const getEditCountryName = (countryId) => {
            const country = editCountries.value.find(c => String(c.id) === String(countryId));
            return country ? country.name : '';
        };

        const paginationButtons = computed(() => {
            const buttons = [];
            const maxButtons = 5;
            let start = Math.max(1, currentPage.value - Math.floor(maxButtons / 2));
            let end = Math.min(pagination.value.lastPage, start + maxButtons - 1);

            if (end - start + 1 < maxButtons) {
                start = Math.max(1, end - maxButtons + 1);
            }

            for (let i = start; i <= end; i++) {
                buttons.push(i);
            }

            return buttons;
        });

        const updatePage = async (newPage) => {
            if (newPage >= 1 && newPage <= pagination.value.lastPage) {
                currentPage.value = newPage;
                await fetchStudents();
            }
        };

        watch(itemsPerPage, async () => {
            currentPage.value = 1;
            await fetchStudents();
        });

        const applyFilter = () => {
            currentPage.value = 1;
            fetchStudents();
        };

        onMounted(() => {
            fetchStudents();
            fetchStats();
        });

        const downloadFile = async (docPath) => {
            try {
                const fileUrl = getDocumentUrl(docPath);

                const response = await fetch(fileUrl, {mode: 'cors'});
                if (!response.ok) throw new Error(`Failed to fetch file: ${response.status}`);

                const blob = await response.blob();
                const fileName = getDocumentName(fileUrl);

                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(link.href);
            } catch (err) {
                console.error('[v0] Error downloading file:', err);
                alert('Failed to download the file. Please try again.');
            }
        };

        return {
            loading,
            error,
            searchQuery,
            statusFilter,
            countryFilter,
            startDate,
            endDate,
            applyFilter,
            stats,
            students,
            currentPage,
            itemsPerPage,
            pagination,
            paginationButtons,
            fetchStudents,
            fetchStats,
            updatePage,
            viewDetails,
            editStudent,
            saveEdit,
            showDetailsModal,
            showEditModal,
            selectedStudent,
            editForm,
            editLoading,
            editErrors,
            editSuccess,
            getStatusClass,
            editIsDragOver,
            handleEditFileUpload,
            handleEditDrop,
            removeEditFile,
            getValidDocuments,
            getDocumentName,
            getDocumentUrl,
            viewingDocument,
            editCourses,
            editSchools,
            editCountries,
            courseSearch,
            schoolSearch,
            countrySearch,
            showEditCourseDropdown,
            showEditSchoolDropdown,
            showEditCountryDropdown,
            editSchoolInputTop,
            editSchoolInputLeft,
            editSchoolInputWidth,
            editCountryInputTop,
            editCountryInputLeft,
            editCountryInputWidth,
            filteredEditCourses,
            filteredEditSchools,
            filteredEditCountries,
            toggleEditSchool,
            removeEditSchool,
            toggleEditCountry,
            removeEditCountry,
            getEditCourseName,
            getEditSchoolName,
            getEditCountryName,
            handleEditCourseFocus,
            handleEditCourseBlur,
            handleEditSchoolFocus,
            handleEditSchoolBlur,
            handleEditCountryFocus,
            handleEditCountryBlur,
            downloadFile,
            viewApplications,
            showApplicationsModal,
            studentApplications,
            applicationLoadingModal,
            selectedStudentIdForApps,
            selectedStudentDetails,
            userRole,
            isCounselor,
            isAgent,
        };
    }
};
</script>
