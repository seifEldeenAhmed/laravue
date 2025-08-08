import axios from 'axios'

// Set base URL for API requests
axios.defaults.baseURL = window.location.origin
axios.defaults.withCredentials = true

// Add CSRF token to requests
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}

// Request interceptor to add auth token
axios.interceptors.request.use((config) => {
    const authToken = localStorage.getItem('auth_token')
    if (authToken) {
        config.headers.Authorization = `Bearer ${authToken}`
    }
    return config
})

// Response interceptor to handle auth errors
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Clear auth data on 401
            localStorage.removeItem('auth_token')
            localStorage.removeItem('user_type')
            localStorage.removeItem('user_data')
            
            // Redirect to login
            const currentPath = window.location.pathname
            if (!currentPath.includes('/login')) {
                window.location.href = '/users/login'
            }
        }
        return Promise.reject(error)
    }
)

export default axios
