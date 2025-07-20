<template>
<SellerDashboardLayout>
  <div class="container mt-5 text-center">
    <h3 class="text-muted mb-4">You don’t have a shop yet</h3>

    <div class="row justify-content-center">
      <div class="col-md-4 mb-3">
        <div class="card p-3 h-100 shadow">
          <h5>🛍️ Your Shop</h5>
          <p>No shop yet</p>
          <Link href="/seller/shop/edit" class="btn btn-sm btn-primary">Create/Edit Shop</Link>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="card p-3 h-100 shadow">
          <h5>📦 Products</h5>
          <p>Manage your items for sale</p>
          <Link href="/seller/products" class="btn btn-sm btn-secondary">Go to Products</Link>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="card p-3 h-100 shadow">
          <h5>🧾 Orders</h5>
          <p>Track and manage customer orders</p>
          <Link href="/seller/orders" class="btn btn-sm btn-success">View Orders</Link>
        </div>
      </div>
    </div>
  </div>
  </SellerDashboardLayout>
</template>

<script setup>
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue';
import { Link } from '@inertiajs/vue3'
</script>
