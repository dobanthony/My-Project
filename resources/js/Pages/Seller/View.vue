<template>
  <SellerDashboardLayout>
    <div class="container py-5">
      <h2 class="mb-4">📦 Order & Delivery Details</h2>

      <div class="card p-4 shadow-sm">
        <h5 class="mb-3">Delivery Information</h5>
        <p><strong>Full Name:</strong> {{ order.delivery_info?.full_name || 'N/A' }}</p>
        <p><strong>Phone Number:</strong> {{ order.delivery_info?.phone_number || 'N/A' }}</p>
        <p><strong>Email:</strong> {{ order.delivery_info?.email || 'N/A' }}</p>
        <p><strong>Address:</strong> {{ order.delivery_info?.delivery_address || 'N/A' }}</p>
        <p v-if="order.delivery_info?.notes"><strong>Notes:</strong> {{ order.delivery_info.notes }}</p>

        <hr />

        <h5 class="mb-3">Order Information</h5>
        <p><strong>Product:</strong> {{ order.product.name }}</p>
        <p><strong>Quantity:</strong> {{ order.quantity }}</p>
        <p><strong>Status:</strong> {{ order.status }}</p>
        <p><strong>Delivery Date:</strong> {{ order.delivery_date ?? 'N/A' }}</p>
        <p><strong>Delivery Status:</strong> {{ order.delivery_status ?? 'N/A' }}</p>
      </div>

      <Link :href="route('seller.orders')" class="btn btn-secondary mt-3">
        ⬅ Back to Orders
      </Link>
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
