<template>
  <Head title="Dashboard" />

  <DashboardLayout>
    <div class="container py-3">

      <!-- 🌟 Introduction Section -->
      <div class="text-center mb-5 px-3">
        <h2 class="fw-bold text-secondary mb-3">
          <i class="bi bi-shop text-success me-2"></i> Welcome
          <span class="text-primary">{{ user ? user.first_name : '' }}</span> to CraftSmart Artisan
        </h2>
        <p class="text-muted fs-6 mx-auto" style="max-width: 750px;">
          CraftSmart Artisan is your trusted online marketplace connecting creative artisans and passionate shoppers.
          Discover handmade crafts, support local creators, and enjoy a seamless shopping experience from browsing to delivery.
        </p>
      </div>

      <!-- 🌀 CAROUSEL -->
      <div
        id="craftsmartCarousel"
        class="carousel slide carousel-fade carousel-3d mb-5 position-relative"
        data-bs-ride="carousel"
        data-bs-interval="3000"
      >

        <!-- Navigation Dots -->
        <div class="carousel-indicators">
          <button
            v-for="(item, index) in carouselItems"
            :key="'indicator-' + index"
            type="button"
            data-bs-target="#craftsmartCarousel"
            :data-bs-slide-to="index"
            :class="{ active: index === 0 }"
          ></button>
        </div>

        <!-- Slides -->
        <div class="carousel-inner fixed-carousel-height">
          <div
            v-for="(item, index) in carouselItems"
            :key="'slide-' + index"
            :class="['carousel-item', { active: index === 0 }]"
          >
            <div class="carousel-content-box">
              <img :src="item.img" class="bigger-img mb-3" :alt="item.title">
              <h5 class="fw-bold text-primary">{{ item.title }}</h5>
              <p class="text-muted small text-center mb-0">{{ item.desc }}</p>
            </div>
          </div>
        </div>

        <!-- Arrows INSIDE -->
        <button class="carousel-control-prev inside-arrow" type="button" data-bs-target="#craftsmartCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="carousel-control-next inside-arrow" type="button" data-bs-target="#craftsmartCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
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

    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'

const { props } = usePage()
const user = props.user || null
const announcements = props.announcements || []

/* Carousel content */
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
  return content.length <= maxLength ? content : content.slice(0, maxLength) + '...'
}
</script>

<style scoped>
/* Fix height */
.fixed-carousel-height {
  height: 320px;
}

/* Center slide content */
.carousel-content-box {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 25px;
  background: #f8f9fa;
  border-radius: 20px;
}

/* Images */
.bigger-img {
  width: 220px;
  max-height: 220px;
  object-fit: contain;
}

/* ARROWS INSIDE THE CAROUSEL */
.inside-arrow {
  width: 50px;
  height: 50px;
  background: rgba(0, 0, 0, 0.45);
  border-radius: 50%;
  top: 50%;
  transform: translateY(-50%);
  opacity: 0;
  transition: 0.3s ease;
  pointer-events: none;
}

#craftsmartCarousel:hover .inside-arrow {
  opacity: 1;
  pointer-events: auto;
}

.inside-arrow.carousel-control-prev {
  left: 15px;
}

.inside-arrow.carousel-control-next {
  right: 15px;
}

/* White icons */
.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: invert(100%);
}

/* Navigation dots */
.carousel-indicators button {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background-color: #aaa;
}
.carousel-indicators .active {
  background-color: #0d6efd;
}

/* Smooth 3D fade */
.carousel-3d .carousel-item {
  transition: opacity 0.8s ease, transform 0.8s ease;
  transform: scale(0.96);
  opacity: 0;
}
.carousel-3d .carousel-item.active {
  transform: scale(1);
  opacity: 1;
}

/* Announcement styling */
.announcement-header {
  background: linear-gradient(90deg, #4db6ac, #80cbc4);
  color: #fff;
  box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);
}
.announcement-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.announcement-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 1rem 2rem rgba(0,0,0,0.08);
}

/* Responsive */
@media (max-width: 768px) {
  .fixed-carousel-height {
    height: 260px;
  }
  .bigger-img {
    max-height: 160px;
  }
}
@media (max-width: 576px) {
  .inside-arrow {
    width: 40px;
    height: 40px;
  }
  .fixed-carousel-height {
    height: 240px;
  }
  .bigger-img {
    max-height: 130px;
  }
}
</style>
