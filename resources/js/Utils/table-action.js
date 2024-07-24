import { ref } from "vue";

// action and confirm dialog handler section
export function useTableAction() {
  const openAction = ref(false);
  const confirmDeleteDialog = ref(null);

  const confirmDelete = (deleteRoute, message) => {
    confirmDeleteDialog.value.openModal(deleteRoute, message);
  };
  return {
    openAction,
    confirmDeleteDialog,
    confirmDelete,
  };
}
