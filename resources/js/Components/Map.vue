<template>
    <div class="map relative">
        <div v-if="data.mapLoaded" class="w-full py-4" style="height: 400px" id="map"></div>
<!--        <div v-if="data.mapLoaded" class="w-full absolute top-0" style="pointer-events: none; height: 400px;background: rgb(2,0,36); background: radial-gradient(circle, rgba(2,0,36,0) 70%, rgba(195,134,99,1) 100%);"></div>-->
        <img v-if="data.mapLoaded" src="/img/mouse.webp" class="absolute w-32 -bottom-10 md:-left-4 md:w-40">

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
</template>

<script setup>
import {Loader} from "@googlemaps/js-api-loader";
const loader = new Loader({
    apiKey: "AIzaSyA5D4hMDFEC0LeERCZikKGGyiWShCkwJ3Y",
    version: "weekly",
    libraries: ["places"]
});

const props = defineProps({
    clickable: Boolean,
    staticMarkers: Array
})

import {reactive} from "vue";

const data = reactive({
    mapLoaded: false,
});
const mapOptions = {
    mapId: "d848f879ef61ec24",
    center: {
        lat: 0,
        lng: 0
    },
    zoom: 18
};


const geolocationOptions = {
    enableHighAccuracy: true,
    maximumAge: 10000,
    timeout: 5000,
};

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition, null, geolocationOptions);
} else {
    alert("You must enable geolocation to use the map")
}


let locationMarker = null;
function showPosition(position) {
    data.mapLoaded = true;
    mapOptions.center.lat = position.coords.latitude
    mapOptions.center.lng = position.coords.longitude
    //
    // emit('latChanged', position.coords.latitude)
    // emit('lngChanged', position.coords.longitude)
    // Promise
    loader
        .load()
        .then((google) => {

            const map = new google.maps.Map(document.getElementById("map"), mapOptions);
            if (props.clickable){
                google.maps.event.addListener(map, "click", (event) => {
                    addMarker(event.latLng, map, google);
                });

            }

            if (props.staticMarkers.length > 0){
                for(let x = 0; x < props.staticMarkers.length; x++){
                    if (props.staticMarkers[x].image){
                        props.staticMarkers[x].icon = {
                            url: props.staticMarkers[x].image,
                            // This marker is 20 pixels wide by 32 pixels high.
                            scaledSize: new google.maps.Size(40, 62),
                            // The origin for this image is (0, 0).
                            origin: new google.maps.Point(0, 0),
                            // The anchor for this image is the base of the flagpole at (0, 32).
                            anchor: new google.maps.Point(20, 32),
                        };
                        props.staticMarkers[x].shape = {
                            coords: [25, 25, 25],
                            type: "circle"
                        }
                    }

                    const marker = new google.maps.Marker(props.staticMarkers[x])
                    marker.setMap(map)
                }
            }

            locationMarker = new google.maps.Marker({
                position: mapOptions.center,
                icon: {
                    url: '/img/location.png',
                    scaledSize: new google.maps.Size(10, 10),
                    anchor: new google.maps.Point(5, 5),
                }
            })

            locationMarker.setMap(map)

            setInterval(function(){

                navigator.geolocation.getCurrentPosition(function (position){
                    locationMarker.setPosition(new google.maps.LatLng(position.coords.latitude, position.coords.longitude))

                }, null, geolocationOptions );
            }, 2000)




        })
        .catch(e => {
            console.log(e);
        });
}



const existingMarkers = []
const emit = defineEmits(['latChanged', 'lngChanged'])
function addMarker(latlng, map, google) {

    emit('latChanged', latlng.lat())
    emit('lngChanged', latlng.lng())

    const marker = new google.maps.Marker({
        position: latlng
    })
    marker.addListener('click', function(){

        emit('latChanged', null)
        emit('lngChanged', null)
        for (let x = 0; x < existingMarkers.length; x++) {
            existingMarkers[x].setMap(null);
        }
    })
    marker.setMap(map)
    for (let x = 0; x < existingMarkers.length; x++) {
        existingMarkers[x].setMap(null);
    }
    existingMarkers.push(marker)
}

</script>
