<script setup>

import {computed} from "vue";

const props = defineProps({
    variant: {
        type: String,
        default: 'success',
    },
});

const colorClass = computed(() => {
    switch (props.variant) {
        case 'success':
            return 'bg-emerald-100 text-emerald-700 ' //dark:bg-emerald-700 dark:text-emerald-100
            break;
        case 'warning':
            return 'bg-amber-100 text-amber-700 ' //dark:bg-amber-700 dark:text-amber-100
            break;
    }
});

</script>

<template>
    <span
        :class="colorClass"
        class="inline-flex items-center justify-center rounded-full  px-2.5 py-0.5 "
    >
      <slot name="icon" />
      <span class="whitespace-nowrap text-sm">
          <slot />
      </span>
    </span>
</template>
