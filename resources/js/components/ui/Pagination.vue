<template>
    <div v-if="pagination.last_page > 1"
        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">

        <!-- Desktop pagination -->
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ pagination.from }}</span>
                    to
                    <span class="font-medium">{{ pagination.to }}</span>
                    of
                    <span class="font-medium">{{ pagination.total }}</span>
                    results
                </p>
            </div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <!-- Previous button -->
                    <button @click="$emit('page-change', pagination.current_page - 1)"
                        :disabled="pagination.current_page === 1"
                        class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:bg-gray-100 disabled:cursor-not-allowed">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Page numbers -->
                    <template v-for="page in visiblePages" :key="page">
                        <button v-if="page !== '...'" @click="$emit('page-change', page)" :class="[
                            page === pagination.current_page
                                ? 'relative z-10 inline-flex items-center bg-orange-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600'
                                : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
                        ]">
                            {{ page }}
                        </button>
                        <span v-else
                            class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">
                            ...
                        </span>
                    </template>

                    <!-- Next button -->
                    <button @click="$emit('page-change', pagination.current_page + 1)"
                        :disabled="pagination.current_page === pagination.last_page"
                        class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:bg-gray-100 disabled:cursor-not-allowed">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    pagination: {
        type: Object,
        required: true
    }
})

defineEmits(['page-change'])

// Calculate visible page numbers
const visiblePages = computed(() => {
    const current = props.pagination.current_page
    const last = props.pagination.last_page
    const delta = 2 // Number of pages to show on each side of current page

    let pages = []

    // Always show first page
    if (current - delta > 1) {
        pages.push(1)
        if (current - delta > 2) {
            pages.push('...')
        }
    }

    // Show pages around current page
    for (let i = Math.max(1, current - delta); i <= Math.min(last, current + delta); i++) {
        pages.push(i)
    }

    // Always show last page
    if (current + delta < last) {
        if (current + delta < last - 1) {
            pages.push('...')
        }
        pages.push(last)
    }

    return pages
})
</script>

<style scoped>
/* Additional styling if needed */
</style>
