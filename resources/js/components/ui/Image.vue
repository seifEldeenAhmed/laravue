<script setup>
import { computed } from 'vue'

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: 'Logo'
  },
  size: {
    type: String,
    default: 'md', // xs, sm, md, lg, xl
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl','xxl'].includes(value)
  },
  variant: {
    type: String,
    default: 'default', // default, circle, square
    validator: (value) => ['default', 'circle', 'square'].includes(value)
  }
})

const logoClass = computed(() => {
  const sizes = {
    xs: 'w-6 h-6',
    sm: 'w-8 h-8', 
    md: 'w-12 h-12',
    lg: 'w-16 h-16',
    xl: 'w-24 h-24',
    xxl: 'w-42 h-42'
  }
  
  const variants = {
    default: 'object-contain',
    circle: 'object-cover rounded-full',
    square: 'object-cover rounded-lg'
  }
  
  return `${sizes[props.size]} ${variants[props.variant]}`
})
</script>

<template>
  <img 
    :src="src" 
    :alt="alt" 
    :class="logoClass"
    class="transition-opacity hover:opacity-80"
  />
</template>