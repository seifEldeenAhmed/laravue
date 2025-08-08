<template>
  <DashboardLayout title="Admin Dashboard">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <StatsCard
        title="Total Posts"
        :value="stats.totalPosts"
        icon="users"
        color="indigo"
      />
      <StatsCard
        title="Published Posts"
        :value="stats.publishedPosts"
        icon="check"
        color="green"
      />
      <StatsCard
        title="Draft Posts"
        :value="stats.draftPosts"
        icon="clock"
        color="yellow"
      />
    </div>

    <!-- Posts Management -->
    <PostsList
      title="All Posts"
      :posts="posts"
      :loading="loading"
      view-mode="table"
      @create="showCreateModal = true"
      @edit="editPost"
      @delete="deletePost"
    />

    <!-- Create/Edit Post Modal -->
    <PostModal
      :show="showCreateModal"
      :post="editingPost"
      @close="closeModal"
      @submit="handlePostSubmit"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import DashboardLayout from '../layouts/DashboardLayout.vue'
import StatsCard from '../components/StatsCard.vue'
import PostsList from '../components/PostsList.vue'
import PostModal from '../components/PostModal.vue'
import axios from 'axios'

const authStore = useAuthStore()

const posts = ref([])
const loading = ref(true)
const showCreateModal = ref(false)
const editingPost = ref(null)

const stats = computed(() => {
  const totalPosts = posts.value.length
  const publishedPosts = posts.value.filter(post => post.status === 'published').length
  const draftPosts = posts.value.filter(post => post.status === 'draft').length
  
  return {
    totalPosts,
    publishedPosts,
    draftPosts
  }
})

const fetchPosts = async () => {
  try {
    const response = await axios.get('/api/posts')
    if (response.data.success) {
      posts.value = response.data.data.data || []
    }
  } catch (error) {
    console.error('Error fetching posts:', error)
  } finally {
    loading.value = false
  }
}

const editPost = (post) => {
  editingPost.value = post
  showCreateModal.value = true
}

const deletePost = async (post) => {
  if (!confirm('Are you sure you want to delete this post?')) return
  
  try {
    await axios.delete(`/api/posts/${post.id}`)
    posts.value = posts.value.filter(p => p.id !== post.id)
  } catch (error) {
    console.error('Error deleting post:', error)
  }
}

const handlePostSubmit = async (postData) => {
  try {
    if (postData.id) {
      // Update existing post
      const response = await axios.put(`/api/posts/${postData.id}`, postData)
      if (response.data.success) {
        const index = posts.value.findIndex(p => p.id === postData.id)
        if (index !== -1) {
          posts.value[index] = response.data.data.post
        }
      }
    } else {
      // Create new post
      const response = await axios.post('/api/posts', postData)
      if (response.data.success) {
        posts.value.unshift(response.data.data.post)
      }
    }
  } catch (error) {
    console.error('Error saving post:', error)
    throw error
  }
}

const closeModal = () => {
  showCreateModal.value = false
  editingPost.value = null
}

onMounted(() => {
  fetchPosts()
})
</script>
