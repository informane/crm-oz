<template>
    <Head title="Account and Deals list" />

    <AuthenticatedLayout>
        <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Accounts and Deals list</h2>
        </template>
        <h3 >{{ data.status }}</h3>
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-7 2 gap-4 font-semibold">
                <div class="">Account_Name</div>
                <div class="">Phone</div>
                <div class="col-span-3">Website</div>
                <div class="">Deal_Name</div>
                <div class="">Stage</div>
            </div>
            <div class="grid grid-cols-7 2 gap-4" v-for="(account,key) in data.accounts" :key="key">
                <div class="">{{ account.Account_Name }}</div>
                <div class="">{{ account.Phone }}</div>
                <div class="col-span-3">{{ account.Website }}</div>
                <div class="">{{ account.Deal_Name }}</div>
                <div class="">{{ account.Stage }}</div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import {router} from '@inertiajs/vue3'
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    const data = defineProps({
        accounts: {
            type: Array,
        },
        status: {
            type: String
        }
    });
</script>

<style scoped>

</style>
