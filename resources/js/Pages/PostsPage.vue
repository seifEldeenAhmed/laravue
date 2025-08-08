<template>
    <DashboardLayout title="Posts">
        <div class="space-y-6">
            <!-- Posts Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">All Posts</h2>
                <input
                    class="appearance-none rounded-none relative block w-100 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm"
                    type="text" v-model="search" name="search" id="search" placeholder="search"
                    @change="handleSearch" />
            </div>

            <!-- Posts List -->
            <PostsList :posts="postsStore.posts" :loading="postsStore.loading"
                :user-role="authStore.isAdmin ? 'admin' : 'user'" @create="openCreateModal" @edit="openEditModal"
                @delete="showConfirm" @search="handleSearch" />

            <!-- Error Message -->
            <div v-if="postsStore.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ postsStore.error }}
                <button @click="postsStore.clearError()" class="float-right text-red-500 hover:text-red-700">
                    ×
                </button>
            </div>

            <Toast :show="showToast" :type="toastType" :message="toastMessage" @close="handleToastClose" />
            <!-- Confirm delete modal -->
            <Confirm v-if="showConfirmModal" :show="showConfirmModal" @confirm="handleDelete" @close="closeConfirm" />
            <!-- Create/Edit Modal -->
            <PostModal v-if="showModal" :show="showModal" :post="selectedPost" @close="closeModal"
                @submit="handlePostSaved" />
        </div>
    </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import DashboardLayout from "../layouts/DashboardLayout.vue";
import PostsList from "../components/dashboard/PostsList.vue";
import PostModal from "../components/dashboard/PostModal.vue";
import { useAuthStore } from "../stores/auth";
import { usePostsStore } from "../stores/posts";
import Confirm from "../components/ui/Confirm.vue";
import Toast from "../components/ui/Toast.vue";

const authStore = useAuthStore();
const postsStore = usePostsStore();
const showModal = ref(false);
const showConfirmModal = ref(false);
const selectedPost = ref(null);
const search = ref('');
const showToast = ref(false);
const toastType = ref('success');
const toastMessage = ref('');

const openCreateModal = () => {
    selectedPost.value = null;
    showModal.value = true;
};

const openEditModal = (post) => {
    selectedPost.value = post;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedPost.value = null;
    postsStore.clearError();
};

const handleToastClose = () => {
    showToast.value = false
}
const handleSearch = (e) => {
    search.value = e.target.value;
    postsStore.fetchPosts(search.value);
}
const handlePostSaved = async (postData) => {
    try {
        if (selectedPost.value) {
            // Update existing post
            await postsStore.updatePost(selectedPost.value.id, postData);
            toastMessage.value = 'Updated Successfully'
        } else {
            // Create new post
            await postsStore.createPost(postData);
            toastMessage.value = 'Added Successfully'
        }
        closeModal();
    } catch (error) {
        // Error is handled in the store, modal can show the error
        toastMessage.value = 'Error Saving Post';
    } finally{
        showToast.value = true; 
    }
};

const handleDelete = async () => {
    try {
        await postsStore.deletePost(selectedPost.value.id);
        toastMessage.value = 'Deleted Successfully'
    } catch (error) {
        toastType.value = 'error'
        toastMessage.value = 'Error Deleting Post'
    } finally {
        closeConfirm()
        showToast.value = true;
        selectedPost.value = null
    }

}

const showConfirm = (post) => {
    showConfirmModal.value = true;
    selectedPost.value = post
};

const closeConfirm = () => {
    showConfirmModal.value = false
}

onMounted(() => {
    // Only fetch if we don't have posts or if we want to refresh
    postsStore.fetchPosts();
});
</script>
