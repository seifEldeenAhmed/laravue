<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation Header -->
    <nav class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center space-x-2">
            <div class="flex me-4">
              <LogoComponent :src=logoImage alt="Company Logo" size="lg" variant="default" />
            </div>
            <div class="flex space-x-6">
              <router-link :to="authStore.isAdmin ? '/admins' : '/users'"
                class="text-gray-500 hover:text-orange-800 px-3 py-2 rounded-md text-sm font-medium"
                :class="{ 'text-orange-600 font-semibold': route.path === (authStore.isAdmin ? '/admins' : '/users') }">
                Dashboard
              </router-link>
              <router-link to="/posts"
                class="text-gray-500 hover:text-orange-800 px-3 py-2 rounded-md text-sm font-medium"
                :class="{ 'text-orange-600 font-semibold': route.path === '/posts' }">
                Posts
              </router-link>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-700">Welcome, {{ authStore.user?.name }}</span>
            <button @click="handleLogout"
              class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import LogoComponent from '../components/ui/Image.vue'
import logoImage from '../../assets/logo.png'

defineProps({
  title: {
    type: String,
    required: true
  }
})

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const handleLogout = async () => {
  try {
    await authStore.logout()
    const redirectPath = authStore.isAdmin ? '/admins/login' : '/users/login'
    router.push(redirectPath)
  } catch (error) {
    console.error('Logout error:', error)
    const redirectPath = authStore.isAdmin ? '/admins/login' : '/users/login'
    router.push(redirectPath)
  }
}
</script>
