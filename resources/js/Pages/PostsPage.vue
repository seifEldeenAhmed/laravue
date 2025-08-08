<template>
    <DashboardLayout title="Posts">
        <div class="space-y-6">
            <!-- Posts Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">All Posts</h2>
                <input
                    class="appearance-none rounded-none relative block w-100 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                    type="text"
                    v-model="search"
                    name="search"
                    id="search"
                    placeholder="search"
                    @change="handleSearch"
                />
            </div>

            <!-- Posts List -->
            <PostsList
                :posts="postsStore.posts"
                :loading="postsStore.loading"
                :user-role="authStore.isAdmin ? 'admin' : 'user'"
                @create="openCreateModal"
                @edit="openEditModal"
                @delete="deletePost"
            />

            <!-- Error Message -->
            <div
                v-if="postsStore.error"
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
            >
                {{ postsStore.error }}
                <button
                    @click="postsStore.clearError()"
                    class="float-right text-red-500 hover:text-red-700"
                >
                    ×
                </button>
            </div>

            <!-- Create/Edit Modal -->
            <PostModal
                v-if="showModal"
                :show="showModal"
                :post="selectedPost"
                @close="closeModal"
                @submit="handlePostSaved"
            />
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

const authStore = useAuthStore();
const postsStore = usePostsStore();
const showModal = ref(false);
const selectedPost = ref(null);
const search = ref('');
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
const handleSearch =  (e)=>{
    search.value= e.target.value;
    postsStore.fetchPosts({search:search.value})
}
const handlePostSaved = async (postData) => {
    try {
        if (selectedPost.value) {
            // Update existing post
            await postsStore.updatePost(selectedPost.value.id, postData);
        } else {
            // Create new post
            await postsStore.createPost(postData);
        }
        closeModal();
    } catch (error) {
        // Error is handled in the store, modal can show the error
        console.error("Error saving post:", error);
    }
};

const deletePost = async (post) => {
    if (!confirm("Are you sure you want to delete this post?")) return;

    try {
        await postsStore.deletePost(post.id);
    } catch (error) {
        alert("Error deleting post: " + (postsStore.error || "Unknown error"));
    }
};

onMounted(() => {
    // Only fetch if we don't have posts or if we want to refresh
    postsStore.fetchPosts();
});
</script>
