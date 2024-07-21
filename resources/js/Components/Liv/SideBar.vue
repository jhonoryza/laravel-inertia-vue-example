<script setup>
import { ref } from "vue";
import { IconHome, IconSettings, IconUsers } from "@tabler/icons-vue";
import SideBarMenuItem from "./SideBarMenuItem.vue";
import ResponsiveNavLink from "../ResponsiveNavLink.vue";

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
      <div
        class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600 xl:hidden"
      >
        <div class="px-4">
          <div class="font-medium text-base text-gray-800 dark:text-gray-200">
            {{ $page.props.auth.user.name }}
          </div>
          <div class="font-medium text-sm text-gray-500">
            {{ $page.props.auth.user.email }}
          </div>
        </div>

        <div class="flex flex-col mt-3 space-y-1">
          <ResponsiveNavLink :href="route('profile.edit')">
            Profile
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('logout')" method="post" as="button">
            Log Out
          </ResponsiveNavLink>
        </div>
      </div>
    </div>
  </aside>
</template>
