<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import { computed } from "vue";
import Badge from "@/Components/Badge.vue";
import Pagination from "@/Components/Pagination.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

const props = defineProps({
    data: Object,
    filters: Array
});

const statements = computed(() => {
    return props.data.data ?? [];
});

</script>

<template>
    <Head title="My Statements" />

    <DashboardLayout>
        <template #header>
            <div class="flex justify-between align-items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    My Statements
                </h2>

                <Link :href="route('statements.new')"
                      class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                >
                    Upload new
                </Link>
            </div>
        </template>


        <div class="mx-auto max-w-7xl ">
            <div class=" bg-white shadow-lg ">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Size</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Last updated</th>
                            <th> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in statements" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ item.title }}</td>
                            <td class="px-4 py-3">{{ item.file_size || '-' }}</td>
                            <td class="px-4 py-3">
                                <Badge :variant="item.status_type">
                                    {{ item.status }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">{{ item.updated_at }}</td>
                            <td>
                                <div class="relative ms-3">
                                    <Dropdown align="right" width="48" :key="'dropdown-'+item.id">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                                >
                                                    Actions
                                                    <svg
                                                        class="-me-0.5 ms-2 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <DropdownLink :href="route('statements.download', item.id)">
                                                Download
                                            </DropdownLink>
                                            <DropdownLink href="#">
                                                Transactions
                                            </DropdownLink>
                                            <DropdownLink
                                                :href="route('statements.destroy', item.id)"
                                                method="DELETE"
                                                as="button"
                                            >
                                                <span class="text-red-600">Delete</span>
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <pagination class="mt-6" :links="data.links" />
        </div>
    </DashboardLayout>
</template>
