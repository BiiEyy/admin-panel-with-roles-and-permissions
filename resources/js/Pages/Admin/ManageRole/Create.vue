<script setup>
import { useForm, router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SuccessButton from '@/Components/SuccessButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { XCircleIcon } from '@heroicons/vue/20/solid';
import { ref, watch } from 'vue';
import SecondaryButtonLink from '@/Components/SecondaryButtonLink.vue';


const props = defineProps({
    defaultPermissions: Object,
    search: String,
})

const form = useForm({
    name: '',
    description: '',
    permissions: {},
});

const selectAll = ref({});

const selectAllPermissions = (arrayName) => {
    const selected = !selectAll[arrayName];
    selectAll[arrayName] = selected;

    for (const permission of props.defaultPermissions[arrayName]) {
        form.permissions[permission.name] = selected;
    }
};

const submit = () => {
    form.post(route('role.store'), {
        onFinish: () => form.reset('name', 'description', 'permissions'),
    });
};

let search = ref(props.search);

const clear = () => {
    router.get(route('role.create'), { },
    {
      replace: true,
    });
    search.value = ''
}

watch(search, (value) => {
    router.get(route('role.create'), { 
        search: value 
    },{
        preserveState: true,
        replace: true,
    });
}); 
</script>

<template>
    <AppLayout title="Manage Roles">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manage Roles
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-[20px]">
                    <form @submit.prevent="submit">
                        <div class="flex mb-4 gap-4">
                            <div class="w-2/4">
                                <InputLabel for="name" value="Name" />
                                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" autofocus autocomplete="name"/>
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>
                            <div class="w-2/4">
                                <InputLabel for="description" value="Description" />
                                <TextInput id="description" v-model="form.description" type="text" class="mt-1 block w-full" autofocus autocomplete="description"/>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>
                        </div>
                        <div class="flex mt-4 w-1/2">
                            <TextInput placeholder="Search Permission" id="text" v-model="search" type="text" class="mt-1 block w-3/4"/>
                            <button type="button" class="flex justify-center items-center ml-2">
                                <XCircleIcon class="h-5 w-6 dark:fill-white" @click="clear()" />
                            </button>
                        </div>
                        <!-- <hr class="mt-2 dark:border-gray-700"/> -->
                        <div class="overflow-y-auto max-h-[576px] px-4">
                            <div class="col-span-6 py-4" v-for="(permissions, arrayName) in defaultPermissions" :key="arrayName">
                                <div class="flex items-center">
                                    <InputLabel :for="arrayName" :value="arrayName" font="text-md"/>
                                    <Checkbox @input="selectAllPermissions(arrayName)" class="w-5 h-5 ml-8"/>
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Select All</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-48 max-w-xl p-4">
                                    <div v-for="permission in permissions" :key="permission">
                                        <div class="flex items-center">
                                            <Checkbox v-model:checked="form.permissions[permission.name]" :value="permission.name" class="w-5 h-5" />
                                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ permission.name.split(' ').pop() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <hr class="dark:border-gray-700 mb-4">
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <SecondaryButtonLink :href="route('role.index')">
                                Cancel
                            </SecondaryButtonLink>
                            <SuccessButton type="submit" class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Save
                            </SuccessButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>