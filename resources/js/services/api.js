import axios from "axios"

const api = axios.create({
    baseURL: "/api", headers: {
        "Content-Type": "application/json", Accept: "application/json", "X-Requested-With": "XMLHttpRequest",
    },
})

api.interceptors.request.use((config) => {
    const token = localStorage.getItem("auth_token")
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
}, (error) => {
    return Promise.reject(error)
},)

api.interceptors.response.use((response) => response, (error) => {
    if (error.response?.status === 401) {
        localStorage.removeItem("auth_token")
        localStorage.removeItem("user")
        window.location.href = "/login"
    }
    return Promise.reject(error)
},)

/**
 * Turn an Axios error into a list of human-readable messages.
 * fileNames maps "application_documents.N" validation keys back to the attached file name.
 */
export const getApiErrorMessages = (err, {fallback = 'Request failed', fileNames = []} = {}) => {
    const response = err?.response
    const data = response?.data
    const serverMessage = typeof data === 'object' ? (data?.message || data?.error?.message) : null

    if (!response) {
        if (err?.code === 'ECONNABORTED') {
            return ['The request timed out. Large files can take a while, please try again.']
        }
        return ['Could not reach the server. Check your internet connection and try again.']
    }

    if (response.status === 413) {
        return [serverMessage || 'The attached files are too large for the server to accept. Try fewer or smaller files.']
    }

    if (response.status === 422 && data?.errors) {
        return Object.entries(data.errors).flatMap(([field, messages]) => {
            const match = field.match(/^application_documents\.(\d+)$/)
            const prefix = match ? `${fileNames[Number(match[1])] || `File ${Number(match[1]) + 1}`}: ` : ''
            return messages.map(message => prefix + message)
        })
    }

    if (response.status >= 500) {
        return [serverMessage && serverMessage !== 'Server Error'
            ? `Server error (${response.status}): ${serverMessage}`
            : `Server error (${response.status}). Please try again or contact support.`]
    }

    return [serverMessage || `${fallback} (status ${response.status})`]
}

const isCounselor = () => {
    const userStr = localStorage.getItem('user')
    if (!userStr) return false
    try {
        const user = JSON.parse(userStr)
        const role = user.role_name?.toLowerCase() || user.role?.toLowerCase()
        return role === 'counselor'
    } catch (e) {
        return false
    }
}

export const authAPI = {
    login: (credentials) => api.post("/auth/login", credentials),
    register: (userData) => api.post("/auth/register", userData),
    logout: () => api.post("/auth/logout"),
    getUser: () => api.get("/user"),
    getOtp: (data) => api.post("/auth/otp", data),
    resetPassword: (data) => api.post("/auth/reset-password", data),
    verifyOtp: (email, token, destination) => api.post("/auth/verify-otp", {email, token, destination}),
    getBDMOfficers: () => api.get("/bdm-officers"),
}

export const dashboardAPI = {
    // Agent endpoints (regular)
    getStats: () => api.get('/dashboard/stats'),
    getApplicationsTrend: () => api.get('/dashboard/applications-trend'),
    getApplicationsStatus: () => api.get('/dashboard/applications-status'),
    getMonthlyApplications: () => api.get('/dashboard/monthly-applications'),
    getRecentActivity: () => api.get('/dashboard/recent-activity'),

    // Counselor endpoints
    getCounselorStats: () => api.get('/admin/dashboard/stats'),
    getCounselorApplicationsTrend: () => api.get('/admin/dashboard/applications-trend'),
    getCounselorApplicationsStatus: () => api.get('/admin/dashboard/applications-status'),
    getCounselorMonthlyApplications: () => api.get('/admin/dashboard/monthly-applications'),
    getCounselorRecentActivity: () => api.get('/admin/dashboard/recent-activity'),
    getCounselorMonthlyAgentApplications: () => api.get('/admin/dashboard/monthly-agent-registration'),
}

