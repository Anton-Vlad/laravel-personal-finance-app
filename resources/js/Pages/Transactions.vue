<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    data: Object,
    filters: Array
});

const expandedRows = ref([]);

const toggleDetails = (item) => {
    const id = item.id;
    console.log("WEdr", id)
    if (expandedRows.value.includes(id)) {
        expandedRows.value = expandedRows.value.filter(rowId => rowId !== id);
    } else {
        expandedRows.value.push(id);
    }
};

onMounted(() => {
    console.log(props.data)
})

const transactions = computed(() => {
    return props.data.data ?? [];
});

const displayAmountValue = (val) => {
    // { notation: "compact", compactDisplay: "long" }
    if (val >= 0) {
        return '+' + new Intl.NumberFormat("en-US").format(val)
    }

    return '-' + new Intl.NumberFormat("en-US").format(Math.abs(val));
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
                        <th class="px-4 py-3 text-left">Category</th>
                        <th class="px-4 py-3 text-left">Transaction Date</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-right">Currency</th>
                        <th class="px-4 py-3 text-center">Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in transactions" :key="item.id" class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">{{ item.name }}</td>
                        <td class="px-4 py-3">{{ item.category || "-" }}</td>
                        <td class="px-4 py-3">{{ displayDate(item.date) }}</td>
                        <td class="px-4 py-3 text-right">
                            <span :class="displayAmountClass(item.amount)">{{ displayAmountValue(item.amount) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">{{ item.currency }}</td>
                        <td class="px-4 py-3 text-center">
                            <button @click="toggleDetails(item)" class="text-blue-500 hover:underline">
                                {{ expandedRows.includes(item.id) ? "Hide" : "Show" }}
                            </button>
                        </td>
                    </tr>
                    <!-- Expandable Row -->
                    <tr v-for="item in transactions" :key="'details-' + item.id" class="bg-gray-50">
                        <template v-if="expandedRows.includes(item.id)">

                            <td colspan="6" class="px-4 py-3">
                                <div class="flex justify-between p-4 border rounded-md bg-white shadow-md">
                                    <div>
                                        <h3 class="font-semibold text-lg text-gray-700 mb-3">
                                            Transaction Details <strong>ID:</strong> {{ item.id }}
                                        </h3>
                                        <p><strong>Name:</strong> <span class="font-bold ms-5">{{ item.name }}</span></p>
                                        <p><strong class="me-1">Amount:</strong> <span :class="displayAmountClass(item.amount)">{{ displayAmountValue(item.amount) }}</span> <span>{{ item.currency }}</span></p>
                                        <p><strong>Date:</strong> <span class="ms-7">{{ displayDate(item.date) }}</span></p>
                                        <p><strong>Statement:</strong> <span class="ms-7">{{ item.statement_id || '-' }}</span></p>
                                    </div>

                                    <div>
                                        <p><strong>Notes:</strong> <pre>{{ item.details || "No additional details" }}</pre></p>
                                    </div>
                                </div>
                            </td>
                        </template>
                    </tr>
                    </tbody>
                </table>
            </div>

            <pagination class="mt-6" :links="data.links" />
        </div>
    </DashboardLayout>
</template>
