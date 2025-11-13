<template>
  <DashboardLayout>
    <div class="container py-1">
      <!-- 🏷️ Page Header -->
      <div class="text-center mb-4">
        <h2 class="fw-bold text-primary mb-2">
          <i class="bi bi-bag-check-fill me-2"></i> Products
        </h2>
        <p class="text-muted small mx-auto w-75">
          Explore a curated selection of local and handmade products crafted by skilled artisans. 
          Use the search and filters below to find the perfect item that suits your style and needs.
        </p>
      </div>

      <!-- 🔍 Search & Filter Bar -->
      <form @submit.prevent="searchProducts" class="row g-2 mb-4 sticky-search rounded-4 shadow-sm px-3 py-2 bg-white backdrop-blur">
        <!-- Search -->
        <div class="col-9 col-md-10">
          <div class="input-group rounded-pill overflow-hidden border border-primary-subtle">
            <input
              type="text"
              class="form-control border-0 shadow-none"
              placeholder="Search for product name..."
              v-model="search"
              aria-label="Search products"
            />
            <span class="input-group-text bg-transparent border-0">
              <i class="bi bi-search text-primary"></i>
            </span>
          </div>
        </div>

        <!-- Search Button -->
        <!-- <div class="col-12 col-md-3">
          <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold shadow-sm">
            <i class="bi bi-search me-1"></i> Search
          </button>
        </div> -->

        <!-- Category Dropdown -->
        <div class="col-3 col-md-2">
          <select v-model="selectedCategory" class="form-select rounded-pill border-primary-subtle">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>
      </form>

      <!-- ✨ Product Cards -->
      <div class="row g-3">
        <div
          class="col-6 col-sm-6 col-md-4 col-lg-3 mb-3"
          v-for="product in products.data"
          :key="product.id"
        >
          <Link
            :href="`/product/${product.id}`"
            class="text-decoration-none text-dark"
          >
            <div class="card h-100 border-0 shadow-sm rounded-4 product-card fade-in">
              <div class="position-relative image-wrapper rounded-top-4 overflow-hidden">
                <img
                  :src="product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/500x300?text=No+Image'"
                  class="card-img-top"
                  alt="Product Image"
                  loading="lazy"
                  :class="{ 'out-of-stock-img': product.stock === 0 }"
                />

                <!-- 🧵 Can Customize badge (Left) -->
                <span
                  v-if="
                    product.customizable_product &&
                    (
                      product.customizable_product.allow_color ||
                      product.customizable_product.allow_size ||
                      product.customizable_product.allow_material ||
                      product.customizable_product.allow_pattern
                    )
                  "
                  class="customize-badge badge-left position-absolute d-flex align-items-center"
                >
                  <i class="bi bi-tools me-1"></i> Can Customize
                </span>

                <!-- 🚫 Out of Stock badge (Right) -->
                <span
                  v-if="Number(product.stock) === 0"
                  class="out-of-stock-badge badge-right position-absolute d-flex align-items-center"
                >
                  <i class="bi bi-exclamation-octagon me-1"></i> Out of Stock
                </span>
              </div>

              <div class="card-body d-flex flex-column px-3 py-2">
                <h6 class="card-title text-success fw-semibold mb-1 ellipsis-two">
                  {{ product.name }}
                </h6>

                <!-- Category -->
                <p class="text-muted small mb-1">
                  <i class="bi bi-tag-fill me-1"></i>
                  {{ product.category?.name ?? '—' }}
                </p>

                <p class="card-text small text-muted mb-1 ellipsis-three">
                  {{ product.description }}
                </p>

                <p class="card-text small text-muted mb-1 ellipsis-three">Stock: {{ product.stock }}</p>

                <!-- Rating -->
                <div class="d-flex align-items-center mb-1 flex-wrap">
                  <div class="rating d-flex align-items-center">
                    <span class="fw-bold small text-secondary me-1">
                      {{ (product.average_rating ?? 0).toFixed(1) }}
                    </span>
                    <div class="stars me-1">
                      <i
                        v-for="i in 5"
                        :key="`star-${product.id}-${i}`"
                        :class="[
                          'bi',
                          i <= Math.round(product.average_rating ?? 0)
                            ? 'bi-star-fill text-warning'
                            : 'bi-star text-secondary'
                        ]"
                      ></i>
                    </div>
                    <span class="small text-muted">({{ product.ratings_count ?? 0 }})</span>
                  </div>
                </div>

                <div class="price fw-bold text-dark mb-1">
                  ₱{{ product.price ?? '0.00' }}
                </div>

                <div class="d-flex justify-content-between small text-muted mb-1">
                  <div>Sold: <strong>{{ product.total_sold ?? 0 }}</strong></div>
                </div>

                <div class="shop-info mb-1 small text-muted">
                  <div class="ellipsis-one">
                    Sold by:
                    <strong>
                      {{ product.shop?.user?.first_name ?? 'Unknown' }}
                      {{ product.shop?.user?.last_name ?? 'Unknown' }}
                    </strong>
                  </div>
                  <span v-if="product.shop?.address" class="ellipsis-one text-dark">
                    <i class="bi bi-geo-alt-fill me-1"></i>
                    {{ product.shop.address }}
                  </span>
                </div>

                <div class="mt-auto text-center">
                  <span class="badge bg-gradient-primary text-primary rounded-pill px-3 py-2 small shadow-sm">
                    <i class="bi bi-eye me-1"></i> View details
                  </span>
                </div>
              </div>
            </div>
          </Link>
        </div>
      </div>

      <!-- 🔢 Pagination -->
      <nav v-if="products.links.length > 3" class="d-flex justify-content-center mt-4">
        <ul class="pagination pagination-rounded shadow-sm">
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

      <!-- 🚫 Empty State -->
      <div v-if="products.data.length === 0" class="text-center py-5 text-muted">
        <i class="bi bi-box-seam display-4 d-block mb-2 text-primary"></i>
        <h5>No products found</h5>
        <p>Try adjusting your search or filters.</p>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { ref, defineProps, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  products: Object,
  search: { type: String, default: '' },
  categories: Array,
  category: { type: [String, Number], default: '' }
})

