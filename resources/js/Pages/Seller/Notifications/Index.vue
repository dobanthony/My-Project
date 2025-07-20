<template>
  <SellerDashboardLayout>
    <div class="container">
      <h2 class="mb-4"><i class="bi bi-bell me-2"></i> Notifications</h2>

      <!-- ✅ Mark All as Read Button -->
      <div class="mb-3 text-end" v-if="hasUnread">
        <button class="btn btn-success btn-sm" @click="markAllAsRead">
          <i class="bi bi-check2-all me-2"></i>Mark All as Read
        </button>
      </div>

      <!-- ℹ️ No notifications -->
      <div
        v-if="Array.isArray(notifications) && notifications.length === 0"
        class="alert alert-info"
      >
        No notifications found.
      </div>

      <!-- 🔔 Notification list -->
      <div v-else class="list-group">
        <div
          v-for="n in notifications"
          :key="n.id"
          class="list-group-item list-group-item-action bg-white"
          @click="handleClick(n)"
          style="cursor: pointer; transition: color 0.3s;"
        >
          <!-- Highlight unread vs read -->
          <p
            :class="{
              'text-dark fw-bold': !n.read_at,
              'text-muted fw-normal': n.read_at
            }"
            class="mb-1"
          >
            {{ n.message }}
          </p>

          <!-- 🟡 Reported Issue -->
          <div v-if="n.message.includes('Issue reported')" class="mt-2">
            <span class="badge bg-warning text-dark me-2">📢 Report</span>
            <p class="mb-1"><small class="text-muted">Report Content:</small></p>
            <p class="bg-light border p-2 rounded">
              {{ extractReport(n.message) }}
            </p>
          </div>

          <!-- ✅ Order Received -->
          <div v-else-if="n.message.includes('was marked as received')" class="mt-2">
            <span class="badge bg-success me-2">✅ Order Received</span>
          </div>

          <!-- Timestamp -->
          <p class="mb-0">
            <small class="text-muted">{{ n.created_at }}</small>
          </p>
        </div>
      </div>

      <!-- ⚠️ Deleted Order Modal -->
      <div class="modal fade" tabindex="-1" id="deletedOrderModal" ref="deletedOrderModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
              <h5 class="modal-title">⚠️ Order Not Found</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>
                Order ID <strong>#{{ missingOrderId }}</strong> has been deleted
                or is no longer available.
              </p>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { useNotificationStore } from '@/stores/notification'
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'

const props = defineProps({
  notifications: {
    type: Array,
    default: () => []
  }
})

// Pinia store
const store = useNotificationStore()

onMounted(() => {
  store.setNotifications(props.notifications)
})

const notifications = computed(() => store.notifications)
const hasUnread = computed(() => store.hasUnread)

const missingOrderId = ref(null)
const deletedOrderModal = ref(null)

function extractReport(msg) {
  const parts = msg.split(':')
  return parts.length > 1 ? parts.slice(1).join(':').trim() : 'No details provided.'
}

function handleClick(notification) {
  const { id, order_id, message } = notification

  // If related to an order, try visiting it
  if (order_id) {
    router.post(`/seller/notifications/${id}/read`, {}, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        store.markAsRead(id)
        router.visit(`/seller/orders/${order_id}/receipt`)
      },
      onError: () => {
        missingOrderId.value = order_id
        const modal = new bootstrap.Modal(deletedOrderModal.value)
        modal.show()
      }
    })
  } else {
    // If not order-related (e.g., product deleted), just mark as read
    router.post(`/seller/notifications/${id}/read`, {}, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        store.markAsRead(id)
      }
    })
  }
}

function markAllAsRead() {
  router.post('/seller/notifications/mark-all-as-read', {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      store.markAllAsRead()
    }
  })
}
</script>
