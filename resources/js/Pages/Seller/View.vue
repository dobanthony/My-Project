<template>
  <SellerDashboardLayout>
    <div class="container py-1">

      <!-- 🌟 Page Header -->
      <div class="text-center mb-5">
        <h2 class="fw-bold text-primary mb-2">
          <i class="bi bi-receipt-cutoff me-2"></i> Order & Delivery Details
        </h2>
        <p class="text-muted mb-0">
          Review the full details of Buyer order, including customer delivery information and customization summary.
        </p>
      </div>

      <!-- 🔙 Back Button -->
      <div class="text-end mb-4">
        <Link href="/seller/orders" class="btn btn-outline-primary btn-sm shadow-sm">
          <i class="bi bi-arrow-left-circle me-1"></i> Back to Orders
        </Link>
      </div>

      <!-- 🚚 Delivery Info -->
      <div class="card shadow-sm border-0 mb-5 rounded-3">
        <div class="card-header delivery-header text-white fw-semibold d-flex align-items-center">
          <i class="bi bi-truck me-2 fs-5"></i> Delivery Information
        </div>
        <div class="card-body">
          <div v-if="order.delivery_info">
            <div class="row g-3">
              <div class="col-md-6">
                <p>
                  <i class="bi bi-person-fill me-2 text-secondary"></i>
                  <strong>Full Name:</strong> {{ order.delivery_info.full_name }}
                </p>
                <p v-if="order.delivery_info.phone_number">
                  <i class="bi bi-telephone-fill me-2 text-secondary"></i>
                  <strong>Phone:</strong> {{ order.delivery_info.phone_number }}
                </p>
              </div>
              <div class="col-md-6">
                <p v-if="order.delivery_info.email">
                  <i class="bi bi-envelope-fill me-2 text-secondary"></i>
                  <strong>Email:</strong> {{ order.delivery_info.email }}
                </p>
                <p>
                  <i class="bi bi-geo-alt-fill me-2 text-secondary"></i>
                  <strong>Address:</strong>
                  {{ order.delivery_info.street_address ? order.delivery_info.street_address + ', ' : '' }}
                  {{ order.delivery_info.barangay?.name ? order.delivery_info.barangay.name + ', ' : '' }}
                  {{ order.delivery_info.municipality?.name ? order.delivery_info.municipality.name + ', ' : '' }}
                  {{ order.delivery_info.province?.name ?? '' }}
                </p>
              </div>
            </div>

            <p v-if="order.delivery_info.notes" class="mt-3 mb-0">
              <i class="bi bi-chat-dots-fill me-2 text-muted"></i>
              <strong>Notes:</strong> {{ order.delivery_info.notes }}
            </p>
          </div>

          <p v-else class="text-muted mb-0">
            No delivery info provided.
          </p>
        </div>
      </div>

      <!-- 📦 Order Info -->
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header order-header text-white fw-semibold d-flex align-items-center">
          <i class="bi bi-box-seam me-2 fs-5"></i> Order Information
        </div>

        <div class="card-body">
          <div class="row align-items-start g-4">
            
            <!-- 🖼 Product Image -->
            <div class="col-md-4 text-center">
              <img
                :src="
                  order.customization_details?.selected_image
                    ? order.customization_details.selected_image
                    : order.product?.image
                    ? `/storage/${order.product.image}`
                    : 'https://via.placeholder.com/200'
                "
                class="rounded border bg-light shadow-sm"
                width="200"
                height="200"
                style="object-fit: contain; padding: 5px;"
                alt="Customized Product Image"
              />
              <p class="text-muted text-start small fst-italic mt-2">
                <i class="bi bi-image me-1"></i>
                {{ order.customization_details?.selected_image ? 'Customized Image' : 'Default Product Image' }}
              </p>
            </div>

            <!-- 🧾 Product Details -->
            <div class="col-md-8">
              <div class="row g-3">
                
                <!-- Left -->
                <div class="col-md-6">
                  <p><i class="bi bi-bag-fill me-2 text-secondary"></i><strong>Product:</strong> {{ order.product.name }}</p>
                  <p>
                    <i class="bi bi-tags-fill me-2 text-secondary"></i><strong>Category:</strong>
                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                      {{ order.product?.category?.name ?? '—' }}
                    </span>
                  </p>
                  <p><i class="bi bi-list-ol me-2 text-secondary"></i><strong>Quantity:</strong> {{ order.quantity }}</p>

                  <div>
                    <p><i class="bi bi-sliders me-2 text-secondary"></i><strong>Customizations:</strong></p>
                    <ul v-if="order.customization_details" class="mb-3 ps-3">
                      <li v-if="order.customization_details.material"><strong>Material:</strong> {{ order.customization_details.material }}</li>
                      <li v-if="order.customization_details.color"><strong>Color:</strong> {{ order.customization_details.color }}</li>
                      <li v-if="order.customization_details.size"><strong>Size:</strong> {{ order.customization_details.size }}</li>
                      <li v-if="order.customization_details.pattern"><strong>Pattern:</strong> {{ order.customization_details.pattern }}</li>
                    </ul>
                    <p v-else class="text-muted fst-italic">No customization provided.</p>
                  </div>
                </div>

                <!-- Right -->
                <div class="col-md-6">
                  <ul v-if="order.customization_details" class="mb-3 ps-3">
                    <li v-if="order.customization_details.custom_name"><strong>Custom Name:</strong> {{ order.customization_details.custom_name }}</li>
                    <li v-if="order.customization_details.custom_description"><strong>Description:</strong> {{ order.customization_details.custom_description }}</li>
                  </ul>

                  <p>
                    <i class="bi bi-info-circle-fill me-2 text-secondary"></i>
                    <strong>Status:</strong>
                    <span
                      class="badge px-3 py-2 text-uppercase"
                      :class="{
                        'bg-warning text-dark': order.status === 'pending',
                        'bg-success': order.status === 'approved',
                        'bg-danger': order.status === 'declined',
                        'bg-secondary': order.status === 'canceled'
                      }"
                    >
                      {{ order.status }}
                    </span>
                  </p>

                  <p>
                    <i class="bi bi-calendar-event-fill me-2 text-secondary"></i>
                    <strong>Delivery Date:</strong> {{ order.delivery_date ?? 'N/A' }}
                  </p>

                  <p>
                    <i class="bi bi-clipboard-check-fill me-2 text-secondary"></i>
                    <strong>Delivery Status:</strong>
                    <span
                      class="badge px-3 py-2 text-uppercase"
                      :class="{
                        'bg-warning text-dark': order.delivery_status === 'pending',
                        'bg-success': order.delivery_status === 'delivered',
                        'bg-danger': order.delivery_status === 'failed'
                      }"
                    >
                      {{ order.delivery_status ?? 'N/A' }}
                    </span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'

const props = defineProps({
  order: Object,
  isSeller: Boolean
})
</script>

<style scoped>
.text-primary {
  color: #0d6efd !important;
}
.bg-success-subtle {
  background-color: rgba(25, 135, 84, 0.1) !important;
}
/* Smooth gradient for Delivery Info header (Secondary - grey) */
.delivery-header {
  background: linear-gradient(90deg, #6c757d, #adb5bd); /* soft grey gradient */
  color: #fff;
  border-radius: 0.5rem 0.5rem 0 0;
  font-weight: 600;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1); /* subtle depth */
}

/* Smooth gradient for Order Info header (Primary - blue) */
.order-header {
  background: linear-gradient(90deg, #0d6efd, #66b2ff); /* soft blue gradient */
  color: #fff;
  border-radius: 0.5rem 0.5rem 0 0;
  font-weight: 600;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1); /* subtle depth */
}

/* Optional: subtle background for card body */
.card-body {
  background-color: #f8f9fa; /* light grey for contrast */
  border-radius: 0 0 0.5rem 0.5rem;
}

</style>
