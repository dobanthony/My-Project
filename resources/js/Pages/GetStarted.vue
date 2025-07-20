<template>
  <Navbars>
    <div class="container py-5">
      <h1 class="mb-4 fw-bold">Getting Started</h1>

      <!-- Step 1: Create Account -->
      <section class="border rounded p-4 mb-4 step-card">
        <h4>1. Create an Account</h4>
        <p class="text-secondary">
          Sign up for free to unlock personalized shopping, order tracking, and exclusive offers.
          You can register as a buyer or a seller depending on your needs.
        </p>
        <router-link to="/register" class="btn btn-outline-primary btn-sm">Sign Up Now</router-link>
      </section>

      <!-- Step 2: Browse Products -->
      <section class="border rounded p-4 mb-4 step-card">
        <h4>2. Browse Our Products</h4>
        <p class="text-secondary">
          Explore our curated catalog of categories including fashion, electronics, handmade goods, and more.
          Use filters to find exactly what you need.
        </p>
        <router-link to="/shop" class="btn btn-outline-secondary btn-sm">Shop Now</router-link>
      </section>

      <!-- Step 3: Add to Cart & Checkout -->
      <section class="border rounded p-4 mb-4 step-card">
        <h4>3. Add to Cart & Checkout</h4>
        <p class="text-secondary">
          Once you've found your item, add it to your cart and proceed to secure checkout using various payment methods, including GCash and bank transfer.
        </p>
      </section>

      <!-- Step 4: Track Your Order -->
      <section class="border rounded p-4 mb-4 step-card">
        <h4>4. Track Your Order</h4>
        <p class="text-secondary">
          Stay updated with real-time tracking. You’ll receive email or SMS updates as your order progresses.
        </p>
      </section>

      <!-- Step 5: Need Help? -->
      <section class="border rounded p-4 step-card">
        <h4>5. Need Help?</h4>
        <p class="text-secondary">
          Our support team is available 24/7 to assist you.
          Visit the <router-link to="/contact">Contact Page</router-link> or email us at support@shopsmart.com.
        </p>
      </section>
    </div>
  </Navbars>
</template>

<script setup>
import Navbars from '../Layouts/Navbars.vue';
</script>

<style scoped>
.step-card {
  background-color: #f8f9fa; /* light gray */
  transition: 0.3s;
}
.step-card:hover {
  box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05);
  background-color: #ffffff;
}
</style>
