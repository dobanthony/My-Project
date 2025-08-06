<template>
  <SellerDashboardLayout>
    <div class="container">
      <h3 class="mb-3 text-success">
        Order & Delivery Details
      </h3>

      <div class="row g-4">
        <!-- Delivery Info -->
        <div class="col-12 col-lg-6">
          <div class="card shadow-sm h-100">
            <div class="card-header bg-success text-white fw-bold">
              Delivery Information
            </div>
            <div class="card-body">
              <p><strong>Full Name:</strong> {{ order.full_name || 'N/A' }}</p>
              <p><strong>Phone Number:</strong> {{ order.phone_number || 'N/A' }}</p>
              <p><strong>Email:</strong> {{ order.email || 'N/A' }}</p>
              <p><strong>Address:</strong> {{ order.delivery_address || 'N/A' }}</p>
              <p v-if="order.notes">
                <strong>Notes:</strong> {{ order.notes }}
              </p>
            </div>
          </div>
        </div>

        <!-- Order Info -->
        <div class="col-12 col-lg-6">
          <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white fw-bold">
              Order Information
            </div>
            <div class="card-body">
              <p><strong>Product:</strong> {{ order.product.name }}</p>
              <p><strong>Quantity:</strong> {{ order.quantity }}</p>

              <p><strong>Customizations:</strong></p>
              <ul v-if="order.customization_details" class="mb-3">
                <li v-if="order.customization_details.color"><strong>Color:</strong> {{ order.customization_details.color }}</li>
                <li v-if="order.customization_details.size"><strong>Size:</strong> {{ order.customization_details.size }}</li>
                <li v-if="order.customization_details.material"><strong>Material:</strong> {{ order.customization_details.material }}</li>
                <li v-if="order.customization_details.custom_name"><strong>Custom Name:</strong> {{ order.customization_details.custom_name }}</li>
                <li v-if="order.customization_details.custom_description"><strong>Description:</strong> {{ order.customization_details.custom_description }}</li>
              </ul>
              <p v-else class="text-muted">No customization provided.</p>

              <p>
                <strong>Status:</strong>
                <span
                  :class="{
                    'text-warning': order.status === 'pending',
                    'text-success': order.status === 'approved',
                    'text-danger': order.status === 'declined',
                    'text-secondary': order.status === 'canceled'
                  }"
                >
                  {{ order.status }}
                </span>
              </p>

              <p><strong>Delivery Date:</strong> {{ order.delivery_date ?? 'N/A' }}</p>
              <p>
                <strong>Delivery Status:</strong>
                <span
                  :class="{
                    'badge bg-warning': order.delivery_status === 'pending',
                    'badge bg-success': order.delivery_status === 'delivered',
                    'badge bg-danger': order.delivery_status === 'failed'
                  }"
                >
                  {{ order.delivery_status ?? 'N/A' }}
                </span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Back Button -->
      <div class="mt-4">
        <Link href="/seller/orders" class="btn btn-outline-success">
          ⬅ Back to Orders
        </Link>
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
