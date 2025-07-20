<template>
  <Navbars>
    <div class="container py-5">

      <!-- Hero Section -->
      <div class="text-center mb-5">
        <h3 class="display-5 fw-bold text-dark">About Us</h3>
        <p class="lead text-muted">
          Empowering your lifestyle through quality products, seamless shopping, and a commitment to excellence.
        </p>
      </div>

      <!-- Who We Are -->
      <div class="card card-hover shadow-sm mb-4">
        <div class="card-body text-center">
          <h4 class="card-title fw-semibold text-dark mb-3">
            <i class="bi bi-people-fill me-2 text-primary"></i>Who We Are
          </h4>
          <p class="card-text text-muted fs-5">
            <strong class="text-success">CraftSmart</strong> is a people-first eCommerce platform designed to make shopping simple, secure,
            and accessible. Whether you're buying essentials or discovering local artisan crafts, our platform connects
            customers with trusted sellers and high-quality products across the country.
          </p>
        </div>
      </div>

      <!-- Mission -->
      <div class="card card-hover shadow-sm mb-4">
        <div class="card-body text-center">
          <h4 class="card-title fw-semibold text-dark mb-3">
            <i class="bi bi-bullseye me-2 text-success"></i>Our Mission
          </h4>
          <p class="card-text text-muted fs-5">
            To empower every Filipino — from buyers to small business owners — through a reliable and inclusive digital marketplace.
            We aim to simplify shopping by offering fast delivery, excellent support, and secure transactions nationwide.
          </p>
        </div>
      </div>

      <!-- Our Story -->
      <div class="card card-hover shadow-sm mb-4">
        <div class="card-body text-center">
          <h4 class="card-title fw-semibold text-dark mb-3">
            <i class="bi bi-book-half me-2 text-warning"></i>Our Story
          </h4>
          <p class="card-text text-muted fs-5">
            ShopSmart was born in 2022 in Sorsogon, Philippines, starting as a small family-run retail shop.
            Seeing the shift in consumer habits, we transitioned online to help local businesses thrive and reach a broader market.
            Today, we serve thousands of Filipinos across the country.
          </p>
        </div>
      </div>

      <!-- Why Choose Us -->
      <div class="card card-hover shadow-sm mb-4">
        <div class="card-body">
          <h4 class="card-title fw-semibold text-dark mb-4 text-center">
            <i class="bi bi-hand-thumbs-up me-2 text-danger"></i>Why Choose ShopSmart?
          </h4>
          <div class="row text-muted fs-5">
            <div class="col-md-6 mb-3">
              <ul class="list-unstyled">
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Curated selection of top-rated products</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Transparent pricing with exclusive deals</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Secure and flexible payment methods</li>
              </ul>
            </div>
            <div class="col-md-6 mb-3">
              <ul class="list-unstyled">
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Efficient nationwide delivery</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Friendly, responsive customer support</li>
                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Partnering with local artisans and MSMEs</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Contact Info -->
      <div class="card card-hover shadow-sm mb-4">
        <div class="card-body text-center">
          <h4 class="card-title fw-semibold text-dark mb-3">
            <i class="bi bi-envelope-at-fill me-2 text-info"></i>Get in Touch
          </h4>
          <p class="text-muted fs-5 mb-1">
            📍 <strong>Head Office:</strong> 123 Market Street, Sorsogon City, Philippines
          </p>
          <p class="text-muted fs-5 mb-1">
            📞 <strong>Phone:</strong> +63 912 345 6789
          </p>
          <p class="text-muted fs-5">
            📧 <strong>Email:</strong> support@shopsmart.com
          </p>
        </div>
      </div>

    </div>
  </Navbars>
</template>

<script setup>
import Navbars from '../Layouts/Navbars.vue';
</script>

<style scoped>
.card-hover {
  transition: all 0.3s ease;
  padding: 24px;
  border-radius: 12px;
   background-color: #f8f9fa;
}
.card-hover:hover {
  transform: translateY(-6px);
  box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.1);
}
</style>
