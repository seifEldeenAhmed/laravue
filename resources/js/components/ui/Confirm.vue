<template>
    <div v-if="show" id="popup-modal" tabindex="0"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden"
        style="background-color: rgba(0, 0, 0, 0.25);" @click="handleClose">
        <div class="relative p-4 w-full max-w-md max-h-full" @click.stop>
            <div class="relative bg-white rounded-lg shadow-xl border border-gray-200">
                <button type="button" @click="handleClose"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <div class="flex justify-center">
                        <Image :src="logoImage" size="xl" variant="circle" />

                    </div>

                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to
                        delete this post?</h3>
                    <button type="button" @click="handleConfirm"
                        class="text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Delete
                    </button>
                    <button type="button" @click="handleClose"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-700 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-orange-500 focus:z-10 focus:ring-4 focus:ring-gray-100">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Image from './Image.vue';
import logoImage from '../../../assets/logo.png';

defineProps({
    show: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['confirm', 'close']);

const handleClose = () => {
    emit('close');
}

const handleConfirm = () => {
    emit('confirm');
}
</script>