<template>
    <Head title="Budgets" />

    <DashboardLayout>
        <template #header>
            <h1
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Budgets
            </h1>
        </template>


        <div class="grid grid-cols-1 xl:grid-cols-[25%_75%] gap-4 mx-auto max-w-7xl">

            <div>
                <div class=" bg-white shadow-lg p-4">
                    <div class="flex justify-center mb-6">
                        <div :class="`relative w-32 h-32 rounded-full after:absolute after:inset-4 after:bg-white after:rounded-full flex items-center justify-center text-xl font-bold`"
                            :style="{'background': generateConicGradient()}"
                        >
                            <span class="relative z-10">{{ formattedTotal(totalSpendings) }}</span>
                        </div>
                    </div>

                    <div>
                        <h4 class="mb-2 font-bold">
                            Spending Summary
                        </h4>
                        <ul>
                            <li v-for="budget in spendings" class="flex items-center justify-between py-2 border-b-gray-200 border-b last:border-0">
                                <span :class="`relative text-sm text-gray-400 font-semibold pl-4 before:absolute before:left-0 before:top-1/2 before:-translate-y-1/2 before:w-1 before:h-4 before:${budget.theme.bg} before:rounded-sm`">
                                    {{ budget.name }}
                                </span>
                                <span>
                                    {{ formattedTotal(budget.spending) }} <small class="text-gray-400">RON</small>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div>
                <div class="grid grid-cols-1 gap-4">
                    <div v-for="budget in budgets" class=" bg-white shadow-lg p-4" :key="budget.id">
                        <h3 :class="`relative pl-6 text-md font-semibold before:absolute before:left-0 before:top-1/2 before:-translate-y-1/2 before:w-3 before:h-3 before:${budget.theme.bg} before:rounded-full`">
                            {{ budget.name }}
                        </h3>
                    </div>
                </div>
            </div>

        </div>
    </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import {onMounted, ref, computed} from "vue";

const props = defineProps({
    budgets: Array,
});

const spendings = computed(() => {
    return props.budgets?.filter(x => !x.exclude_from_summary) || [];
})
const donutValues = computed(() => {
    return spendings.value.map(x => x.spending);
});
const donutColors = computed(() => {
    return spendings.value.map(x => x.theme.hex);
});
const totalSpendings = computed(() => {
    return donutValues.value.reduce((sum, num) => sum + num, 0);
});


onMounted(() => {

})

function generateConicGradient() {
    const total = donutValues.value.reduce((sum, num) => sum + num, 0);
    let degrees = 0;

    return 'conic-gradient(' + donutValues.value.map((value, index) => {
        const start = degrees;
        const angle = (value / total) * 360;
        degrees += angle;
        return `${donutColors.value[index]} ${start}deg ${degrees}deg`;
    }).join(", ") + ')';
}

const formattedTotal = (value) => {
    return new Intl.NumberFormat("en-US", {
        notation: "compact", // Converts to '1.2K', '3.4M', etc.
        maximumFractionDigits: 1,
    }).format(value);
};
</script>
