<?php

use Carbon\Carbon;

function generateYearlyGroups(int $yearsBack = 4): array
{
    $currentYear = Carbon::now()->year;
    $currentMonth = Carbon::now()->month; // Get current month
    $groups = [];

    for ($year = $currentYear; $year >= $currentYear - $yearsBack; $year--) {
        $months = [];

        // If it's the current year, only include past and current months
        $maxMonth = ($year == $currentYear) ? $currentMonth : 12;

        for ($month = $maxMonth; $month >= 1; $month--) {
            $date = Carbon::create($year, $month, 1);
            $months[$date->format('Y-m')] = $date->format('F'); // e.g., '2024-02' => 'February'
        }

        $groups[] = [
            'label' => (string) $year,
            'values' => $months,
        ];
    }

    return $groups;
}

function generateYearValues(int $yearsBack = 4): array
{
    $currentYear = Carbon::now()->year;
    $values = [];

    for ($year = $currentYear; $year >= $currentYear - $yearsBack; $year--) {
        $values[] = [
            'label' => (string) $year,
            'value' => (string) $year,
        ];
    }

    return $values;
}

return [
    'period-filter' => [
        [
            'label' => 'Quick picks',
            'values' => [
                '15days' => 'Last 15 days',
//                '30days' => 'Last 30 days',
                'thisMonth' => 'This month',
                'lastMonth' => 'Last month',
                'last3Months' => 'Last 3 months',
                'thisYear' => 'This year',
            ]
        ],
        [
            'label' => 'Months',
            'entity' => [
                'key' => 'monthPicker',
                'value' => 'month',
                'text' => 'Pick a month'
            ],
            'groups' => generateYearlyGroups()
        ],
//        [
//            'label' => 'Weeks',
//            'entity' => [
//                'key' => 'weekPicker',
//                'value' => 'week',
//                'text' => 'Pick a week'
//            ],
//            'groups' => [
//            ]
//        ],
        [
            'label' => 'Years',
            'entity' => [
                'key' => 'yearPicker',
                'value' => 'year',
                'text' => 'Pick a year'
            ],
            'values' => generateYearValues()
        ],
    ]
];
