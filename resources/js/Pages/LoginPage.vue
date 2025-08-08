<!-- resources/js/pages/LoginPage.vue -->
<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import LoginForm from '../components/LoginForm.vue'

const route = useRoute()
const role = computed(() => route.params.role) // "admins" or "users"
</script>

<template>
  <LoginForm :role="role" />
</template>
