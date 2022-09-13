<template>
    <QrcodeVue :size="500" :value="route('qr', uuid)"></QrcodeVue>
</template>

<script setup>
import QrcodeVue from 'qrcode.vue'
const props = defineProps({
    uuid: String
})
</script>

<style scoped>

</style>
