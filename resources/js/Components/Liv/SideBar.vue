<script setup>
import { ref } from "vue";
import { IconHome, IconSettings, IconUsers } from "@tabler/icons-vue";
import SideBarMenuItem from "./SideBarMenuItem.vue";

/**
 * id harus unique
 * semua attribut wajib diisi
 */
const menuItems = ref([
  {
    id: 1,
    title: "Dashboard",
    routeName: "dashboard",
    component: "Dashboard",
    icon: IconHome,
  },
  {
    id: 2,
    title: "Core",
    children: [
      {
        id: 21,
        title: "User",
        routeName: "users.index",
        component: "User/Index",
        icon: IconUsers,
      },
      {
        id: 22,
        title: "Setting",
        routeName: "settings.index",
        component: "Setting/Index",
        icon: IconSettings,
      },
    ],
  },
]);
</script>

<template>
  <aside
    class="absolute top-0 left-0 z-40 w-64 h-screen transition-transform"
    aria-label="Sidebar"
  >
    <div
      class="h-full px-3 overflow-y-auto bg-white dark:bg-gray-800 text-sm dark:text-white"
    >
      <ul class="font-medium flex flex-col">
        <li v-for="item in menuItems" :key="item.id">
          <SideBarMenuItem :item="item" />
        </li>
      </ul>
    </div>
  </aside>
</template>
