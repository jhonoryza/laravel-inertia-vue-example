<script setup>
import debounce from '@/Utils/debounce';
import {router} from '@inertiajs/vue3';
import {
    IconDotsVertical,
    IconFilterFilled,
    IconTableColumn,
    IconSearch,
    IconX
} from '@tabler/icons-vue';
import {FwbPagination} from 'flowbite-vue';
import {computed, reactive, ref, watch, watchEffect} from 'vue';
import AppConfirmDeleteDialog from './Overlay/AppConfirmDeleteDialog.vue';
import Checkbox from './Checkbox.vue';
import InputText from './InputText.vue';

const props = defineProps({
    items: {
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
    module: {
        type: String,
        default: ''
    },
    filters: {
        type: Array,
        default: () => ['search']
    }
})

// filter handler section

const openFilter = ref(false)

const filter = reactive({});

// function to initialize dynamic filter
const updateReactiveFilters = (items) => {
    Object.keys(filter).forEach(key => {
        delete filter[key];
    });
    items.forEach(item => {
        filter[item] = '';
    });
};

// init filter
updateReactiveFilters(props.filters);

props.filters.forEach(item => {
    watchEffect(
        () => {
            if (filter[item] !== '') {
                debouncedFilter()
            }
        }
    )
})

const debouncedFilter = debounce(() => {
    applyFilter()
}, 300)

const applyFilter = () => {
    let params = {
        filter: {}
    }
    for (const [key, value] of Object.entries(props.filters)) {
        if (filter[value] !== '') {
            params.filter[value] = filter[value]
        }
    }
    router.get(route(`${props.module}.index`), params, {
        preserveState: true,
    })
    selectAll.value = false
    selectedRows.value = []
}

const resetFilter = () => {
    for (const [key, value] of Object.entries(props.filters)) {
        if (value == 'search') {
            continue
        }
        filter[value] = ''
    }
    applyFilter()
}

// search handler section

const onSearchInput = () => {
    debouncedFilter()
}

const clearSearch = () => {
    filter.search = ''
    applyFilter()
}

const clearSearchAndFilter = () => {
    clearSearch()
    resetFilter()
}

const removeFilter = (key) => {
    if (key == 'search') {
        clearSearch()
    } else {
        filter[key] = ''
    }
    applyFilter()
}

const filterCount = computed(() => {
    let count = 0
    for (const [key, value] of Object.entries(props.filters)) {
        if (filter[value] != '') {
            count++
        }
    }
    return count
})

// select row handler section

const selectedRows = ref([]);
const selectAll = ref(false);

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedRows.value = props.items.data.map(item => item.id);
    } else {
        selectedRows.value = [];
    }
};

const selectAllRows = () => {
    router.reload({only: ['allIds'], preserveState: true});
};

const clearSelectedRows = () => {
    selectedRows.value = [];
    selectAll.value = false;
};

watch(() => props.allIds, (value) => {
    if (value.length > 0) {
        selectedRows.value = value
        selectAll.value = true
    }
})

// sorting handler section

const sortKey = ref('id');
const sortOrder = ref('desc');

const sortColumn = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
    }
    const sort = sortOrder.value === 'desc' ? `-${sortKey.value}` : sortKey.value
    router.get(route(`${props.module}.index`), {sort: sort}, {preserveState: true});
}

// action and confirm dialog handler section

const openAction = ref(false)

const confirmDeleteDialog = ref(null)

const confirmDelete = (deleteRoute) => {
    confirmDeleteDialog.value.openModal(deleteRoute)
}

// toggle column handler section

const columns = ref(props.columns);

const visibleColumns = computed(() => columns.value.filter(column => column.visible));

const openToggleColumn = ref(false)

const toggleColumn = (key) => {
    const column = columns.value.find(col => col.key === key);
    column.visible = !column.visible;
};

// page handler section

const currentPage = ref(props.items.current_page)
const pageOptionValue = ref(props.limit)

const changePageOptions = () => {
    router.get(route(`${props.module}.index`), {limit: pageOptionValue.value}, {preserveState: true})
}

const onPageChanged = (page) => {
    router.get(route(`${props.module}.index`), {page: page, limit: pageOptionValue.value}, {preserveState: true})
}

</script>

