<template>
    <div class="flex items-center">
        <select
            v-model="periodPickerType"
            name="periodPickerType"
            id="periodPickerType"
            class="w-full rounded-md border-gray-200 text-gray-600 hover:text-gray-700 py-2.5 pe-10 shadow-xs sm:text-sm"
        >
            <optgroup label="Quick picks">
                <option value="15days">Last 15 days</option>
                <option value="thisMonth">This month</option>
                <option value="lastMonth">Last month</option>
                <option value="last3Months">Last 3 months</option>
                <option value="thisYear">This year</option>
            </optgroup>

            <optgroup label="Weeks">
                <option value="week">Pick a week</option>
            </optgroup>
            <optgroup label="Months">
                <option value="month">Pick a month</option>
            </optgroup>
            <optgroup label="Years">
                <option value="year">Pick a year</option>
            </optgroup>
        </select>

        <select
            v-if="showValuePicker[0]"
            v-model="periodPickerValue"
            name="monthPicker"
            id="monthPicker"
            class="w-full rounded-md ms-3 border-gray-200 text-gray-600 hover:text-gray-700 py-2.5 pe-10 shadow-xs sm:text-sm"
        >
            <optgroup label="2025">
                <option value="2025-02">February</option>
                <option value="2025-01">January</option>
            </optgroup>
            <optgroup label="2024">
                <option value="2024-12">December</option>
                <option value="2024-11">November</option>
                <option value="2024-10">October</option>
                <option value="2024-09">September</option>
                <option value="2024-08">August</option>
                <option value="2024-07">July</option>
                <option value="2024-06">June</option>
                <option value="2024-05">May</option>
                <option value="2024-04">April</option>
                <option value="2024-03">March</option>
                <option value="2024-02">February</option>
                <option value="2024-01">January</option>
            </optgroup>
            <optgroup label="2023">
                <option value="2023-12">December</option>
                <option value="2023-11">November</option>
                <option value="2023-10">October</option>
                <option value="2023-09">September</option>
                <option value="2023-08">August</option>
                <option value="2023-07">July</option>
                <option value="2023-06">June</option>
                <option value="2023-05">May</option>
                <option value="2023-04">April</option>
                <option value="2023-03">March</option>
                <option value="2023-02">February</option>
                <option value="2023-01">January</option>
            </optgroup>
            <optgroup label="2022">
                <option value="2022-12">December</option>
                <option value="2022-11">November</option>
                <option value="2022-10">October</option>
                <option value="2022-09">September</option>
                <option value="2022-08">August</option>
                <option value="2022-07">July</option>
                <option value="2022-06">June</option>
                <option value="2022-05">May</option>
                <option value="2022-04">April</option>
                <option value="2022-03">March</option>
                <option value="2022-02">February</option>
                <option value="2022-01">January</option>
            </optgroup>
        </select>

        <select
            v-if="showValuePicker[1]"
            v-model="periodPickerValue"
            name="yearPicker"
            id="yearPicker"
            class="w-full rounded-md ms-3 border-gray-200 text-gray-600 hover:text-gray-700 py-2.5 pe-10 shadow-xs sm:text-sm"
        >
                <option value="2025">2025</option>
                <option value="2024">2024</option>
                <option value="2023">2023</option>
                <option value="2022">2022</option>
                <option value="2021">2021</option>
                <option value="2020">2020</option>
        </select>
    </div>
</template>

<script setup>
import {ref, watch} from 'vue';

const props = defineProps({
    filters: Array|Object
});

const emit = defineEmits(['period'])

const periodPickerType = ref(props.filters.periodType || 'year');
const periodPickerValue = ref(props.filters.periodValue || '2024');

const showValuePicker = ref([false, false])

watch(periodPickerType, value => {
    showValuePicker.value = [false, false];

    if (value === 'month') {
        periodPickerValue.value = '2024-08'
        showValuePicker.value = [true, false];
        return;
    }
    if (value === 'year') {
        periodPickerValue.value = '2024'
        showValuePicker.value = [false, true];
        return;
    }

    emit('period', {
        periodType: periodPickerType.value,
        periodValue: null
    })
}, {
    immediate: true
});

watch(periodPickerValue, value => {
    emit('period', {
        periodType: periodPickerType.value,
        periodValue: periodPickerValue.value
    })
});
</script>
