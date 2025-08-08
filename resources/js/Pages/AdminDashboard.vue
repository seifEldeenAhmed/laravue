<template>
  <DashboardLayout title="Admin Dashboard">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <StatsCard title="Total Posts" :value="totalPosts" icon="document" color="orange" />
      <StatsCard title="Published Posts" :value="publishedPosts" icon="check" color="green" />
      <StatsCard title="Draft Posts" :value="draftPosts" icon="clock" color="yellow" />
    </div>


  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useDashboardStore } from "../stores/dashboard.js";
import DashboardLayout from '../layouts/DashboardLayout.vue'
import StatsCard from '../components/dashboard/StatsCard.vue'

const dashboardStore = useDashboardStore()

const totalPosts = computed(() => dashboardStore.totalPosts || 0)
const draftPosts = computed(() => dashboardStore.draftPosts || 0)
const publishedPosts = computed(() => dashboardStore.publishedPosts || 0)
const totalUsers = computed(() => dashboardStore.totalUsers || 0)
const topUsers = computed(() => dashboardStore.topUsers || [])

onMounted(() => {
  dashboardStore.fetchStats()
})
</script>
