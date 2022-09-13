<template>
    <div class="pt-[20%]">
        <img class="z-0 mx-auto w-4/5 relative rounded-xl rounded"
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
