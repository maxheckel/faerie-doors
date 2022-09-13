<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {useForm} from "@inertiajs/inertia-vue3";
import BigFirstLetter from "@/Components/BigFirstLetter.vue";
import {reactive} from "vue";
import QrcodeVue from 'qrcode.vue'

import Button from "@/Components/Button.vue";
import Textarea from "@/Components/Textarea.vue";
import Portrait from "@/Components/Portrait.vue";
import Map from "@/Components/Map.vue";

const form = useForm({
    name: '',
    latitude: '',
    longitude: '',
    bio: '',
    newBio: '',
    template_id: 0
})

const data = reactive({
    bioLoaded: true,
    manuallyEditing: false,
    faerieImage: "",
    staticIcons: []
})





const props = defineProps({
    template: Object,
    profanity: Boolean,
    faerie: Object
})


if (props.template != null){
    form.name = props.template.name;
    form.bio = props.template.bio;
    form.newBio = props.template.bio;

    form.template_id = props.template.id;
    data.faerieImage = 'https://faerie-doors.s3.us-east-2.amazonaws.com/'+props.template.image;
}



if (props.faerie != null){
    form.name = props.faerie.name;
    form.bio = props.faerie.bio;
    form.newBio = props.faerie.bio;
    form.template_id = props.faerie.template_id;
    data.faerieImage = props.faerie.image_url;
    data.staticIcons = [{
        position: {
            lat: props.faerie.latitude,
            lng: props.faerie.longitude,
        },
        image: props.faerie.image_url
    }]
}




function newBio() {
    data.bioLoaded = false
    fetch('/api/bio?name=' + form.name).then((res) => {
        return res.json();
    }).then((json) => {
        data.bioLoaded = true
        form.bio = json.bio;
        form.newBio = form.bio;
    })
}



function newName() {
    fetch('/api/name').then((res) => {
        return res.json();
    }).then((json) => {
        form.bio = form.bio.replace(form.name, json.name);
        form.newBio = form.bio;
        form.name = json.name;

    })
}

function cancelEditBio(){
    form.newBio = form.bio;
    data.manuallyEditing = false;
}

function saveNewBio(){
    form.bio = form.newBio;
    data.manuallyEditing = false;
}

function submit() {
    form.post(route('doors.store'), {
        preserveScroll: false,
        preserveState: false
    }, {
        resetOnSuccess: false,
    })
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
                <img src="/img/topright.webp" class="absolute w-[100px] sm:w-[200px] h-auto right-0 md:right-[100px] z-10">
                <div class="bg-white shadow-xl sm:rounded-lg p-4 framed">
                    <div class="text-5xl mb-8 md:pr-[200px] text-center">
                        <div class="md:hidden w-[50px] h-[40px] inline-block float-right"></div>
                        As you peer into your spellbook you see a faerie, they introduce themselves as <span
                        class="font-bold"> {{ form.name }}</span>
                        <a v-if="faerie == null" @click="newName()" class="underline block text-md mt-8 cursor-pointer">Or...maybe that isn't
                            their name?</a>
                    </div>
                    <div class="font-bold text-2xl text-red-500" v-if="props.profanity">
                        Please do not use profanity in your translations, faeries don't like swearies.
                    </div>

                    <div class="md:grid md:grid-cols-[30%_70%] gap-4 relative pr-4 z-0">
                        <div class="relative md:w-full w-1/2 mx-auto">
                            <Portrait :image_url="data.faerieImage"/>
                            <a v-if="faerie != null" class="relative mx-auto mt-10 w-[100px] block text-center">
                                <QrcodeVue class="" :value="route('doors.public', faerie.uuid)" :size="100" :level="'H'"></QrcodeVue>
                                <a class="text-center relative" :href="route('qr', faerie.uuid)">Print</a>
                            </a>
                            <a v-if="faerie == null" class="block underline mt-20 cursor-pointer" href="/doors/create">Maybe a new faerie?</a>
                        </div>
                        <div class="bg-amber-100 p-4 rounded-lg text-4xl mt-4 p-8">
                            <div class="w-[90px] h-[50px] float-right inline-block font-serif"></div>
                            <span class="text-8xl block -mb-16" v-if="data.bioLoaded">"</span>
                            <BigFirstLetter v-if="data.bioLoaded && !data.manuallyEditing" class="block">
                                {{ form.bio }}
                            </BigFirstLetter>
                            <div v-if="data.manuallyEditing">
                                <Textarea class="w-full h-60 text-2xl" v-model="form.newBio"></Textarea>
                            </div>

                            <span class="text-8xl" v-if="data.bioLoaded">"</span>
                            <div v-if="!data.manuallyEditing">
                                <a v-if="data.bioLoaded" @click="newBio()"
                                   class="text-lg underline cursor-pointer block -mt-8">Hmm, that intro doesn't seem
                                    right,
                                    let's try magically translating
                                    again...</a>
                                <a v-if="data.bioLoaded" class="text-lg underline cursor-pointer block mt-8"
                                   @click="data.manuallyEditing = !data.manuallyEditing">I want to manually translate
                                    {{ form.name }}'s introduction</a>
                            </div>
                            <div v-else class="-mt-8">
                                <Button class="bg-emerald-500" @click="saveNewBio()">Save</Button>
                                <Button class="ml-4" @click="cancelEditBio()">Cancel</Button>
                            </div>
                            <span v-if="!data.bioLoaded" class="material-symbols-outlined animate-spin text-8xl">
                            autorenew
                            </span>
                        </div>
                    </div>
                    <div class="text-5xl text-center mt-20">Where does {{ form.name }} live?</div>
                    <div class="text-lg text-center">You don't have to choose this now, you can always add it later.
                    </div>
                    <Map :staticMarkers="data.staticIcons" :clickable="true" @latChanged="(lat) => form.latitude = lat" @lngChanged="(lng) => form.longitude = lng"></Map>
                    <Button @click="submit" class="text-4xl mt-8">Create Door</Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>

</style>
