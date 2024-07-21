<template>
  <Transition name="fade" :duration="1000" mode="in-out">
    <div
      v-if="isVisible"
      class="border border-gray-200 dark:border-gray-100 shadow-xl rounded-lg"
    >
      <fwb-toast :type="type" closable class="dark:text-white">
        {{ message }}
      </fwb-toast>
    </div>
  </Transition>
</template>

<script setup>
import { FwbToast } from "flowbite-vue";
import { onMounted, ref } from "vue";
const props = defineProps({
  message: String,
  type: String,
});
const isVisible = ref(false);

const hideMessage = () => {
  isVisible.value = false;
};

onMounted(() => {
  isVisible.value = true;
  setTimeout(() => hideMessage(), 5000);
});
</script>
