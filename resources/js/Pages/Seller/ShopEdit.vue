<template>
  <Head title="Shop " />
  <SellerDashboardLayout>
    <!-- ✅ Toast Notification -->
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

    <!-- 🌟 Page Header -->
    <div class="container mb-5">
      <div class="text-center py-4 border-bottom">
        <h1 class="fw-bold text-primary mb-2">
          <i class="bi bi-shop me-2"></i> Manage Your Shop
        </h1>
        <p class="text-muted fs-6 mb-0">
          Customize your shop’s details and branding. This information helps buyers recognize and trust your store.
        </p>
      </div>
    </div>

    <!-- ✅ Main Content -->
    <div class="container">
      <div class="row gy-4">
        <!-- 🏪 Shop Preview Card -->
        <div class="col-12">
          <div class="card shadow-sm border-0 rounded-4 p-4 h-100 text-center bg-white">
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
                  <i class="bi bi-shop-window fs-1 text-secondary"></i>
                </template>
              </div>
            </div>

            <!-- Shop Name & Description -->
            <h4 class="mb-2 text-dark fw-semibold">
              {{ shop?.shop_name ?? 'No Shop Yet' }}
            </h4>
            <p class="text-muted">
              {{ shop?.shop_description ?? 'Describe your shop to attract buyers.' }}
            </p>

            <!-- Shop Info -->
            <div v-if="shop" class="mt-4 text-start mx-auto" style="max-width: 600px;">
              <div class="row g-3 fs-6 fw-medium">
                <div class="col-12 col-md-6">
                  <i class="bi bi-telephone me-2 text-primary"></i>
                  {{ shop.phone_number ?? '-' }}
                </div>
                <div class="col-12 col-md-6">
                  <i class="bi bi-envelope me-2 text-primary"></i>
                  {{ shop.email_address ?? '-' }}
                </div>
                <div class="col-12 col-md-6">
                  <i class="bi bi-geo-alt-fill me-2 text-primary"></i>
                  {{ shop.address ?? '-' }}
                </div>
                <div class="col-12 col-md-6">
                  <i class="bi bi-calendar-event me-2 text-primary"></i>
                  {{ new Date().toLocaleDateString() }}
                </div>
              </div>
            </div>

            <div v-else class="alert alert-warning py-2 mt-3 mb-0">
              <i class="bi bi-info-circle me-2"></i> You haven't created a shop yet. Fill out the form below to get started.
            </div>
          </div>
        </div>

        <!-- 📝 Shop Form -->
        <div class="col-12">
          <div class="card shadow-sm border-0 rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="text-dark fw-bold mb-0">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                {{ shop ? 'Update Your Shop' : 'Create Your Shop' }}
              </h5>
              <span v-if="shop" class="badge bg-primary rounded-pill px-3 py-2">Editing</span>
            </div>

            <form @submit.prevent="submit" enctype="multipart/form-data" class="row g-3">
              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="bi bi-shop me-1 text-primary"></i> Shop Name
                </label>
                <input
                  type="text"
                  class="form-control form-control-lg"
                  v-model="form.shop_name"
                  required
                  placeholder="e.g., Jane's Accessories"
                />
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="bi bi-card-text me-1 text-primary"></i> Shop Description
                </label>
                <textarea
                  class="form-control"
                  rows="3"
                  v-model="form.shop_description"
                  placeholder="Tell buyers what makes your shop special."
                ></textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-telephone me-1 text-primary"></i> Phone Number
                </label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.phone_number"
                  placeholder="09XXXXXXXXX"
                />
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-envelope me-1 text-primary"></i> Email Address
                </label>
                <input
                  type="email"
                  class="form-control"
                  v-model="form.email_address"
                  placeholder="you@example.com"
                />
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="bi bi-geo-alt me-1 text-primary"></i> Address
                </label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.address"
                  placeholder="Shop address (e.g., City, Street, Barangay)"
                />
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="bi bi-image me-1 text-primary"></i> Shop Logo
                </label>
                <input
                  type="file"
                  class="form-control"
                  @change="e => form.shop_logo = e.target.files[0]"
                  accept="image/*"
                />
                <div v-if="form.shop_logo" class="mt-2 small text-muted">
                  <i class="bi bi-file-earmark-image me-1"></i> Selected: {{ form.shop_logo.name }}
                </div>
              </div>

              <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold btn-shop">
                  <i class="bi bi-save me-2"></i> {{ shop ? 'Update Shop' : 'Create Shop' }}
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
import { useForm, usePage, Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import { defineOptions, defineProps } from 'vue'

defineOptions({ layout: Flash })

const props = defineProps({
  user: Object,
  shop: Object
})

const page = usePage()
const showToast = ref(false)
const toastMessage = ref('')

onMounted(() => {
  const success = page.props.flash?.success
  if (success) {
    toastMessage.value = success
    showToast.value = true
    setTimeout(() => (showToast.value = false), 3000)
  }
})

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
      setTimeout(() => (showToast.value = false), 3000)
    },
  })
}
</script>

<style scoped>
h1 {
  font-size: 2.2rem;
}

.card {
  border-radius: 1rem;
  transition: transform 0.2s ease-in-out;
}
.card:hover {
  transform: translateY(-3px);
}

.avatar:hover {
  transform: scale(1.05);
  transition: all 0.3s ease;
}

.btn-shop {
  font-size: 1rem;
  border-radius: 0.5rem;
  transition: all 0.3s ease;
}
.btn-shop:hover {
  background-color: #0b5ed7;
  box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
}
</style>
