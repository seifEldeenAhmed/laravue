<template>
    <!-- Fixed container positioned at top center -->
    <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50">
        <Transition enter-active-class="transition-all duration-500 ease-out"
            enter-from-class="transform -translate-y-full opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition-all duration-400 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform -translate-y-full opacity-0">
            <div v-if="visible" :class="toastClasses"
                class="flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-lg border"
                role="alert">
                <div :class="iconClasses" class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path :d="iconPath" />
                    </svg>
                    <span class="sr-only">{{ type }} icon</span>
                </div>

                <div class="ms-3 text-sm font-normal">{{ message }}</div>

                <button v-if="dismissible" type="button" @click="closeToast"
                    class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8"
                    aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'

const props = defineProps({
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    message: {
        type: String,
        required: true
    },
    duration: {
        type: Number,
        default: 4000 // 4 seconds
    },
    dismissible: {
        type: Boolean,
        default: true
    },
    show: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['close'])

const visible = ref(props.show)
let timeoutId = null

// Watch for prop changes
watch(() => props.show, (newValue) => {
    if (newValue) {
        showToast()
    } else {
        visible.value = false
    }
})

// Toast styles based on type
const toastStyles = {
    success: {
        iconClasses: 'text-green-500 bg-green-100',
        borderClass: 'border-green-200',
        textClass: 'text-green-800'
    },
    error: {
        iconClasses: 'text-red-500 bg-red-100',
        borderClass: 'border-red-200',
        textClass: 'text-red-800'
    },
    warning: {
        iconClasses: 'text-yellow-500 bg-yellow-100',
        borderClass: 'border-yellow-200',
        textClass: 'text-yellow-800'
    },
    info: {
        iconClasses: 'text-blue-500 bg-blue-100',
        borderClass: 'border-blue-200',
        textClass: 'text-blue-800'
    }
}

// SVG paths for different types
const iconPaths = {
    success: 'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z',
    error: 'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 0 1-1-1V9a1 1 0 0 1 2 0v5a1 1 0 0 1-1 1Zm0-8a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z',
    warning: 'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 0 1-1-1V9a1 1 0 0 1 2 0v5a1 1 0 0 1-1 1Zm0-8a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z',
    info: 'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z'
}

const toastClasses = computed(() => {
    return `${toastStyles[props.type].borderClass} ${toastStyles[props.type].textClass}`
})

const iconClasses = computed(() => {
    return toastStyles[props.type].iconClasses
})

const iconPath = computed(() => {
    return iconPaths[props.type]
})

const showToast = () => {
    visible.value = true

    if (props.duration > 0) {
        clearTimeout(timeoutId)
        timeoutId = setTimeout(() => {
            closeToast()
        }, props.duration)
    }
}

const closeToast = () => {
    visible.value = false
    clearTimeout(timeoutId)
    emit('close')
}

// Show toast on mount if show prop is true
onMounted(() => {
    if (props.show) {
        showToast()
    }
})

// Expose methods for parent components
defineExpose({
    showToast,
    closeToast
})
</script>