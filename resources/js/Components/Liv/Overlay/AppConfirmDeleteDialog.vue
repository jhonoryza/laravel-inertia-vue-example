<template>
    <AppModal :is-modal-open="isModalOpen" @modal:toggle="toggleModal">
        <!-- Modal header -->
        <template #header>
            <div class="flex items-center justify-between rounded-t border-b border-slate-300 p-5">
                <h3 class="text-xl font-semibold lg:text-2xl">
                    Confirmation
                </h3>
                <Button class="text-slate-500 hover:text-slate-700" @click="closeModal">
                    <IconSquareRoundedX class="size-8" />
                </Button>
            </div>
        </template>

        <!-- Modal body -->
        <template #body>
            <div class="space-y-6 p-5">
                <p class="text-base leading-relaxed">
                    Are you sure you want to proceed?
                </p>
            </div>
        </template>

        <!-- Modal footer -->
        <template #footer>
            <div class="flex items-center justify-end space-x-2 rounded-b border-t border-slate-300 p-5">
                <Button class="btn btn-neutral mr-3" @click="closeModal">
                    No
                </Button>

                <Button class="btn btn-destructive" @click="deleteItem()">
                    Yes
                </Button>
            </div>
        </template>
    </AppModal>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppModal from '@/Components/Liv/Overlay/AppModal.vue';
import Button from '@/Components/Liv/Button.vue';
import { IconSquareRoundedX, IconSquareRoundedXFilled } from '@tabler/icons-vue';

const isModalOpen = ref(false)
const deleteItemRoute = ref(null)

const toggleModal = () => {
    isModalOpen.value = !isModalOpen.value
}

const openModal = (deleteRoute) => {
    deleteItemRoute.value = deleteRoute
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
}

const deleteItem = () => {
    isModalOpen.value = false
    router.visit(deleteItemRoute.value, {
        method: 'delete'
    })
}

defineExpose({
    openModal
})
</script>
