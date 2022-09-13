<template>
    <div class="bg-amber-100 h-full">
        <div class="w-full h-80 bg-cover bg-center" :style="{'background-image': randomImage()}"></div>

        <div class="relative lg:w-1/6 w-1/2 mx-auto -mt-60">
            <Portrait :image_url="props.faerie.image_url"></Portrait>
        </div>
        <div class="text-2xl mt-8 text-center">
            You've found the home of {{props.faerie.name}}!<br>
            <Button class="bg-amber-800 block">Leave {{props.faerie.name}} a message</Button>
        </div>
        <div class="mt-4 p-4 text-2xl font-bold bg-amber-50 m-8 rounded-lg">
            <BigFirstLetter>{{props.faerie.bio}}</BigFirstLetter>
        </div>


    </div>


</template>

<script setup>
    import Portrait from "@/Components/Portrait.vue";
    import BigFirstLetter from "@/Components/BigFirstLetter.vue";
    import Button from "@/Components/Button.vue";

    function randomImage(){
        const num = 1+Math.floor(Math.random() * 52);
        return "linear-gradient(to bottom, rgba(245, 246, 252, 0), rgba(254, 241, 192, 0.7)), url('/img/covers/"+num+".jpg')";
    }

  const props = defineProps({
      faerie: Object
  })
</script>

<style scoped>

</style>
