<template>
    <ul v-if="details && details.length">
        <li v-for="(line, index) in details" :key="'details-row' + index" class="text-sm ">
            {{ line }}
        </li>
    </ul>
    <ul v-else>
        <li>No additional details</li>
    </ul>
</template>

<script setup>

const props = defineProps({
    details: {
        type: Array,
        required: true
    }
})
</script>
