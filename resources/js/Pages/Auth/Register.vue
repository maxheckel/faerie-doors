<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import JetButton from '@/Components/Button.vue';
import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetCheckbox from '@/Components/Checkbox.vue';
import JetLabel from '@/Components/Label.vue';
import Textarea from "@/Components/Textarea.vue";
import {reactive, watch} from "vue";

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

const form = useForm({
    name: '',
    email: '',
    password: '',
    zip: '',
    localeName: '',
    wizardCount: '',
    localeState: '',
    essay: '',
    password_confirmation: '',
    terms: false,
});

const props = defineProps({
    key: String
})

const data = reactive({
    loadingLocale: false,
    lastLocaleLoaded: ''
})

watch(form, function(value, oldValue, onCleanup){
    if (value.zip.length >= 5 && value.zip !== data.lastLocaleLoaded){
        data.loadingLocale = true;

        fetch(`/api/locale`, {
            method: 'POST', // *GET, POST, PUT, DELETE, etc.
            headers: {
                'Content-Type': 'application/json'
                // 'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: JSON.stringify({
                'zip': value.zip
            }) // body data type must match "Content-Type" header
        }).then((res) => {
            return res.json();
        }).then((localeData) => {
            data.loadingLocale = false;
            form.localeName = localeData.name;
            form.localeState = localeData.state;
            form.wizardCount = localeData.wizards;
            data.lastLocaleLoaded = form.zip
        })
    }
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <div>
                <JetLabel for="name" value="Wizard Name" />
                <JetInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                />
                <JetInputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <JetLabel for="email" value="Email" />
                <JetInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                />
                <JetInputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <JetLabel for="password" value="Password" />
                <JetInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <JetInputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <JetLabel for="password_confirmation" value="Confirm Password" />
                <JetInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <JetInputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4">
                <JetLabel for="zip" value="Zip Code" />
                <JetInput
                    id="zip"
                    v-model="form.zip"
                    type="number"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <JetInputError class="mt-2" :message="form.errors.zip" />
            </div>
            <div class="mt-4" v-if="data.loadingLocale">
                <span class="material-symbols-outlined animate-spin">
                autorenew
                </span>
            </div>
            <div class="text-sm " v-if="form.localeName">
                {{capitalizeFirstLetter(form.localeName)}}, {{capitalizeFirstLetter(form.localeState)}} has {{form.wizardCount}} fellow wizards
            </div>

            <div class="mt-4">
                <JetLabel for="essay" value="Why do you want to be a wizard?" />
                <Textarea
                    id="essay"
                    v-model="form.essay"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <JetInputError class="mt-2" :message="form.errors.essay" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <JetLabel for="terms">
                    <div class="flex items-center">
                        <JetCheckbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ml-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-gray-600 hover:text-gray-900">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-gray-600 hover:text-gray-900">Privacy Policy</a>
                        </div>
                    </div>
                    <JetInputError class="mt-2" :message="form.errors.terms" />
                </JetLabel>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Already registered?
                </Link>

                <JetButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </JetButton>
            </div>
        </form>
    </JetAuthenticationCard>
</template>
