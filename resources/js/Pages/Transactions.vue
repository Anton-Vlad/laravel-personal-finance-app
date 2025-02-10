<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Details from "@/Components/Transaction/Details.vue";

const props = defineProps({
    data: Object,
    filters: Array
});

onMounted(() => {})

const transactions = computed(() => {
    return props.data.data ?? [];
});

const paginationLinks = computed(() => {
    return props.data?.meta?.links ?? [];
});

</script>

<template>

    <Head title="Transactions" />

    <DashboardLayout>
        <template #header>
            <div class="flex justify-between align-items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Transactions
                </h2>

                <Link :href="route('statements.new')"
                    class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                >
                    Upload new
                </Link>
            </div>
        </template>

        <div class="mx-auto max-w-7xl ">

            <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
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
