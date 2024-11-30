<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Recurring bills" />

    <DashboardLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Recurring bills
            </h2>
        </template>

        <div class="mx-auto max-w-7xl ">
            The Recurring bills page
        </div>
    </DashboardLayout>
</template>
