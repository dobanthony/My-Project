<template>
  <Navbars>
    <div>
      <!-- 💚 Hero Section -->
      <section 
        class="hero-section d-flex justify-content-center align-items-center text-center"
        style="
          background-image: url('/images/bg.jpg'); 
          background-size: cover; 
          background-position: center; 
          background-repeat: no-repeat;
          height: 70vh;
          position: relative;
        "
      >
        <!-- Overlay: slightly visible by default, darkens more on hover -->
        <div class="overlay w-100"></div>

        <!-- Hero content centered above overlay -->
        <div class="hero-content text-white">
          <h1 class="display-5 fw-bold">
            Welcome to <span class="text-success">CraftSmart</span>
          </h1>
          <p class="lead my-3">
            Discover quality products, support local sellers, and enjoy a seamless shopping experience.
          </p>
          <button
            class="btn btn-success btn-lg mt-3 px-4 py-2"
            @click="scrollToFeatured"
          >
            Get Started
          </button>
        </div>
      </section>

      <!-- 🛍️ Featured Products -->
      <section id="featured" class="py-5 bg-white border-bottom">
        <div class="container">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">
              <i class="bi bi-bag-heart-fill text-success me-2"></i>Products
            </h4>
            <div class="mb-4">
              <div class="input-group">
                <input
                  v-model="searchQuery"
                  type="text"
                  class="form-control"
                  placeholder="Search products..."
                  @keyup.enter="searchProducts"
                />
                <button
                  class="btn btn-primary"
                  type="button"
                  @click="searchProducts"
                >
                  <i class="bi bi-search"></i> Search
                </button>
              </div>
            </div>
          </div>

          <!-- Product -->
          <div class="row g-3">
            <!-- Show products if available -->
            <div
              v-if="products && products.length > 0"
              v-for="product in products"
              :key="product.id"
              class="col-6 col-sm-6 col-md-4 col-lg-3"
            >
              <div
                class="card h-100 border-0 shadow-sm product-card"
                @click="goToGuestProduct(product.id)"
                role="button"
              >
                <div class="position-relative image-wrapper">
                  <img
                    :src="product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/500x300?text=No+Image'"
                    class="card-img-top"
                    alt="Product Image"
                    loading="lazy"
                  />
                  <span
                    v-if="product.eco_friendly"
                    class="badge eco-badge position-absolute"
                  >
                    <i class="bi bi-leaf-fill me-1"></i> Eco-friendly
                  </span>
                </div>

                <div class="card-body d-flex flex-column px-2 py-2">
                  <h6 class="fw-bold text-success mb-1 ellipsis-two">
                    {{ product.name }}
                  </h6>
                  <p class="small text-muted ellipsis-three mb-2">
                    {{ product.description }}
                  </p>

                  <div class="fw-semibold text-dark mb-1">
                    ₱{{ product.price }}
                  </div>

                  <div class="small text-muted">
                    <i class="bi bi-star-fill text-warning"></i>
                    {{ product.average_rating }} ({{ product.ratings_count }}) •
                    Sold: {{ product.total_sold }}
                  </div>

                  <div class="small text-muted mt-1">
                    <i class="bi bi-shop"></i>
                    {{ product.shop?.user?.first_name ?? 'Unknown' }}
                    {{ product.shop?.user?.last_name ?? '' }}
                  </div>

                  <div class="mt-auto">
                    <span class="badge bg-primary w-100 text-center small">
                      View details
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- No Products Found -->
            <div v-else class="text-center py-5 text-muted">
              <i class="bi bi-emoji-frown display-3 text-secondary"></i>
              <p class="mt-3 fs-5">No products found. Try searching for something else.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 💎 Why Shop -->
      <section class="py-5 bg-white">
        <div class="container">
          <h2 class="h3 fw-semibold text-success text-center mb-4">Why Shop With Us?</h2>
          <div class="row text-center">
            <div class="col-md-4 mb-4 feature-card">
              <i class="bi bi-truck display-4 text-success"></i>
              <h5 class="fw-bold mt-3 text-dark">Fast & Reliable Delivery</h5>
              <p class="text-muted">Receive your orders quickly with our delivery services.</p>
            </div>
            <div class="col-md-4 mb-4 feature-card">
              <i class="bi bi-shield-lock display-4 text-success"></i>
              <h5 class="fw-bold mt-3 text-dark">Secure Shopping</h5>
              <p class="text-muted">Shop with confidence using secure payment methods.</p>
            </div>
            <div class="col-md-4 mb-4 feature-card">
              <i class="bi bi-bag-heart display-4 text-success"></i>
              <h5 class="fw-bold mt-3 text-dark">Support Local Sellers</h5>
              <p class="text-muted">Discover authentic products from artisans and small businesses in the Philippines.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 💚 CTA -->
      <section class="py-5 bg-success text-white text-center">
        <div class="container">
          <h2 class="h3 fw-bold">Join Thousands of Happy Customers</h2>
          <p class="lead mb-4">Start your journey with us today.</p>
          <Link href="/register" class="btn btn-outline-light btn-lg px-4 py-2">
            Register Now
          </Link>
        </div>
      </section>
    </div>
  </Navbars>