<template>
    <!-- table container -->
    <div class="bg-white dark:bg-gray-800 dark:text-white border border-skin-neutral-5 rounded-lg">
        <!-- search dan filter -->
        <div class="grid grid-cols-2">
            <!-- action -->
            <div class="relative flex justify-start items-center gap-2 px-3">
                <button class="flex items-center border border-skin-neutral-6 p-2 rounded-lg hover:bg-skin-neutral-2"
                        v-if="selectedRows.length" @click="openAction = !openAction">
                    <IconDotsVertical class="size-6 hover:cursor-pointer text-sm"/>
                    <span class="text-sm font-semibold">Action</span>
                </button>
                <Transition enter-active-class="transition-opacity duration-500 ease-in-out"
                            leave-active-class="transition-opacity duration-500 ease-in-out"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-if="openAction" v-click-away="() => openAction = false"
                         class="z-10 bg-white dark:bg-gray-800 dark:text-white w-1/4 absolute top-[85%] border border-skin-neutral-5 rounded-lg p-1">

                        <div class="flex flex-col text-skin-neutral-12 justify-start items-start">
                            <template v-if="$slots.bulkaction">
                                <slot name="bulkaction" :selectedRows="selectedRows" :confirmDelete="confirmDelete"
                                      :route="route" :module="props.module"></slot>
                            </template>
                        </div>
                    </div>
                </Transition>
            </div>
            <!-- end action -->

            <!-- toggle columns, search and filter -->
            <div class="relative flex justify-end items-center gap-2 px-3">
                <!-- search -->
                <div class="rounded-tl rounded-tr p-2">
                    <label for="search" class="sr-only">Search</label>
                    <div class="flex items-center align-middle relative">
                        <div class="pointer-events-none absolute flex items-center pl-3">
                            <IconSearch class="size-4"/>
                        </div>
                        <InputText
                            autocomplete="off"
                            @input="onSearchInput" v-model="filter.search" placeholder="Search"
                            name="search" class="w-min-sm py-2 pl-9" v-on:keyup.escape="clearSearch"
                        />
                        <div v-if="filter.search" class="absolute left-[90%] hover:cursor-pointer flex items-center"
                             @click="clearSearch">
                            <IconX class="size-4"/>
                        </div>
                    </div>
                </div>
                <!-- end search -->

                <!-- custom filter -->
                <div class="relative" v-if="props.filters.length > 1">
                    <IconFilterFilled
                        class="size-6 hover:cursor-pointer text-gray-600 hover:text-gray-400 dark:hover:text-gray-300 dark:text-gray-100"
                        @click="openFilter = !openFilter"/>
                    <span class="absolute -top-2 -right-2 bg-skin-neutral-5 px-1 text-xs rounded-3xl">
                        {{ filterCount }}
                    </span>
                </div>
                <Transition enter-active-class="transition-opacity duration-500 ease-in-out"
                            leave-active-class="transition-opacity duration-500 ease-in-out"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-if="openFilter" v-click-away="() => openFilter = false"
                         class="z-10 bg-white dark:bg-gray-800 dark:text-white w-1/2 absolute top-[85%] border border-skin-neutral-5 rounded-lg p-2">

                        <div class="flex flex-col flex-wrap gap-2 text-skin-neutral-12">
                            <div class="flex justify-between">
                                <span class="font-semibold">Filter</span>
                                <button type="button" class="text-red-400 font-semibold hover:text-red-500 text-sm"
                                        @click="resetFilter">
                                    Reset
                                </button>
                            </div>

                            <template v-if="$slots.filter">
                                <slot name="filter" :filter="filter"></slot>
                            </template>
                        </div>
                    </div>
                </Transition>
                <!-- end custom filter -->

                <!-- toggle columns -->
                <IconTableColumn
                    class="size-6 hover:cursor-pointer text-gray-600 hover:text-gray-400 dark:hover:text-gray-300 dark:text-gray-100"
                    @click="openToggleColumn = !openToggleColumn"/>
                <Transition enter-active-class="transition-opacity duration-500 ease-in-out"
                            leave-active-class="transition-opacity duration-500 ease-in-out"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-if="openToggleColumn" v-click-away="() => openToggleColumn = false"
                         class="z-10 bg-white dark:bg-gray-800 dark:text-white w-1/2 absolute top-[85%] border border-skin-neutral-5 rounded-lg p-4">

                        <div class="flex flex-col flex-wrap gap-4 text-skin-neutral-12 text-sm">
                            <span class="font-semibold">Toggle Column</span>
                            <div class="flex flex-col gap-2 justify-between">
                                <div v-for="column in columns" :key="column.key" class="flex gap-3 items-center">
                                    <Checkbox :id="column.key" v-model="column.visible"
                                              @change="toggleColumn(column.key)"
                                              class="text-skin-neutral-12 focus:ring-0"/>
                                    <label class="block font-medium">{{ column.label }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
                <!-- end toggle columns -->
            </div>
            <!-- end toggle columns, search and filter -->
        </div>
        <!-- end search dan filter -->

        <!-- selected rows -->
        <div
            class="flex justify-between items-center border-t bg-slate-100 dark:bg-gray-800 dark:text-white py-2 px-4 text-sm font-bold"
            v-if="selectedRows.length">
            <span>{{ selectedRows.length }} records selected</span>
            <div class="flex justify-between items-center gap-4">
                <button @click="selectAllRows">
                    <span class="text-yellow-500">Select all ({{ items.total }})</span>
                </button>
                <button @click="clearSelectedRows">
                    <span class="text-red-500">Deselect all</span>
                </button>
            </div>
        </div>
        <!-- end selected rows -->

        <!-- filter indicator to list all filtered name -->
        <div
            class="flex justify-between items-center border-t border-slate-300 dark:border-gray-300 bg-slate-100 py-2 px-4 dark:bg-gray-800 dark:text-white"
            v-if="route().params.filter">
            <div class="text-sm flex gap-2 items-center">
                <span>Filter aktif</span>
                <div class="border border-slate-300 px-1 rounded" v-for="key in Object.keys(route().params.filter)"
                     :key="key">
                    <div class="flex gap-1 justify-between items-center hover:cursor-pointer hover:text-neutral-500"
                         @click="removeFilter(key)">
                        <span>{{ key }} : {{ route().params.filter[key] }}</span>
                        <IconX class="size-4"/>
                    </div>
                </div>
            </div>
            <button>
                <IconX class="size-4" @click="clearSearchAndFilter"></IconX>
            </button>
        </div>
        <!-- end filter indicator to list all filtered name -->

        <!-- table -->
        <div class="overflow-x-auto border-t border-slate-300" v-if="items.data.length">
            <table class="w-full table-auto text-left text-sm">
                <!-- table head -->
                <thead>
                <tr>
                    <th class="bg-slate-100 dark:bg-gray-800 dark:text-white px-4 py-2">
                        <input type="checkbox" id="selectAll" v-model="selectAll" @change="toggleSelectAll"
                               class="text-skin-neutral-12 h-4 w-4 rounded focus:ring-0"/>
                    </th>
                    <th v-for="column in visibleColumns" :key="column.key"
                        class="bg-slate-100 dark:bg-gray-800 dark:text-white px-4 py-2 hover:cursor-pointer"
                        @click="column.sortable ? sortColumn(column.key) : ''">
                        {{ column.label }}
                        <span v-if="sortKey === column.key" :class="{ 'text-red-500': sortOrder === 'desc', 'text-green-500': sortOrder === 'asc' }">
                            {{ sortOrder === 'asc' ? '↑' : '↓' }}
                        </span>
                    </th>
                    <th class="bg-slate-100 dark:bg-gray-800 dark:text-white px-4 py-2">Action</th>
                </tr>
                </thead>
                <!-- end table head -->

                <!-- table body -->
                <tbody class="bg-white dark:bg-gray-800 dark:text-white">
                <tr class="border-t border-slate-300 hover:bg-skin-neutral-3" v-for="item in items.data"
                    :key="item.id">

                    <!-- checkbox -->
                    <td class="whitespace-nowrap px-4 py-2 font-medium">
                        <input type="checkbox" :id="item.id" v-model="selectedRows" :value="item.id"
                               class="text-skin-neutral-12 h-4 w-4 rounded focus:ring-0"/>
                    </td>

                    <!-- columns item -->
                    <td class="whitespace-nowrap px-4 py-2 font-medium" v-for="column in visibleColumns"
                        :key="column.key">
                        {{ item[column.key] }}
                    </td>

                    <!-- actions -->
                    <td class="whitespace-nowrap px-4 py-2 font-medium">
                        <template v-if="$slots.rowaction">
                            <slot name="rowaction" :item-id="item.id" :confirmDelete="confirmDelete" :route="route"
                                  :module="props.module"></slot>
                        </template>
                    </td>
                    <!-- end actions -->
                </tr>
                </tbody>
                <!-- end table body -->
            </table>
        </div>
        <!-- end table -->

        <!-- empty state -->
        <div v-if="!items.data.length"
             class="text-center p-4 h-72 flex flex-col justify-center items-center gap-2 border-t border-slate-300 dark:border-gray-300">
            <IconX class="size-7"></IconX>
            <span>No Data</span>
        </div>
        <!-- end empty state -->

        <!-- pagination -->
        <div
            class="border-t border-slate-300 dark:border-gray-300 flex justify-between items-center p-4 dark:text-white"
            v-if="items.data.length">
            <span class="mt-4 text-sm">Showing {{ items.from }} to {{ items.to }} of {{ items.total }} entries</span>
            <div
                class="mt-4 rounded-lg text-sm grid grid-cols-2 divide-x-4 divide-slate-400 items-center justify-evenly">
                <span class="">Showing</span>
                <select v-model="pageOptionValue" @change="changePageOptions"
                        class="border-none focus:ring-0 dark:bg-gray-800">
                    <option v-for="option in pageOptions" :key="option" :value="option">
                        <span class="text-sm">{{ option }}</span>
                    </option>
                </select>
            </div>
            <FwbPagination :total-pages="items.last_page" v-model="currentPage" :per-page="limit"
                           :total-items="items.total"
                           v-on:page-changed="onPageChanged" :enable-first-and-last-buttons="true"
                           class="text-sm mt-4 justify-center">
            </FwbPagination>
        </div>
        <!-- end pagination -->
    </div>
    <!-- end table container -->

    <!-- confirm delete dialog -->
    <AppConfirmDeleteDialog ref="confirmDeleteDialog"></AppConfirmDeleteDialog>
    <!-- end confirm delete dialog -->
</template>
