import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { useAuthStore } from './auth'

export const usePostsStore = defineStore('posts', () => {
  // State
  const posts = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
    from: 0,
    to: 0
  })
  const meta = ref({
    count: 0,
    drafted: 0,
    published: 0
  })

  // Getters
  const publishedPosts = computed(() =>
    posts.value.filter(post => post.status === 'published')
  )

  const draftPosts = computed(() =>
    posts.value.filter(post => post.status === 'draft')
  )

  const totalPosts = computed(() => pagination.value.total)

  // Actions
  const fetchPosts = async (search, page = 1, perPage = 10) => {
    if (loading.value) return // Prevent duplicate requests
    loading.value = true
    error.value = null
    try {
      const authStore = useAuthStore()
      const response = await axios.get('/api/posts', {
        headers: {
          Authorization: `Bearer ${authStore.token}`
        },
        params: {
          search: search || '',
          page: page,
          per_page: perPage
        }
      })

      // Handle paginated response
      posts.value = response.data.data

      // Update pagination data
      if (response.data.meta) {
        pagination.value = {
          current_page: response.data.meta.current_page[0],
          last_page: response.data.meta.last_page[0],
          per_page: response.data.meta.per_page[0],
          total: response.data.meta.total[0] || response.data.meta.count,
          from: response.data.meta.from[0] || ((response.data.meta.current_page[0] - 1) * response.data.meta.per_page[0] + 1),
          to: response.data.meta.to[0] || Math.min(response.data.meta.current_page[0] * response.data.meta.per_page[0], response.data.meta.total[0] || response.data.meta.count)
        }

        meta.value = {
          count: response.data.meta.count || posts.value.length,
          drafted: response.data.meta.drafted || 0,
          published: response.data.meta.published || 0
        }
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch posts'
      console.error('Error fetching posts:', err)
    } finally {
      loading.value = false
    }
  }

  const createPost = async (postData) => {
    loading.value = true
    error.value = null

    try {
      const authStore = useAuthStore()

      const isFormData = postData instanceof FormData

      const response = await axios.post('/api/posts', postData, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          // Only set Content-Type for JSON, let browser set it for FormData
          ...(isFormData ? {} : { 'Content-Type': 'application/json' })
        }
      })

      // Add the new post to the beginning of the array
      posts.value.unshift(response.data.data.post)
      return response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create post'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updatePost = async (postId, postData) => {
    loading.value = true
    error.value = null

    try {
      const authStore = useAuthStore()

      // Determine if we're sending FormData or regular JSON
      const isFormData = postData instanceof FormData

      // For updates with FormData, we need to use POST with _method field
      const method = isFormData ? 'post' : 'patch'
      const url = isFormData ? `/api/posts/${postId}?_method=Patch` : `/api/posts/${postId}`

      if (isFormData) {
        postData.append('_method', 'PATCH')
      }

      const response = await axios[method](url, postData, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          // Only set Content-Type for JSON, let browser set it for FormData
          ...(isFormData ? {} : { 'Content-Type': 'application/json' })
        }
      })

      // Update the post in the array
      const index = posts.value.findIndex(post => post.id === postId)
      if (index !== -1) {
        posts.value[index] = response.data.data.post
      }

      return response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update post'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deletePost = async (postId) => {
    loading.value = true
    error.value = null

    try {
      const authStore = useAuthStore()
      await axios.delete(`/api/posts/${postId}`, {
        headers: {
          Authorization: `Bearer ${authStore.token}`
        }
      })

      // Remove the post from the array
      posts.value = posts.value.filter(post => post.id !== postId)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete post'
      throw err
    } finally {
      loading.value = false
    }
  }

  const getPostById = (postId) => {
    return posts.value.find(post => post.id === postId)
  }

  const clearPosts = () => {
    posts.value = []
    error.value = null
  }

  const clearError = () => {
    error.value = null
  }

  return {
    // State
    posts,
    loading,
    error,
    pagination,
    meta,

    // Getters
    publishedPosts,
    draftPosts,
    totalPosts,

    // Actions
    fetchPosts,
    createPost,
    updatePost,
    deletePost,
    getPostById,
    clearPosts,
    clearError
  }
})
