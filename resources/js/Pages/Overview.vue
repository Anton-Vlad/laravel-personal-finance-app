<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import StatsCard from "@/Widgets/StatsCard.vue";
import StatsCard2 from "@/Widgets/StatsCard2.vue";

const props = defineProps({
    totalBalance: String|Number,
    totalTransactions: String|Number
});

</script>

<template>
    <Head title="Overview" />

    <DashboardLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Overview
            </h2>
        </template>

        <div class="mx-auto max-w-7xl ">

            <div class="grid grid-cols-3 gap-3">
                <StatsCard :label="'Total Balance'" :value="totalBalance" />
                <StatsCard :label="'Transactions'" :value="totalTransactions" />
            </div>

        </div>
    </DashboardLayout>
</template>
