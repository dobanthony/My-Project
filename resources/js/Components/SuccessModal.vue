<template>
  <div v-if="visible" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.3)">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-success">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Success</h5>
        </div>
        <div class="modal-body">
          <p class="mb-0">{{ message }}</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success" @click="visible = false">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const { props } = usePage();
const visible = ref(false);
const message = ref('');

onMounted(() => {
  if (props.flash?.success) {
    message.value = props.flash.success;
    visible.value = true;

    // Optional: Auto-close after 3 seconds
    setTimeout(() => {
      visible.value = false;
    }, 3000);
  }
});
</script>
