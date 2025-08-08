import { createRouter, createWebHistory } from "vue-router";
import LoginPage from "../Pages/LoginPage.vue";
import AdminDashboard from "../Pages/AdminDashboard.vue";
import UserDashboard from "../Pages/UserDashboard.vue";
import PostsPage from "../Pages/PostsPage.vue";
import { useAuthStore } from "../stores/auth";

const validRoles = ['admins', 'users'];

const routes = [
    { path: "/:role/login", name: "login", component: LoginPage, props: true, meta: { requiresGuest: true } },
    { path: "/", redirect: "/users/login" },
    { 
        path: "/admins", 
        name: "admins.dashboard", 
        component: AdminDashboard,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    { 
        path: "/users", 
        name: "users.dashboard", 
        component: UserDashboard,
        meta: { requiresAuth: true, requiresUser: true }
    },
    {
        path:"/posts",
        name: "posts",
        component: PostsPage,
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    
    // Check if route requires authentication
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        const redirectRole = to.meta.requiresAdmin ? 'admins' : 'users';
        return next(`/${redirectRole}/login`);
    }
    
    // Check if route requires guest (not authenticated)
    if (to.meta.requiresGuest && authStore.isAuthenticated) {
        const redirectPath = authStore.isAdmin ? '/admins' : '/users';
        return next(redirectPath);
    }
    
    // Check role-specific access
    if (to.meta.requiresAdmin && !authStore.isAdmin) {
        return next('/users');
    }
    
    if (to.meta.requiresUser && !authStore.isUser) {
        return next('/admins');
    }
    
    // Validate role parameter for login routes
    if (to.name === "login") {
        const role = to.params.role;
        if (!validRoles.includes(role)) {
            return next({ path: "/users/login" }); // fallback
        }
    }
    
    next();
});

export default router;
