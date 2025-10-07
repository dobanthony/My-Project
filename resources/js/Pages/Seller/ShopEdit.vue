<template>
  <SellerDashboardLayout>
    <!-- Toast Notification -->
    <div
      v-if="showToast"
      class="toast-container position-fixed start-50 translate-middle-x p-3"
      style="top: 1.5rem; z-index: 9999;"
    >
      <div
        class="toast show align-items-center text-white bg-success border-0 shadow"
        role="alert"
      >
        <div class="d-flex">
          <div class="toast-body">
            <i class="bi bi-check-circle-fill me-2"></i> {{ toastMessage }}
          </div>
          <button
            type="button"
            class="btn-close btn-close-white me-2 m-auto"
            aria-label="Close"
            @click="showToast = false"
          ></button>
        </div>
      </div>
    </div>

    <!-- ✅ Shop Edit Main Content -->
    <div class="container">
      <!-- Header -->
      <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-6 gap-3"
      >
        <div>
          <h2 class="mb-1">
            <i class="bi bi-shop-window me-2 text-secondary"></i>
            Hello, {{ user.first_name }} {{ user.last_name }}
          </h2>
          <p class="text-muted mb-0">
            Manage your shop details. Buyers will see this on your storefront.
          </p>
        </div>
      </div>

      <div class="row gy-4">
        <!-- Top: Existing shop preview -->
        <div class="col-12">
          <div class="card shadow-lg rounded-4 p-4 h-100 text-center">
            <!-- Avatar -->
            <div class="mb-4">
              <div
                class="avatar rounded-circle bg-light border mx-auto d-flex align-items-center justify-content-center"
                style="width: 150px; height: 150px; overflow: hidden;"
              >
                <template v-if="shop && shop.shop_logo">
                  <img
                    :src="`/storage/${shop.shop_logo}`"
                    alt="Logo"
                    class="rounded-circle"
                    style="width: 150px; height: 150px; object-fit: cover;"
                  />
                </template>
                <template v-else>
                  <i class="bi bi-shop fs-1 text-secondary"></i>
                </template>
              </div>
            </div>

            <!-- Shop Name & Description -->
            <h4 class="mb-2 text-secondary">
              {{ shop?.shop_name ?? 'No Shop Yet' }}
            </h4>
            <p class="text-muted fs-6">
              {{ shop?.shop_description ?? 'Describe your shop to attract buyers.' }}
            </p>

            <!-- Shop Info -->
            <div v-if="shop" class="mt-4">
              <div class="row text-start g-3 fs-6 fw-medium">
                <div class="col-12 col-md-6">
                  <i class="bi bi-telephone me-2 text-secondary"></i>
                  {{ shop.phone_number ?? '-' }}
                </div>
                <div class="col-12 col-md-6">
                  <i class="bi bi-envelope me-2 text-secondary"></i>
                  {{ shop.email_address ?? '-' }}
                </div>
                <div class="col-12 col-md-6">
                  <i class="bi bi-geo-alt-fill me-2 text-secondary"></i>
                  {{ shop.address ?? '-' }}
                </div>
                <div class="col-12 col-md-6">
                  <i class="bi bi-calendar-event me-2 text-secondary"></i>
                  {{ new Date().toLocaleDateString() }}
                </div>
              </div>
            </div>

            <div v-else class="alert alert-warning py-2 mt-3">
              You haven't created a shop yet. Fill the form to get started.
            </div>
          </div>
        </div>

        <!-- Bottom: Form -->
        <div class="col-12">
          <div class="card shadow-lg rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="text-black mb-0">
                {{ shop ? 'Update Your Shop' : 'Create Your Shop' }}
              </h5>
              <span
                v-if="shop"
                class="badge bg-primary d-none d-md-inline"
                >Editing</span
              >
            </div>

            <form
              @submit.prevent="submit"
              enctype="multipart/form-data"
              class="row g-3"
            >
              <div class="col-12">
                <label class="form-label fw-semibold">Shop Name</label>
                <input
                  type="text"
                  class="form-control form-control-lg"
                  v-model="form.shop_name"
                  required
                  placeholder="e.g., Jane's Accessories"
                />
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Shop Description</label>
                <textarea
                  class="form-control"
                  rows="3"
                  v-model="form.shop_description"
                  placeholder="Tell buyers what makes your shop special."
                ></textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Phone Number</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.phone_number"
                  placeholder="09XXXXXXXXX"
                />
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Email Address</label>
                <input
                  type="email"
                  class="form-control"
                  v-model="form.email_address"
                  placeholder="you@example.com"
                />
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Address</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.address"
                  placeholder="Shop address (e.g., City, Street, Barangay)"
                />
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Shop Logo</label>
                <input
                  type="file"
                  class="form-control"
                  @change="e => form.shop_logo = e.target.files[0]"
                  accept="image/*"
                />
                <div v-if="form.shop_logo" class="mt-2 small text-muted">
                  Selected: {{ form.shop_logo.name }}
                </div>
              </div>

              <div class="col-12">
                <button type="submit" class="btn btn-secondary btn-lg w-100">
                  {{ shop ? 'Update Shop' : 'Create Shop' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'
import Flash from '@/Layouts/Flash.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import { defineOptions, defineProps } from 'vue'

defineOptions({
  layout: Flash
})

const props = defineProps({
  user: Object,
  shop: Object
})

// Toast handling
const page = usePage()
const showToast = ref(false)
const toastMessage = ref('')

onMounted(() => {
  const success = page.props.flash?.success
  if (success) {
    toastMessage.value = success
    showToast.value = true
    setTimeout(() => {
      showToast.value = false
    }, 3000)
  }
})

// Form
const form = useForm({
  shop_name: props.shop?.shop_name ?? '',
  shop_description: props.shop?.shop_description ?? '',
  shop_logo: null,
  phone_number: props.shop?.phone_number ?? '',
  email_address: props.shop?.email_address ?? '',
  address: props.shop?.address ?? '',
})

const submit = () => {
  form.post('/seller/shop', {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      toastMessage.value = 'Shop updated successfully!'
      showToast.value = true
      setTimeout(() => {
        showToast.value = false
      }, 3000)
    }
  })
}
</script>

<style scoped>
/* Focus states */
/* input.form-control:focus,
textarea.form-control:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5);
} */

/* Card styling */
.card {
  border: none;
  border-radius: 1rem;
}

/* Headings */
h2 {
  font-weight: 600;
  font-size: 2rem;
}
h4 {
  font-size: 1.4rem;
  font-weight: 600;
}
h5 {
  font-size: 1.2rem;
  font-weight: 600;
}

/* Avatar */
.avatar {
  transition: transform 0.2s ease-in-out;
}
.avatar:hover {
  transform: scale(1.05);
}

/* Buttons */
button {
  font-weight: 500;
  padding: 0.75rem;
  border-radius: 0.75rem;
}
</style>
