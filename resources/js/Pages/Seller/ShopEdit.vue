<template>
  <SellerDashboardLayout>
    <!-- Toast Notification -->
    <div
    v-if="showToast"
    class="toast-container position-fixed start-50 translate-middle-x p-3"
    style="top: 1.5rem; z-index: 9999;"
    >
      <div class="toast show align-items-center text-white bg-success border-0 shadow" role="alert">
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
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4 gap-3">
        <div>
          <h2 class="mb-1"><i class="bi bi-shop-window me-2 text-success"></i>Hello, {{ user.first_name }} {{ user.last_name }}</h2>
          <p class="text-muted mb-0">Manage your shop details. Buyers will see this on your storefront.</p>
        </div>
      </div>

      <div class="row gy-4">
        <!-- Left: Existing shop preview -->
        <div class="col-12 col-lg-5">
          <div class="card shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex align-items-center mb-3 gap-3">
              <div class="flex-shrink-0">
                <div
                  class="avatar rounded-circle bg-light border d-flex align-items-center justify-content-center"
                  style="width: 80px; height: 80px; overflow: hidden;"
                >
                  <template v-if="shop && shop.shop_logo">
                    <img
                      :src="`/storage/${shop.shop_logo}`"
                      alt="Logo"
                      class="rounded-circle"
                      style="width: 80px; height: 80px; object-fit: cover;"
                    />
                  </template>
                  <template v-else>
                    <i class="bi bi-shop fs-2 text-secondary"></i>
                  </template>
                </div>
              </div>
              <div>
                <h5 class="mb-1 text-success">{{ shop?.shop_name ?? 'No Shop Yet' }}</h5>
                <div class="small text-muted">
                  {{ shop?.shop_description ?? 'Describe your shop to attract buyers.' }}
                </div>
              </div>
            </div>

            <div v-if="shop" class="mb-3">
              <div class="mb-2">
                <strong class="text-success">Contact Info</strong>
              </div>
              <p class="mb-1">
                <i class="bi bi-telephone me-2"></i>
                {{ shop.phone_number ?? '-' }}
              </p>
              <p class="mb-1">
                <i class="bi bi-envelope me-2"></i>
                {{ shop.email_address ?? '-' }}
              </p>
              <p class="mb-1">
                <i class="bi bi-geo-alt-fill me-2"></i>
                {{ shop.address ?? '-' }}
              </p>
            </div>

            <div v-else class="alert alert-warning py-2">
              You haven't created a shop yet. Fill the form to get started.
            </div>
          </div>
        </div>

        <!-- Right: Form -->
        <div class="col-12 col-lg-7">
          <div class="card shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="text-success mb-0">{{ shop ? 'Update Your Shop' : 'Create Your Shop' }}</h5>
              <span v-if="shop" class="badge bg-success d-none d-md-inline">Editing</span>
            </div>

            <form @submit.prevent="submit" enctype="multipart/form-data" class="row g-3">
              <div class="col-12">
                <label class="form-label small fw-semibold">Shop Name</label>
                <input
                  type="text"
                  class="form-control form-control-lg"
                  v-model="form.shop_name"
                  required
                  placeholder="e.g., Jane's Accessories"
                />
              </div>

              <div class="col-12">
                <label class="form-label small fw-semibold">Shop Description</label>
                <textarea
                  class="form-control"
                  rows="3"
                  v-model="form.shop_description"
                  placeholder="Tell buyers what makes your shop special."
                ></textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label small fw-semibold">Phone Number</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.phone_number"
                  placeholder="09XXXXXXXXX"
                />
              </div>

              <div class="col-md-6">
                <label class="form-label small fw-semibold">Email Address</label>
                <input
                  type="email"
                  class="form-control"
                  v-model="form.email_address"
                  placeholder="you@example.com"
                />
              </div>

              <div class="col-12">
                <label class="form-label small fw-semibold">Address</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.address"
                  placeholder="Shop address (e.g., City, Street, Barangay)"
                />
              </div>

              <div class="col-12">
                <label class="form-label small fw-semibold">Shop Logo</label>
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
                <button type="submit" class="btn btn-success btn-lg w-100">
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

// Setup toast handling
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

// Setup form
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
input.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
textarea.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
/* General Card Styling */
.card {
  border: none;
  border-radius: 1rem;
}

/* Headings */
h2 {
  font-weight: 600;
  font-size: clamp(1.5rem, 2vw + 1rem, 2.25rem); /* Responsive scaling */
}

h5 {
  font-size: clamp(1.1rem, 1vw + 0.8rem, 1.4rem);
  font-weight: 600;
}

/* Small texts */
.small,
.text-muted {
  font-size: clamp(0.8rem, 0.5vw + 0.7rem, 0.95rem);
  line-height: 1.4;
}

/* Toast */
.toast-body {
  font-size: clamp(0.9rem, 0.6vw + 0.8rem, 1rem);
}

/* Badge */
.badge {
  font-size: clamp(0.7rem, 0.5vw + 0.6rem, 0.85rem);
  padding: 0.4em 0.8em;
  border-radius: 12px;
}

/* Inputs */
input,
textarea {
  font-size: clamp(0.9rem, 0.6vw + 0.8rem, 1rem);
}

/* Buttons */
button {
  font-size: clamp(0.95rem, 0.7vw + 0.8rem, 1.1rem);
  font-weight: 500;
  padding: 0.75rem;
  border-radius: 0.75rem;
}

button.btn-lg {
  padding: 0.5rem 1rem;  /* reduce height */
  font-size: 1rem;       /* smaller text */
}

/* Responsive adjustments for smaller devices */
@media (max-width: 991px) {
  .container {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
  }

  h2 {
    font-size: 1.5rem; /* keep heading readable on mobile */
  }

  .avatar {
    width: 64px !important;
    height: 64px !important;
  }

  .card {
    padding: 1rem !important;
  }
}
</style>
