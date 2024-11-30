<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Budgets" />

    <DashboardLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Budgets
            </h2>
        </template>

        <div class="mx-auto max-w-7xl ">
            The Budgets page
        </div>
    </DashboardLayout>
</template>
