<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ title }}</h3>
        <button v-if="showCreateButton" @click="$emit('create')"
          class="bg-orange-600 text-white px-4 py-2 rounded-md text-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
          Create New Post
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-4">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-orange-600"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="posts.length === 0" class="text-center py-8 text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
          </path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No posts</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new post.</p>
      </div>

      <!-- Table View (for admin) -->
      <div v-else-if="viewMode === 'table'"
        class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
              <th v-if="showActions" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
              <td v-if="showActions" class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button @click="$emit('edit', post)" class="text-orange-600 hover:text-orange-900">

                  Edit
                </button>
                <button @click="$emit('delete', post)" class="text-red-600 hover:text-red-900">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- News Feed View (enhanced card view) -->
      <div v-else class="space-y-6">
        <article v-for="post in posts" :key="post.id"
          class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">

          <!-- Post Header -->
          <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <!-- Author Avatar (placeholder) -->
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                  <span class="text-orange-600 font-semibold text-sm">
                    {{ post.author?.name?.charAt(0)?.toUpperCase() || 'U' }}
                  </span>
                </div>
                <div>
                  <h5 class="text-sm font-medium text-gray-900">{{ post.author?.name || 'Unknown Author' }}</h5>
                  <p class="text-xs text-gray-500">{{ formatDate(post.created_at) }}</p>
                </div>
              </div>

              <!-- Status Badge -->
              <StatusBadge :status="post.status" />
            </div>
          </div>

          <!-- Post Content -->
          <div class="px-6 py-4">
            <!-- Post Title -->
            <h2 class="text-xl font-bold text-gray-900 mb-3 leading-tight">{{ post.title }}</h2>

            <!-- Post Content -->
            <p class="text-gray-700 text-base leading-relaxed mb-4 line-clamp-4">{{ post.content }}</p>

            <!-- Post Image -->
            <div v-if="post.image" class="mb-4 rounded-lg overflow-hidden">
              <img :src="getImageUrl(post.image)" :alt="post.title"
                class="w-full h-64 object-cover hover:scale-105 transition-transform duration-300"
                @error="handleImageError" />
            </div>
          </div>

          <!-- Post Actions -->
          <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
            <div class="flex items-center justify-between">
              <!-- Engagement Stats (placeholder) -->
              <div class="flex items-center space-x-4 text-sm text-gray-500">
                <span class="flex items-center space-x-1">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                  </svg>
                  <span>{{ post.likes || 0 }}</span>
                </span>
                <span class="flex items-center space-x-1">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                      clip-rule="evenodd" />
                  </svg>
                  <span>{{ post.comments || 0 }}</span>
                </span>
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center space-x-2">
                <button @click="$emit('edit', post)"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 hover:border-orange-300 hover:text-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-colors">
                  <PencilIcon class="w-3 h-3 mr-1" />
                  Edit
                </button>
                <button @click="$emit('delete', post)"
                  class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 hover:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                  <TrashIcon class="w-3 h-3 mr-1" />
                  Delete
                </button>
              </div>
            </div>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import TrashIcon from '../ui/TrashIcon.vue'
import PencilIcon from '../ui/PencilIcon.vue'
import StatusBadge from '../ui/StatusBadge.vue'

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
  showActions: {
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
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Image handling functions
const getImageUrl = (imagePath) => {
  if (!imagePath) return null

  // If it's already a full URL, return as is
  if (imagePath.startsWith('http')) {
    return imagePath
  }

  // If it's a relative path, prepend the storage URL
  return `/storage/${imagePath}`
}

const handleImageError = (event) => {
  // Hide the image if it fails to load
  event.target.style.display = 'none'
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

.line-clamp-4 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 4;
  line-clamp: 4;
}

/* News feed article styling */
article {
  background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
}

article:hover {
  transform: translateY(-2px);
}

/* Image hover effect */
img {
  transition: transform 0.3s ease;
}

/* Enhanced button styling */
button {
  transition: all 0.2s ease;
}

button:hover {
  transform: translateY(-1px);
}

/* Author avatar animation */
.w-10.h-10 {
  transition: transform 0.2s ease;
}

.w-10.h-10:hover {
  transform: scale(1.1);
}

/* Status badge positioning */
.status-badge {
  position: relative;
  z-index: 1;
}

/* Engagement stats hover effect */
.flex.items-center.space-x-4 span:hover {
  color: #ea580c;
  cursor: pointer;
}
</style>
