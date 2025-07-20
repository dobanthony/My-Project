<template>
  <DashboardLayout>
  <div class="container">
    <div class="bg-success text-white p-2 mb-1 rounded-top-2">
      <h4><i class="bi bi-bell me-2"></i>Notifications</h4>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <button
        v-if="notifications.length > 0"
        class="btn btn-sm btn-success"
        @click="markAllAsRead"
      >
        Mark All as Read
      </button>
    </div>

    <div v-if="notifications.length === 0" class="alert alert-info">
      No notifications.
    </div>

    <div v-else class="list-group">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="list-group-item list-group-item-action"
        :class="{
          'bg-white text-success fw-bold': !notification.read_at,
          'bg-white text-muted fw-bold': notification.read_at
        }"
        @click="markAsRead(notification.id, notification.data.order_id)"
        style="cursor: pointer"
      >
        <p class="mb-1">{{ notification.data.message }}</p>
        <small class="text-muted">
          Product: {{ notification.data.product_name }} |
          Status: {{ notification.data.status }} |
          {{ new Date(notification.created_at).toLocaleString() }}
        </small>
      </div>
    </div>
  </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { router } from '@inertiajs/vue3'


const props = defineProps({
  notifications: Array,
})

function markAllAsRead() {
  router.post('/notifications/mark-all-as-read', {}, {
    preserveScroll: true,
    preserveState: true,
  })
}

function markAsRead(notificationId, orderId) {
  // Update read_at in local notification list (instant blue style)
  const notif = props.notifications.find(n => n.id === notificationId)
  if (notif) {
    notif.read_at = new Date().toISOString()
  }

  // Send request to backend to mark it read, then redirect
  router.post(`/notifications/${notificationId}/mark-as-read`, {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      window.location.href = `/receipt/${orderId}`
    }
  })
}
</script>
