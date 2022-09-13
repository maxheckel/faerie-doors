<template>
    <div>
        <b class="block">{{comment.name}} {{ comment.parent_id == null ? 'says' : 'replies' }}:</b>
        <span class="text-md block">{{comment.created_at}}</span>
        {{comment.comment}}

        <Comment class="border-t-1 border p-4 block mt-4" :comment="child" v-for="child in comment.comments"></Comment>
    </div>
</template>

<script setup>

const props = defineProps({
    comment: Object
})

</script>
