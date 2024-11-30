<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Transactions" />

    <DashboardLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Transactions
            </h2>
        </template>

        <div class="mx-auto max-w-7xl ">
            The transactions page
        </div>
    </DashboardLayout>
</template>
