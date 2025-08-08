<template>
  <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="$emit('close')">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
      <div class="mt-3">
        <h3 class="text-lg font-medium text-gray-900 text-center">{{ title }}</h3>
        <form @submit.prevent="handleSubmit" class="mt-4 space-y-4">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              required
              class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Enter post title"
            >
          </div>
          
          <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
            <textarea
              id="content"
              v-model="form.content"
              required
              rows="4"
              class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Enter post content"
            ></textarea>
          </div>
          
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select
              id="status"
              v-model="form.status"
              class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="draft">Draft</option>
              <option value="published">Published</option>
            </select>
          </div>
          
          <div v-if="error" class="text-red-600 text-sm">
            {{ error }}
          </div>
          
          <div class="flex space-x-2">
            <button
              type="submit"
              :disabled="loading"
              class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              {{ loading ? 'Saving...' : (isEdit ? 'Update' : 'Create') }}
            </button>
            <button
              type="button"
              @click="$emit('close')"
              class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  post: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'submit'])

const form = ref({
  title: '',
  content: '',
  status: 'draft'
})

const loading = ref(false)
const error = ref('')

const isEdit = computed(() => !!props.post)
const title = computed(() => isEdit.value ? 'Edit Post' : 'Create New Post')

// Watch for post changes to populate form for editing
watch(() => props.post, (newPost) => {
  if (newPost) {
    form.value = {
      title: newPost.title || '',
      content: newPost.content || '',
      status: newPost.status || 'draft'
    }
  } else {
    form.value = {
      title: '',
      content: '',
      status: 'draft'
    }
  }
  error.value = ''
}, { immediate: true })

// Reset form when modal is closed
watch(() => props.show, (isShowing) => {
  if (!isShowing) {
    error.value = ''
    loading.value = false
  }
})

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  
  try {
    await emit('submit', { ...form.value, id: props.post?.id })
    emit('close')
  } catch (err) {
    error.value = err.message || 'An error occurred'
  } finally {
    loading.value = false
  }
}
</script>
