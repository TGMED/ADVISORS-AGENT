<template>
    <div class="min-h-screen bg-background animate-fade-in">
        <main class="container mx-auto px-4 sm:px-6 py-6 sm:py-8">
            <div v-if="loading" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>

            <div v-else>
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-6 mb-6 sm:mb-8">
                    <div v-for="(stat, index) in stats" :key="index"
                         class="glass-card p-4 sm:p-6 rounded-xl animate-slide-up hover:shadow-lg hover:border-primary transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                         @click="filterStudentsByStatus(stat)"
                         :style="{ animationDelay: `${index * 0.1}s` }">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center" :class="stat.bgColor">
                                <svg class="w-6 h-6" :class="stat.iconColor" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          :d="stat.icon"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium" :class="stat.changeColor">{{ stat.change }}</span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold mb-1">{{ stat.value }}</div>
                        <div class="text-xs sm:text-sm text-muted-foreground">{{ stat.label }}</div>
                    </div>
                </div>

                <!-- Monthly Applications Bar Chart -->
                <div class="mb-6 sm:mb-8">
                    <div class="glass-card rounded-xl p-4 sm:p-6 animate-slide-up border-l-4 border-primary"
                         style="animation-delay: 0.3s;">
                        <h3 class="font-bold mb-4 sm:mb-6">Monthly Applications Overview</h3>
                        <div class="h-64 sm:h-80">
                            <BarChart v-if="monthlyChartData" :data="monthlyChartData" label="Applications"/>
                            <div v-else class="flex items-center justify-center h-full text-muted-foreground">No data
                                available
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Agent SignUp -->
                <div class="mb-6 sm:mb-8" v-if="isCounselor">
                    <div class="glass-card rounded-xl p-4 sm:p-6 animate-slide-up border-l-4 border-primary"
                         style="animation-delay: 0.3s;">
                        <h3 class="font-bold mb-4 sm:mb-6">Monthly Agent SignUp</h3>
                        <div class="h-64 sm:h-80">
                            <BarChart v-if="monthlyAgentSignUpData" :data="monthlyAgentSignUpData" label="Agent Signup"/>
                            <div v-else class="flex items-center justify-center h-full text-muted-foreground">No data
                                available
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Line Chart & Status Chart -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 sm:mb-8">
                    <div class="glass-card rounded-xl p-4 sm:p-6 animate-slide-up border-l-4 border-primary"
                         style="animation-delay: 0.4s;">
                        <h3 class="font-bold mb-4 sm:mb-6">Applications Trend</h3>
                        <div class="h-64 sm:h-80">
                            <LineChart v-if="applicationsChartData" :data="applicationsChartData"/>
                            <div v-else class="flex items-center justify-center h-full text-muted-foreground">No data
                                available
                            </div>
                        </div>
                    </div>
                    <div class="glass-card rounded-xl p-4 sm:p-6 animate-slide-up border-l-4 border-primary"
                         style="animation-delay: 0.5s;">
                        <h3 class="font-bold mb-4 sm:mb-6">Applications by Status</h3>
                        <div class="h-64 sm:h-80 overflow-y-auto">
                            <div :style="{ height: statusChartHeight }">
                                <HorizontalBarChart v-if="statusChartData" :data="statusChartData"/>
                                <div v-else class="flex items-center justify-center h-full text-muted-foreground">
                                    No data available
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Table & Sidebar -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 glass-card rounded-xl p-4 sm:p-6 animate-slide-up border-t-2 border-primary min-w-0">
                        <!-- Mobile card list -->
                        <div class="md:hidden divide-y divide-border">
                            <div v-for="student in limitedStudents" :key="student.id" class="py-3 flex items-start gap-3">
                                <div
                                    class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-primary text-sm font-medium">{{ student.initials }}</span>
                                </div>
                                <div class="min-w-0 flex-1 text-sm">
                                    <div class="font-medium break-words">{{ student.name }}</div>
                                    <div class="text-muted-foreground break-all">{{ student.email }}</div>
                                    <div class="text-muted-foreground">
                                        {{ [student.phone, student.gender, student.country].filter(Boolean).join(' / ') }}
                                    </div>
                                    <div class="text-xs text-muted-foreground mt-1">{{ student.date }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                <tr class="border-b-2 border-primary">
                                    <th class="text-left py-3 px-4 text-sm font-medium text-muted-foreground">Student
                                        Full Name
                                    </th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-muted-foreground">Email</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-muted-foreground">Phone</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-muted-foreground">
                                        Gender
                                    </th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-muted-foreground">
                                        Country
                                    </th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-muted-foreground">
                                        Enrolled Date
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="student in limitedStudents" :key="student.id"
                                    class="border-b border-border hover:bg-muted/50 hover:border-l-2 hover:border-l-primary transition-all duration-200 cursor-pointer">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-primary text-sm font-medium">
                                                    {{ student.initials }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ student.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-sm">{{ student.email }}</td>
                                    <td class="py-4 px-4 text-sm">{{ student.phone }}</td>
                                    <td class="py-4 px-4 text-sm">{{ student.gender }}</td>
                                    <td class="py-4 px-4 text-sm">{{ student.country }}</td>
                                    <td class="py-4 px-4 text-sm text-muted-foreground">{{ student.date }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Quick Actions - Only show for Agents -->
                        <div v-if="isAgent" class="glass-card rounded-xl p-4 sm:p-6 animate-slide-up border-l-4 border-primary"
                             style="animation-delay: 0.2s;">
                            <h3 class="font-bold mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <router-link to="/applications/create"
                                             class="flex items-center space-x-3 p-3 hover:bg-muted rounded-lg border border-transparent hover:border-primary transition-all duration-200 hover:scale-105">
                                    <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-sm">New Application</div>
                                        <div class="text-xs text-muted-foreground">Submit student application</div>
                                    </div>
                                </router-link>
                                <router-link to="/students/create"
                                             class="flex items-center space-x-3 p-3 hover:bg-muted rounded-lg border border-transparent hover:border-primary transition-all duration-200 hover:scale-105">
                                    <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-sm">New Student</div>
                                        <div class="text-xs text-muted-foreground">Add a new student</div>
                                    </div>
                                </router-link>
                            </div>
                        </div>

                        <div class="glass-card rounded-xl p-4 sm:p-6 animate-slide-up border-l-4 border-primary"
                             style="animation-delay: 0.3s;">
                            <h3 class="font-bold mb-4">Recent Activity</h3>
                            <div v-if="loadingActivity" class="flex items-center justify-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                            </div>
                            <div v-else-if="limitedRecentActivity.length === 0"
                                 class="text-center py-8 text-muted-foreground text-sm">No recent activity
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="activity in limitedRecentActivity" :key="activity.id"
                                     class="flex items-start space-x-3 hover:bg-muted/50 p-2 rounded-lg transition-all duration-200">
                                    <div class="w-2 h-2 bg-primary rounded-full mt-2 flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm break-words">{{ activity.text }}</p>
                                        <p class="text-xs text-muted-foreground mt-1">{{ activity.time }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import {computed, onMounted, ref} from 'vue'
import {dashboardAPI, studentsAPI} from '../services/api'
import {useAuth} from '../composables/useAuth'
import {useRouter} from 'vue-router'
import LineChart from '../components/LineChart.vue'
import DoughnutChart from '../components/DoughnutChart.vue'
import BarChart from '../components/BarChart.vue'
import HorizontalBarChart from '../components/HorizontalBarChart.vue'

export default {
    name: 'Dashboard',
    components: {LineChart, DoughnutChart, BarChart, HorizontalBarChart},
    setup() {
        const {userRole, isCounselor, isAgent} = useAuth()
        const router = useRouter()

        const loading = ref(true)
        const loadingActivity = ref(true)
        const stats = ref([])
        const applicationsChartData = ref(null)
        const statusChartData = ref(null)
        const monthlyChartData = ref(null)
        const monthlyAgentSignUpData = ref(null)
        const studentCountryData = ref([])
        const students = ref([])
        const recentActivity = ref([])

        const fetchDashboardData = async () => {
            try {
                loading.value = true

                const statsPromise = isCounselor.value
                    ? dashboardAPI.getCounselorStats()
                    : dashboardAPI.getStats()

                const studentsPromise = isCounselor.value
                    ? studentsAPI.getCounselorStudents()
                    : studentsAPI.getAll()

                const [statsRes, studentRes] = await Promise.all([
                    statsPromise.catch(e => ({data: {data: {}}})),
                    studentsPromise.catch(e => ({data: []}))
                ])

                const statsData = statsRes.data.data || statsRes.data

                const baseStats = [
                    {
                        value: statsData.total_students || 0,
                        label: 'Total Students',
                        change: '+12%',
                        changeColor: 'text-primary',
                        bgColor: 'bg-primary/20',
                        iconColor: 'text-primary',
                        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
                    }
                ];

                if (isCounselor.value) {
                    baseStats.push({
                        value: statsData.total_agents || 0,
                        label: 'Total Agents',
                        change: '+12%',
                        changeColor: 'text-primary',
                        bgColor: 'bg-primary/20',
                        iconColor: 'text-primary',
                        icon: 'M17 20a4 4 0 10-8 0m8 0H9m8 0h5v-2a4 4 0 00-3-3.87M5 17h14m-9-4h4m-4 0V5a2 2 0 114 0v8m-4 0h4'
                    });
                }

                baseStats.push(
                    {
                        value: statsData.total_applications || 0,
                        label: 'Total Applications',
                        change: '+8%',
                        changeColor: 'text-primary',
                        bgColor: 'bg-primary/20',
                        iconColor: 'text-primary',
                        icon: 'M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m14 0h5v-2a4 4 0 00-3-3.87M5 17h14m-9-4h4m-4 0V5a2 2 0 114 0v8m-4 0h4'
                    },
                    {
                        value: statsData.visa_application_granted || 0,
                        label: 'Visa Granted',
                        change: '+4%',
                        changeColor: 'text-success',
                        bgColor: 'bg-success/20',
                        iconColor: 'text-success',
                        icon: 'M5 13l4 4L19 7'
                    }
                );

                if (!isCounselor.value) {
                    baseStats.push({
                        value: statsData.cas_ceo_loa_i20_stage || 0,
                        label: 'CAS/CEO/LOA/I20 Stage',
                        change: '+2%',
                        changeColor: 'text-primary',
                        bgColor: 'bg-primary/20',
                        iconColor: 'text-primary',
                        icon: 'M12 6v6l4 2'
                    });
                }

                baseStats.push(
                    {
                        value: statsData.document_in_check || 0,
                        label: 'Documents in Check',
                        change: '+1%',
                        changeColor: 'text-warning',
                        bgColor: 'bg-warning/20',
                        iconColor: 'text-warning',
                        icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0zm6 4a3 3 0 11-6 0 3 3 0 016 0z'
                    },
                    {
                        value: statsData.application_rejected || 0,
                        label: 'Rejected Applications',
                        change: '+2%',
                        changeColor: 'text-destructive',
                        bgColor: 'bg-danger/20',
                        iconColor: 'text-danger',
                        icon: 'M6 18L18 6M6 6l12 12'
                    }
                );

                stats.value = baseStats;

                const trendPromise = isCounselor.value
                    ? dashboardAPI.getCounselorApplicationsTrend()
                    : dashboardAPI.getApplicationsTrend()

                const statusPromise = isCounselor.value
                    ? dashboardAPI.getCounselorApplicationsStatus()
                    : dashboardAPI.getApplicationsStatus()

                const monthlyPromise = isCounselor.value
                    ? dashboardAPI.getCounselorMonthlyApplications()
                    : dashboardAPI.getMonthlyApplications()

                const monthlyAgentPromise = isCounselor.value
                    ? dashboardAPI.getCounselorMonthlyAgentApplications()
                    : {data: []}

                const [trendRes, statusRes, monthlyRes, monthlyAgent] = await Promise.all([
                    trendPromise,
                    statusPromise,
                    monthlyPromise,
                    monthlyAgentPromise
                ])

                monthlyAgentSignUpData.value = monthlyAgent.data.data || monthlyRes.data
                monthlyChartData.value = monthlyRes.data.data || monthlyRes.data
                statusChartData.value = statusRes.data.data || statusRes.data
                applicationsChartData.value = trendRes.data.data || trendRes.data

                // Process students data
                const studentsData = Array.isArray(studentRes.data) ? studentRes.data : (studentRes.data.data || [])
                students.value = studentsData.map(s => ({
                    id: s.id,
                    name: `${s.first_name} ${s.middle_name || ''} ${s.last_name}`.trim(),
                    email: s.email,
                    phone: s.phone_number,
                    gender: s.gender,
                    initials: `${s.first_name?.[0] || ''}${s.last_name?.[0] || ''}`,
                    country: s.country,
                    date: new Date(s.created_at).toLocaleDateString()
                }))

            } catch (error) {
                console.error('Error fetching dashboard data:', error)
            } finally {
                loading.value = false
            }
        }

        const fetchRecentActivity = async () => {
            try {
                loadingActivity.value = true
                const response = isCounselor.value
                    ? await dashboardAPI.getCounselorRecentActivity()
                    : await dashboardAPI.getRecentActivity()
                recentActivity.value = response.data.data || response.data || []
            } catch (error) {
                console.error('Error fetching recent activity:', error)
                recentActivity.value = []
            } finally {
                loadingActivity.value = false
            }
        }

        const limitedStudents = computed(() => students.value.slice(0, 8))
        const limitedRecentActivity = isCounselor.value
            ? computed(() => recentActivity.value.slice(0, 9))
            : computed(() => recentActivity.value.slice(0, 5))

        const statusChartHeight = computed(() => {
            const count = statusChartData.value?.labels?.length || 0
            const height = Math.max(count * 35, 200)
            return `${height}px`
        })

        const filterStudentsByStatus = (stat) => {
            // Navigate to applications page with status filter
            router.push({
                name: 'StudentApplication',
                query: {status: stat.label.toLowerCase().replace(/[^a-z]/g, '_')}
            });
        };

        onMounted(() => {
            fetchDashboardData()
            fetchRecentActivity()
        })

        return {
            loading,
            loadingActivity,
            stats,
            applicationsChartData,
            statusChartData,
            monthlyChartData,
            studentCountryData,
            statusChartHeight,
            students,
            limitedStudents,
            limitedRecentActivity,
            recentActivity,
            isCounselor,
            isAgent,
            monthlyAgentSignUpData,
            filterStudentsByStatus
        }
    }
}
</script>
