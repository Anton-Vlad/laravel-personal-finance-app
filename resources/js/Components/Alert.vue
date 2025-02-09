<template>
    <div v-show="show" role="alert" id="flashAlert" class="rounded-lg shadow-lg  border  bg-emerald-100 text-emerald-700 p-4">
        <div class="surface flex items-start gap-4">
              <span class="text-emerald-700">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                >
                  <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </span>

            <div class="flex-1">
                <strong class="block font-medium text-gray-900"> Changes saved </strong>

                <p class="mt-1 text-sm text-gray-700">
                    {{ flashMessage }}
                </p>
            </div>

            <button class="text-gray-500 transition hover:text-gray-600" @click="dismiss">
                <span class="sr-only">Dismiss popup</span>

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="layer bg-emerald-300 opacity-50  fdsfs" :style="{'width': width + '%'}"></div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import {computed, onMounted, ref, watch} from "vue";

const page = usePage();

const flashMessage = computed(() => page.props?.flash?.message);

const show = ref(false);
const width = ref(0);
const interval1 = ref();
const timeout1 = ref();

onMounted(() => {})

const startAnimation = () => {
    resetAnimation();

    interval1.value = setInterval(() => {
        width.value += 1;
    }, 100);

    timeout1.value = setTimeout(() => {
        resetAnimation();

        dismiss();
    }, 10000);

}

const resetAnimation = () => {
    width.value = 0;

    if (interval1.value) {
        clearInterval(interval1.value);
    }
    if (timeout1.value) {
        clearTimeout(timeout1.value)
    }
}

const dismiss = () => {
    show.value = false;
    if (interval1.value) {
        clearInterval(interval1.value);
    }
    if (timeout1.value) {
        clearTimeout(timeout1.value)
    }
}

watch(page.props, (newVal, oldVal) => {
    if (newVal?.flash?.message) {
        show.value = true;
        startAnimation();
    }
},{
    immediate: true,
    deep: true,
});
</script>

<style>
#flashAlert {
    position: fixed;
    top: 5rem;
    right: 2rem;
    overflow: hidden;
}

#flashAlert .layer {
    position: absolute;
    top: 0;
    left: 0;
    width: 70%;
    height: 100%;
    z-index: -1;
    transition: width 0.3s ease;
}
</style>
