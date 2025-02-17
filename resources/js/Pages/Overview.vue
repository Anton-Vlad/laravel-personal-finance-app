<template>
    <Head title="Overview" />

    <DashboardLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="text-xl font-semibold leading-tight text-gray-800"
                >
                    Overview
                </h2>

                <PeriodFilter :filters="filters" :frontendFilters="frontendFilters" @period="onPeriodChange" />
            </div>
        </template>

        <div class="mx-auto max-w-7xl ">

            <div class="grid grid-cols-3 gap-3">
                <StatsCard :label="'Total Balance'" :value="totalBalance" />
                <StatsCard :label="'Transactions'" :value="totalTransactions" />
                <StatsCard :label="'Income/Expense Ratio'" :value="expenseIncomeRatio" />
            </div>

        </div>
    </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import StatsCard from "@/Widgets/StatsCard.vue";
import {ref} from 'vue';
import PeriodFilter from "@/Components/PageFilters/PeriodFilter.vue";

const props = defineProps({
    totalBalance: String|Number,
    totalTransactions: String|Number,
    expenseIncomeRatio: String|Number,
    filters: Array|Object,
    frontendFilters: Array|Object
});

const makeRequest = ({periodType, periodValue}) => {
    router.get('/overview', {period_type: periodType, period_value: periodValue}, {
        preserveState: true,
        replace: true
    });
}

const onPeriodChange = (period) => {
    makeRequest(period);
}
</script>
