<template>

    <Head title="Transactions" />

    <DashboardLayout>
        <template #header>
            <div class="flex justify-between align-items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Transactions
                </h2>

                <PeriodFilter :filters="filters" @period="onPeriodChange" />
            </div>
        </template>

        <div class="mx-auto max-w-7xl ">

            <div class=" bg-white shadow-lg p-4">
                <div>
                    <div class="flex pb-3 justify-between items-center">
                            <div class="relative min-w-40">
                                <label for="Search" class="sr-only"> Search </label>

                                <input
                                    v-model="search"
                                    type="text"
                                    id="Search"
                                    placeholder="Search for..."
                                    class="w-full rounded-md border-gray-200 py-2.5 pe-10 shadow-xs sm:text-sm"
                                />

                                <span class="absolute inset-y-0 end-0 grid w-10 place-content-center">
                                  <button type="button" class="text-gray-600 hover:text-gray-700">
                                    <span class="sr-only">Search</span>

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-4"
                                    >
                                      <path
                                          stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                                      />
                                    </svg>
                                  </button>
                                </span>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex items-center">
                                    <label for="HeadlineAct" class="text-nowrap text-sm text-gray-600 pe-3">Sort by</label>
                                    <select
                                        v-model="orderBy"
                                        name="HeadlineAct"
                                        id="HeadlineAct"
                                        class="w-full rounded-md border-gray-200 text-gray-600 hover:text-gray-700 py-2.5 pe-10 shadow-xs sm:text-sm"
                                    >
                                        <option value="latest">Latest</option>
                                        <option value="oldest">Oldest</option>
                                        <option value="a2z">A to Z</option>
                                        <option value="z2a">Z to A</option>
                                        <option value="highest">Highest</option>
                                        <option value="lowest">Lowest</option>
                                    </select>
                                </div>
                                <div class="flex items-center">
                                <label for="categoryFilter" class="text-nowrap text-sm text-gray-600 pe-3">Category</label>
                                <select
                                    name="categoryFilter"
                                    id="categoryFilter"
                                    class="w-full rounded-md border-gray-200 text-gray-600 hover:text-gray-700 py-2.5 pe-10 shadow-xs sm:text-sm"
                                >
                                    <option value="latest">All Transactions</option>
                                    <option value="oldest">Entertainment</option>
                                    <option value="a2t">Bills</option>
                                    <option value="z2a">Groceries</option>
                                </select>
                            </div>
                            </div>
                    </div>
                </div>
                <table class="w-full border-collapse">
                    <thead class="border-b-2 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">Recipient / Sender</th>
                        <th class="px-4 py-3 text-left">Details</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in transactions" :key="item.id" class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span :title="item.name">{{ item.name_short }}</span>

                            <div class="text-gray-500 text-sm">
                                {{ item.date }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <Details :details="item.details" />
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div :class="item.amount_class">{{ item.amount }}</div>
                            <span class="text-gray-500 text-sm">{{ item.currency }}</span>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>

            <pagination class="mt-6" :links="paginationLinks" />
        </div>
    </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {Head, Link, router} from '@inertiajs/vue3';
import {ref, computed, onMounted, watch} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Details from "@/Components/Transaction/Details.vue";
import debounce from "lodash/debounce";
import PeriodFilter from "@/Components/PageFilters/PeriodFilter.vue";

const props = defineProps({
    data: Object,
    filters: Array|Object
});

const search = ref(props.filters.search);
const orderBy = ref(props.filters.orderBy || 'latest');
const periodPickerType = ref(props.filters.periodType || 'year');
const periodPickerValue = ref(props.filters.periodValue || '2024');

onMounted(() => {})

const transactions = computed(() => {
    return props.data.data ?? [];
});

const paginationLinks = computed(() => {
    return props.data?.meta?.links ?? [];
});

const makeRequest = () => {
    router.get('/transactions', {
        search: search.value,
        orderBy: orderBy.value,
        period_type: periodPickerType.value,
        period_value: periodPickerValue.value
    }, {
        preserveState: true,
        replace: true
    });
}

watch(search, debounce(function (value) {
    makeRequest();
}, 500));

watch(orderBy, value => {
    makeRequest();
});

const onPeriodChange = (period) => {
    periodPickerType.value = period.periodType;
    periodPickerValue.value = period.periodValue;
    makeRequest();
}
</script>
