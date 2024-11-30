<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'flex items-center px-4 sm:px-6 lg:px-8 nav-link nav-link--active'
        : 'flex items-center px-4 sm:px-6 lg:px-8 nav-link'
        // ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
        // : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out',
);
</script>

<template>
    <li :class="classes">
        <Link :href="href">
            <slot />
        </Link>
    </li>
</template>

<style>
.nav-link {
    position: relative;
}

.nav-link::after {
    content: "";
    display: block;
    background-color: #201F24;
    transition: background-color 0.3s ease;
    position: absolute;
    top: 0;
    left: -32px;
    width: 100%;
    height: 100%;
    z-index: 1;
    border-top-right-radius: 12px;
    border-bottom-right-radius: 12px;
}

.nav-link > a {
    color: #fff;
    font-size: 1.2rem;
    line-height: 1;
    cursor: pointer;
    transition: color 0.3s ease;
    position: relative;
    z-index: 2;

    display: flex;
    align-items: center;
    width: 100%;
    gap: 1rem;
    padding: 1rem 0;
}

.nav-link > a > span {
    transition: opacity 0.3s ease, width 0.3s ease;
    white-space: nowrap;
}
.nav-link > a > svg {
    min-width: 20px;
}

.body-main-wrapper.sidebar-minimized .nav-link > a > span {
    opacity: 0;
    width: 0px;
    pointer-events: none;
}


.nav-link.nav-link--active::after {
    background-color: #F8F4F0;
}

.nav-link.nav-link--active > a {
    color: #201F24;
}

.nav-link.nav-link--active > a > svg path {
    fill: #277C78;
}

@media (min-width: 640px) {
    .nav-link::after {
        left: -20px;
    }
}
</style>
