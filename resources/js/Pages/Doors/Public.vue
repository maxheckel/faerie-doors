<template>
    <div class="bg-amber-100 h-full">
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
import {useForm} from "@inertiajs/inertia-vue3";
import Textarea from "@/Components/Textarea.vue";
import {ref} from "vue";
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

if (props.old){
    form.email = props.old.email;
    form.name = props.old.name;
    form.message = props.old.message;
}

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
