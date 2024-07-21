<script setup>
import Container from "@/Components/Liv/Container.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <Container>
      <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <section class="max-w-xl">
          <h2>Dashboard</h2>
        </section>
      </div>
    </Container>
  </AuthenticatedLayout>
</template>
