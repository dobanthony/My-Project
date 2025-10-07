<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
})

const user = usePage().props.auth.user

const form = useForm({
  first_name: user.first_name || '',
  middle_name: user.middle_name || '',
  last_name: user.last_name || '',
  email: user.email || '',
  phone: user.phone || '',
  address: user.address || '',
  dob: user.dob || '',
})

// ✅ Bootstrap toast state
const showToast = ref(false)
watch(
  () => form.recentlySuccessful,
  (val) => {
    if (val) {
      showToast.value = true
      setTimeout(() => (showToast.value = false), 3000)
    }
  }
)
</script>

<template>
  <section class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
      <!-- Header -->
      <header class="mb-4 text-center">
        <i class="bi bi-person-circle text-secondary fs-2"></i>
        <h2 class="h5 mt-2 mb-1 fw-bold">Profile Information</h2>
        <p class="text-muted small">
          Update your account's profile information and email address.
        </p>
      </header>

      <form @submit.prevent="form.patch(route('profile.update'))" class="row g-3">
        <!-- First Name -->
        <div class="col-md-4">
          <label for="first_name" class="form-label fw-semibold text-secondary">
            <i class="bi bi-person me-1 text-black"></i> First Name
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person text-black"></i></span>
            <input
              id="first_name"
              v-model="form.first_name"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': form.errors.first_name }"
              required
            />
          </div>
          <div class="invalid-feedback">{{ form.errors.first_name }}</div>
        </div>

        <!-- Middle Name -->
        <div class="col-md-4">
          <label for="middle_name" class="form-label fw-semibold text-secondary">
            <i class="bi bi-person-lines-fill me-1"></i> Middle Name (Optional)
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person-lines-fill text-black"></i></span>
            <input
              id="middle_name"
              v-model="form.middle_name"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': form.errors.middle_name }"
            />
          </div>
          <div class="invalid-feedback">{{ form.errors.middle_name }}</div>
        </div>

        <!-- Last Name -->
        <div class="col-md-4">
          <label for="last_name" class="form-label fw-semibold text-secondary">
            <i class="bi bi-person-badge me-1"></i> Last Name
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person-badge text-black"></i></span>
            <input
              id="last_name"
              v-model="form.last_name"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': form.errors.last_name }"
              required
            />
          </div>
          <div class="invalid-feedback">{{ form.errors.last_name }}</div>
        </div>

        <!-- Email -->
        <div class="col-md-6">
          <label for="email" class="form-label fw-semibold text-secondary">
            <i class="bi bi-envelope-at me-1"></i> Email
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope-at text-black"></i></span>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="form-control"
              :class="{ 'is-invalid': form.errors.email }"
              required
            />
          </div>
          <div class="invalid-feedback">{{ form.errors.email }}</div>
        </div>

        <!-- Phone -->
        <div class="col-md-6">
          <label for="phone" class="form-label fw-semibold text-secondary">
            <i class="bi bi-telephone me-1"></i> Phone Number
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-telephone text-black"></i></span>
            <input
              id="phone"
              v-model="form.phone"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': form.errors.phone }"
            />
          </div>
          <div class="invalid-feedback">{{ form.errors.phone }}</div>
        </div>

        <!-- Address -->
        <div class="col-12">
          <label for="address" class="form-label fw-semibold text-secondary">
            <i class="bi bi-geo-alt me-1"></i> Address
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-geo-alt text-black"></i></span>
            <input
              id="address"
              v-model="form.address"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': form.errors.address }"
            />
          </div>
          <div class="invalid-feedback">{{ form.errors.address }}</div>
        </div>

        <!-- Date of Birth -->
        <div class="col-md-6">
          <label for="dob" class="form-label fw-semibold text-secondary">
            <i class="bi bi-calendar-date me-1"></i> Date of Birth
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-calendar-date text-black"></i></span>
            <input
              id="dob"
              v-model="form.dob"
              type="date"
              class="form-control"
              :class="{ 'is-invalid': form.errors.dob }"
            />
          </div>
          <div class="invalid-feedback">{{ form.errors.dob }}</div>
        </div>

        <!-- Email Verification -->
        <div v-if="mustVerifyEmail && user.email_verified_at === null" class="col-12">
          <div class="alert alert-warning small d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
              Your email address is unverified.
              <Link
                :href="route('verification.send')"
                method="post"
                as="button"
                class="btn btn-link btn-sm p-0 align-baseline"
              >
                Click here to re-send the verification email.
              </Link>
            </div>
          </div>
          <div v-show="status === 'verification-link-sent'" class="alert alert-success small mt-2 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            <span>A new verification link has been sent to your email address.</span>
          </div>
        </div>

        <!-- Save Button -->
        <div class="col-12 d-flex align-items-center gap-3 mt-3">
          <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" :disabled="form.processing">
            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
            <i v-else class="bi bi-save-fill"></i>
            {{ form.processing ? 'Saving...' : 'Save Changes' }}
          </button>
          <span v-if="form.recentlySuccessful" class="text-success small d-flex align-items-center gap-1">
            <i class="bi bi-check-circle-fill"></i> Saved successfully
          </span>
        </div>
      </form>
    </div>

    <!-- ✅ Bootstrap Toast -->
    <div
      v-if="showToast"
      class="toast align-items-center text-white bg-success border-0 show position-fixed top-0 start-50 translate-middle-x mt-3 shadow"
      role="alert"
      aria-live="assertive"
      aria-atomic="true"
      style="z-index: 1080; min-width: 300px;"
    >
      <div class="d-flex">
        <div class="toast-body d-flex align-items-center gap-2">
          <i class="bi bi-check-circle-fill"></i> Profile updated successfully!
        </div>
      </div>
    </div>
  </section>
</template>
<!-- <style scoped>
input.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
textarea.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
</style> -->
