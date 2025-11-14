<template>
  <Head title="View " />
  <AdminDashboardLayout>
    <div class="container py-4 animate-fade-in">
      <!-- Header -->
      <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4"
      >
        <h4 class="fw-bold mb-3 mb-md-0 text-success d-flex align-items-center">
          <i class="bi bi-person-vcard me-2"></i>
          Seller Application Details
        </h4>

        <Link
          :href="route('admin.seller-applications.index')"
          class="btn btn-outline-secondary btn-sm shadow-sm d-flex align-items-center"
        >
          <i class="bi bi-arrow-left me-1"></i>
          Back to Applications
        </Link>
      </div>

      <!-- Card -->
      <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <!-- Header -->
        <div class="card-header bg-success text-white py-3">
          <h5 class="mb-0 d-flex align-items-center">
            <i class="bi bi-person-circle me-2"></i>
            {{ user.first_name }} {{ user.middle_name }} {{ user.last_name }}
          </h5>
        </div>

        <!-- Body -->
        <div class="card-body p-4">
          <!-- Personal Info -->
          <div class="row mb-4 g-3">
            <div class="col-md-6 col-12" v-for="info in infoFields" :key="info.label">
              <div class="info-card p-3 rounded-3 shadow-sm h-100">
                <h6 class="text-muted mb-1 d-flex align-items-center">
                  <i :class="info.icon + ' text-success me-2'"></i>
                  {{ info.label }}
                </h6>
                <div class="fw-semibold">{{ info.value || 'N/A' }}</div>
              </div>
            </div>
          </div>

          <hr />

          <!-- Uploaded ID Photos -->
          <div v-if="user.valid_id_photos && user.valid_id_photos.length > 0">
            <h6 class="fw-bold text-success mb-3 d-flex align-items-center">
              <i class="bi bi-card-image me-2"></i>
              Uploaded ID Photos
            </h6>

            <div class="row g-4">
              <div
                class="col-lg-4 col-md-6 col-sm-12"
                v-for="(photo, index) in user.valid_id_photos"
                :key="index"
              >
                <div
                  class="card border-0 shadow-sm rounded-3 overflow-hidden hover-scale"
                  @click="openModal(index)"
                  style="cursor: pointer;"
                >
                  <img
                    :src="`/storage/${photo}`"
                    class="card-img-top"
                    style="height: 220px; object-fit: cover;"
                    alt="Valid ID"
                  />
                  <div class="card-footer text-center bg-light py-2 small text-muted">
                    <i class="bi bi-eye me-1"></i> Click to view full image
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="alert alert-warning mt-4 mb-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            No ID photos uploaded.
          </div>
        </div>
      </div>

      <!-- 🖼️ Blurred Image Viewer Modal -->
      <div
        class="modal fade"
        id="imageModal"
        tabindex="-1"
        aria-hidden="true"
        ref="imageModal"
      >
        <div class="modal-dialog modal-fullscreen">
          <div class="modal-content modal-blur border-0">
            <div
              class="modal-body p-0 d-flex justify-content-center align-items-center position-relative"
            >
              <!-- Image -->
              <img
                :src="currentImage"
                class="img-fluid rounded-3 shadow-lg"
                alt="Preview"
                style="max-height: 90vh; object-fit: contain;"
              />

              <!-- Close Button -->
              <button
                type="button"
                class="btn btn-light position-absolute top-0 end-0 m-3 rounded-circle shadow"
                data-bs-dismiss="modal"
                aria-label="Close"
              >
                <i class="bi bi-x-lg"></i>
              </button>

              <!-- Navigation Arrows -->
              <button
                v-if="user.valid_id_photos.length > 1"
                class="btn btn-outline-light position-absolute start-0 top-50 translate-middle-y ms-3 rounded-circle"
                @click="prevImage"
              >
                <i class="bi bi-chevron-left fs-4"></i>
              </button>

              <button
                v-if="user.valid_id_photos.length > 1"
                class="btn btn-outline-light position-absolute end-0 top-50 translate-middle-y me-3 rounded-circle"
                @click="nextImage"
              >
                <i class="bi bi-chevron-right fs-4"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminDashboardLayout>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { Link, Head } from '@inertiajs/vue3'
import * as bootstrap from 'bootstrap'
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'

// Props
const props = defineProps(['user'])

const currentIndex = ref(0)
const currentImage = ref(null)
const imageModal = ref(null)
let modalInstance = null

// User info data
const infoFields = computed(() => [
  { label: 'Email', icon: 'bi bi-envelope-fill', value: props.user.email },
  { label: 'Phone', icon: 'bi bi-telephone-fill', value: props.user.phone },
  { label: 'Application Reason', icon: 'bi bi-chat-right-text-fill', value: props.user.application_reason },
  { label: 'Valid ID Type', icon: 'bi bi-credit-card-2-front-fill', value: props.user.valid_id_type },
])

function openModal(index) {
  currentIndex.value = index
  currentImage.value = `/storage/${props.user.valid_id_photos[index]}`
  modalInstance = new bootstrap.Modal(imageModal.value)
  modalInstance.show()
}

function nextImage() {
  currentIndex.value = (currentIndex.value + 1) % props.user.valid_id_photos.length
  currentImage.value = `/storage/${props.user.valid_id_photos[currentIndex.value]}`
}

function prevImage() {
  currentIndex.value =
    (currentIndex.value - 1 + props.user.valid_id_photos.length) %
    props.user.valid_id_photos.length
  currentImage.value = `/storage/${props.user.valid_id_photos[currentIndex.value]}`
}

// Keyboard navigation
function handleKeydown(e) {
  if (!modalInstance || !imageModal.value.classList.contains('show')) return
  if (e.key === 'ArrowRight') nextImage()
  else if (e.key === 'ArrowLeft') prevImage()
}
onMounted(() => document.addEventListener('keydown', handleKeydown))
onBeforeUnmount(() => document.removeEventListener('keydown', handleKeydown))
</script>

<style scoped>
/* ✨ Smooth fade-in */
.animate-fade-in {
  animation: fadeIn 0.6s ease-in-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* 🖼️ Hover scale */
.hover-scale {
  transition: all 0.3s ease;
}
.hover-scale:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

/* 🪪 Info card */
.info-card {
  background-color: #f9f9f9;
  border-left: 4px solid #198754;
}

/* 📱 Responsive tweaks */
@media (max-width: 767px) {
  .card-body {
    padding: 1.5rem !important;
  }
  .info-card {
    font-size: 0.9rem;
  }
}

/* 🪩 Blurred Modal Background */
.modal-blur {
  background-color: rgba(255, 255, 255, 0.1) !important;
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
}
.modal-body img {
  transition: transform 0.3s ease;
}
.modal-body img:hover {
  transform: scale(1.03);
}

/* Buttons */
button.rounded-circle {
  width: 45px;
  height: 45px;
}
</style>
