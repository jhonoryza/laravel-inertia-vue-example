import { computed, ref } from "vue";

// toggle column handler section
export function useTableToggleColumn(props) {
  const columns = ref(props.columns);
  const openToggleColumn = ref(false);

  const visibleColumns = computed(() =>
    columns.value.filter((column) => column.visible),
  );

  const toggleColumn = (key) => {
    const column = columns.value.find((col) => col.key === key);
    column.visible = !column.visible;
  };

  return {
    columns,
    visibleColumns,
    openToggleColumn,
    toggleColumn,
  };
}