export const applicationsAPI = {
    getAll: (page, perPage, search = '', start_date = '', end_date = '') => api.get('/applications', {params: {page, per_page: perPage, ...(search && {search}), ...(start_date && {start_date}), ...(end_date && {end_date})}}),
    getOne: (id) => api.get(`/applications/${id}`),
    getStatus: () => api.get(`/application-status`),
    create: (data) => {
        const formData = new FormData()

        // Add all form fields except files
        Object.keys(data).forEach((key) => {
            if (key !== "application_documents") {
                if (Array.isArray(data[key])) {
                    1
                    data[key].forEach((item, index) => {
                        formData.append(`${key}[${index}]`, item)
                    })
                } else {
                    formData.append(key, data[key])
                }
            }
        })

        // Add files with proper array syntax for Laravel
        if (data.application_documents && data.application_documents.length > 0) {
            data.application_documents.forEach((file, index) => {
                formData.append(`application_documents[${index}]`, file)
            })
        }

        return api.post("/applications", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
    },
    update: (id, data) => {
        const formData = new FormData()

        Object.keys(data).forEach((key) => {
            if (key !== "application_documents") {
                if (Array.isArray(data[key])) {
                    data[key].forEach((item, index) => {
                        formData.append(`${key}[${index}]`, item)
                    })
                } else {
                    formData.append(key, data[key])
                }
            }
        })

        if (data.application_documents && data.application_documents.length > 0) {
            data.application_documents.forEach((file, index) => {
                formData.append(`application_documents[${index}]`, file)
            })
        }

        return api.put(`/applications/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
    },
    patch: (id, data) => {
        const formData = new FormData()

        Object.keys(data).forEach((key) => {
            if (key !== "application_documents") {
                if (Array.isArray(data[key])) {
                    data[key].forEach((item, index) => {
                        formData.append(`${key}[${index}]`, item)
                    })
                } else {
                    formData.append(key, data[key])
                }
            }
        })

        if (data.application_documents && data.application_documents.length > 0) {
            data.application_documents.forEach((file, index) => {
                formData.append(`application_documents[${index}]`, file)
            })
        }

        return api.patch(`/applications/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
    },
    delete: (id) => api.delete(`/applications/${id}`),
    updateDocument(applicationId, payload) {
        // If payload is FormData (for file uploads)
        if (payload instanceof FormData) {
            // Add _method field for Laravel method spoofing
            payload.append('_method', 'PATCH');

            return api.post(`/update-application-document/${applicationId}`, payload, {
                headers: {"Content-Type": "multipart/form-data"}
            });
        }

        // Otherwise send JSON (for delete operations)
        return api.patch(`/update-application-document/${applicationId}`, payload, {
            headers: {"Content-Type": "application/json"}
        });
    },

    // Counselor endpoints
    getCounselorApplications: (page, perPage, search = '', start_date = '', end_date = '') => api.get('/admin/applications', {
        params: {
            page,
            per_page: perPage,
            ...(search && {search}),
            ...(start_date && {start_date}),
            ...(end_date && {end_date})
        }
    }),
    getCounselorApplicationById: (id) => api.get(`/admin/applications/${id}`),
    updateCounselorApplicationStatus: (id, data) => api.patch(`/admin/applications/${id}`, data),

}

export const studentsAPI = {
    // Agent endpoints (regular)
    getAll: (page, perPage, search = '', start_date = '', end_date = '') => api.get('/students', {params: {page, per_page: perPage, ...(search && {search}), ...(start_date && {start_date}), ...(end_date && {end_date})}}),
    getById: (id) => api.get(`/students/${id}`),
    create: (studentData) => api.post('/students', studentData),
    update: (id, studentData) => api.put(`/students/${id}`, studentData),
    delete: (id) => api.delete(`/students/${id}`),
    search: (query) => api.get(`/students/search?q=${query}`),
    validateStudent: (email) => api.post("/students/validate", {email}),

    // Counselor endpoints (assigned students only)
    getCounselorStudents: (page, perPage, search = '', start_date = '', end_date = '') => api.get('/admin/students', {params: {page, per_page: perPage, ...(search && {search}), ...(start_date && {start_date}), ...(end_date && {end_date})}}),
    getCounselorStudentById: (id) => api.get(`/admin/students/${id}`),
    updateCounselorStudent: (id, studentData) => api.put(`/admin/students/${id}`, studentData),
    searchCounselorStudents: (query) => api.get(`/admin/students/search?q=${query}`),
}

export const bdmOfficersAPI = {
    getAll: () => api.get("/bdm-officers"), getOne: (id) => api.get(`/bdm-officers/${id}`),
}

export const coursesAPI = {
    getAll: () => api.get("/courses"),
    search: (searchTerm = "", perPage = 10) => api.get("/courses", {params: {search: searchTerm, per_page: perPage}}),
    getOne: (id) => api.get(`/courses/${id}`),
}

export const schoolsAPI = {
    getAll: () => api.get("/schools"),
    search: (searchTerm = "", perPage = 10) => api.get("/schools", {params: {search: searchTerm, per_page: perPage}}),
    getOne: (id) => api.get(`/schools/${id}`),
}

export const countriesAPI = {
    getAll: () => api.get("/countries"),
    search: (searchTerm = "", perPage = 10) => api.get("/countries", {params: {search: searchTerm, per_page: perPage}}),
    getOne: (id) => api.get(`/countries/${id}`),
}

export const agentsAPI = {
    getAll: (page, per_page, term, start_date = '', end_date = '') => api.get('/admin/agents', {
        params: {
            page,
            per_page,
            ...(term && {term}),
            ...(start_date && {start_date}),
            ...(end_date && {end_date})
        }
    })
}

export default api
