import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token') || null)
  const userType = ref(localStorage.getItem('user_type') || null) // 'admin' or 'user'

  // Getters
  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => userType.value === 'admin')
  const isUser = computed(() => userType.value === 'user')

  // Actions
  const login = async (credentials, role) => {
    try {
      const endpoint = role === 'admins' ? '/api/auth/admin/login' : '/api/auth/user/login'
      
      const response = await axios.post(endpoint, credentials)
      
      if (response.data.success) {
        token.value = response.data.data.auth.token
        user.value = response.data.data.user
        userType.value = role === 'admins' ? 'admin' : 'user'
        
        // Store in localStorage
        localStorage.setItem('auth_token', token.value)
        localStorage.setItem('user_type', userType.value)
        localStorage.setItem('user_data', JSON.stringify(user.value))
        
        // Set default Authorization header
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        
        return response.data
      } else {
        throw new Error(response.data.message || 'Login failed')
      }
    } catch (error) {
      console.error('Login error:', error)
      throw error
    }
  }

  const logout = async () => {
    try {
      const endpoint = isAdmin.value ? '/api/auth/admin/logout' : '/api/auth/user/logout'
      await axios.post(endpoint)
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      // Clear state regardless of API response
      clearAuthData()
    }
  }

  const clearAuthData = () => {
    user.value = null
    token.value = null
    userType.value = null
    
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_type')
    localStorage.removeItem('user_data')
    
    delete axios.defaults.headers.common['Authorization']
  }

  const initializeAuth = () => {
    const storedToken = localStorage.getItem('auth_token')
    const storedUserType = localStorage.getItem('user_type')
    const storedUserData = localStorage.getItem('user_data')
    
    if (storedToken && storedUserType) {
      token.value = storedToken
      userType.value = storedUserType
      user.value = storedUserData ? JSON.parse(storedUserData) : null
      
      // Set default Authorization header
      axios.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`
    }
  }

  // Initialize auth on store creation
  initializeAuth()

  return {
    // State
    user,
    token,
    userType,
    // Getters
    isAuthenticated,
    isAdmin,
    isUser,
    // Actions
    login,
    logout,
    clearAuthData,
    initializeAuth
  }
})
