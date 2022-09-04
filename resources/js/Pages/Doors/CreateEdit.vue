<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetCheckbox from '@/Components/Checkbox.vue';
import JetLabel from '@/Components/Label.vue';
import Textarea from "@/Components/Textarea.vue";
import {useForm} from "@inertiajs/inertia-vue3";
import Button from "@/Components/Button.vue";

const form = useForm({
    name: '',
    latitude: '',
    longitude: '',
    bio: ''
})

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
} else {
    x.innerHTML = "Geolocation is not supported by this browser.";
}

function showPosition(position) {
    form.latitude = String(position.coords.latitude);
    form.longitude = String(position.coords.longitude);
}

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Door
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg p-4">
                    <div class="mb-4">
                        <JetLabel for="name" value="Faerie Name"/>
                        <JetInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autocomplete="name"
                        />
                        <JetInputError class="mt-2" :message="form.errors.name"/>
                    </div>
                    <div class="grid grid-cols-[20%_80%] gap-4 p-8">
                        <div class="-ml-8">
                            <div class="w-60 h-60 max-w-full bg-gray-200 flex items-center">
                                <span class="mx-auto text-8xl text-gray-300 material-symbols-outlined">image</span>
                            </div>


                        </div>
                        <div>
                        <div class="mb-4">
                            <JetLabel for="latitude" value="Bio"/>
                            <Textarea
                                id="bio"
                                v-model="form.bio"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autocomplete="bio"
                            />
                            <JetInputError class="mt-2" :message="form.errors.bio"/>
                        </div>
                        <div class="mb-4">
                            <JetLabel for="latitude" value="Latitude"/>
                            <JetInput
                                id="latitude"
                                v-model="form.latitude"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autocomplete="latitude"
                            />
                            <JetInputError class="mt-2" :message="form.errors.latitude"/>
                        </div>
                        <div class="mb-4">
                            <JetLabel for="longitude" value="Longitude"/>
                            <JetInput
                                id="longitude"
                                v-model="form.longitude"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autocomplete="longitude"
                            />
                            <JetInputError class="mt-2" :message="form.errors.longitude"/>
                        </div>
                        </div>
                    </div>
                    <div class="flex">
                        <Button class="ml-auto text-4xl bg-amber-800">Create Thy Faerie</Button>
                    </div>


                </div>
            </div>
        </div>
    </AppLayout>
</template>
