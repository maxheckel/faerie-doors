<script setup>
import {reactive} from "vue";

function getRandomInt(max) {
    return Math.floor(Math.random() * max);
}
const mushrooms = reactive({
    data: []
});
for (let x = 0; x < 2 + getRandomInt(10); x++){
    mushrooms.data.push({
        margin: -180 + getRandomInt(360),
        width: 30 + getRandomInt(30),
    })
}

console.log(mushrooms.data)

</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-amber-50">
        <div>
            <slot name="logo"/>
        </div>




        <div class="hidden sm:visible sm:max-w-2xl z-20">
            <img v-for="mush in mushrooms.data" :style="{width: mush.width+'px', 'margin-left': mush.margin+'px', transform: getRandomInt(10) % 2 === 0 ? 'rotateY(180deg)' : '' + ' rotateX('+getRandomInt(20)+'px)'}" src="/img/mushroom.webp" class="mushroom">

        </div>




        <img src="/img/arch.webp" class="sm:max-w-2xl" style="margin-left: -40px; margin-bottom: -230px; ">

        <div class="forest w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg z-10">
            <div class="z-20">
                <slot/>
            </div>

        </div>
        <img src="/img/arch.webp" class="sm:max-w-2xl bottom-arch">
    </div>
</template>

<style>
.bottom-arch {
    margin-left: 40px;
    margin-top: -200px;
    transform: rotateX(180deg) rotateY(180deg);
}

.mushroom{
    position: absolute;
    width: 40px;
    height: auto;
    margin-bottom: -30px;
    margin-left: 180px;
}

</style>
