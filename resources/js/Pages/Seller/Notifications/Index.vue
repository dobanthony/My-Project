<template>
  <Head title="Notifications " />
  <SellerDashboardLayout>
    <div class="container py-1">

      <!-- 🏷️ Page Header -->
      <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle p-3 mb-3">
          <i class="bi bi-bell-fill text-primary fs-2"></i>
        </div>
        <h2 class="fw-bold text-dark">Notifications</h2>
        <p class="text-muted mb-0">
          Stay updated with your latest store activities, order updates, and admin alerts.
        </p>
      </div>

      <!-- ✅ Mark All as Read -->
      <div class="text-end mb-3" v-if="hasUnread">
        <button class="btn btn-sm btn-primary rounded-pill shadow-sm" @click="markAllAsRead">
          <i class="bi bi-check2-all me-2"></i>Mark All as Read
        </button>
      </div>

      <!-- No Notifications -->
      <div
        v-if="Array.isArray(notifications) && notifications.length === 0"
        class="alert alert-info text-center shadow-sm rounded-3"
      >
        <i class="bi bi-inbox me-2"></i>No notifications found.
      </div>

      <!-- 🔔 Notification List -->
      <div v-else class="list-group">
        <div
          v-for="n in notifications"
          :key="n.id"
          class="list-group-item list-group-item-action border-0 shadow-sm mb-3 rounded-3 p-3"
          @click="handleClick(n)"
          style="cursor: pointer; transition: all 0.3s ease;"
        >
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <p
                :class="{
                  'fw-bold text-dark mb-1': !n.read_at,
                  'text-muted mb-1': n.read_at
                }"
                class="fs-6"
              >
                <i
                  :class="[
                    'me-2',
                    n.message.includes('deleted') ? 'bi bi-trash text-danger' :
                    n.message.includes('Issue') ? 'bi bi-exclamation-triangle text-warning' :
                    n.message.includes('received') ? 'bi bi-check-circle text-success' :
                    n.message.includes('canceled') ? 'bi bi-x-circle text-danger' :
                    'bi bi-info-circle text-primary'
                  ]"
                ></i>
                {{ n.message }}
              </p>

              <!-- Product Deleted -->
              <div v-if="n.message.includes('was deleted by an admin')" class="mt-2">
                <span class="badge bg-danger-subtle text-danger border border-danger me-2">
                  <i class="bi bi-trash me-1"></i> Product Deleted
                </span>
                <small class="text-muted d-block">This product was removed from your listings.</small>
              </div>

              <!-- Reported Issue -->
              <div v-else-if="n.message.includes('Issue reported')" class="mt-2">
                <span class="badge bg-warning text-dark me-2">
                  <i class="bi bi-exclamation-triangle me-1"></i> Reported Issue
                </span>
                <p class="text-muted mb-1"><small>Report Content:</small></p>
                <div class="bg-light border rounded p-2">
                  {{ extractReport(n.message) }}
                </div>
              </div>

              <!-- Order Received -->
              <div v-else-if="n.message.includes('was marked as received')" class="mt-2">
                <span class="badge bg-success">
                  <i class="bi bi-check2-circle me-1"></i> Order Received
                </span>
              </div>

              <!-- Order Canceled -->
              <div v-else-if="n.message.includes('was canceled')" class="mt-2">
                <span class="badge bg-danger">
                  <i class="bi bi-x-circle me-1"></i> Order Canceled
                </span>
              </div>

              <!-- Timestamp -->
              <p class="mb-0 mt-2">
                <small class="text-muted">
                  <i class="bi bi-clock me-1"></i>{{ n.created_at }}
                </small>
              </p>
            </div>

            <!-- Unread Indicator -->
            <span
              v-if="!n.read_at"
              class="badge bg-primary rounded-circle align-self-center"
              style="width: 10px; height: 10px;"
            ></span>
          </div>
        </div>
      </div>
    </div>

    <!-- 🧾 Modal for Deleted Product Info -->
    <div class="modal fade" ref="deletedProductModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <div class="modal-header bg-danger text-white rounded-top-3">
            <h5 class="modal-title">
              <i class="bi bi-trash3-fill me-2"></i>Product Deleted
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p><strong>Product Name:</strong> {{ modalProductName }}</p>
            <p><strong>Reason:</strong> {{ modalReason }}</p>
          </div>
          <div class="modal-footer border-0">
            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
              <i class="bi bi-x-circle me-1"></i> Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import { useNotificationStore } from '@/stores/notification'
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'
import * as bootstrap from 'bootstrap'

const props = defineProps({
  notifications: {
    type: Array,
    default: () => []
  }
})

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

  if (order_id) {
    router.post(`/seller/notifications/${id}/read`, {}, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        store.markAsRead(id)
        router.visit(`/seller/orders/${order_id}/receipt`)
      },
      onError: () => alert('Order not found.')
    })
    return
  }

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

  router.post(`/seller/notifications/${id}/read`, {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => store.markAsRead(id)
  })
}

function markAllAsRead() {
  router.post('/seller/notifications/mark-all-as-read', {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => store.markAllAsRead()
  })
}
</script>
