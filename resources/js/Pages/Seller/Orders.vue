<template>
  <SellerDashboardLayout>
    <div class="container py-3">

      <!-- Search & Filter -->
      <div class="row gy-2 gx-2 align-items-center mb-3">
        <div class="col-12 col-sm-6 col-md-4">
          <input
            v-model="search"
            @keyup.enter="handleSearch"
            class="form-control"
            placeholder="Search by buyer, product, or status"
          />
        </div>
        <div class="col-6 col-sm-3 col-md-2">
          <button class="btn btn-secondary w-100" @click="handleSearch">
            Search
          </button>
        </div>
        <div class="col-6 col-sm-3 col-md-3">
          <select v-model="statusFilter" class="form-select" @change="handleSearch">
            <option value="">All Statuses</option>
            <option class="text-warning" value="pending">Pending</option>
            <option class="text-success" value="approved">Approved</option>
            <option class="text-dark" value="declined">Declined</option>
            <option class="text-danger" value="canceled">Canceled</option>
          </select>
        </div>
      </div>

      <!-- Table View (Desktop) -->
      <div class="table-responsive d-none d-md-block">
        <table class="table table-hover table-bordered align-middle text-center">
          <thead class="table-success">
            <tr>
              <th>Buyer</th>
              <th>Product</th>
              <th>Qty</th>
              <th>Status</th>
              <th>Delivery Date</th>
              <th>Delivery Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders.data" :key="order.id">
              <td>{{ order.user.first_name }}</td>
              <td>{{ order.product.name }}</td>
              <td>{{ order.quantity }}</td>
              <td>
                <span :class="statusClass(order.status)">{{ order.status }}</span>
              </td>
              <td>
                <div v-if="order.status === 'pending'">
                  <input type="date" v-model="deliveryDates[order.id]" class="form-control form-control-sm" />
                </div>
                <span v-else>{{ order.delivery_date ?? 'N/A' }}</span>
              </td>
              <td>
                <div v-if="order.status === 'approved'">
                  <select
                    class="form-select form-select-sm"
                    v-model="deliveryStatuses[order.id]"
                    @change="updateDeliveryStatus(order.id)"
                  >
                    <option value="pending">Pending</option>
                    <option value="delivered">Delivered</option>
                    <option value="failed">Failed</option>
                  </select>
                </div>
                <span v-else class="badge bg-secondary">
                  {{ order.delivery_status ?? 'N/A' }}
                </span>
              </td>
              <td>
                <div class="d-flex gap-1 flex-wrap justify-content-center">
                  <Link :href="route('seller.orders.view', order.id)" class="btn btn-sm btn-outline-primary">View</Link>
                  <button v-if="order.status === 'pending'" @click="approve(order.id)" class="btn btn-sm btn-success">Approve</button>
                  <button v-if="order.status === 'pending'" @click="decline(order.id)" class="btn btn-sm btn-danger">Decline</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="d-block d-md-none">
        <div v-for="order in orders.data" :key="order.id" class="card mb-3 shadow-sm">
          <div class="card-body">
            <p><strong>👤 Buyer:</strong> {{ order.user.name }}</p>
            <p><strong>🛍️ Product:</strong> {{ order.product.name }}</p>
            <p><strong>🔢 Qty:</strong> {{ order.quantity }}</p>
            <p>
              <strong>📌 Status:</strong>
              <span :class="statusClass(order.status)">{{ order.status }}</span>
            </p>
            <p>
              <strong>📅 Delivery Date:</strong>
              <span v-if="order.status !== 'pending'">{{ order.delivery_date ?? 'N/A' }}</span>
              <input v-else type="date" v-model="deliveryDates[order.id]" class="form-control form-control-sm" />
            </p>
            <p>
              <strong>🚚 Delivery Status:</strong>
              <span v-if="order.status !== 'approved'" class="badge bg-secondary">
                {{ order.delivery_status ?? 'N/A' }}
              </span>
              <select
                v-else
                class="form-select form-select-sm"
                v-model="deliveryStatuses[order.id]"
                @change="updateDeliveryStatus(order.id)"
              >
                <option value="pending">📦 Pending</option>
                <option value="delivered">✅ Delivered</option>
                <option value="failed">❌ Failed</option>
              </select>
            </p>
            <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
              <Link :href="route('seller.orders.view', order.id)" class="btn btn-sm btn-primary">View</Link>
              <button v-if="order.status === 'pending'" @click="approve(order.id)" class="btn btn-sm btn-success">Approve</button>
              <button v-if="order.status === 'pending'" @click="decline(order.id)" class="btn btn-sm btn-danger">Decline</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="orders.data.length === 0" class="alert alert-info mt-3 text-center">
        No matching orders found.
      </div>

      <!-- Pagination -->
      <nav v-if="orders.links.length > 3" class="mt-4">
        <ul class="pagination justify-content-center flex-wrap gap-1">
          <li v-for="(link, index) in orders.links" :key="index" class="page-item" :class="{ active: link.active, disabled: !link.url }">
            <Link class="page-link" :href="link.url || ''" v-html="link.label" preserve-scroll preserve-state />
          </li>
        </ul>
      </nav>

      <!-- Toast -->
      <div v-if="toastMessage" class="position-fixed top-0 start-50 translate-middle-x p-3 w-100" style="z-index: 1055; max-width: 360px;">
        <div class="toast show text-white bg-success d-flex align-items-center" role="alert">
          <div class="toast-body">
            <span v-html="toastMessage"></span>
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="toastMessage = ''"></button>
        </div>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'

