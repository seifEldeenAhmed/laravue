<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ title }}</h3>
        <button
          v-if="showCreateButton"
          @click="$emit('create')"
          class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          Create New Post
        </button>
      </div>
      
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-4">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      </div>
      
      <!-- Empty State -->
      <div v-else-if="posts.length === 0" class="text-center py-8 text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No posts</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new post.</p>
      </div>
      
      <!-- Table View (for admin) -->
      <div v-else-if="viewMode === 'table'" class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="post in posts" :key="post.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ post.title }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ post.author?.name || 'Unknown' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <StatusBadge :status="post.status" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(post.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button @click="$emit('edit', post)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                <button @click="$emit('delete', post)" class="text-red-600 hover:text-red-900">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Card View (for user) -->
      <div v-else class="space-y-4">
        <div v-for="post in posts" :key="post.id" class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h4 class="text-lg font-medium text-gray-900">{{ post.title }}</h4>
              <p class="mt-1 text-sm text-gray-500 line-clamp-3">{{ post.content }}</p>
              <div class="mt-2 flex items-center space-x-4">
                <StatusBadge :status="post.status" />
                <span class="text-sm text-gray-500">{{ formatDate(post.created_at) }}</span>
              </div>
            </div>
            <div class="ml-4 flex-shrink-0 space-x-2">
              <button @click="$emit('edit', post)" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
              <button @click="$emit('delete', post)" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import StatusBadge from './StatusBadge.vue'

defineProps({
  posts: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Posts'
  },
  showCreateButton: {
    type: Boolean,
    default: true
  },
  viewMode: {
    type: String,
    default: 'card', // 'table' or 'card'
    validator: (value) => ['table', 'card'].includes(value)
  }
})

defineEmits(['create', 'edit', 'delete'])

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}
</script>

<style scoped>
.line-clamp-3 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
  line-clamp: 3;
}
</style>
