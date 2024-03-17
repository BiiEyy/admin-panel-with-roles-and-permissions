<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import BaseLevel from '@/Components/BaseLevel.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import SuccessButton from '@/Components/SuccessButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButtonLink from '@/Components/PrimaryButtonLink.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SuccessButtonLink from '@/Components/SuccessButtonLink.vue';
import TextInput from '@/Components/TextInput.vue';
import { XCircleIcon } from '@heroicons/vue/20/solid';
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
    users: {
        type: Object,
        default: () => ({}),
    },
    search: String,
    can: {
        type: Object,
        default: () => ({}),
    },
})

let search = ref(props.search);

const clear = () => {
    router.get(route('user.index'), { 
        search: '' 
    },
    {
        preserveState: true,
        replace: true,
    });
    search.value = ''
}

watch(search, (value) => {
    router.get(route('user.index'), { 
        search: value 
    },
    {
        preserveState: true,
        replace: true,
    });
}); 

const confirmingUserDeletion = ref(false);
const successUserDeletion = ref(false);
const userId = ref()

const confirmUserDeletion = (id) => {
    confirmingUserDeletion.value = true;
    userId.value = id
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    successUserDeletion.value = false;
    successMessage.value = false;
};

const deleteUser = () => {
    router.delete(route('user.destroy', userId.value ), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal(); 
            successUserDeletion.value = true;
        }
    });
};

const successMessage = ref(false);
const message = ref(null);

onMounted(() => {
    message.value = usePage().props.flash.message;

    if (message.value !== null) {
        successMessage.value = true;
    }
});
</script>
<template>
    <AppLayout title="Manage User">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manage User
            </h2>
        </template>

        <DialogModal :show="successMessage" @close="closeModal" maxWidth="sm" position="justify-center">
            <template #title>
                Success
            </template>

            <template #content>
                {{ message }}
            </template>

            <template #footer>
                <SuccessButton @click="closeModal">
                    Confirm
                </SuccessButton>
            </template>
        </DialogModal>

        <div class="py-12">
            <div class="w-full mx-auto sm:px-6 lg:px-8 mb-5">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex dark:bg-gray-800 bg-gray-50 justify-between items-center p-5">
                        <div class="flex space-x-2 items-center text-gray-700 dark:text-white">
                            Users Settings Page! Here you can list, create, update or delete user!
                        </div>
                        <div class="flex space-x-4 items-center w-1/3" v-if="can.create">
                            <button class="flex justify-center items-center">
                                <XCircleIcon class="h-5 w-6 dark:fill-white" @click="clear()" />
                            </button>
                            <TextInput placeholder="Search" id="text" v-model="search" type="text" class="mt-1 block w-2/3" />

                            <SuccessButtonLink v-if="can.create" :href="route('user.create')">
                                Create User
                            </SuccessButtonLink>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full mx-auto sm:px-6 lg:px-8 mb-2">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="py-3 px-6">ID</th>
                                <th scope="col" class="py-3 px-6">Name</th>
                                <th scope="col" class="py-3 px-6">Email</th>
                                <th scope="col" class="py-3 px-6">Role</th>
                                <th scope="col" class="py-3 px-6 w-1/6">Created At</th>
                                <th scope="col" class="py-3 px-6 w-1/6">Updated At</th>
                                <th v-if="can.edit || can.delete" scope="col" class="py-3 px-6 w-1/12">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in props.users.data" :key="user.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td data-label="Name" class="py-4 px-6">
                                    {{ user.id }}
                                </td>
                                <td data-label="Name" class="py-4 px-6">
                                    {{ user.first_name }} {{ user.last_name }}
                                </td>
                                <td data-label="Email" class="py-4 px-6">
                                    {{ user.email }}
                                </td>
                                <td data-label="Role" class="py-4 px-6">
                                    <span v-for="(role, index) in user.roles" :key="index">
                                        {{ role.description }}
                                        <template v-if="index !== user.roles.length - 1">, </template>
                                    </span>
                                </td>
                                <td data-label="Created At" class="py-4 px-6">
                                    {{ user.created_at }}
                                </td>
                                <td data-label="Updated At" class="py-4 px-6">
                                    {{ user.updated_at }}
                                </td>
                                <td v-if="can.edit || can.delete" class="py-4 px-6">
                                    <div class="flex justify-start space-x-4" no-wrap>
                                        <PrimaryButtonLink v-if="can.edit" :href="route('user.edit', user.id)">
                                            Edit
                                        </PrimaryButtonLink>
                                        <DangerButton v-if="can.delete" @click="confirmUserDeletion(user.id)">
                                            Delete
                                        </DangerButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 p-2 px-4" v-if="props.users.last_page > 1">
                        <BaseLevel>
                            <BaseButtons>
                                <Link as="button" :href="links.url !== null ? links.url : '#'" v-for="links in props.users.links" :disabled="links.url === null" :class="['inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded text-sm p-1 mb-3', links.url === null ? 'text-gray-500 bg-gray-300 border border-gray-300 cursor-not-allowed opacity-50' : '', links.active ? 'bg-gray-600 text-white border-2' : 'lightDark']">
                                    <span v-html="links.label" class="px-2"></span>
                                </Link>   
                            </BaseButtons>
                        <small>Page {{ props.users.current_page }} of {{ props.users.last_page }}</small>
                        </BaseLevel>
                    </div>
                </div>
            </div>
        </div>
        <DialogModal :show="confirmingUserDeletion" @close="closeModal">
            <template #title>
                Delete User
            </template>

            <template #content>
                Are you sure you want to delete this user?
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>

                <DangerButton class="ms-3" @click="deleteUser()">
                    Delete User
                </DangerButton>
            </template>
        </DialogModal>

        <DialogModal :show="successUserDeletion" @close="closeModal" maxWidth="sm" position="justify-center">
            <template #title>
                Success
            </template>

            <template #content>
                User has been successfully deleted.
            </template>

            <template #footer>
                <SuccessButton @click="closeModal">
                    Confirm
                </SuccessButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>