const search = ref(props.search)
const selectedCategory = ref(props.category ?? '')

watch([search, selectedCategory], ([newSearch, newCategory]) => {
  router.get('/view', { search: newSearch, category: newCategory }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
})

const searchProducts = () => {
  router.get('/view', { search: search.value, category: selectedCategory.value }, {
    preserveScroll: true,
    preserveState: true,
  })
}
</script>

<style scoped>
/* 🪄 General Styles */
body {
  background-color: #f9fafc;
  color: #333;
}

/* ✨ Fade-in animation */
.fade-in {
  animation: fadeIn 0.4s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* 🌿 Product Cards */
.product-card {
  transition: all 0.3s ease-in-out;
}
.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(13, 110, 253, 0.2);
}

/* 🧵 Can Customize badge */
.customize-badge {
  top: 10px;
  background-color: #0d6efd;
  color: #fff;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 0.35em 0.6em;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
  z-index: 3;
}

/* 🚫 Out of Stock badge */
.out-of-stock-badge {
  top: 10px;
  background-color: #dc3545;
  color: #fff;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 0.35em 0.6em;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
  z-index: 3;
}

.badge-left {
  left: 10px;
}
.badge-right {
  right: 10px;
}

/* 🖼 Dim image if out of stock */
.out-of-stock-img {
  filter: grayscale(70%) brightness(70%);
}

/* 🖼 Image Wrapper */
.image-wrapper {
  width: 100%;
  padding-top: 75%;
  position: relative;
  overflow: hidden;
}
.image-wrapper img {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform .3s ease-in-out;
}
.product-card:hover .image-wrapper img {
  transform: scale(1.05);
}

/* 📍 Sticky Search */
.sticky-search {
  position: sticky;
  top: 0;
  z-index: 100;
  transition: all 0.3s ease;
}

/* ⭐ Ratings */
.stars i {
  font-size: 0.9rem;
}

/* 🔢 Pagination */
.pagination .page-link {
  color: #0b84ff;
  border-radius: 50% !important;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .25s ease;
}
.pagination .page-item.active .page-link {
  background-color: #0b84ff;
  color: #fff;
  box-shadow: 0 3px 10px rgba(11, 132, 255, 0.3);
}
</style>
