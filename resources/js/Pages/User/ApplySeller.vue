<template>
  <DashboardLayout>
    <div class="container py-3">
      <!-- Header -->
      <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-10 col-lg-8">
          <div class="card text-center shadow-sm border-0">
            <div class="card-body text-success">
              <h2 class="mb-1">
                <i class="bi bi-envelope-paper me-2 text-secondary"></i>Apply to Become a Seller
              </h2>
              <p class="mb-0 text-secondary">
                Fill out the form below to start selling on our platform.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Layout -->
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
          <!-- Already Applied -->
          <div
            v-if="
              user.seller_status === 'pending' ||
              user.seller_status === 'approved' ||
              user.seller_status === 'declined'
            "
            class="row g-3 mb-3"
          >
            <!-- User Info -->
            <div class="col-12 col-md-6">
              <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                  <p class="text-start">
                    <strong>Name:</strong> {{ user.first_name }} {{ user.last_name }}
                  </p>
                  <p class="text-start"><strong>Email:</strong> {{ user.email }}</p>
                  <p class="text-start"><strong>Phone:</strong> {{ user.phone || 'N/A' }}</p>
                  <p class="text-start"><strong>Address:</strong> {{ user.address || 'N/A' }}</p>
                  <p class="text-start"><strong>Date of Birth:</strong> {{ user.dob || 'N/A' }}</p>
                  <p class="text-start">
                    <strong>Application Reason:</strong><br />{{ user.application_reason }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Status Card -->
            <div class="col-12 col-md-6">
              <div
                v-if="user.seller_status === 'pending'"
                class="alert alert-warning d-flex align-items-center h-100"
              >
                <i class="bi bi-person-lock me-2 fs-4"></i>
                <div>Your application is currently <strong>Pending</strong>.</div>
              </div>

              <div
                v-else-if="user.seller_status === 'approved'"
                class="alert alert-success d-flex flex-column h-100 justify-content-center"
              >
                <div>
                  <i class="bi bi-unlock me-2 fs-4"></i>
                  <strong>Your application has been approved!</strong>
                </div>
                <div class="mt-2">
                  <i class="bi bi-box-arrow-right me-2"></i>Please logout and log back in to
                  access the seller dashboard.
                </div>
                <button
                  class="btn btn-outline-dark mt-3 w-100 w-md-auto"
                  @click.prevent="logout"
                >
                  Logout
                </button>
              </div>

              <div
                v-else-if="user.seller_status === 'declined'"
                class="alert alert-danger d-flex flex-column h-100 justify-content-center"
              >
                <div>
                  <i class="bi bi-x-circle me-2 fs-4"></i>
                  <strong>Your application has been declined.</strong>
                </div>
                <div class="mt-2">
                  <i class="bi bi-arrow-repeat me-2"></i>You can try again by submitting a new
                  application.
                </div>
                <button class="btn btn-danger mt-3 w-100 w-md-auto" @click="tryAgain">
                  Try Again
                </button>
              </div>
            </div>
          </div>

          <!-- Application Form -->
          <div v-else class="row g-3">
            <div class="col-12">
              <div class="card shadow-sm">
                <div class="card-body">
                  <form @submit.prevent="submitApplication">
                    <!-- Basic Info -->
                    <div class="row g-3 mb-3">
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
                      <div class="col-12 col-md-6">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input
                          id="phone"
                          type="text"
                          class="form-control"
                          :value="user.phone || 'N/A'"
                          readonly
                        />
                      </div>

                      <div class="col-12 col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <input
                          id="address"
                          type="text"
                          class="form-control"
                          :value="user.address || 'N/A'"
                          readonly
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
                        :value="user.dob || 'N/A'"
                        readonly
                      />
                    </div>

                    <!-- Application Reason -->
                    <div class="mb-3">
                      <label for="reason" class="form-label"
                        >Why do you want to become a seller?</label
                      >

                      <select
                        id="reason"
                        v-model="selectedReason"
                        class="form-select mb-3"
                        required
                      >
                        <option value="">Select a reason</option>
                        <option value="I want to sell my own products">
                          I want to sell my own products
                        </option>
                        <option value="I run a small business">I run a small business</option>
                        <option value="I want to expand my reach online">
                          I want to expand my reach online
                        </option>
                        <option value="I have handmade or unique items">
                          I have handmade or unique items
                        </option>
                        <option value="Other">Other (please specify)</option>
                      </select>

                      <transition name="fade">
                        <div v-if="selectedReason === 'Other'">
                          <textarea
                            v-model="customReason"
                            class="form-control"
                            rows="4"
                            placeholder="Please specify your reason..."
                            required
                          ></textarea>
                        </div>
                      </transition>
                    </div>

                    <!-- Valid ID Type -->
                    <div class="mb-3">
                      <label for="valid_id_type" class="form-label">Select a Valid ID Type</label>
                      <select
                        id="valid_id_type"
                        v-model="form.valid_id_type"
                        class="form-select"
                        required
                      >
                        <option value="">Choose valid ID</option>
                        <option value="Philippine Passport">Philippine Passport</option>
                        <option value="Driver’s License">Driver’s License</option>
                        <option value="UMID">UMID</option>
                        <option value="SSS ID">SSS ID</option>
                        <option value="GSIS ID">GSIS ID</option>
                        <option value="PRC ID">PRC ID</option>
                        <option value="PhilHealth ID">PhilHealth ID</option>
                        <option value="TIN ID">TIN ID</option>
                        <option value="Postal ID">Postal ID</option>
                        <option value="Voter’s ID">Voter’s ID</option>
                        <option value="National ID">National ID</option>
                        <option value="Student ID">Student ID</option>
                      </select>
                    </div>

                    <!-- Upload ID Photos -->
                    <div class="mb-3">
                      <label for="valid_id_photos" class="form-label"
                        >Upload Photo Proof(s) of Valid ID</label
                      >
                      <input
                        id="valid_id_photos"
                        type="file"
                        class="form-control"
                        accept="image/*"
                        multiple
                        @change="handleMultipleFileUpload"
                        required
                      />
                      <small class="text-muted">
                        Upload clear images (max 2MB each). You can upload multiple photos (front &
                        back).
                      </small>

                      <!-- Preview -->
                      <div
                        v-if="filePreviews.length"
                        class="mt-3 d-flex flex-wrap gap-3 justify-content-start"
                      >
                        <div
                          v-for="(src, i) in filePreviews"
                          :key="i"
                          class="border rounded bg-light p-2"
                          style="width: 120px; text-align: center"
                        >
                          <img
                            :src="src"
                            alt="ID Preview"
                            class="img-fluid rounded"
                            style="height: 100px; object-fit: cover"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Submit -->
                    <button
                      class="btn btn-success w-100 d-flex align-items-center justify-content-center"
                      :disabled="form.processing"
                    >
                      <span
                        v-if="form.processing"
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                        aria-hidden="true"
                      ></span>
                      <i class="bi bi-send me-2"></i>
                      {{ form.processing ? "Submitting..." : "Submit Application" }}
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
                  ></button>
                </div>
                <div class="modal-body">
                  <p>Your application has been submitted successfully.</p>
                </div>
                <div class="modal-footer">
                  <button
                    class="btn btn-success w-100"
                    data-bs-dismiss="modal"
                    @click="refreshPage"
                  >
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
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";

const user = usePage().props.user;

// State
const selectedReason = ref("");
const customReason = ref("");
const filePreviews = ref([]);

// Form setup
const form = useForm({
  application_reason: "",
  valid_id_type: "",
  valid_id_photos: [],
  phone: user.phone || "",
  address: user.address || "",
  dob: user.dob || "",
});

// Watch reason
watch([selectedReason, customReason], () => {
  if (selectedReason.value === "Other") {
    form.application_reason = customReason.value;
  } else {
    form.application_reason = selectedReason.value;
  }
});

// Handle multiple uploads
const handleMultipleFileUpload = (e) => {
  const files = Array.from(e.target.files);
  const validFiles = [];
  filePreviews.value = [];

  files.forEach((file) => {
    if (file.size <= 2 * 1024 * 1024) {
      validFiles.push(file);

      const reader = new FileReader();
      reader.onload = (event) => {
        filePreviews.value.push(event.target.result);
      };
      reader.readAsDataURL(file);
    } else {
      alert(`${file.name} is too large (max 2MB).`);
    }
  });

  form.valid_id_photos = validFiles;
};

// Bootstrap Modal
let bootstrapModal = null;
onMounted(() => {
  const modalElement = document.getElementById("successModal");
  bootstrapModal = new bootstrap.Modal(modalElement);
});

// Submit form
function submitApplication() {
  form.post("/apply-seller", {
    forceFormData: true,
    onSuccess: () => {
      bootstrapModal.show();
      form.reset();
      selectedReason.value = "";
      customReason.value = "";
      filePreviews.value = [];
    },
  });
}

function refreshPage() {
  window.location.reload();
}
function logout() {
  router.post("/logout");
}
function tryAgain() {
  user.seller_status = null;
}
</script>

<style scoped>
input.form-control:focus,
textarea.form-control:focus,
select.form-select:focus {
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
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
