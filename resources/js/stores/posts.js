import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { useAuthStore } from './auth'

export const usePostsStore = defineStore('posts', () => {
  // State
  const posts = ref([])
  const loading = ref(false)
  const error = ref(null)

  // Getters
  const publishedPosts = computed(() => 
    posts.value.filter(post => post.status === 'published')
  )
  
  const draftPosts = computed(() => 
    posts.value.filter(post => post.status === 'draft')
  )
  
  const totalPosts = computed(() => posts.value.length)

  // Actions
  const fetchPosts = async (paginateObj) => {
    if (loading.value) return // Prevent duplicate requests
    const {pageNumber, perPage, search}= paginateObj;
    loading.value = true
    error.value = null
    
    try {
      const authStore = useAuthStore()
      const response = await axios.get('/api/posts', {
        headers: {
          Authorization: `Bearer ${authStore.token}`
        },
        params:{
            page:pageNumber,
            perPage:perPage,
            search:search
        }
      })
      posts.value = response.data.data
      console.log(posts.value);
      
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
      
      const response = await axios.post('/api/posts', postData, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          'Content-Type': 'application/json'
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
      const response = await axios.put(`/api/posts/${postId}`, postData, {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          'Content-Type': 'application/json'
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
