<template>
  <button @click="logout" class="btn btn-danger mt-4">Logout</button>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

function logout() {
  router.post('/logout')
}
</script>
