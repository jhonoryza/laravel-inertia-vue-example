import debounce from "@/Utils/debounce";
import { computed, reactive, ref, watchEffect } from "vue";
import { router } from "@inertiajs/vue3";

// filter handler section
export function useTableFilter(props, selectedRows, selectAll) {
  const openFilter = ref(false);
  const filter = reactive({});

  // function to initialize dynamic filter
  const updateReactiveFilters = (items) => {
    Object.keys(filter).forEach((key) => {
      delete filter[key];
    });
    items.forEach((item) => {
      filter[item] = "";
    });
  };

  // init filter
  updateReactiveFilters(props.filters);

  props.filters.forEach((item) => {
    watchEffect(() => {
      if (filter[item] !== "") {
        debouncedFilter();
      }
    });
  });

  const debouncedFilter = debounce(() => {
    applyFilter();
  }, 300);

  const applyFilter = () => {
    let params = {
      filter: {},
    };
    for (const [key, value] of Object.entries(props.filters)) {
      if (filter[value] !== "") {
        params.filter[value] = filter[value];
      }
    }
    router.get(route(`${props.module}.index`), params, {
      preserveState: true,
    });
    selectAll.value = false;
    selectedRows.value = [];
  };

  const resetFilter = () => {
    for (const [key, value] of Object.entries(props.filters)) {
      if (value == "search") {
        continue;
      }
      filter[value] = "";
    }
    applyFilter();
  };

  // search handler section

  const onSearchInput = () => {
    debouncedFilter();
  };

  const clearSearch = () => {
    filter.search = "";
    applyFilter();
  };

  const clearSearchAndFilter = () => {
    clearSearch();
    resetFilter();
  };

  const removeFilter = (key) => {
    if (key == "search") {
      clearSearch();
    } else {
      filter[key] = "";
    }
    applyFilter();
  };

  const filterCount = computed(() => {
    let count = 0;
    for (const [key, value] of Object.entries(props.filters)) {
      if (filter[value] != "") {
        count++;
      }
    }
    return count;
  });

  return {
    filterCount,
    openFilter,
    filter,
    resetFilter,
    onSearchInput,
    clearSearch,
    clearSearchAndFilter,
    removeFilter,
  };
}
