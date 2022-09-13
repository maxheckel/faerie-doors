<template>
    <div class="inline stylized-button py-4 px-16 w-auto m-4 relative">
        <img src="/img/rock.webp" class="inline-block -ml-20 h-20">
        <span class="text-4xl inline-block mt-4 text-white font-bold">
        Some Text
        </span>
        <img src="/img/mushroom.webp" class="inline-block -mr-16 h-20">

    </div>
</template>

<script setup>

function randomImage(){
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
    return items[Math.floor(Math.random()*items.length)];
}

</script>

<style scoped>
    .stylized-button{

        background-image: radial-gradient(circle, rgba(0,0,0,0) 20%, rgba(255,255,255,1) 100%), url("/img/buttonbg.webp");
        background-size: cover;
        background-position: center;
    }
</style>
