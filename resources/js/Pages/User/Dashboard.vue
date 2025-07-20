<template>
  <Head title="Dashboard" />

  <DashboardLayout>
    <div class="container">
      <!-- ✅ Card: Update Profile -->
      <div class="card shadow-sm border-0 mb-5 rounded-4">
        <div class="card-header bg-success text-white rounded-top-4">
          <strong>Update Your Profile Information</strong>
        </div>
        <div class="card-body">
          <p class="mb-3">
            Keep your account information up-to-date. You can edit your name, email, and password here.
          </p>
          <Link href="/profile" class="btn btn-outline-success">
            Go to Profile Settings
          </Link>
        </div>
      </div>

      <!-- 📢 Announcements Header -->
      <div class="d-flex justify-content-between align-items-center bg-success text-white p-2 rounded mb-4">
        <strong class="ms-2"><i class="bi bi-megaphone me-2"></i> Latest Announcements</strong>
      </div>

      <!-- No Announcements -->
      <div v-if="announcements.length === 0" class="alert alert-success text-center shadow-sm rounded-3">
        No announcements have been posted yet.
      </div>

      <!-- ✅ Responsive Announcement Grid: 2 on mobile, 4 on desktop -->
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <div
          v-for="announcement in announcements"
          :key="announcement.id"
          class="col"
        >
          <div class="card h-100 border-0 shadow-sm announcement-card rounded-4">
            <div class="card-body d-flex flex-column justify-content-between">
              <div>
                <h5 class="card-title announcement-title p-2 rounded bg-light text-success fw-semibold">
                  {{ announcement.title }}
                </h5>
                <p class="card-text text-muted mt-2">
                  {{ truncateContent(announcement.content) }}
                </p>
              </div>
              <div class="mt-3">
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
import { Head, Link } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
  announcements: {
    type: Array,
    default: () => [],
  }
})

// Format date to "June 30, 2025, 5:30 PM"
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

// Truncate long content
function truncateContent(content, maxLength = 120) {
  if (content.length <= maxLength) return content
  return content.slice(0, maxLength) + '...'
}
</script>

<style scoped>
.announcement-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.announcement-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 0.8rem 1.5rem rgba(0, 0, 0, 0.08);
}

.card-title {
  font-size: 1.1rem;
}

.card-text {
  font-size: 0.95rem;
  line-height: 1.5;
}

.announcement-title:hover {
  background-color: #d1e7dd !important;
  color: #0f5132 !important;
  cursor: default;
}
</style>
