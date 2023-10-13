<template>
    <AuthenticatedLayout>
        <div class="container">
            <div v-for="(account,key) in accounts" :key="key">
                <div class="col-2">{{ account.Account_Name }}</div>
                <div class="col-2">{{ account.Phone }}</div>
                <div class="col-2">{{ account.Website }}</div>
                <div class="col-2">{{ account.Deal_Name }}</div>
                <div class="col-2">{{ account.Stage }}</div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script>
    import {router} from '@inertiajs/vue3'

    export default {
        name: "AccountDeals",
        props: {
            accounts: Array
        },
        data() {
            return {}
        },
        methods: {}
    }
</script>

<style scoped>

</style>
