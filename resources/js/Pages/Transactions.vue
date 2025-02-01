<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import {computed, onMounted} from 'vue';

const props = defineProps({
    data: Object,
    filters: Array
});

onMounted(() => {
    console.log(props.data)
})

const transactions = computed(() => {
    return props.data.data ?? [];
});

const displayAmountValue = (val) => {
    if (val >= 0) {
        return '+$' + val;
    }

    return '-$' + Math.abs(val);
}
const displayAmountClass = (val) => {
    return val >= 0 ? 'font-bold text-green-600' : 'font-bold text-gray-900';
}

const displayDate = (val) => {
    const date = new Date(val);
    return new Intl.DateTimeFormat('en-GB', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(date);
}
</script>

<template>

    <Head title="Transactions" />

    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Transactions
            </h2>
        </template>

        <div class="mx-auto max-w-7xl ">
            <div class="p-5 bg-white rounded">

                <div class="table w-full ">
                    <div class="table-header-group ">
                        <div class="table-row">
                            <div class="table-cell text-left ...">Recipient / Sender</div>
                            <div class="table-cell text-left ...">Category</div>
                            <div class="table-cell text-left ...">Transaction Date</div>
                            <div class="table-cell text-right ...">Amount</div>
                        </div>
                    </div>
                    <div class="table-row-group">
                        <div v-for="item in transactions" :key="item.id" class="table-row">
                            <div class="table-cell">
                                {{ item.name }}
                            </div>
                            <div class="table-cell">
                                {{ item.category }}
                            </div>
                            <div class="table-cell">
                                {{ displayDate(item.date) }}
                            </div>
                            <div class="table-cell text-right">
                                <span :class="displayAmountClass(item.amount)">{{ displayAmountValue(item.amount)
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