const props = defineProps({
  orders: Object,
  filters: Object,
})

const search = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || '')
const deliveryDates = ref({})
const deliveryStatuses = ref({})
const toastMessage = ref('')
const loadingStates = ref({})

// Status color helper
const statusClass = (status) => {
  return {
    'text-warning': status === 'pending',
    'text-success': status === 'approved',
    'text-danger': status === 'declined',
    'text-secondary': status === 'canceled'
  }
}

watch(
  () => props.orders,
  (newOrders) => {
    deliveryStatuses.value = {}
    newOrders.data.forEach(order => {
      deliveryStatuses.value[order.id] = order.delivery_status || 'pending'
      loadingStates.value[order.id] = false
    })
  },
  { immediate: true }
)

const handleSearch = () => {
  router.get('/seller/orders', {
    search: search.value,
    status: statusFilter.value,
  }, { preserveScroll: true, preserveState: true })
}

const approve = (id) => {
  const order = props.orders.data.find(o => o.id === id)
  if (!order || order.status !== 'pending') return
  if (!deliveryDates.value[id]) {
    toastMessage.value = '<i class="bi bi-exclamation-circle-fill me-2"></i>Please select a delivery date.'
    return
  }
  if (loadingStates.value[id]) return
  loadingStates.value[id] = true

  router.post(`/seller/orders/${id}/approve`, {
    delivery_date: deliveryDates.value[id],
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toastMessage.value = '<i class="bi bi-check-circle-fill me-2"></i>Order approved.'
      router.reload({ only: ['orders'] })
    },
    onFinish: () => { loadingStates.value[id] = false }
  })
}

const decline = (id) => {
  if (loadingStates.value[id]) return
  loadingStates.value[id] = true

  router.post(`/seller/orders/${id}/decline`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      toastMessage.value = '<i class="bi bi-x-circle-fill me-2"></i>Order declined.'
      router.reload({ only: ['orders'] })
    },
    onFinish: () => { loadingStates.value[id] = false }
  })
}

const updateDeliveryStatus = (id) => {
  const status = deliveryStatuses.value[id]
  const iconMap = {
    pending: 'bi-box-seam',
    delivered: 'bi-truck',
    failed: 'bi-exclamation-triangle-fill',
  }
  router.post(`/seller/orders/${id}/delivery-status`, {
    delivery_status: status,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toastMessage.value = `<i class="bi ${iconMap[status]} me-2"></i>Delivery status updated to "${status}".`
      router.reload({ only: ['orders'] })
    }
  })
}

watch(toastMessage, (val) => {
  if (val) {
    setTimeout(() => { toastMessage.value = '' }, 5000)
  }
})

let pollingInterval = null
onMounted(() => {
  pollingInterval = setInterval(() => { router.reload({ only: ['orders'] }) }, 10000)
})
onBeforeUnmount(() => {
  if (pollingInterval) clearInterval(pollingInterval)
})
</script>

<style scoped>
/* input.form-control:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5);
} */

.pagination .page-link {
  color: #28a745;
  background-color: #fff;
  border-color: #28a745;
}
.pagination .page-link:hover {
  color: white;
  background-color: #28a745;
  border-color: #28a745;
}
.pagination .page-link:focus {
  border-color: black;
  box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.5);
}
.pagination .page-link {
  color: #28a745;
  background-color: #fff;
  border-color: #28a745;
  padding: 6px 12px;
  min-width: 40px;
  text-align: center;
  font-size: 1rem;
}
.pagination .page-link:hover {
  color: #fff;
  background-color: #28a745;
  border-color: #28a745;
}
.pagination .page-link:focus {
  border-color: black;
  box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.5);
}

/* Mobile adjustments */
@media (max-width: 576px) {
  .pagination {
    flex-wrap: wrap;
    gap: 4px;
  }
  .pagination .page-link {
    padding: 4px 8px;
    font-size: 0.85rem;
    min-width: 32px;
  }
  .pagination li {
    flex: 0 0 auto;
  }
}
@media (max-width: 576px) {
  .btn {
    width: 100%;
  }
  .form-control, .form-select {
    font-size: 14px;
  }
}
</style>
