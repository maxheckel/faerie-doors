<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {useForm} from "@inertiajs/inertia-vue3";
import BigFirstLetter from "@/Components/BigFirstLetter.vue";
import {reactive} from "vue";
import {Loader} from "@googlemaps/js-api-loader";
import Button from "@/Components/Button.vue";
import Textarea from "@/Components/Textarea.vue";
import Portrait from "@/Components/Portrait.vue";

const form = useForm({
    name: '',
    latitude: '',
    longitude: '',
    bio: '',
    newBio: '',
    template_id: 0
})

const data = reactive({
    mapLoaded: false,
    bioLoaded: true,
    manuallyEditing: false
})

const loader = new Loader({
    apiKey: "AIzaSyA5D4hMDFEC0LeERCZikKGGyiWShCkwJ3Y",
    version: "weekly",
    libraries: ["places"]
});


const mapOptions = {
    mapId: "d848f879ef61ec24",
    center: {
        lat: 0,
        lng: 0
    },
    zoom: 18
};


const props = defineProps({
    template: Object
})

form.name = props.template.name;
form.bio = props.template.bio;
form.newBio = props.template.bio;
form.template_id = props.template.id;

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
} else {
    x.innerHTML = "Geolocation is not supported by this browser.";
}

function showPosition(position) {
    data.mapLoaded = true;
    form.latitude = String(position.coords.latitude);
    form.longitude = String(position.coords.longitude);
    mapOptions.center.lat = position.coords.latitude
    mapOptions.center.lng = position.coords.longitude
    console.log('here');
    // Promise
    loader
        .load()
        .then((google) => {

            const map = new google.maps.Map(document.getElementById("map"), mapOptions);
            google.maps.event.addListener(map, "click", (event) => {
                addMarker(event.latLng, map, google);
            });


        })
        .catch(e => {
            console.log(e);
        });
}

const existingMarkers = []

function addMarker(latlng, map, google) {

    form.latitude = latlng.lat();
    form.longitude = latlng.lng();
    console.log(form.latitude, form.longitude);
    const marker = new google.maps.Marker({
        position: latlng
    })
    marker.setMap(map)
    for (let x = 0; x < existingMarkers.length; x++) {
        existingMarkers[x].setMap(null);
    }
    existingMarkers.push(marker)
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
    form.post(route('doors.store'))
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
                        <a @click="newName()" class="underline block text-md mt-8 cursor-pointer">Or...maybe that isn't
                            their name?</a>
                    </div>

                    <div class="md:grid md:grid-cols-[30%_70%] gap-4 relative pr-4 z-0">
                        <div class="relative md:w-full w-1/2 mx-auto">
                            <Portrait :image_url="'https://faerie-doors.s3.us-east-2.amazonaws.com/'+props.template.image"/>
                            <a class="block underline mt-20 cursor-pointer" href="/doors/create">Maybe a new faerie?</a>
                        </div>
                        <div class="bg-amber-100 p-4 rounded-lg text-4xl mt-4 p-8">
                            <div class="w-[90px] h-[50px] float-right inline-block font-serif"></div>
                            <span v-if="data.bioLoaded"
                                  class="text-xl"> Your mind buzzes as you translate the faerie introduces themselves:</span>
                            <span class="text-8xl block -mb-16" v-if="data.bioLoaded">"</span>
                            <BigFirstLetter v-if="data.bioLoaded && !data.manuallyEditing" class="block">{{
                                    form.bio
                                }}
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
                    <div class="text-5xl text-center mt-20">Where does {{ props.template.name }} live?</div>
                    <div class="text-lg text-center">You don't have to choose this now, you can always add it later.
                    </div>
                    <div class="map relative">
                        <div v-if="data.mapLoaded" class="w-full my-4" style="height: 400px" id="map"></div>
                        <div v-if="data.mapLoaded" class="w-full mt-4 absolute top-0"
                             style="pointer-events: none; height: 400px;background: rgb(2,0,36); background: radial-gradient(circle, rgba(2,0,36,0) 70%, rgba(195,134,99,1) 100%);"></div>
                        <img v-if="data.mapLoaded" src="/img/mouse.webp" class="absolute -bottom-10 -left-4 w-40">

                        <div v-if="!data.mapLoaded" class="w-full text-center py-10">
                        <span class="material-symbols-outlined text-8xl ml-auto relative">
                            map
                            </span>
                            <br>
                            <span class="text-2xl">
                        Loading Map
                        </span>

                        </div>
                    </div>
                    <Button @click="submit" class="text-4xl mt-8">Create Door</Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>

</style>
