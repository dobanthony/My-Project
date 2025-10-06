<template>
  <SellerDashboardLayout>
    <div class="container py-4">
      <!-- Page Header -->
      <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="fw-bold text-success mb-0">
          <i class="bi bi-receipt-cutoff me-2"></i> Order & Delivery Details
        </h3>
        <Link href="/seller/orders" class="btn btn-outline-success">
          <i class="bi bi-arrow-left-circle me-1"></i> Back to Orders
        </Link>
      </div>

      <!-- Delivery Info -->
      <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-success text-white fw-semibold">
          <i class="bi bi-truck me-2"></i> Delivery Information
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <p><i class="bi bi-person-fill me-2 text-success"></i><strong>Full Name:</strong> {{ order.full_name || 'N/A' }}</p>
              <p><i class="bi bi-telephone-fill me-2 text-success"></i><strong>Phone:</strong> {{ order.phone_number || 'N/A' }}</p>
            </div>
            <div class="col-md-6 mb-3">
              <p><i class="bi bi-envelope-fill me-2 text-success"></i><strong>Email:</strong> {{ order.email || 'N/A' }}</p>
              <p><i class="bi bi-geo-alt-fill me-2 text-success"></i><strong>Address:</strong> {{ order.delivery_address || 'N/A' }}</p>
            </div>
          </div>
          <p v-if="order.notes" class="mt-2">
            <i class="bi bi-chat-dots-fill me-2 text-muted"></i><strong>Notes:</strong> {{ order.notes }}
          </p>
        </div>
      </div>

      <!-- Order Info -->
      <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white fw-semibold">
          <i class="bi bi-box-seam me-2"></i> Order Information
        </div>
        <div class="card-body">
          <div class="row">
            <!-- Left column -->
            <div class="col-md-6 mb-3">
              <p><i class="bi bi-bag-fill me-2 text-success"></i><strong>Product:</strong> {{ order.product.name }}</p>
              <p><i class="bi bi-list-ol me-2 text-success"></i><strong>Quantity:</strong> {{ order.quantity }}</p>

              <div>
                <p><i class="bi bi-sliders me-2 text-success"></i><strong>Customizations:</strong></p>
                <ul v-if="order.customization_details" class="mb-3 ps-3">
                  <li v-if="order.customization_details.material"><strong>Material:</strong> {{ order.customization_details.material }}</li>
                  <li v-if="order.customization_details.color"><strong>Color:</strong> {{ order.customization_details.color }}</li>
                  <li v-if="order.customization_details.size"><strong>Size:</strong> {{ order.customization_details.size }}</li>
                </ul>
                <p v-else class="text-muted fst-italic">No customization provided.</p>
              </div>
            </div>

            <!-- Right column -->
            <div class="col-md-6 mb-3">
              <ul v-if="order.customization_details" class="mb-3 ps-3">
                <li v-if="order.customization_details.custom_name"><strong>Custom Name:</strong> {{ order.customization_details.custom_name }}</li>
                <li v-if="order.customization_details.custom_description"><strong>Description:</strong> {{ order.customization_details.custom_description }}</li>
              </ul>

              <p>
                <i class="bi bi-info-circle-fill me-2 text-success"></i>
                <strong>Status:</strong>
                <span
                  class="badge px-3 py-2"
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
                <i class="bi bi-calendar-event-fill me-2 text-success"></i>
                <strong>Delivery Date:</strong> {{ order.delivery_date ?? 'N/A' }}
              </p>

              <p>
                <i class="bi bi-clipboard-check-fill me-2 text-success"></i>
                <strong>Delivery Status:</strong>
                <span
                  class="badge px-3 py-2"
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
