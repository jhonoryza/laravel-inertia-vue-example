<script setup>
import Table from '@/Components/Liv/Table.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {IconTrash, IconEdit} from '@tabler/icons-vue';
import InputText from '@/Components/Liv/InputText.vue';

const props = defineProps({
    users: {
        type: Object,
        default: () => {
        }
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
    },
    filters: {
        type: Array,
        default: () => ['search']
    }
})

</script>

<template>

    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 shadow-sm sm:rounded-lg">

            <Table :items="users" :page-options="pageOptions" :limit="limit" :all-ids="allIds"
                   :columns="columns" module="user" :filters="filters">

                <!-- bulk action slot -->
                <template #bulkaction="{ selectedRows, confirmDelete, route, module }">
                    <button type="button"
                            class="text-sm text-skin-error hover:bg-red-100 rounded-lg flex gap-2 justify-start items-center p-2 w-full"
                            @click="() => {
                                        const posts = { ids: selectedRows };
                                        confirmDelete(route(`${module}.bulk-delete`, posts));
                                    }">
                        <IconTrash class="size-4"/>
                        <span>Bulk Delete</span>
                    </button>
                </template>
                <!-- end bulk action slot -->

                <!-- row action slot -->
                <template #rowaction="{ itemId, confirmDelete, route, module }">
                    <!-- edit item -->
                    <div class="inline-block mr-2">
                        <button type="button"
                                class="flex gap-1 justify-center items-center min-h-8 hover:text-yellow-400"
                                @click="$inertia.visit(route(`${module}.edit`, itemId))">
                            <IconEdit class="size-4"/>
                            Edit
                        </button>
                    </div>

                    <!-- delete item -->
                    <div class="inline-block mr-2">
                        <button type="button"
                                class="flex gap-1 justify-center items-center min-h-8 hover:text-red-400"
                                @click="confirmDelete(route(`${module}.destroy`, itemId))">
                            <IconTrash class="size-4"/>
                            Delete
                        </button>
                    </div>

                </template>
                <!-- end row action slot -->

                <!-- filter slot -->
                <template #filter="{ filter }">
                    <label class="block font-medium text-sm" for="name">Name</label>
                    <InputText v-model="filter.name"/>
                    <label class="block font-medium text-sm" for="email">Email</label>
                    <InputText v-model="filter.email"/>
                </template>
                <!-- end filter slot -->
            </Table>

        </div>
    </AuthenticatedLayout>
</template>
