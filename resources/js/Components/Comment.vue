<template>
    <div>
        <b class="block">{{comment.name}} {{ comment.parent_id == null ? 'says' : 'replies' }}:</b>
        <span class="text-md block">{{comment.created_at}}</span>
        {{comment.comment}}
        <div v-if="allowReplying">
            <form @submit.prevent="submit">
                <div v-if="messageSent" class="text-emerald-500 font-bold">
                    {{faerie.name}} has received your message!
                </div>
                <label class="mt-8 block font-bold text-md">Reply:</label>
                <Textarea class="w-full" v-model="form.message"></Textarea>
                <Button>Reply</Button>
            </form>
        </div>
        <Comment class="border-t-1 border p-4 block mt-4" :comment="child" v-for="child in comment.comments"></Comment>
    </div>
</template>

<script setup>

import {useForm} from "@inertiajs/inertia-vue3";
import Textarea from "./Textarea.vue";
import Button from "./Button.vue";

const props = defineProps({
    comment: Object,
    allowReplying: Boolean,
    messageSent: Boolean
})


const form = useForm({
    message: ""
})

function submit(){
    form.post(route('leave-reply', props.comment.id),{
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
