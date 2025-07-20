<template>
  <div>
    <!-- Modal Here -->
    <FlashModal />

    <!-- Your existing layout -->
    <header><!-- navbar --></header>
    <main class="p-4">
      <slot />
    </main>
  </div>
</template>

<script setup>
import FlashModal from './FlashModal.vue';
</script>
