<template>
  <DashboardLayout>
    <div class="container py-3">
      <!-- Header -->
      <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-10 col-lg-8">
          <div class="card text-center shadow-sm border-0">
            <div class="card-body text-success">
              <h2 class="mb-1">
                <i class="bi bi-envelope-paper me-2"></i>Apply to Become a Seller
              </h2>
              <p class="mb-0">Fill out the form below to start selling on our platform.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Grid Layout -->
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">

          <!-- Already Applied (Pending / Approved / Declined) -->
          <div v-if="user.seller_status === 'pending' || user.seller_status === 'approved' || user.seller_status === 'declined'" class="row g-3 mb-3">
            <!-- User Info Card -->
            <div class="col-12 col-md-6">
              <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                  <p class="text-start"><strong>Name:</strong> {{ user.first_name }} {{ user.last_name }}</p>
                  <p class="text-start"><strong>Email:</strong> {{ user.email }}</p>
                  <p class="text-start"><strong>Phone:</strong> {{ user.phone || 'N/A' }}</p>
                  <p class="text-start"><strong>Address:</strong> {{ user.address || 'N/A' }}</p>
                  <p class="text-start"><strong>Date of Birth:</strong> {{ user.dob || 'N/A' }}</p>
                  <p class="text-start"><strong>Application Reason:</strong><br /> {{ user.application_reason }}</p>
                </div>
              </div>
            </div>

            <!-- Status Alert Card -->
            <div class="col-12 col-md-6">
              <!-- Pending -->
              <div v-if="user.seller_status === 'pending'" class="alert alert-warning d-flex align-items-center h-100">
                <i class="bi bi-person-lock me-2 fs-4"></i>
                <div>Your application is currently <strong>Pending</strong>.</div>
              </div>

              <!-- Approved -->
              <div v-else-if="user.seller_status === 'approved'" class="alert alert-success d-flex flex-column h-100 justify-content-center">
                <div>
                  <i class="bi bi-unlock me-2 fs-4"></i>
                  <strong>Your application has been approved!</strong>
                </div>
                <div class="mt-2">
                  <i class="bi bi-box-arrow-right me-2"></i>Please logout and log back in to access the seller dashboard.
                </div>
                <button class="btn btn-outline-dark mt-3 w-100 w-md-auto" @click.prevent="logout">
                  Logout
                </button>
              </div>

              <!-- Declined -->
              <div v-else-if="user.seller_status === 'declined'" class="alert alert-danger d-flex flex-column h-100 justify-content-center">
                <div>
                  <i class="bi bi-x-circle me-2 fs-4"></i>
                  <strong>Your application has been declined.</strong>
                </div>
                <div class="mt-2">
                  <i class="bi bi-arrow-repeat me-2"></i>You can try again by submitting a new application.
                </div>
                <button class="btn btn-danger mt-3 w-100 w-md-auto" @click="tryAgain">
                  Try Again
                </button>
              </div>
            </div>
          </div>

          <!-- Application Form (only shows if not pending/approved and not declined yet or after retry) -->
          <div v-else class="row g-3">
            <div class="col-12">
              <div class="card shadow-sm">
                <div class="card-body">
                  <form @submit.prevent="submitApplication">
                    <div class="row g-3 mb-3">
                      <!-- Full Name -->
                      <div class="col-12 col-md-6">
                        <label for="name" class="form-label">Full Name</label>
                        <input
                          id="name"
                          type="text"
                          class="form-control"
                          :value="user.first_name + ' ' + user.last_name"
                          readonly
                        />
                      </div>

                      <!-- Email -->
                      <div class="col-12 col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input
                          id="email"
                          type="email"
                          class="form-control"
                          :value="user.email"
                          readonly
                        />
                      </div>
                    </div>

                    <div class="row g-3 mb-3">
                      <!-- Phone -->
                      <div class="col-12 col-md-6">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input
                          id="phone"
                          type="text"
                          class="form-control"
                          v-model="form.phone"
                          required
                        />
                      </div>

                      <!-- Address -->
                      <div class="col-12 col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <input
                          id="address"
                          type="text"
                          class="form-control"
                          v-model="form.address"
                          required
                        />
                      </div>
                    </div>

                    <!-- DOB -->
                    <div class="mb-3">
                      <label for="dob" class="form-label">Date of Birth</label>
                      <input
                        id="dob"
                        type="date"
                        class="form-control"
                        v-model="form.dob"
                        required
                      />
                    </div>

                    <!-- Application Reason -->
                    <div class="mb-3">
                      <label for="reason" class="form-label">Why do you want to become a seller?</label>
                      <textarea
                        id="reason"
                        v-model="form.application_reason"
                        class="form-control"
                        rows="5"
                        required
                      />
                    </div>

                    <!-- Submit Button -->
                    <button class="btn btn-success w-100 d-flex align-items-center justify-content-center" :disabled="form.processing">
                      <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                      <i class="bi bi-send me-2"></i>
                      {{ form.processing ? 'Submitting...' : 'Submit Application' }}
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Success Modal -->
          <div
            class="modal fade"
            id="successModal"
            tabindex="-1"
            aria-labelledby="successModalLabel"
            aria-hidden="true"
          >
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content shadow-lg">
                <div class="modal-header bg-success text-white">
                  <h5 class="modal-title" id="successModalLabel">
                    <i class="bi bi-check-circle me-2"></i>Application Submitted
                  </h5>
                  <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                  />
                </div>
                <div class="modal-body">
                  <p>Your application has been submitted successfully.</p>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-success w-100" data-bs-dismiss="modal" @click="refreshPage">
                    OK
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import { onMounted } from 'vue'

const user = usePage().props.user

// Pre-fill with user data
const form = useForm({
  application_reason: '',
  phone: user.phone || '',
  address: user.address || '',
  dob: user.dob || ''
})

let bootstrapModal = null

onMounted(() => {
  const modalElement = document.getElementById('successModal')
  bootstrapModal = new bootstrap.Modal(modalElement)
})

function submitApplication() {
  form.post('/apply-seller', {
    onSuccess: () => {
      bootstrapModal.show()
      form.reset()
    }
  })
}

function refreshPage() {
  window.location.reload()
}

function logout() {
  router.post('/logout')
}

function tryAgain() {
  // Reset seller status so form shows again
  user.seller_status = null
}
</script>

<style scoped>
input.form-control:focus, textarea.form-control:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
}
textarea.form-control {
  border-color: #28a745;
}
.btn:hover {
  transform: translateY(-1px);
  transition: 0.2s;
}
.alert i {
  font-size: 1.5rem;
}
.card-body p {
  margin-bottom: 0.5rem;
}
</style>
