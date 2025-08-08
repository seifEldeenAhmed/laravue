import {defineStore} from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { useAuthStore } from './auth'


export const useDashboardStore = defineStore('dashboard', () => {
    // State
    const stats = ref({})
    const loading = ref(false)
    const error = ref(null)

    // Getters
    const topUsers = computed(() => stats.value.top_users || [])
    const draftPosts = computed(() => stats.value.draft_posts || 0)
    const publishedPosts = computed(() => stats.value.published_posts || 0)
    const totalPosts = computed(() => stats.value.total_posts || 0)
    const totalUsers = computed(() => stats.value.total_users || 0)

    // Actions
    const fetchStats = async () => {
        if (loading.value) return // Prevent duplicate requests
        loading.value = true
        error.value = null

        try {
        const authStore = useAuthStore()
        const response = await axios.get('/api/admin/dashboard', {
            headers: {
            Authorization: `Bearer ${authStore.token}`
            }
        })
        stats.value = response.data.data.stats
        } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch dashboard stats'
        console.error('Error fetching dashboard stats:', err)
        } finally {
        loading.value = false
        }
    }

    return {
        stats,
        loading,
        error,
        topUsers,
        totalPosts,
        draftPosts,
        publishedPosts,
        totalUsers,
        fetchStats
    }
});
