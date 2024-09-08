<script setup>
// import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// import { Head, Link } from '@inertiajs/vue3';
// import FlashMessage from '@/Components/FlashMessage.vue';
// import { onMounted } from 'vue'
import Pagenation from '@/Components/Pagenation.vue'

defineProps({
    customers: Object
})

// onMounted(()=> {
//     console.log(props.customers)
//     console.log(props.customers.last_page)
// })

</script>

<template>
    <ul v-for="customer in customers.data" key="customer.id">
        <li>{{ customer.id }}</li>
        <li>{{ customer.name }}</li>
    </ul>
    <Pagenation class="mt-6" :links="customers.links"></Pagenation>

</template>
