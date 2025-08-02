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
              <p><strong>Full Name:</strong> {{ order.delivery_info?.full_name || 'N/A' }}</p>
              <p><strong>Phone Number:</strong> {{ order.delivery_info?.phone_number || 'N/A' }}</p>
              <p><strong>Email:</strong> {{ order.delivery_info?.email || 'N/A' }}</p>
              <p><strong>Address:</strong> {{ order.delivery_info?.delivery_address || 'N/A' }}</p>
              <p v-if="order.delivery_info?.notes">
                <strong>Notes:</strong> {{ order.delivery_info.notes }}
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
