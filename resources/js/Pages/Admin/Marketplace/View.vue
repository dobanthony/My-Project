<template>
  <Head title="View " />
  <AdminDashboardLayout>
    <div class="container py-3">
      <!-- Header -->
      <div class="d-flex align-items-center mb-3">
        <i class="bi bi-eye text-primary me-2 fs-5"></i>
        <h5 class="mb-0 fw-bold">Product Details</h5>
      </div>

      <!-- Product Card -->
      <div class="card shadow-sm border-0">
        <div class="row g-0 align-items-center">
          <!-- Product Image -->
          <div class="col-md-5 border-end">
            <div class="p-3 text-center">
              <img
                :src="getImageUrl(product.image)"
                class="img-fluid rounded"
                style="max-height: 350px; object-fit: contain"
                alt="Product Image"
              />
            </div>
          </div>

          <!-- Product Info -->
          <div class="col-md-7">
            <div class="card-body p-4">
              <!-- Product Name -->
              <h3 class="card-title mb-3 d-flex align-items-center text-success fw-bold">
                {{ product.name }}
                <span
                  v-if="product.eco_friendly"
                  class="badge bg-success ms-2"
                >
                  <i class="bi bi-leaf me-1"></i> Eco-Friendly
                </span>
              </h3>

              <!-- Product Info Grid (3 x 2 layout) -->
              <div class="row g-3">
                <!-- Row 1 -->
                <div class="col-md-4">
                  <p class="mb-1 fw-bold">Description</p>
                  <p class="text-muted small mb-0">
                    {{ product.description || 'No description' }}
                  </p>
                </div>
                <div class="col-md-4">
                  <p class="mb-1 fw-bold">Price</p>
                  <p class="text-success fw-bold mb-0">
                    ₱{{ Number(product.price).toFixed(2) }}
                  </p>
                </div>
                <div class="col-md-4">
                  <p class="mb-1 fw-bold">Stock</p>
                  <span
                    :class="{
                      'badge bg-success': product.stock > 0,
                      'badge bg-danger': product.stock === 0
                    }"
                  >
                    {{ product.stock > 0 ? product.stock + ' available' : 'Out of stock' }}
                  </span>
                </div>

                <!-- Row 2 -->
                <div class="col-md-4">
                  <p class="mb-1 fw-bold">Shop</p>
                  <p class="mb-0">
                    <i class="bi bi-shop me-1"></i>
                    {{ product.shop ? product.shop.shop_name : 'N/A' }}
                  </p>
                </div>
              </div>

              <!-- Back Button -->
              <div class="mt-4">
                <Link
                  href="/admin/marketplace"
                  class="btn btn-outline-primary btn-sm"
                >
                  <i class="bi bi-arrow-left me-1"></i> Back to List
                </Link>
              </div>
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
import { Link, Head } from '@inertiajs/vue3'

const props = defineProps({
  product: Object,
})

const getImageUrl = (imagePath) => {
  return imagePath
    ? `/storage/${imagePath}`
    : 'https://via.placeholder.com/400x300?text=No+Image'
}
</script>
