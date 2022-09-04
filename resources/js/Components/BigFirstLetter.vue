<script setup>
import {useSlots} from "vue";

const slots = useSlots();
const firstChar = slots.default()[0].children[0]
</script>
<template>
    <p class="big-first-letter">
        <span class="text-8xl pl-8 pr-2 rounded rounded-md inline-block relative text-amber-500">
            <span style="text-shadow: 3px 3px rgba(0,0,0,0.3);" class="z-10 relative"> {{firstChar}}</span>
            <img src="/img/sidebranch.webp" class="w-[50px] absolute left-0 top-0 z-0">
            <img src="/img/mushrooms.webp" class="w-[30px] absolute -right-2 bottom-6 z-20">
        </span>{{slots.default()[0].children.substring(1)}}
    </p>

</template>

<style>
/* Style that first letter! */
.big-first-letter::first-letter {
    font-size: 30px;
}

</style>
