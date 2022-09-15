<template>

    <Head>
        <title>{{faerie.name}}</title>
        <meta name="description" :content="faerie.bio">
    </Head>

    <div v-if="data.loadingLocation" class="min-h-screen w-full absolute z-20 overflow-hidden">
        <div class=" text-2xl bg-white rounded rounded-xl p-8 text-center m-10">Loading Location...</div>
    </div>
    <div v-if="!data.isNearby && !data.loadingLocation" class="min-h-screen w-full absolute z-20 overflow-hidden">
        <div class=" text-2xl bg-white rounded rounded-xl p-8 text-center m-10">You must be closer to view the faerie!</div>
    </div>

    <div :class="{blur: !data.isNearby}" class="bg-amber-100 h-full">
        <div class="w-full h-80 bg-cover bg-center  rounded-bl-2xl rounded-br-2xl" :style="{'background-image': randomCover()}"></div>

        <div class="relative lg:w-1/6 w-1/2 mx-auto -mt-60">
            <Portrait :image_url="faerie.image_url"></Portrait>
        </div>
        <div class="text-2xl my-16 text-center">
            You've found the home of {{ faerie.name }}!<br>
            <span class="block text-md"> {{faerie.name}} has been visited {{visits}} times!</span>
            <Button class="bg-amber-800 block" @click="goto('comment')">Leave {{ faerie.name }} a message</Button>
        </div>
        <div class="mt-4 p-4 text-2xl font-bold bg-amber-50 m-8 rounded-lg">
            <BigFirstLetter>{{ faerie.bio }}</BigFirstLetter>
        </div>
        <img :src="randomImage()" class="w-1/4 mb-8 relative mx-auto">
        <AuthenticationCard class="overflow-hidden bg-amber-100">
            <div class="text-2xl font-bold" ref="comments">
                Leave a message for {{ faerie.name }}
            </div>
            <div v-if="props.profanity" class="text-red-500 font-bold">
                Do not use profanity in your messages, faeries don't like swearies
            </div>
            <div v-if="props.messageSent" class="text-emerald-500 font-bold">
                {{faerie.name}} has received your message!
            </div>
            <form @submit.prevent="submit">

                <div>
                    <label class="font-bold text-lg">Email
                        <span
                            class="text-xs block">*Will be used to alert you when {{ faerie.name }} replies.</span>
                    </label>
                    <JetInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                    />
                    <JetInputError class="mt-2" :message="form.errors.email"/>
                </div>

                <div>
                    <JetLabel for="name" value="Name"/>
                    <JetInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                    <JetInputError class="mt-2" :message="form.errors.email"/>
                </div>
                <div class="mt-4">
                    <JetLabel for="message" value="Message"/>
                    <Textarea
                        id="message"
                        v-model="form.message"
                        class="mt-1 block w-full"
                        required
                    />
                    <JetInputError class="mt-2" :message="form.errors.message"/>
                </div>

                <div class="flex items-center justify-end mt-4">


                    <Button class="ml-4 w-full text-center items-center py-2 text-2xl bg-amber-800"
                            :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save
                    </Button>
                </div>
            </form>
        </AuthenticationCard>
        <img :src="randomImage()" class="w-1/4 py-8 relative mx-auto">
        <div v-if="faerie.comments.length > 0">
        <div class="text-2xl text-center" >Other Messages:</div>
        <Comment class="text-2xl m-4 rounded-lg p-4 bg-white" :comment="comment" v-for="comment in faerie.comments.filter((c) => c.parent_id == null)"></Comment>
        <img :src="randomImage()" class="w-1/4 py-8 relative mx-auto">
        </div>
        <div class="text-center text-2xl font-bold mb-4">There may be other faeries nearby</div>

        <div class="p-4 pb-10">
            <Map class="" :staticMarkers="staticMarkers"/>
        </div>


    </div>


</template>

<script setup>
import Portrait from "@/Components/Portrait.vue";
import BigFirstLetter from "@/Components/BigFirstLetter.vue";
import Button from "@/Components/Button.vue";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import JetInput from '@/Components/Input.vue';
import JetInputError from '@/Components/InputError.vue';
import JetLabel from '@/Components/Label.vue';
import {useForm, Head} from "@inertiajs/inertia-vue3";
import Textarea from "@/Components/Textarea.vue";
import {reactive, ref} from "vue";
import Comment from "@/Components/Comment.vue";
import Map from "@/Components/Map.vue";