</template>

<script setup>
import Navbars from '@/Layouts/Navbars.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { route } from 'ziggy-js'

defineProps({
  products: Array
})

const searchQuery = ref('')

const searchProducts = () => {
  router.visit('/', {
    method: 'get',
    data: { search: searchQuery.value },
    preserveScroll: true,
    preserveState: true,
  })
}
const goToGuestProduct = (id) => {
  router.visit(route('products.guest.show', id))
}

// 🌟 Smooth scroll to Featured Products
const scrollToFeatured = () => {
  const featuredSection = document.getElementById('featured')
  if (featuredSection) {
    const offset = 80 // adjust if navbar overlaps section
    const topPosition = featuredSection.getBoundingClientRect().top + window.pageYOffset - offset
    window.scrollTo({ top: topPosition, behavior: 'smooth' })
  }
}
</script>

<style scoped>
.hero-section .overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  /* Slightly transparent by default */
  background: rgba(0, 0, 0, 0.15);
  transition: background 0.4s ease-in-out;
  z-index: 1;
}

/* Darker overlay on hover */
.hero-section:hover .overlay {
  background: rgba(0, 0, 0, 0.4);
}

/* Hero content above overlay */
.hero-section .hero-content {
  position: relative;
  z-index: 2;
  max-width: 800px;
  transition: transform 0.3s ease;
}

/* Optional slight zoom on hover */
.hero-section:hover .hero-content {
  transform: scale(1.02);
}

/* Responsive text sizes */
.hero-section h1 {
  font-size: clamp(2rem, 5vw, 3.5rem);
}
.hero-section p {
  font-size: clamp(1rem, 2.2vw, 1.3rem);
  margin-bottom: 1.5rem;
}
.hero-section button {
  font-size: 1.1rem;
}

/* 🛍️ Product Cards */
.product-card {
  border-radius: 10px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: all 0.25s ease-in-out;
  background: #fff;
  cursor: pointer;
}
.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 18px rgba(13, 110, 253, 0.25);
}
.eco-badge {
  top: 10px;
  left: 10px;
  background-color: #198754;
  color: white;
  font-size: 0.7rem;
  border-radius: 12px;
  padding: 0.4em 0.6em;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
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
  transition: transform 0.3s ease-in-out;
}
.product-card:hover .image-wrapper img {
  transform: scale(1.05);
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

/* ❌ No Product Message */
.text-center i {
  opacity: 0.8;
}

/* 💎 Features */
.feature-card {
  transition: all 0.3s ease;
  padding: 24px;
  border-radius: 12px;
}
.feature-card:hover {
  background-color: #f8f9fa;
  transform: translateY(-6px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

/* 📱 Mobile Optimizations */
@media (max-width: 768px) {
  .product-card {
    border-radius: 8px;
  }
  .feature-card {
    padding: 16px;
  }
  .hero-section {
    padding: 3rem 1rem;
  }
}
</style>
