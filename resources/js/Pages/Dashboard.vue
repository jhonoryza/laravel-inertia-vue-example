<script setup>
import Table from '@/Components/Liv/Table.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    users: {
        type: Object,
        default: () => { }
    },
    pageOptions: {
        type: Array,
        default: () => []
    },
    limit: {
        type: Number,
        default: 10
    },
    allIds: {
        type: Array,
        default: () => []
    },
    columns: {
        type: Array,
        default: () => []
    }
})

</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <Table :items="users" :page-options="pageOptions" :limit="limit" :all-ids="allIds"
                        :columns="columns" module="user" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
