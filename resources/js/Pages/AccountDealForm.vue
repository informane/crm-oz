<template>
    <AuthenticatedLayout>
        <div class="container">
            <button>Login</button>
            <form @submit.prevent="submit" method="post">
                <div class="col-2">
                    <label for="Account_Name">Account Name</label>
                    <input type="text" id="Account_Name" name="Account_Name" :value="form.Account_Name">
                </div>
                <div class="col-2">
                    <label for="Phone">Phone</label>
                    <input type="text" id="Phone" name="Phone" :value="form.Phone">
                </div>
                <div class="col-2">
                    <label for="Website">Website</label>
                    <input type="text" id="Website" name="Website" :value="form.Website">
                </div>
                <div class="col-2">
                    <label for="Deal_Name">Deal Name</label>
                    <input type="text" id="Deal_Name" name="Deal_Name" :value="form.Deal_Name">
                </div>
                <div class="col-2">
                    <label for="Stage">Stage</label>
                    <input type="text" id="Stage" name="Stage" :value="form.Stage">
                </div>
                <button type="submit">
                    Create
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
    import {router} from '@inertiajs/vue3'

    export default {
        name: "AccountDealForm",
        data() {
            return {
                form: {
                    Account_Name: '',
                    Phone: '',
                    Website: '',
                    Deal_Name: '',
                    Stage: ''
                }
            }
        },
        methods: {
            submit() {
                router.post('/zoho/store-account-deal', this.form)
            }
        }
    }
</script>

<style scoped>

</style>
