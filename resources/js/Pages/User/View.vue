<template>
   <DashboardLayout>
      <div class="container">
        <div class="bg-success text-white p-2 rounded-top-4">
          <h4 class="text-white">Products</h4>
        </div>

        <!-- 🔍 Search Form -->
        <form @submit.prevent="searchProducts" class="row g-2 mb-4 mt-4">
          <div class="col-md-10">
            <input
              type="text"
              class="form-control"
              placeholder="Search products..."
              v-model="search"
            />
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Search</button>
          </div>
        </form>

        <!-- 📦 Product Cards -->
        <div class="row">
          <div
            class="col-6 col-md-4 col-lg-3 mb-4"
            v-for="product in products.data"
            :key="product.id"
          >
            <Link
              :href="`/product/${product.id}`"
              class="text-decoration-none text-dark"
            >
              <div class="card h-100 shadow-sm border border-light">
                <img
                  :src="product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/500x300?text=No+Image'"
                  class="card-img-top"
                  alt="Product Image"
                  style="height: 200px; object-fit: cover;"
                />
                <div class="card-body">
                  <h5 class="card-title text-dark">{{ product.name }}</h5>
                  <p class="card-text small text-muted">
                    {{ product.description }}
                  </p>
                  <p class="fw-bold text-success">
                    ₱{{ product.price ?? '0.00' }}
                  </p>
                  <p class="text-muted small">
                    Sold by: <strong>{{ product.shop?.user?.name ?? 'Unknown' }}</strong>
                  </p>
                </div>
              </div>
            </Link>
          </div>
        </div>

        <!-- 🔢 Traditional Pagination -->
        <nav v-if="products.links.length > 3" class="d-flex justify-content-center mt-4">
          <ul class="pagination">
            <li
              v-for="(link, index) in products.links"
              :key="index"
              :class="['page-item', { active: link.active, disabled: !link.url }]"
            >
              <Link
                class="page-link"
                :href="link.url || ''"
                v-html="link.label"
                preserve-scroll
                preserve-state
              />
            </li>
          </ul>
        </nav>

        <!-- ℹ️ No Products Message -->
        <div v-if="products.data.length === 0" class="alert alert-info mt-3">
          No products found.
        </div>
      </div>
   </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  products: Object, // pagination object (data, links, meta)
  search: {
    type: String,
    default: ''
  }
})

const search = ref(props.search)

const searchProducts = () => {
  router.get(
    '/view',
    { search: search.value },
    {
      preserveScroll: true,
      preserveState: true,
    }
  )
}
</script>

<style scoped>
input.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25); /* green with 25% opacity */
}
.pagination .page-link {
  color: #28a745; /* green */
  background-color: rgb(255, 255, 255);
  border-color: #28a745; /* green */
}
.pagination .page-link:hover {
  color: white;
  background-color: #28a745; /* green */
  border-color: #ffffff;
}
.pagination .page-link:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25); /* black with 50% opacity */
}
</style>

