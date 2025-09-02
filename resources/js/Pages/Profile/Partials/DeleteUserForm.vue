<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
  password: '',
});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;
  setTimeout(() => passwordInput.value?.focus(), 300);
};

const deleteUser = () => {
  form.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value?.focus(),
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  confirmingUserDeletion.value = false;
  form.clearErrors();
  form.reset();
};
</script>

<template>
  <section>
    <header class="mb-3">
      <h2 class="h5 fw-bold text-danger d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Delete Account
      </h2>
      <p class="text-muted">
        Once your account is deleted, all of its resources and data will be permanently deleted.
        Please download any data or information that you wish to retain before proceeding.
      </p>
    </header>

    <!-- ✅ Trigger Button -->
    <button class="btn btn-outline-danger d-flex align-items-center" @click="confirmUserDeletion">
      <i class="bi bi-trash me-2"></i>
      Delete Account
    </button>

    <!-- ✅ Bootstrap Modal -->
    <div
      class="modal fade"
      tabindex="-1"
      :class="{ show: confirmingUserDeletion }"
      style="display: block"
      v-if="confirmingUserDeletion"
      aria-modal="true"
      role="dialog"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
          <!-- Modal Header -->
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title d-flex align-items-center">
              <i class="bi bi-exclamation-octagon-fill me-2"></i>
              Confirm Account Deletion
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
          </div>

          <!-- Modal Body -->
          <div class="modal-body">
            <p class="fw-semibold text-dark">
              Are you absolutely sure you want to <span class="text-danger">delete your account</span>?
            </p>
            <p class="text-muted small">
              Once deleted, all your resources and data will be <strong>permanently removed</strong>.
              Please enter your password to confirm.
            </p>

            <!-- Password Input -->
            <div class="mb-3">
              <label for="password" class="form-label d-flex align-items-center">
                <i class="bi bi-lock-fill me-2"></i> Password
              </label>
              <input
                id="password"
                ref="passwordInput"
                v-model="form.password"
                type="password"
                class="form-control border-danger"
                placeholder="Enter your password"
                @keyup.enter="deleteUser"
              />
              <div v-if="form.errors.password" class="text-danger mt-1 small">
                <i class="bi bi-exclamation-circle me-1"></i> {{ form.errors.password }}
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">
              <i class="bi bi-x-circle me-1"></i> Cancel
            </button>
            <button
              type="button"
              class="btn btn-danger d-flex align-items-center"
              :disabled="form.processing"
              @click="deleteUser"
            >
              <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
              <i class="bi bi-trash me-1"></i> Delete Account
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ✅ Modal backdrop -->
    <div
      v-if="confirmingUserDeletion"
      class="modal-backdrop fade show"
      @click="closeModal"
    ></div>
  </section>
</template>
