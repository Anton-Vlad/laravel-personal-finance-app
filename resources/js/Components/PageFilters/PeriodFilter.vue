<template>
    <div class="flex items-center">
        <select
            v-model="periodPickerType"
            name="periodPickerType"
            id="periodPickerType"
            class="w-full rounded-md border-gray-200 text-gray-600 hover:text-gray-700 py-2.5 pe-10 shadow-xs sm:text-sm"
        >
            <optgroup v-for="filter in frontendFilters" :label="filter.label">
                <template v-if="filter.entity">
                    <option :value="filter.entity.value">
                        {{ filter.entity.text }}
                    </option>
                </template>
                <template v-else>
                    <option v-for="(optionVal, optionKey) in filter.values" :value="optionKey">
                        {{ optionVal }}
                    </option>
                </template>
            </optgroup>
        </select>

        <select
            v-if="showValuePicker[0]"
            v-model="periodPickerValue"
            name="monthPicker"
            id="monthPicker"
            class="w-full rounded-md ms-3 border-gray-200 text-gray-600 hover:text-gray-700 py-2.5 pe-10 shadow-xs sm:text-sm"
        >
            <optgroup v-for="(group, index) in frontendFilters[1].groups" :key="group.label+index" :label="group.label">
                <option v-for="(optionLabel, optionKey) in group.values" :key="optionKey" :value="optionKey">
                    {{ optionLabel }}
                </option>
            </optgroup>
        </select>

        <select
            v-if="showValuePicker[1]"
            v-model="periodPickerValue"
            name="yearPicker"
            id="yearPicker"
            class="w-full rounded-md ms-3 border-gray-200 text-gray-600 hover:text-gray-700 py-2.5 pe-10 shadow-xs sm:text-sm"
        >
                <option v-for="(option) in frontendFilters[2].values" :key="option.label" :value="option.value">
                    {{ option.label }}
                </option>
        </select>
    </div>
</template>

<script setup>
import {onMounted, ref, watch} from 'vue';

const props = defineProps({
    filters: Array|Object,
    frontendFilters: Array|Object
});

const emit = defineEmits(['period'])

let queryParams = route().params;
const periodPickerType = ref((props.filters.periodType || queryParams.period_type) || 'year');
const periodPickerValue = ref((props.filters.periodValue || queryParams.period_value) || '2024');

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
});

watch(periodPickerValue, value => {
    emit('period', {
        periodType: periodPickerType.value,
        periodValue: periodPickerValue.value
    })
});

onMounted(async () => {
    if (periodPickerType.value === 'month') {
        showValuePicker.value = [true, false];
    } else if (periodPickerType.value === 'year') {
        showValuePicker.value = [false, true];
    }
})
</script>
