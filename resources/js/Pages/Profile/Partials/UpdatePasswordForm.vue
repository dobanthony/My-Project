<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

// Track visibility toggles
const showCurrentPassword = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const updatePassword = () => {
  form.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
    onError: () => {
      if (form.errors.password) {
        form.reset('password', 'password_confirmation');
        passwordInput.value.focus();
      }
      if (form.errors.current_password) {
        form.reset('current_password');
        currentPasswordInput.value.focus();
      }
    },
  });
};
</script>

<template>
  <section class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
      <!-- Header -->
      <header class="mb-4 text-center">
        <i class="bi bi-shield-lock-fill text-success fs-2"></i>
        <h2 class="h5 mt-2 mb-1 fw-bold">Update Password</h2>
        <p class="text-muted small">
          Ensure your account is using a strong, unique password to stay secure.
        </p>
      </header>

      <!-- Form -->
      <form @submit.prevent="updatePassword" class="needs-validation">
        <!-- Current Password -->
        <div class="mb-3 position-relative">
          <label for="current_password" class="form-label fw-semibold text-success">
            <i class="bi bi-key-fill me-1"></i> Current Password
          </label>
          <div class="input-group">
            <input
              id="current_password"
              ref="currentPasswordInput"
              v-model="form.current_password"
              :type="showCurrentPassword ? 'text' : 'password'"
              class="form-control"
              placeholder="Enter current password"
              autocomplete="current-password"
            />
            <button
              type="button"
              class="btn btn-outline-success"
              @click="showCurrentPassword = !showCurrentPassword"
              tabindex="-1"
            >
              <i :class="showCurrentPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
            </button>
          </div>
          <div v-if="form.errors.current_password" class="invalid-feedback d-block">
            <i class="bi bi-exclamation-circle me-1"></i> {{ form.errors.current_password }}
          </div>
        </div>

        <!-- New Password -->
        <div class="mb-3 position-relative">
          <label for="password" class="form-label fw-semibold text-success">
            <i class="bi bi-lock-fill me-1"></i> New Password
          </label>
          <div class="input-group">
            <input
              id="password"
              ref="passwordInput"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="form-control"
              placeholder="Enter new password"
              autocomplete="new-password"
            />
            <button
              type="button"
              class="btn btn-outline-success"
              @click="showPassword = !showPassword"
              tabindex="-1"
            >
              <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
            </button>
          </div>
          <div v-if="form.errors.password" class="invalid-feedback d-block">
            <i class="bi bi-exclamation-circle me-1"></i> {{ form.errors.password }}
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-3 position-relative">
          <label for="password_confirmation" class="form-label fw-semibold text-success">
            <i class="bi bi-check2-circle me-1"></i> Confirm Password
          </label>
          <div class="input-group">
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              :type="showConfirmPassword ? 'text' : 'password'"
              class="form-control"
              placeholder="Re-enter new password"
              autocomplete="new-password"
            />
            <button
              type="button"
              class="btn btn-outline-success"
              @click="showConfirmPassword = !showConfirmPassword"
              tabindex="-1"
            >
              <i :class="showConfirmPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
            </button>
          </div>
          <div v-if="form.errors.password_confirmation" class="invalid-feedback d-block">
            <i class="bi bi-exclamation-circle me-1"></i> {{ form.errors.password_confirmation }}
          </div>
        </div>

        <!-- Actions -->
        <div class="d-flex align-items-center gap-3">
          <button
            type="submit"
            class="btn btn-success d-flex align-items-center gap-2"
            :disabled="form.processing"
          >
            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
            <i v-else class="bi bi-save-fill"></i>
            {{ form.processing ? 'Saving...' : 'Save Changes' }}
          </button>

          <transition enter-active-class="fade show" leave-active-class="fade">
            <span v-if="form.recentlySuccessful" class="text-success small d-flex align-items-center gap-1">
              <i class="bi bi-check-circle-fill"></i> Saved successfully
            </span>
          </transition>
        </div>
      </form>
    </div>
  </section>
</template>
<style scoped>
input.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
textarea.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
</style>
