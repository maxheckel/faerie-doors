<template>
    <div>
        <img class="z-0 mx-auto w-4/5 mt-[20%] relative rounded-xl rounded"
             :src="props.image_url">
        <img src="/img/frame.webp" class="absolute top-0 float-left z-10">
    </div>

</template>

<script setup>
const props = defineProps({
    image_url: String
})
</script>

<style scoped>

</style>
