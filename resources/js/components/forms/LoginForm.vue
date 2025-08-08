<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div class="flex max-w-md w-full space-x-0 justify-center">
        <LogoComponent :src=logoImage alt="Company Logo" size="xxl" variant="default" />
      </div>

      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-600">
          {{ pageTitle }}
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Sign in to your {{ roleDisplay }} account
        </p>
      </div>

      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="email" class="sr-only">Email address</label>
            <input id="email" v-model="email" name="email" type="email" autocomplete="email" required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm"
              placeholder="Email address" />
          </div>
          <div>
            <label for="password" class="sr-only">Password</label>
            <input id="password" v-model="password" name="password" type="password" autocomplete="current-password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm"
              placeholder="Password" />
          </div>
        </div>

        <div v-if="error" class="text-red-600 text-sm text-center">
          {{ error }}
        </div>

        <div>
          <button type="submit" :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed">
            <span v-if="loading" class="mr-2">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
              </svg>
            </span>
            {{ loading ? 'Signing in...' : 'Sign in' }}
          </button>
        </div>

        <div class="text-center">
          <router-link :to="switchRoleLink" class="text-orange-600 hover:text-orange-500 text-sm">
            Switch to {{ switchRoleText }} login
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import LogoComponent from '../ui/Image.vue'
import logoImage from '../../../assets/logo.png'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const props = defineProps({
  role: {
    type: String,
    required: true,
    validator: (value) => ['admins', 'users'].includes(value)
  }
})

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

// Computed properties
const roleDisplay = computed(() => {
  return props.role === 'admins' ? 'Admin' : 'User'
})

const pageTitle = computed(() => {
  return `${roleDisplay.value} Login`
})

const switchRoleText = computed(() => {
  return props.role === 'admins' ? 'User' : 'Admin'
})

const switchRoleLink = computed(() => {
  const targetRole = props.role === 'admins' ? 'users' : 'admins'
  return `/${targetRole}/login`
})

// Methods
const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    const credentials = {
      email: email.value,
      password: password.value
    }

    await authStore.login(credentials, props.role)

    // Redirect to appropriate dashboard
    const dashboardRoute = props.role === 'admins' ? '/admins' : '/users'
    router.push(dashboardRoute)
  } catch (err) {
    error.value = err.response?.data?.message || err.message || 'Login failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
