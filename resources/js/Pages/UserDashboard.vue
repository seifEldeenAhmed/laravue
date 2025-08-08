<template>
  <DashboardLayout title="Admin Dashboard">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <StatsCard
        title="Total Posts"
        :value="postsStore.totalPosts"
        icon="users"
        color="indigo"
      />
      <StatsCard
        title="Published Posts"
        :value="postsStore.publishedPosts.length"
        icon="check"
        color="green"
      />
      <StatsCard
        title="Draft Posts"
        :value="postsStore.draftPosts.length"
        icon="clock"
        color="yellow"
      />
    </div>

    <!-- Posts Management -->
    <PostsList
      title="All Posts"
      :posts="postsStore.posts"
      :loading="postsStore.loading"
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
import { usePostsStore } from '../stores/posts'
import DashboardLayout from '../layouts/DashboardLayout.vue'
import StatsCard from '../components/dashboard/StatsCard.vue'
import PostsList from '../components/dashboard/PostsList.vue'
import PostModal from '../components/dashboard/PostModal.vue'

const authStore = useAuthStore()
const postsStore = usePostsStore()

const showCreateModal = ref(false)
const editingPost = ref(null)

const editPost = (post) => {
  editingPost.value = post
  showCreateModal.value = true
}

const deletePost = async (post) => {
  if (!confirm('Are you sure you want to delete this post?')) return
  
  try {
    await postsStore.deletePost(post.id)
  } catch (error) {
    alert('Error deleting post: ' + (postsStore.error || 'Unknown error'))
  }
}

const handlePostSubmit = async (postData) => {
  try {
    if (postData.id) {
      // Update existing post
      await postsStore.updatePost(postData.id, postData)
    } else {
      // Create new post
      await postsStore.createPost(postData)
    }
    closeModal()
  } catch (error) {
    console.error('Error saving post:', error)
    throw error
  }
}

const closeModal = () => {
  showCreateModal.value = false
  editingPost.value = null
  postsStore.clearError()
}

onMounted(() => {
  // Only fetch if we don't have posts already
  if (postsStore.posts.length === 0) {
    postsStore.fetchPosts()
  }
})
</script>
