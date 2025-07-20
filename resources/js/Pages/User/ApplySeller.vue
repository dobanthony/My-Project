<template>
  <DashboardLayout>
    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
          <div class="bg-success text-white p-2 mb-3 rounded-top-4">
            <h2 class="mb-1 text-center text-md-start"><i class="bi bi-envelope-paper me-2"></i>Apply to Become a Seller</h2>
          </div>

          <!-- 🔒 Already Applied -->
          <div v-if="user.seller_status === 'pending' || user.seller_status === 'approved'">
            <div class="bg-light border p-4 rounded mb-3">
              <p class="text-success mb-1"><strong class="text-dark">Name:</strong> {{ user.first_name }}</p>
              <p class="text-success mb-1"><strong class="text-dark">Email:</strong> {{ user.email }}</p>
              <p class="text-success"><strong class="text-dark">Application Reason:</strong><br /> {{ user.application_reason }}</p>
            </div>

            <div v-if="user.seller_status === 'pending'" class="alert alert-warning">
              <i class="bi bi-person-lock me-2"></i>Your application is currently <strong>Pending</strong>.
            </div>

            <div v-else-if="user.seller_status === 'approved'" class="alert alert-success">
              <strong><i class="bi bi-unlock me-2"></i>Your application has been approved!</strong>
              <br />
              <i class="bi bi-box-arrow-right me-2"></i>Please logout and log back in to access the seller dashboard.
              <br />
              <button class="btn btn-outline-dark mt-3 w-100 w-md-auto" @click.prevent="logout">
                Logout
              </button>
            </div>
          </div>

          <!-- ✅ Application Form -->
          <div v-else>
            <form @submit.prevent="submitApplication">
              <!-- Name (read-only) -->
              <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input
                  id="name"
                  type="text"
                  class="form-control"
                  :value="user.first_name + ' ' + user.last_name"
                  readonly
                />
              </div>

              <!-- Email (read-only) -->
              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input
                  id="email"
                  type="email"
                  class="form-control"
                  :value="user.email"
                  readonly
                />
              </div>

              <!-- Reason Textarea -->
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

              <button class="btn btn-success w-100" :disabled="form.processing">
                Submit Application
              </button>
            </form>
          </div>

          <!-- ✅ Success Modal -->
          <div
            class="modal fade"
            id="successModal"
            tabindex="-1"
            aria-labelledby="successModalLabel"
            aria-hidden="true"
          >
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="successModalLabel">✅ Application Submitted</h5>
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

const form = useForm({
  application_reason: ''
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
</script>

<style scoped>
input.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
textarea.form-control {
  border-color: #28a745; /* green */
  box-shadow: none;
}
textarea.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
</style>
