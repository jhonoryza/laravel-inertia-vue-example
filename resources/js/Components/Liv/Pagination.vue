<script setup>
import { IconChevronLeft, IconChevronRight } from "@tabler/icons-vue";

defineProps({
  nextPageUrl: {
    type: String,
  },
  prevPageUrl: {
    type: String,
  },
  links: {
    type: Object,
  },
});
</script>

<template>
  <nav aria-label="Page navigation example">
    <ul class="flex items-center -space-x-px h-8 text-sm">
      <li>
        <a
          :href="prevPageUrl"
          :class="{
            'flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white':
              prevPageUrl,
            'flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:cursor-default':
              !prevPageUrl,
          }"
        >
          <span class="sr-only">Previous</span>
          <IconChevronLeft class="size-5" />
        </a>
      </li>
      <li v-for="(link, index) in links" :key="index">
        <a
          v-if="index != 0 && index != links.length - 1"
          :href="link.url"
          :class="{
            'z-10 flex items-center justify-center px-3 h-8 leading-tight text-blue-600 border border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white':
              link.active,
            'flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white':
              !link.active,
          }"
          >{{ link.label }}</a
        >
      </li>
      <li>
        <a
          :href="nextPageUrl"
          :class="{
            'flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white':
              nextPageUrl,
            'flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:cursor-default':
              !nextPageUrl,
          }"
        >
          <span class="sr-only">Next</span>
          <IconChevronRight class="size-5" />
        </a>
      </li>
    </ul>
  </nav>
</template>
