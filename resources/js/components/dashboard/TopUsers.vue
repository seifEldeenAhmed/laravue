<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Top Contributors</h3>
                        <p class="text-sm text-gray-500">Most active content creators</p>
                    </div>
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Trending
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div v-if="topUsers.length === 0" class="text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <p class="text-gray-500">No users data available</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="(user, index) in topUsers" :key="user.id"
                    class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r hover:from-gray-50 hover:to-orange-50 transition-all duration-300 group border border-transparent hover:border-orange-100">

                    <!-- User Info -->
                    <div class="flex items-center space-x-4">

                        <!-- Avatar -->
                        <div class="flex-shrink-0 relative">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow duration-300">
                                <span class="text-white font-semibold text-lg">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>

                            <!-- Crown Component for Top 3 -->
                            <Crown :rank="index + 1" size="lg" position="top-left" :rotation="true" :translate-x="-2"
                                :translate-y="-14" />
                        </div>

                        <!-- User Details -->
                        <div class="flex-1 min-w-0">
                            <h4
                                class="text-base font-semibold text-gray-900 group-hover:text-orange-600 transition-colors duration-200">
                                {{ user.name }}
                            </h4>
                            <p class="text-sm text-gray-500">
                                Content Creator
                            </p>
                        </div>
                    </div>

                    <!-- Posts Count -->
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <div
                                class="text-2xl font-bold text-gray-900 group-hover:text-orange-600 transition-colors duration-200">
                                {{ user.posts_count }}
                            </div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">
                                {{ user.posts_count === 1 ? 'Post' : 'Posts' }}
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-2 h-16 bg-gray-200 rounded-full overflow-hidden">
                            <div :style="{ height: getProgressHeight(user.posts_count) }"
                                class="w-full bg-gradient-to-t from-orange-500 to-amber-400 rounded-full transition-all duration-500 ease-out">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">
                    Updated {{ getLastUpdated() }}
                </span>
                <button class="text-orange-600 hover:text-orange-700 font-medium transition-colors duration-200">
                    <router-link to="/posts">
                        View All →
                    </router-link>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useDashboardStore } from '../../stores/dashboard';
import Crown from '../ui/Crown.vue';
import { computed } from 'vue';

const dashboardStore = useDashboardStore();
const topUsers = computed(() => dashboardStore.topUsers);

// Get ranking badge class based on position
const getRankingBadgeClass = (index) => {
    switch (index) {
        case 0:
            return 'bg-gradient-to-br from-yellow-400 to-yellow-600'; // Gold
        case 1:
            return 'bg-gradient-to-br from-gray-300 to-gray-500'; // Silver
        case 2:
            return 'bg-gradient-to-br from-orange-400 to-orange-600'; // Bronze
        default:
            return 'bg-gradient-to-br from-blue-400 to-blue-600'; // Default blue
    }
}

// Calculate progress bar height based on posts count
const getProgressHeight = (postsCount) => {
    if (topUsers.value.length === 0) return '0%';

    const maxPosts = Math.max(...topUsers.value.map(user => user.posts_count));
    const percentage = (postsCount / maxPosts) * 100;
    return `${Math.max(percentage, 10)}%`; // Minimum 10% for visibility
}

// Get last updated time (placeholder)
const getLastUpdated = () => {
    return 'just now';
}
</script>