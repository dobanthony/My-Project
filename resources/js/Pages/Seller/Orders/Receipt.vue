<template>
  <SellerDashboardLayout>
    <div class="container">
      <div class="border p-4 shadow-sm rounded bg-white mx-auto" style="max-width: 800px">
        <!-- Delivery Info -->
        <div class="mb-4">
          <h5 class="text-danger display-5"><i class="bi bi-geo-alt-fill"></i> Delivery Address</h5>
          <p class="mb-1">
            <strong>{{ order.user?.name }}</strong>
            <span v-if="order.user?.phone"> ({{ order.user?.phone }})</span>
          </p>
          <p>{{ order.user?.address ?? 'No address provided' }}</p>
        </div>

        <!-- Product Ordered -->
        <div class="mb-4">
          <h5 class="fw-bold mb-3">Products Ordered</h5>
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div>
                <strong>Shop Name: </strong>
                <strong>{{ order.product?.shop?.shop_name ?? 'Shop Name' }}</strong>
              </div>
              <small class="text-muted">Order ID: #{{ order.id }}</small>
            </div>
            <div class="d-flex">
              <img
                :src="order.product?.image ? `/storage/${order.product.image}` : 'https://via.placeholder.com/60'"
                class="me-3 rounded"
                width="70"
              />
              <div class="flex-grow-1">
                <h6 class="mb-1">
                  {{ order.product?.name }}
                  <!-- ✅ Reported Badge -->
                  <span v-if="order.reported" class="badge bg-danger ms-2">⚠ Reported</span>
                </h6>
                <p class="text-muted mb-1">{{ order.product?.description ?? 'No description' }}</p>
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <span class="text-muted">₱{{ order.product?.price }}</span> × {{ order.quantity }}
                  </div>
                  <strong class="text-dark">₱{{ totalAmount }}</strong>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Info -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <p><strong>Seller:</strong> {{ order.product?.shop?.user?.name ?? 'Unknown Seller' }}</p>
            <p><strong>Status:</strong>
              <span
                class="badge"
                :class="{
                  'bg-warning text-dark': order.status === 'pending',
                  'bg-success': order.status === 'approved',
                  'bg-danger': order.status === 'declined',
                  'bg-info text-dark': order.status === 'received',
                  'bg-dark': order.status === 'canceled'
                }"
              >
                {{ order.status }}
              </span>
            </p>
            <p><strong>Date Ordered:</strong> {{ new Date(order.created_at).toLocaleString() }}</p>
            <p><strong>Delivery Status:</strong>
              <span
                class="badge"
                :class="{
                  'bg-secondary': order.delivery_status === 'pending',
                  'bg-success': order.delivery_status === 'delivered',
                  'bg-danger': order.delivery_status === 'failed'
                }"
              >
                {{ order.delivery_status || 'N/A' }}
              </span>
            </p>
          </div>
          <div class="col-md-6 mb-3 text-md-end">
            <p><strong>Shipping Option:</strong> Standard Local</p>
            <p><strong>Estimated Delivery:</strong> {{ order.delivery_date ?? 'N/A' }}</p>
            <p><strong>Total Payment:</strong> ₱{{ totalAmount }}</p>

            <!-- ✅ Contact User Button -->
            <button
              v-if="order.reported"
              @click="goToChat"
              class="btn btn-outline-danger mt-3"
            >
              Contact User
            </button>
          </div>
        </div>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  order: Object,
  userId: Number,
  isSeller: Boolean
})

const totalAmount = computed(() => {
  return (props.order.product?.price ?? 0) * (props.order.quantity ?? 1)
})

// ✅ Redirect to chat with reported flag
const goToChat = () => {
  router.visit(
    `/seller/inbox/${props.order.user?.id}?product_id=${props.order.product?.id}&reported=1`
  )
}
</script>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  .container, .container * {
    visibility: visible;
  }
  .container {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
}
</style>
