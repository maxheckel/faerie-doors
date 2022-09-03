<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetCheckbox from '@/Components/Checkbox.vue';
import JetLabel from '@/Components/Label.vue';
import Textarea from "@/Components/Textarea.vue";
import {useForm} from "@inertiajs/inertia-vue3";

const form = useForm({
    name: ''
})

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
                    <div class="grid grid-cols-[20%_80%] gap-4">
                        <div>
                            <div class="w-60 h-60 max-w-full bg-gray-200 flex items-center">
                                <span class="mx-auto text-8xl text-gray-300 material-symbols-outlined">image</span>
                            </div>


                        </div>
                        <div>
                            <JetLabel for="name" value="Latitude"/>
                            <JetInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            <JetInputError class="mt-2" :message="form.errors.name"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
