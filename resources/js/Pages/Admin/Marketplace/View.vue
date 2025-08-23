<template>
  <AdminDashboardLayout>
    <div class="container py-4">
      <h4 class="mb-4">
        <i class="bi bi-eye text-primary me-2"></i>Product Details
      </h4>

      <div class="card shadow-sm">
        <div class="row g-0">
          <!-- Product Image -->
          <div class="col-md-5">
            <div class="p-3 text-center">
              <img
                :src="getImageUrl(product.image)"
                class="img-fluid rounded border"
                style="max-height: 400px; object-fit: contain"
                alt="Product Image"
              />
            </div>
          </div>

          <!-- Product Info -->
          <div class="col-md-7">
            <div class="card-body p-4">
              <h3 class="card-title mb-3 d-flex align-items-center">
                {{ product.name }}
                <!-- Show eco-friendly only if true -->
                <span
                  v-if="product.eco_friendly"
                  class="badge bg-success ms-3 d-flex align-items-center"
                >
                  <i class="bi bi-leaf me-1"></i> Eco-Friendly
                </span>
              </h3>

              <p class="text-muted mb-2">
                <i class="bi bi-info-circle me-1"></i>
                {{ product.description || 'No description provided.' }}
              </p>

              <h5 class="text-success mb-3">
                <i class="bi bi-cash-coin me-1"></i>
                ₱{{ Number(product.price).toFixed(2) }}
              </h5>

              <p>
                <span class="fw-bold">Stock:</span>
                <span
                  :class="{
                    'badge bg-success': product.stock > 0,
                    'badge bg-danger': product.stock === 0
                  }"
                >
                  {{ product.stock > 0 ? product.stock + ' available' : 'Out of stock' }}
                </span>
              </p>

              <p v-if="product.shop">
                <i class="bi bi-shop me-1"></i>
                <strong>Shop:</strong> {{ product.shop.shop_name }}
              </p>

              <!-- Back Button -->
              <Link
                href="/admin/marketplace"
                class="btn btn-outline-primary mt-4"
              >
                <i class="bi bi-arrow-left me-1"></i>Back to List
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminDashboardLayout>
</template>

<script setup>
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  product: Object,
})

const getImageUrl = (imagePath) => {
  return imagePath
    ? `/storage/${imagePath}`
    : 'https://via.placeholder.com/400x300?text=No+Image'
}
</script>
