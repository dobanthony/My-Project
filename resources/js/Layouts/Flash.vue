<script setup>
</script>

<template>
  <div>
    <!-- Global Flash -->
    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show m-3" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Slot for all page content -->
    <slot />
  </div>
</template>
