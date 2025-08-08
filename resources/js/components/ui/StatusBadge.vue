<template>
  <span :class="statusClass" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
    {{ status }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: true,
    validator: (value) => ['published', 'draft'].includes(value)
  }
})

const statusClass = computed(() => {
  return props.status === 'published' 
    ? 'bg-green-100 text-green-800'
    : 'bg-yellow-100 text-yellow-800'
})
</script>
