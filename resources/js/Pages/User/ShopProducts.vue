<template>
  <div class="container py-5">
    <h2 class="mb-4">🛒 Products from {{ shop.shop_name }}</h2>

    <div class="row">
      <div
        v-for="product in products"
        :key="product.id"
        class="col-md-4 mb-4"
      >
        <div class="card h-100">
          <img
            :src="product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/300x200?text=No+Image'"
            class="card-img-top"
            alt="Product"
          />

          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ product.name }}</h5>
            <p class="card-text text-success fw-bold">
              ₱{{ parseFloat(product.price).toFixed(2) }}
            </p>

            <!-- ✅ Inertia Link for ProductDetails.vue -->
            <Link
              :href="`/product/${product.id}`"
              class="btn btn-primary btn-sm mt-auto"
            >
              View Details
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  shop: Object,
  products: Array
})
</script>
