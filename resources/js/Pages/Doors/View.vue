<template>
    <div class="bg-amber-100 h-full">
        <div class="w-full h-80 bg-cover bg-center rounded-bl-2xl rounded-br-2xl" :style="{'background-image': randomCover()}">
            <div class="absolute left-10 top-10">
                <Button>
                    <a :href="route('dashboard')">Back</a>
                </Button>
            </div>
            <div class="hidden md:visible absolute right-10 text-center top-10">
                <QrcodeVue class="" :value="route('doors.public', faerie.uuid)" :size="100" :level="'H'"></QrcodeVue>
                <Button class="text-center mx-auto inline-block relative mt-4">
                    <a target="_blank" :href="route('qr', faerie.uuid)">Print</a>
                </Button>
            </div>
        </div>

        <div class="relative lg:w-1/6 w-1/2 mx-auto -mt-60">
            <Portrait :image_url="faerie.image_url"></Portrait>
        </div>
        <div class="text-2xl mt-8 text-center">
             {{ faerie.name }}<br>
            <span class="text-xl block">
                {{visits}} visits
            </span>
            <Button>
                <a :href="route('doors.edit', faerie.id)">Edit</a>
            </Button>
            <Button class="ml-4 bg-blue-400 text-white">
                <a :href="route('doors.public', faerie.uuid)">Go To Public Page</a>
            </Button>


            <Button class="ml-4 bg-red-500" @click="goto(comments)">
                {{noReplies}} New Messages
            </Button>
        </div>
        <div class="mt-4 p-4 text-2xl font-bold bg-amber-50 m-8 rounded-lg">
            <BigFirstLetter>{{ faerie.bio }}</BigFirstLetter>
        </div>
        <img :src="randomImage()" class="w-1/4 md:w-1/12 mb-8 relative mx-auto">

        <div v-if="faerie.comments.length > 0" ref="comments">
            <div class="text-2xl text-center" >Other Messages:</div>
            <Comment :allowReplying="comment.comments.length === 0" class="text-2xl m-4 rounded-lg p-4 bg-white" :comment="comment" v-for="comment in faerie.comments.filter((c) => c.parent_id == null)"></Comment>
            <img :src="randomImage()" class="w-1/4 md:w-1/12 py-8 relative mx-auto">
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
import QrcodeVue from 'qrcode.vue'
import Comment from "@/Components/Comment.vue";
import Map from "@/Components/Map.vue";
import {computed, ref} from "vue";


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

const props = defineProps({
    faerie: Object,
    otherFaeries: Array,
    profanity: Boolean,
    messageSent: Boolean,
    old: Object,
    visits: Number
})


const noReplies = computed(() => {
    return props.faerie.comments.filter((comment) => comment.comments.length === 0).length
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


const comments = ref(null)

function goto() {
    var top = comments.value.offsetTop;

    window.scrollTo(0, top);
}


</script>

<style scoped>

</style>
