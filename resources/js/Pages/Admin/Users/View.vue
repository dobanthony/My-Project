<template>
  <AdminDashboardLayout>
    <div class="container py-4">
      <!-- Page Title -->
      <h4 class="mb-4 fw-bold text-success d-flex align-items-center">
        <i class="bi bi-person-circle me-2"></i> Profile Overview
      </h4>

      <!-- Profile Card -->
      <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="row g-0">
          <!-- Left Section: Avatar + Basic Info -->
          <div class="col-md-4 bg-light d-flex flex-column align-items-center justify-content-center p-4 text-center">
            <!-- Avatar -->
            <img
              :src="user.avatar ? `/storage/${user.avatar}` : defaultAvatar"
              alt="User Avatar"
              class="rounded-circle border shadow-sm mb-3"
              style="width: 130px; height: 130px; object-fit: cover;"
            />

            <!-- Full Name -->
            <h4 class="fw-bold mb-1 text-dark">
              {{ user.first_name }}
              <template v-if="user.middle_name"> {{ user.middle_name }} </template>
              {{ user.last_name }}
            </h4>

            <!-- Role -->
            <span class="badge bg-gradient text-success px-3 py-2">
              <i class="bi bi-person-badge me-1"></i> {{ user.role }}
            </span>
          </div>

          <!-- Right Section: Details -->
          <div class="col-md-8 p-4">
            <div class="row row-cols-1 row-cols-md-2 g-4">
              <!-- Email -->
              <div>
                <small class="text-muted d-flex align-items-center mb-1">
                  <i class="bi bi-envelope-at text-success me-2"></i> Email
                </small>
                <p class="fw-semibold mb-0">{{ user.email }}</p>
              </div>

              <!-- Phone -->
              <div>
                <small class="text-muted d-flex align-items-center mb-1">
                  <i class="bi bi-telephone text-success me-2"></i> Phone Number
                </small>
                <p class="fw-semibold mb-0">{{ user.phone || '—' }}</p>
              </div>

              <!-- Address -->
              <div>
                <small class="text-muted d-flex align-items-center mb-1">
                  <i class="bi bi-house text-success me-2"></i> Address
                </small>
                <p class="fw-semibold mb-0">{{ user.address || '—' }}</p>
              </div>

              <!-- Date of Birth -->
              <div>
                <small class="text-muted d-flex align-items-center mb-1">
                  <i class="bi bi-cake2 text-success me-2"></i> Date of Birth
                </small>
                <p class="fw-semibold mb-0">
                  {{ user.dob ? formatDate(user.dob) : '—' }}
                </p>
              </div>

              <!-- Facebook -->
              <div v-if="user.social_links?.facebook">
                <small class="text-muted mb-1">🔗 Facebook</small>
                <p class="fw-semibold mb-0">
                  <a
                    :href="user.social_links.facebook"
                    class="text-success text-decoration-none hover-link"
                    target="_blank"
                  >
                    {{ user.social_links.facebook }}
                  </a>
                </p>
              </div>

              <!-- Twitter -->
              <div v-if="user.social_links?.twitter">
                <small class="text-muted mb-1">🔗 Twitter</small>
                <p class="fw-semibold mb-0">
                  <a
                    :href="user.social_links.twitter"
                    class="text-success text-decoration-none hover-link"
                    target="_blank"
                  >
                    {{ user.social_links.twitter }}
                  </a>
                </p>
              </div>

              <!-- Registration Date -->
              <div>
                <small class="text-muted d-flex align-items-center mb-1">
                  <i class="bi bi-calendar3 text-success me-2"></i> Registered At
                </small>
                <p class="fw-semibold mb-0">{{ formatDate(user.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Back Button -->
      <div class="mt-4">
        <Link href="/admin/users" class="btn btn-success px-4 py-2 rounded-pill shadow-sm">
          <i class="bi bi-backspace me-2"></i> Back
        </Link>
      </div>
    </div>
  </AdminDashboardLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { defineProps } from 'vue'
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'

const props = defineProps({
  user: Object,
})

const defaultAvatar = '/images/default-avatar.jpg'

function formatDate(date) {
  const d = new Date(date)
  return d.toLocaleString('en-PH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
  })
}
</script>

<style scoped>
.hover-link:hover {
  text-decoration: underline;
  color: #0d6efd;
}
</style>
