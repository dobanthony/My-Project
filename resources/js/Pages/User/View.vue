<template>
  <DashboardLayout>
    <div class="container py-3">
      <!-- Search Form -->
      <form @submit.prevent="searchProducts" class="row g-2 mb-3 sticky-search">
        <div class="col-9 col-sm-10">
          <div class="input-group">
            <input
              type="text"
              class="form-control"
              placeholder="Search products..."
              v-model="search"
              aria-label="Search products"
            />
          </div>
        </div>
        <div class="col-3 col-sm-2">
          <button type="submit" class="btn btn-success w-100">Search</button>
        </div>
      </form>

      <!-- Product Cards -->
      <div class="row g-2">
        <div
          class="col-6 col-sm-6 col-md-4 col-lg-3 mb-3"
          v-for="product in products.data"
          :key="product.id"
        >
          <Link
            :href="`/product/${product.id}`"
            class="text-decoration-none text-dark card-link"
          >
            <div class="card h-100 shadow-sm product-card">
              <div class="position-relative image-wrapper">
                <img
                  :src="product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/500x300?text=No+Image'"
                  class="card-img-top"
                  alt="Product Image"
                  loading="lazy"
                />
              </div>
              <div class="card-body d-flex flex-column px-2 py-2">
                <h6 class="card-title text-dark mb-1 ellipsis-two">
                  {{ product.name }}
                </h6>
                <p class="card-text small text-muted mb-1 ellipsis-three">
                  {{ product.description }}
                </p>

                <!-- Rating / Sold row -->
                <div class="d-flex align-items-center mb-1 gap-2 flex-wrap">
                  <div class="rating d-flex align-items-center">
                    <span class="me-1 fw-bold small text-success">
                      {{ (product.average_rating ?? 0).toFixed(1) }}
                    </span>
                    <div class="stars me-1">
                      <i
                        v-for="i in 5"
                        :key="`star-${product.id}-${i}`"
                        :class="[
                          'bi',
                          i <= Math.round(product.average_rating ?? 0)
                            ? 'bi-star-fill'
                            : 'bi-star'
                        ]"
                        aria-hidden="true"
                      ></i>
                    </div>
                    <span class="small text-muted">({{ product.ratings_count ?? 0 }})</span>
                  </div>
                </div>

                <div class="price mb-1 fw-bold text-success">
                  ₱{{ product.price ?? '0.00' }}
                </div>

                <div class="d-flex justify-content-between small text-muted mb-1">
                  <div class="sold">
                    Sold: <strong>{{ product.total_sold ?? 0 }}</strong>
                  </div>
                </div>

                <div class="shop-info mb-1 small text-muted">
                  <div class="mb-1 ellipsis-one">
                    Sold by: <strong>{{ product.shop?.user?.first_name ?? 'Unknown' }} {{ product.shop?.user?.last_name ?? 'Unknown' }}</strong>
                  </div>
                  <div v-if="product.shop?.address" class="ellipsis-one">
                    <i class="bi bi-geo-alt-fill me-1"></i>
                    {{ product.shop.address }}
                  </div>
                </div>

                <div class="mt-auto">
                  <span class="badge bg-success w-100 text-center small">
                    View details
                  </span>
                </div>
              </div>
            </div>
          </Link>
        </div>
      </div>

      <!-- Pagination -->
      <nav v-if="products.links.length > 3" class="d-flex justify-content-center mt-3">
        <ul class="pagination flex-wrap">
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

      <!-- No Products -->
      <div v-if="products.data.length === 0" class="alert alert-success mt-3">
        No products found.
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { ref, defineProps } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  products: Object,
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
/* Card tweaks */
.product-card {
  border-radius: 8px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: box-shadow .25s ease-in-out, transform .25s ease-in-out, border-color .25s ease-in-out;
  background: white;
  border: 1px solid transparent;
}
.product-card:hover {
  box-shadow: 0 8px 20px rgba(40, 167, 69, 0.25); /* green glow shadow */
  transform: translateY(-4px);
  border-color: #28a745; /* green border on hover */
}
.image-wrapper {
  width: 100%;
  padding-top: 75%;
  position: relative;
  overflow: hidden;
}
.image-wrapper img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform .3s ease-in-out;
}
.product-card:hover .image-wrapper img {
  transform: scale(1.05); /* slight zoom */
}

/* Text truncation helpers */
.ellipsis-one {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.ellipsis-two {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.ellipsis-three {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Search bar sticky on mobile */
.sticky-search {
  position: sticky;
  top: 0;
  background: white;
  z-index: 10;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

/* Stars coloring */
.stars i.bi-star-fill {
  color: #ffc107;
}
.stars i.bi-star {
  color: #ddd;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .card-title {
    font-size: 0.9rem;
  }
  .product-card .price {
    font-size: 1rem;
  }
  .rating i {
    font-size: 0.9rem;
  }
  .shop-info {
    font-size: 0.65rem;
  }
  .sold {
    font-size: 0.65rem;
  }
  .badge {
    padding: 0.35em 0.5em;
  }
}

/* Input focus */
input.form-control:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
}

/* Pagination styling */
.pagination .page-link {
  color: #28a745;
  background-color: #fff;
  border-color: #28a745;
}
.pagination .page-link:hover {
  color: #fff;
  background-color: #28a745;
  border-color: #fff;
}
.pagination .page-link:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
}
.page-item.active .page-link {
  background-color: #28a745;
  border-color: #28a745;
  color: #fff;
}
</style>
