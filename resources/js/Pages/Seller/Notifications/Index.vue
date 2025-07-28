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
          <p
            :class="{
              'text-dark fw-bold': !n.read_at,
              'text-muted fw-normal': n.read_at
            }"
            class="mb-1"
          >
            {{ n.message }}
          </p>

          <!-- 🗑 Product Deleted -->
          <div v-if="n.message.includes('was deleted by an admin')" class="mt-2">
            <span class="badge bg-danger me-2">🗑 Product Deleted</span>
            <p class="mb-0">
              <small class="text-muted">This product was removed from your listings.</small>
            </p>
          </div>

          <!-- 🟡 Reported Issue -->
          <div v-else-if="n.message.includes('Issue reported')" class="mt-2">
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

          <!-- ❌ Order Canceled -->
          <div v-else-if="n.message.includes('was canceled')" class="mt-2">
            <span class="badge bg-danger me-2">❌ Order Canceled</span>
          </div>

          <!-- Timestamp -->
          <p class="mb-0">
            <small class="text-muted">{{ n.created_at }}</small>
          </p>
        </div>
      </div>
    </div>

    <!-- 🧾 Modal for Deleted Product Info -->
    <div class="modal fade" ref="deletedProductModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Product <span class="text-danger">Deleted</span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p><strong>Product Name:</strong> {{ modalProductName }}</p>
            <p><strong>Reason:</strong> {{ modalReason }}</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline-success" data-bs-dismiss="modal">Close</button>
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
import * as bootstrap from 'bootstrap'

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

const deletedProductModal = ref(null)
const modalInstance = ref(null)
const modalProductName = ref('')
const modalReason = ref('')

function extractReport(msg) {
  const parts = msg.split(':')
  return parts.length > 1 ? parts.slice(1).join(':').trim() : 'No details provided.'
}

function extractProductDeletionInfo(msg) {
  const match = msg.match(/Your product "(.*?)" was deleted by an admin\. Reason: (.*)/)
  if (match) {
    return {
      productName: match[1],
      reason: match[2]
    }
  }
  return {
    productName: 'Unknown',
    reason: 'No reason provided.'
  }
}

function handleClick(notification) {
  const { id, order_id, product_id, message } = notification

  // Handle order receipt-related
  if (order_id) {
    router.post(`/seller/notifications/${id}/read`, {}, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        store.markAsRead(id)
        router.visit(`/seller/orders/${order_id}/receipt`)
      },
      onError: () => {
        // fallback error modal if order doesn't exist
        alert('Order not found.')
      }
    })
    return
  }

  // Handle deleted product notifications
  if (message.includes('was deleted by an admin')) {
    const info = extractProductDeletionInfo(message)
    modalProductName.value = info.productName
    modalReason.value = info.reason

    router.post(`/seller/notifications/${id}/read`, {}, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        store.markAsRead(id)
        modalInstance.value = new bootstrap.Modal(deletedProductModal.value)
        modalInstance.value.show()
      }
    })
    return
  }

  // Default notification handler
  router.post(`/seller/notifications/${id}/read`, {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      store.markAsRead(id)
    }
  })
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
