<template>
    <div class="flex justify-center">
        <div class="relative">
            <button class="flex text-md border border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                <SunIcon v-if="usePage().props.auth.user.theme === 'dark'" class="h-6 w-6 fill-gray-300" aria-hidden="true" @click="setOption('light')"/>
                <MoonIcon v-else class="h-6 w-6 fill-gray-800" aria-hidden="true" @click="setOption('dark')"/>
            </button>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted, watch} from 'vue';
import {SunIcon, MoonIcon} from '@heroicons/vue/20/solid';
import { usePage, router } from '@inertiajs/vue3';
const systemDarkMode = window.matchMedia('(prefers-color-scheme: dark)');
const option = ref(usePage().props.auth.user.theme);

const setOption = (theme) => {
    router.post(route('themes.store'), { theme })
    option.value = theme
}

const setTheme = () => {
    if (option.value === 'system') {
        window.matchMedia('(prefers-color-scheme: dark)').matches ? toggleDarkClass('dark') : toggleDarkClass('light')
    } else {
        option.value === 'dark' ? toggleDarkClass('dark') : toggleDarkClass('light')
    }
};
const toggleDarkClass = (className) => {
    if (className === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};
watch(option, setTheme);
onMounted(() => {
    setTheme();
    systemDarkMode.addListener((event) => {
        if (option.value === 'system') {
            if (event.matches) {
                toggleDarkClass('dark')
            } else {
                toggleDarkClass('light')
            }
        }
    });
});
</script>