const comments = ref(null)

function goto() {
    var top = comments.value.offsetTop;

    window.scrollTo(0, top);
}

function randomCover() {
    const num = 1 + Math.floor(Math.random() * 52);
    return "linear-gradient(to bottom, rgba(245, 246, 252, 0), rgba(254, 241, 192, 0.7)), url('/img/covers/" + num + ".jpg')";
}


function randomImage() {
    const items = [
        '/img/mushrooms.webp',
        '/img/mouse.webp',
        '/img/leaf.webp',
        '/img/frog.webp',
        '/img/leafy.webp',
        '/img/mush-with-books.webp',
        '/img/stump.webp',
        '/img/rock.webp',
        '/img/sidebranch.webp',
        '/img/mushroom.webp',
        '/img/logo.png'
    ]
    return items[Math.floor(Math.random() * items.length)];
}

const form = useForm({
    email: '',
    name: '',
    message: ''
})

const props = defineProps({
    faerie: Object,
    otherFaeries: Array,
    profanity: Boolean,
    messageSent: Boolean,
    visits: Number,
    old: Object
})

function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
    var R = 6371; // Radius of the earth in km
    var dLat = deg2rad(lat2-lat1);  // deg2rad below
    var dLon = deg2rad(lon2-lon1);
    var a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
        Math.sin(dLon/2) * Math.sin(dLon/2)
    ;
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    var d = R * c; // Distance in km
    return d;
}

function deg2rad(deg) {
    return deg * (Math.PI/180)
}


const geolocationOptions = {
    enableHighAccuracy: true,
    maximumAge: 10000,
    timeout: 5000,
};

// if (navigator.geolocation) {
//     navigator.geolocation.getCurrentPosition(function(position){
//         data.loadingLocation = false
//         if (getDistanceFromLatLonInKm(position.coords.latitude, position.coords.longitude, props.faerie.latitude, props.faerie.longitude) > 1){
//             data.isNearby = false
//         } else {
//             enableScroll()
//             data.isNearby = true;
//         }
//     }, null, geolocationOptions);
// }

if (props.old){
    form.email = props.old.email;
    form.name = props.old.name;
    form.message = props.old.message;
}

const data = reactive({
    isNearby: true,
    loadingLocation: false
})

const staticMarkers = [
    {
        position: {
            lat: props.faerie.latitude,
            lng: props.faerie.longitude,
        },
        image: props.faerie.image_url
    },
    ...props.otherFaeries.map((faerie) => {
        return {
            position: {
                lat: faerie.latitude,
                lng: faerie.longitude,
            }
        }
    })
]

// left: 37, up: 38, right: 39, down: 40,
// spacebar: 32, pageup: 33, pagedown: 34, end: 35, home: 36
var keys = {37: 1, 38: 1, 39: 1, 40: 1};

function preventDefault(e) {
    e.preventDefault();
}

function preventDefaultForScrollKeys(e) {
    if (keys[e.keyCode]) {
        preventDefault(e);
        return false;
    }
}

// modern Chrome requires { passive: false } when adding event
var supportsPassive = false;
try {
    window.addEventListener("test", null, Object.defineProperty({}, 'passive', {
        get: function () { supportsPassive = true; }
    }));
} catch(e) {}

var wheelOpt = supportsPassive ? { passive: false } : false;
var wheelEvent = 'onwheel' in document.createElement('div') ? 'wheel' : 'mousewheel';

// call this to Disable
function disableScroll() {
    window.addEventListener('DOMMouseScroll', preventDefault, false); // older FF
    window.addEventListener(wheelEvent, preventDefault, wheelOpt); // modern desktop
    window.addEventListener('touchmove', preventDefault, wheelOpt); // mobile
    window.addEventListener('keydown', preventDefaultForScrollKeys, false);
}

// call this to Enable
function enableScroll() {
    window.removeEventListener('DOMMouseScroll', preventDefault, false);
    window.removeEventListener(wheelEvent, preventDefault, wheelOpt);
    window.removeEventListener('touchmove', preventDefault, wheelOpt);
    window.removeEventListener('keydown', preventDefaultForScrollKeys, false);
}
// disableScroll()


function submit() {
    form.post(route('leave-comment', props.faerie.uuid), {
        preserveScroll: true,
        preserveState: false,
        onFinish: (data) => {
            console.log(data);
        }
    }, {
        resetOnSuccess: false,
    })
}
</script>

<style scoped>

</style>
