<template>
    <Link href="admins/login">
        Admin 
    </Link>
    {{ counter }}
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
const counter = ref(0);
setInterval(()=>counter.value++,1000)
</script>