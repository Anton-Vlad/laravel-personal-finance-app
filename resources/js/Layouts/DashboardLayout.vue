<script setup>
import { ref } from 'vue';
import Sidebar from '@/Components/Navigation/Sidebar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const sidebarMinimized = ref(localStorage.getItem('laravelSidebarExpanded') === 'true');

const toggleSidebar = (payload) => {
    sidebarMinimized.value = payload;
}
</script>


<template>
    <div class="body-main-wrapper" :class="{'sidebar-minimized': sidebarMinimized}">
        <!-- Page Heading -->
        <header
            class="dashboard-header "
            v-if="$slots.header"
        >
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <Sidebar @minimize="toggleSidebar" />

        <!-- Page Content -->
        <main class="dashboard-main px-4 py-6 sm:px-6 lg:px-8">
            <slot />
        </main>

        <!-- <footer class="dashboad-footer bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                The footer
            </div>
        </footer> -->
    </div>
</template>
