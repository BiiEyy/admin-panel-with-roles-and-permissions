<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SecondaryButtonLink from '@/Components/SecondaryButtonLink.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/20/solid';
import { ref } from 'vue';

const props = defineProps({
    users: Object,
    roles: Array,
})

const form = useForm({
    first_name: props.users.first_name,
    last_name: props.users.last_name,
    email: props.users.email,
    role: props.users['roles'][0].name,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('user.update', props.users.id), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const showPassword = ref(false);
const showPassword1 = ref(false);

const togglePasswordVisibility = (index) => {
    if (index === 0) {
        showPassword.value = !showPassword.value;
    } else if (index === 1) {
        showPassword1.value = !showPassword1.value;
    }
};
</script>
<template>
    <AppLayout title="Manage User">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit User
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 mb-2">
                <div class="overflow-hidden shadow-md sm:rounded-lg">
                    <div class="flex bg-gray-50 dark:bg-gray-800 py-5 px-12">
                        <form @submit.prevent="submit" class="w-full">
                            <div class="flex gap-2">
                                <div class="w-1/2">
                                    <InputLabel for="first_name" value="First Name" />
                                    <TextInput id="first_name" v-model="form.first_name" type="text" class="mt-1 block w-full" autofocus autocomplete="first_name"/>
                                    <InputError class="mt-2" :message="form.errors.first_name" />
                                </div>

                                <div class="w-1/2">
                                    <InputLabel for="last_name" value="Last Name" />
                                    <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full" autofocus autocomplete="last_name"/>
                                    <InputError class="mt-2" :message="form.errors.last_name" />
                                </div>
                            </div>

                            <div class="mt-4">
                                <InputLabel for="email" value="Email" />
                                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" autocomplete="username"/>
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="password" value="Password" />
                                <div class="flex">
                                    <TextInput id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" class="mt-1 block w-[90%] rounded-none rounded-l-md" autocomplete="new-password"/>
                                    <SecondaryButton class="mt-1 rounded-none rounded-r-md" @click="togglePasswordVisibility(0)">
                                        <EyeIcon class="h-5 w-6" v-if="!showPassword"/>
                                        <EyeSlashIcon class="h-5 w-6" v-else/>
                                    </SecondaryButton>
                                </div>
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="password_confirmation" value="Confirm Password" />
                                <div class="flex">
                                    <TextInput id="password_confirmation" v-model="form.password_confirmation" :type="showPassword1 ? 'text' : 'password'" class="mt-1 block w-full rounded-none rounded-l-md" autocomplete="new-password"/>
                                    <SecondaryButton class="mt-1 rounded-none rounded-r-md" @click="togglePasswordVisibility(1)">
                                        <EyeIcon class="h-5 w-6" v-if="!showPassword1"/>
                                        <EyeSlashIcon class="h-5 w-6" v-else/>
                                    </SecondaryButton>
                                </div>
                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="role" value="Roles" />
                                <SelectInput v-model="form.role" name="role" type="select" :options="props.roles" autocomplete="role"/>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <SecondaryButtonLink :href="route('user.index')">
                                    Cancel
                                </SecondaryButtonLink>
                                <PrimaryButton type="submit" class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Update User
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>