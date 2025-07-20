<template>
  <Navbars>
    <div>
      <!-- Hero Section -->
      <section class="bg-light text-center py-5 border-bottom">
        <div class="container">
          <h1 class="display-5 fw-bold text-dark">Welcome to <span class="text-success">CraftSmart</span></h1>
          <p class="lead text-muted">
            Discover quality products, support local sellers, and enjoy a seamless shopping experience.
          </p>
          <Link href="/getStarted" class="btn btn-success btn-lg mt-3">
            Get Started
          </Link>
        </div>
      </section>

      <!-- Features Section with Hover -->
      <section class="py-5 bg-white">
        <div class="container">
          <h2 class="h3 fw-semibold text-success text-center mb-4">Why Shop With Us?</h2>
          <div class="row text-center">
            <div class="col-md-4 mb-4 feature-card">
              <i class="bi bi-truck display-4 text-success"></i>
              <h5 class="fw-bold mt-3 text-dark">Fast & Reliable Delivery</h5>
              <p class="text-muted">
                Receive your orders quickly with nationwide delivery services.
              </p>
            </div>
            <div class="col-md-4 mb-4 feature-card">
              <i class="bi bi-shield-lock display-4 text-success"></i>
              <h5 class="fw-bold mt-3 text-dark">Secure Shopping</h5>
              <p class="text-muted">
                Shop with confidence using secure payment methods and encrypted checkout.
              </p>
            </div>
            <div class="col-md-4 mb-4 feature-card">
              <i class="bi bi-bag-heart display-4 text-success"></i>
              <h5 class="fw-bold mt-3 text-dark">Support Local Sellers</h5>
              <p class="text-muted">
                Discover authentic products from artisans and small businesses in the Philippines.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Call to Action -->
      <section class="py-5 bg-success text-white text-center">
        <div class="container">
          <h2 class="h3 fw-bold">Join Thousands of Happy Customers</h2>
          <p class="lead mb-4">Start your journey with us today.</p>
          <Link href="/register" class="btn btn-outline-light btn-lg">
            Sign Up Now
          </Link>
        </div>
      </section>
    </div>
  </Navbars>
</template>

<script setup>
import Navbars from '@/Layouts/Navbars.vue';
import { Link } from '@inertiajs/vue3'
</script>

<style scoped>
.feature-card {
  transition: all 0.3s ease;
  padding: 24px;
  border-radius: 12px;
}

.feature-card:hover {
  background-color: #f8f9fa; /* light gray */
  transform: translateY(-6px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}
</style>
