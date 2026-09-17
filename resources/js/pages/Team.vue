<template>
    <div class="min-h-screen bg-background animate-fade-in">

        <main class="container mx-auto px-4 sm:px-6 py-6 sm:py-8">
            <div class="mb-8 animate-slide-up">
                <h2 class="text-xl sm:text-2xl font-bold mb-2">Business Development Officers</h2>
                <p class="text-muted-foreground">Manage and view your team of BDO officers</p>
            </div>

            <div v-if="loading" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>

            <div v-else>
                <div class="glass-card rounded-xl p-4 sm:p-6 mb-6 animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                        <div class="flex-1 relative sm:max-w-md">
                            <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search team members..."
                                class="w-full pl-10 pr-4 py-2 bg-input border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                            />
                        </div>
                        <button
                            class="px-4 py-2 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:bg-primary/90 transition-all duration-200 hover:shadow-lg hover:scale-105">
                            + Add Member
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="(officer, index) in filteredOfficers"
                            :key="officer.id"
                            class="glass-card p-4 sm:p-6 rounded-xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1 animate-slide-up"
                            :style="{ animationDelay: `${index * 0.1}s` }"
                        >
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-primary/20 rounded-full flex items-center justify-center">
                                        <span class="text-primary font-medium">{{ officer.initials }}</span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold">{{ officer.name }}</h3>
                                        <p class="text-sm text-muted-foreground">{{ officer.role }}</p>
                                    </div>
                                </div>
                                <span
                                    :class="officer.statusClass"
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                >
                                    {{ officer.status }}
                                </span>
                            </div>

                            <div class="space-y-3 mb-4">
                                <div class="flex items-center space-x-2 text-sm">
                                    <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-muted-foreground break-all">{{ officer.email }}</span>
                                </div>
                                <div class="flex items-center space-x-2 text-sm">
                                    <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span class="text-muted-foreground">{{ officer.phone }}</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-border">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-muted-foreground">Applications</span>
                                    <span class="font-bold">{{ officer.applications }}</span>
                                </div>
                            </div>

                            <div class="mt-4 flex space-x-2">
                                <button
                                    class="flex-1 px-3 py-2 bg-primary/10 text-primary rounded-lg text-sm font-medium hover:bg-primary/20 transition-all duration-200">
                                    View Profile
                                </button>
                                <button
                                    class="px-3 py-2 bg-muted hover:bg-muted/80 rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="glass-card p-4 sm:p-6 rounded-xl">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-2xl font-bold">{{ officers.length }}</div>
                                <div class="text-sm text-muted-foreground">Total Officers</div>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card p-4 sm:p-6 rounded-xl">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-2xl font-bold">{{ activeOfficers }}</div>
                                <div class="text-sm text-muted-foreground">Active Officers</div>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card p-4 sm:p-6 rounded-xl">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-2xl font-bold">{{ totalApplications }}</div>
                                <div class="text-sm text-muted-foreground">Total Applications</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import {ref, computed, onMounted} from 'vue';
import {useTheme} from '../composables/useTheme';
import {bdmOfficersAPI} from '../services/api';
import Navigation from '../components/Navigation.vue';

export default {
    name: 'Team',
    components: {
        Navigation
    },
    setup() {
        const {isDark, toggleTheme} = useTheme();

        const searchQuery = ref('');
        const loading = ref(true);
        const officers = ref([]);

        const fetchOfficers = async () => {
            try {
                loading.value = true;
                const response = await bdmOfficersAPI.getAll();

                officers.value = response.data.map(officer => ({
                    id: officer.id,
                    name: `${officer.first_name} ${officer.last_name}`,
                    initials: `${officer.first_name[0]}${officer.last_name[0]}`,
                    role: officer.position || 'BDO',
                    email: officer.email,
                    phone: officer.phone_number,
                    status: officer.status || 'Active',
                    statusClass: officer.status === 'Active' ? 'bg-emerald-500/20 text-emerald-500' : 'bg-amber-500/20 text-amber-500',
                    applications: officer.applications_count || 0
                }));

            } catch (error) {
                console.error('[v0] Error fetching officers:', error);
            } finally {
                loading.value = false;
            }
        };

        const filteredOfficers = computed(() => {
            if (!searchQuery.value) return officers.value;
            return officers.value.filter(officer =>
                officer.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                officer.email.toLowerCase().includes(searchQuery.value.toLowerCase())
            );
        });

        const activeOfficers = computed(() => {
            return officers.value.filter(o => o.status === 'Active').length;
        });

        const totalApplications = computed(() => {
            return officers.value.reduce((sum, officer) => sum + officer.applications, 0);
        });

        onMounted(() => {
            fetchOfficers();
        });

        return {
            loading,
            searchQuery,
            officers,
            filteredOfficers,
            activeOfficers,
            totalApplications,
            isDark,
            toggleTheme
        };
    }
};
</script>
