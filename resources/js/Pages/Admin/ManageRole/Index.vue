<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import BaseLevel from '@/Components/BaseLevel.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import SuccessButton from '@/Components/SuccessButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { XCircleIcon } from '@heroicons/vue/20/solid';
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
    search: String,
    roles: {
        type: Object,
        default: () => ({}),
    },
    can: {
        type: Object,
        default: () => ({}),
    },
})

let search = ref(props.search);

const clear = () => {
    router.get(route('role.index'), { },
    {
        replace: true,
    });
}

watch(search, (value) => {
    router.get(route('role.index'), { 
        search: value 
    },
    {
        preserveState: true,
        replace: true,
    });
}); 

const confirmingRoleDeletion = ref(false);
const successRoleDeletion = ref(false);
const roleId = ref()

const confirmRoleDeletion = (id) => {
    confirmingRoleDeletion.value = true;
    roleId.value = id
};

const closeModal = () => {
    confirmingRoleDeletion.value = false;
    successRoleDeletion.value = false;
    successMessage.value = false;
};

const deleteRole = () => {
    router.delete(route('role.destroy', roleId.value ), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal(); 
            successRoleDeletion.value = true;
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
    <AppLayout title="Manage Roles">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manage Roles
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
            <div class="max-w-full mx-auto sm:px-6 lg:px-8 mb-5">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex bg-gray-50 dark:bg-gray-800 justify-between items-center p-4">
                        <div class="flex space-x-2 items-center dark:text-white">
                            Role Settings Page! Here you can list, create, update or delete role!
                        </div>
                        <div class="flex space-x-2 items-center" v-if="can.create">
                            <button class="flex justify-center items-center">
                                <XCircleIcon class="h-5 w-6 dark:fill-white" @click="clear()" />
                            </button>
                            <TextInput placeholder="Search" id="text" v-model="search" type="text" class="mt-1 block w-full"/>
                            <Link as="button" :href="route('role.create')" type="button" class="min-w-fit inline-flex items-center justify-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Create Role
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <div class="max-w-full mx-auto sm:px-6 lg:px-8 mb-2">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="py-3 px-6 w-1/12">ID</th>
                                <th scope="col" class="py-3 px-6 w-1/6">Name</th>
                                <th scope="col" class="py-3 px-6 w-1/6">Description</th>
                                <th scope="col" class="py-3 px-6 w-1/6">Created At</th>
                                <th scope="col" class="py-3 px-6 w-1/6">Updated At</th>
                                <th v-if="can.edit || can.delete" scope="col" class="py-3 px-6 w-1/6">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="role in props.roles.data" :key="role.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td data-label="ID" class="py-4 px-6">
                                    {{ role.id }}
                                </td>
                                <td data-label="Name" class="py-4 px-6">
                                    {{ role.name }}
                                </td>
                                <td data-label="Description" class="py-4 px-6">
                                    {{ role.description }}
                                </td>
                                <td data-label="Created At" class="py-4 px-6">
                                    {{ role.created_at }}
                                </td>
                                <td data-label="Updated At" class="py-4 px-6">
                                    {{ role.updated_at }}
                                </td>
                                <td v-if="can.edit || can.delete" class="py-4 px-6">
                                    <div type="justify-start lg:justify-end" no-wrap>
                                        <Link :href="route('role.edit', { role })" as="button" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 ms-4" v-if="can.edit">
                                            Edit
                                        </Link>
                                        <DangerButton @click="confirmRoleDeletion(role.id)" class="ml-4" v-if="can.delete">Delete</DangerButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 p-2 px-4" v-if="props.roles.last_page > 1">
                        <BaseLevel>
                            <BaseButtons>
                                <Link as="button" :href="links.url !== null ? links.url : '#'" v-for="links in props.roles.links" :disabled="links.url === null" :class="['inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded text-sm p-1 mb-3', links.url === null ? 'text-gray-500 bg-gray-300 border border-gray-300 cursor-not-allowed opacity-50' : '', links.active ? 'bg-gray-600 text-white border-2' : 'lightDark']">
                                    <span v-html="links.label" class="px-2"></span>
                                </Link>   
                            </BaseButtons>
                        <small>Page {{ props.roles.current_page }} of {{ props.roles.last_page }}</small>
                        </BaseLevel>
                    </div>
                </div>
            </div>
        </div>
        <DialogModal :show="confirmingRoleDeletion" @close="closeModal">
            <template #title>
                Delete Role
            </template>

            <template #content>
                Are you sure you want to delete this role?
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>

                <DangerButton class="ms-3" @click="deleteRole()">
                    Delete Role
                </DangerButton>
            </template>
        </DialogModal>

        <DialogModal :show="successRoleDeletion" @close="closeModal" maxWidth="sm" position="justify-center">
            <template #title>
                Success
            </template>

            <template #content>
                Role has been successfully deleted.
            </template>

            <template #footer>
                <SuccessButton @click="closeModal">
                    Confirm
                </SuccessButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>