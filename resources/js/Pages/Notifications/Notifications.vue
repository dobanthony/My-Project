<template>
  <Head title="Notifications " />
  <DashboardLayout>
    <div class="container py-4">
      <!-- Page Header -->
      <div class="text-center mb-4">
        <h3 class="fw-bold text-primary">
          <i class="bi bi-bell-fill me-2"></i> Notifications
        </h3>
        <p class="text-muted mb-0">
          Stay up to date with your latest order updates, promotions, and alerts.
        </p>
      </div>

      <!-- Header Bar -->
      <div
        class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 shadow-sm mb-3"
      >
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-inbox-fill text-primary fs-5"></i>
          <h5 class="mb-0 fw-semibold text-dark">Recent Notifications</h5>
        </div>
        <button
          v-if="notifications.length > 0"
          class="btn btn-sm btn-outline-primary d-flex align-items-center"
          @click="markAllAsRead"
        >
          <i class="bi bi-check2-all me-1"></i> Mark All as Read
        </button>
      </div>

      <!-- Empty State -->
      <div
        v-if="notifications.length === 0"
        class="alert alert-info text-center py-5 shadow-sm rounded-3"
      >
        <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>
        <h5 class="fw-semibold">No notifications yet</h5>
        <p class="text-muted mb-0">You're all caught up! Check back later for updates.</p>
      </div>

      <!-- Notifications List -->
      <div v-else class="d-flex flex-column gap-3">
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="card border-0 shadow-sm rounded-3 p-3 position-relative notification-card"
          :class="{
            'bg-light border-start border-4 border-primary': !notification.read_at,
            'bg-white text-muted': notification.read_at
          }"
          @click="markAsRead(notification.id, notification.data.order_id)"
          style="cursor: pointer"
        >
          <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex flex-column">
              <div class="d-flex align-items-center mb-1">
                <i
                  class="bi"
                  :class="notification.read_at ? 'bi-envelope-open text-muted' : 'bi-envelope-fill text-primary'"
                ></i>
                <span
                  class="ms-2 fw-semibold"
                  :class="{ 'text-dark': !notification.read_at }"
                >
                  {{ notification.data.message }}
                </span>
              </div>
              <small class="text-muted">
                <i class="bi bi-box-seam me-1"></i>
                Product: <strong>{{ notification.data.product_name }}</strong>
                <span class="mx-2">|</span>
                <i class="bi bi-info-circle me-1"></i>
                Status: <strong>{{ notification.data.status }}</strong>
                <span class="mx-2">|</span>
                <i class="bi bi-clock me-1"></i>
                {{ new Date(notification.created_at).toLocaleString() }}
              </small>
            </div>

            <span
              v-if="!notification.read_at"
              class="badge bg-primary rounded-pill shadow-sm"
            >
              New
            </span>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  notifications: Array,
})

function markAllAsRead() {
  props.notifications.forEach(n => {
    if (!n.read_at) n.read_at = new Date().toISOString()
  })

  router.post('/user/notifications/mark-all-as-read', {}, {
    preserveScroll: true,
    onSuccess: () => router.reload({ only: ['notifications'] })
  })
}

function markAsRead(notificationId, orderId) {
  const notif = props.notifications.find(n => n.id === notificationId)
  if (notif) notif.read_at = new Date().toISOString()

  router.post(`/notifications/${notificationId}/mark-as-read`, {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      window.location.href = `/receipt/${orderId}`
    }
  })
}
</script>

<style scoped>
.notification-card:hover {
  transform: translateY(-2px);
  transition: all 0.2s ease-in-out;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
