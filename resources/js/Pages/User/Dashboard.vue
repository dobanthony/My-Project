<template>
  <Head title="Dashboard" />

  <DashboardLayout>
    <div class="container py-3">

      <!-- 👋 User Greeting -->
      <!-- <div class="mb-5 text-start">
        <h4 class="fw-bold text-success">
          👋 Hello, {{ user ? user.first_name : '' }}!
        </h4>
        <p class="text-muted small mb-0">
          Welcome back to CraftSmart Artisan. Here's what's new today.
        </p>
      </div> -->

      <!-- 🌟 Introduction Section -->
      <div class="text-center mb-5 px-3">
        <h2 class="fw-bold text-secondary mb-3">
          <i class="bi bi-shop text-success me-2"></i> Welcome <span class="text-primary">{{ user ? user.first_name : '' }}</span> to CraftSmart Artisan
        </h2>
        <p class="text-muted fs-6 mx-auto" style="max-width: 750px;">
          CraftSmart Artisan is your trusted online marketplace connecting creative artisans and passionate shoppers.
          Discover handmade crafts, support local creators, and enjoy a seamless shopping experience from browsing to delivery.
        </p>
      </div>

      <!-- 🌀 E-commerce Journey Carousel -->
      <div
        id="craftsmartCarousel"
        class="carousel slide mb-5 carousel-hover rounded-4 shadow-sm"
        data-bs-ride="carousel"
        data-bs-interval="2500"
        data-bs-pause="hover"
      >
        <div class="carousel-inner">

          <div
            v-for="(item, index) in carouselItems"
            :key="index"
            :class="['carousel-item', { active: index === 0 }]"
          >
            <div class="d-flex flex-column align-items-center justify-content-center p-4 bg-light rounded-4">
              <img :src="item.img" class="d-block mb-3" :alt="item.title" width="150">
              <h5 class="fw-bold text-primary">{{ item.title }}</h5>
              <p class="text-muted small text-center mb-0">{{ item.desc }}</p>
            </div>
          </div>

        </div>

        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#craftsmartCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#craftsmartCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>

      <!-- 📢 Announcements -->
      <div class="d-flex justify-content-between align-items-center bg-primary text-white p-2 rounded mb-4 announcement-header">
        <strong class="ms-2"><i class="bi bi-megaphone me-2"></i> Latest Announcements</strong>
      </div>

      <div v-if="announcements.length === 0" class="alert alert-success text-center shadow-sm rounded-3">
        No announcements have been posted yet.
      </div>

      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
        <div v-for="announcement in announcements" :key="announcement.id" class="col">
          <div class="card h-100 border-0 shadow-sm rounded-4 announcement-card">
            <div class="card-body d-flex flex-column justify-content-between">
              <div>
                <h5 class="card-title p-2 rounded bg-light text-success fw-semibold">
                  {{ announcement.title }}
                </h5>
                <p class="card-text text-muted mt-2">{{ truncateContent(announcement.content) }}</p>
              </div>
              <div class="mt-3 text-end">
                <span class="badge bg-light text-secondary">
                  <i class="bi bi-calendar3 text-success"></i> {{ formatDate(announcement.created_at) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 🕒 Recent Orders -->
      <div class="bg-white shadow-sm rounded-4 p-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold text-success mb-0">
            <i class="bi bi-bag-check me-2"></i> Recent Orders
          </h5>
          <Link href="/my-orders" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-eye me-1"></i> View All
          </Link>
        </div>

        <div v-if="recentOrders.length === 0" class="text-center text-muted py-3">
          No recent orders found.
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Product</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(order, index) in recentOrders" :key="order.id">
                <td>{{ index + 1 }}</td>
                <td>{{ order.product }}</td>
                <td>
                  <span
                    class="badge"
                    :class="{
                      'bg-success': order.status === 'Delivered',
                      'bg-warning text-dark': order.status === 'Pending',
                      'bg-danger': order.status === 'Cancelled'
                    }"
                  >
                    {{ order.status }}
                  </span>
                </td>
                <td>{{ formatDate(order.date) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const { props } = usePage()
const user = props.user || null
const announcements = props.announcements || []

const recentOrders = ref([
  { id: 1, product: 'Handcrafted Wooden Bowl', status: 'Delivered', date: '2025-10-15T14:30:00' },
  { id: 2, product: 'Custom Clay Mug', status: 'Pending', date: '2025-10-17T09:00:00' },
  { id: 3, product: 'Woven Basket', status: 'Cancelled', date: '2025-10-10T16:45:00' },
])

const carouselItems = [
  { img: '/images/shopping.svg', title: 'Browse Unique Crafts', desc: 'Explore our collection of handmade and artisan-crafted goods.' },
  { img: '/images/support.svg', title: 'Show Support', desc: 'Support local artisans by purchasing and promoting their crafts.' },
  { img: '/images/add_to_cart.svg', title: 'Add to Cart', desc: 'Easily add your favorite crafts to your cart for a smooth checkout.' },
  { img: '/images/successful_purchase.svg', title: 'Secure Purchase', desc: 'Experience safe, reliable, and seamless transactions.' },
  { img: '/images/delivery.svg', title: 'Fast Deliveries', desc: 'Your handcrafted items delivered swiftly and securely.' },
  { img: '/images/order_confirmed.svg', title: 'Order Confirmed', desc: 'Get notified instantly once your order is confirmed.' },
]

function formatDate(date) {
  const d = new Date(date)
  return d.toLocaleString('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
  })
}

function truncateContent(content, maxLength = 120) {
  if (content.length <= maxLength) return content
  return content.slice(0, maxLength) + '...'
}
</script>

<style scoped>
/* Carousel */
.carousel-item img {
  max-height: 180px;
  object-fit: contain;
}

/* Hide carousel controls until hover */
.carousel-hover .carousel-control-prev,
.carousel-hover .carousel-control-next {
  opacity: 0;
  transition: opacity 0.3s ease;
}
.carousel-hover:hover .carousel-control-prev,
.carousel-hover:hover .carousel-control-next {
  opacity: 1;
}
.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: invert(1);
}
/* Announcement header */
.announcement-header {
  background: linear-gradient(90deg, #4db6ac, #80cbc4); /* Smooth teal gradient example */
  color: #fff;
  box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);
}
/* Announcement cards */
.announcement-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.announcement-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 1rem 2rem rgba(0,0,0,0.08);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .carousel-item img { max-height: 140px; }
  .text-center h2 { font-size: 1.5rem; }
  .card-text { font-size: 0.9rem; }
}

@media (max-width: 576px) {
  .carousel-item img { max-height: 120px; }
  .text-center h2 { font-size: 1.25rem; }
  .card-text { font-size: 0.85rem; }
}
</style>
