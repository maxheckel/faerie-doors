<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import Portrait from "@/Components/Portrait.vue";
const props = defineProps({
    faeries: Array
})
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="pt-4 pl-4 text-4xl">Your spellbook reveals the following faeries</div>
                    <div class="grid grid-cols-2 md:grid-cols-4 relative gap-4 p-8">

                        <a :href="route('doors.show', faerie.id)" class="relative cursor-pointer" v-for="faerie in props.faeries">
                            <Portrait :image_url="faerie.image_url"></Portrait>
                            <div class="text-2xl text-center mt-8 font-bold">{{faerie.name}}</div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
