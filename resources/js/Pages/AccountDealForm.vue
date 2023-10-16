<template>
    <AuthenticatedLayout>
        <div></div>
        <div class="mx-auto w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <form @submit.prevent="submit" method="post">
                <div class="col-2">
                    <InputLabel for="Account_Name" value="Account Name"/>
                    <TextInput
                        id="Account_Name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.Account_Name"
                        autofocus
                        autocomplete="Account_Name"
                    />
                    <InputError class="mt-2" :message="form.errors.Account_Name" />
                </div>
                <div class="col-2">
                    <InputLabel for="Phone" value="Phone"/>
                    <TextInput
                        id="Phone"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.Phone"
                        autofocus
                        autocomplete="Phone"
                    />
                    <InputError class="mt-2" :message="form.errors.Phone" />
                </div>
                <div class="col-2">
                    <InputLabel for="Website" value="Website"/>
                    <TextInput
                        id="Website"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.Website"
                        autofocus
                        autocomplete="Website"
                    />
                    <InputError class="mt-2" :message="form.errors.Website" />
                </div>
                <div class="col-2">
                    <InputLabel for="Deal_Name" value="Deal Name"/>
                    <TextInput
                        id="Deal_Name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.Deal_Name"
                        autofocus
                        autocomplete="Deal_Name"
                    />
                    <InputError class="mt-2" :message="form.errors.Deal_Name" />
                </div>
                <div class="col-2">
                    <InputLabel for="Stage" value="Stage"/>
                    <TextInput
                        id="Stage"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.Stage"
                        autofocus
                        autocomplete="Stage"
                    />
                    <InputError class="mt-2" :message="form.errors.Stage" />
                </div>
                <PrimaryButton class="mt-2" type="submit">
                    Create
                </PrimaryButton>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import {Head, router, useForm} from '@inertiajs/vue3'
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';

    const form = useForm({
        Account_Name: '',
        Phone: '',
        Website: '',
        Deal_Name: '',
        Stage: ''
    });

    const submit = () => {
        form.post(route('save'));
        //router.post('/zoho/save-account', form)
    };

    /*export default {
        name: "AccountDealForm",
        components: [
            AuthenticatedLayout,
            Head
        ],
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
                router.post('/zoho/store-account-deal', data.form)
            }
        }
    }*/
</script>

<style scoped>

</style>
