<template>
    <div class="image-upload-container">
        <div class="upload-area" @click="triggerFileInput" @dragover.prevent @drop.prevent="handleDrop">
            <div v-if="!imgSrc" class="upload-placeholder">
                <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" />
                </svg>
                <p class="upload-text">Click to upload or drag and drop</p>
                <p class="upload-subtext">PNG, JPG, GIF up to 2MB</p>
            </div>
            <div v-else class="image-preview">
                <img :src="imgSrc" alt="Preview" class="preview-image" />
                <div class="image-overlay">
                    <button @click.stop="removeImage" class="remove-btn">
                        <svg class="remove-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <input ref="fileInput" type="file" @change="preview" accept="image/*" class="hidden-input" />
        <div v-if="uploaded" class="upload-status">
            <p class="success-message">✓ Image uploaded successfully!</p>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const imgSrc = ref('')
const uploaded = ref(false)
const fileInput = ref(null)

const props = defineProps({
    imgSrc: {
        type: String,
        default: ''
    }
})

const triggerFileInput = () => {
    fileInput.value.click()
}

const preview = (e) => {
    const file = e.target.files[0]
    if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
            imgSrc.value = e.target.result
        }
        reader.readAsDataURL(file)
        uploaded.value = false
    }
}

const handleDrop = (e) => {
    const files = e.dataTransfer.files
    if (files.length > 0) {
        const file = files[0]
        if (file.type.startsWith('image/')) {
            const reader = new FileReader()
            reader.onload = (e) => {
                imgSrc.value = e.target.result
            }
            reader.readAsDataURL(file)
            uploaded.value = false
        }
    }
}
const getImageUrl = (imagePath) => {
    if (!imagePath) return null

    // If it's already a full URL, return as is
    if (imagePath.startsWith('http')) {
        return imagePath
    }

    // If it's a relative path, prepend the storage URL
    return `/storage/${imagePath}`
}

watch(() => props.imgSrc, (newSrc) => {
    if (newSrc) {
        imgSrc.value = getImageUrl(newSrc);
    }
}, { immediate: true })
const removeImage = () => {
    imgSrc.value = ''
    uploaded.value = false
    if (fileInput.value) {
        fileInput.value.value = ''
    }
}


// Expose the upload function for parent components
defineExpose({
    getFile: () => fileInput.value?.files[0],
    clear: removeImage,
})
</script>

<style scoped>
.image-upload-container {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
}

.upload-area {
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8fafc;
    position: relative;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-area:hover {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

.upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.upload-icon {
    width: 48px;
    height: 48px;
    color: #64748b;
    margin-bottom: 0.5rem;
}

.upload-text {
    font-size: 1.1rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
}

.upload-subtext {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

.image-preview {
    position: relative;
    width: 100%;
    height: 100%;
    border-radius: 8px;
    overflow: hidden;
}

.preview-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-preview:hover .image-overlay {
    opacity: 1;
}

.remove-btn {
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.remove-btn:hover {
    background: #dc2626;
}

.remove-icon {
    width: 20px;
    height: 20px;
}

.hidden-input {
    display: none;
}

.upload-status {
    margin-top: 1rem;
    text-align: center;
}

.success-message {
    color: #059669;
    font-weight: 500;
    margin: 0;
    padding: 0.5rem;
    background: #ecfdf5;
    border-radius: 6px;
    border: 1px solid #a7f3d0;
}

/* Responsive design */
@media (max-width: 640px) {
    .upload-area {
        padding: 1.5rem;
        min-height: 150px;
    }

    .upload-icon {
        width: 40px;
        height: 40px;
    }

    .upload-text {
        font-size: 1rem;
    }

    .upload-subtext {
        font-size: 0.8rem;
    }

    .preview-image {
        height: 150px;
    }
}
</style>