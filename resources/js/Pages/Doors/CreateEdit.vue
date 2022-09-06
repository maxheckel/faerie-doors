<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {useForm} from "@inertiajs/inertia-vue3";
import BigFirstLetter from "@/Components/BigFirstLetter.vue";
import {reactive} from "vue";
import {Loader} from "@googlemaps/js-api-loader";

const form = useForm({
    name: '',
    latitude: '',
    longitude: '',
    bio: ''
})

const loader = new Loader({
    apiKey: "AIzaSyA5D4hMDFEC0LeERCZikKGGyiWShCkwJ3Y",
    version: "weekly",
    libraries: ["places"]
});


const mapOptions = {
    center: {
        lat: 0,
        lng: 0
    },
    zoom: 18
};


const props = defineProps({
    template: Object
})

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
} else {
    x.innerHTML = "Geolocation is not supported by this browser.";
}

function showPosition(position) {

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
    for(let x = 0; x < existingMarkers.length; x++){
        existingMarkers[x].setMap(null);
    }
    existingMarkers.push(marker)
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
                <img src="/img/topright.webp" class="absolute w-[200px] h-auto right-[100px] z-10">
                <div class="bg-white shadow-xl sm:rounded-lg p-4 framed">
                    <div class="text-5xl mb-8 pr-[200px] text-center">
                        As you peer into your spellbook you see a faerie, they introduce themselves as <span class="font-bold"> {{props.template.name}}</span>

                    </div>

                    <div class="md:grid md:grid-cols-[30%_70%] gap-4 relative pr-4 z-0">
                        <div class="relative md:w-full w-1/2 mx-auto">
                            <img class="z-0 mx-auto w-4/5 mt-16 md:mt-8 lg:mt-16 top-0 relative rounded-xl rounded" :src="'https://faerie-doors.s3.us-east-2.amazonaws.com/'+props.template.image">
                            <img src="/img/frame.webp" class="absolute md:top-0 -top-16 float-left z-10">
                        </div>
                        <div class="bg-amber-100 p-4 rounded-lg text-4xl mt-4 p-8">
                            <div class="w-[90px] h-[50px] float-right inline-block font-serif"></div>
                            <span class="text-xl"> Your mind buzzes as you translate the faerie introducing themselves:</span>
                            <span class="text-8xl block -mb-16">"</span><BigFirstLetter class="block">{{props.template.bio}}</BigFirstLetter><span class="text-8xl">"</span>
                            <a class="text-lg underline">Hmm, that intro doesn't seem right, let's try translating again...</a>
                        </div>
                    </div>
                    <div class="text-6xl text-center mt-10">Where are you placing this door?</div>
                    <div class="w-full mt-4" style="height: 400px" id="map"></div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>

</style>
